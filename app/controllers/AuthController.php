<?php
class AuthController extends BaseController {
    private $userModel;

    public function __construct($db) {
        $this->userModel = new User($db);
    }

    private function validateEmail($email) {
        return filter_var($email, FILTER_VALIDATE_EMAIL);
    }

    private function validatePassword($password) {
        return strlen($password) >= 8;
    }

    public function register() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $fname = trim($_POST['first_name'] ?? '');
            $lname = trim($_POST['last_name'] ?? '');
            $mname = trim($_POST['middle_name'] ?? '');
            $email = trim($_POST['email'] ?? '');
            $password = $_POST['password'] ?? '';
            $confirmPassword = $_POST['confirm_password'] ?? '';

            // Validation
            if (empty($fname) || empty($lname) || empty($email) || empty($password)) {
                $_SESSION['error'] = 'All required fields must be filled';
                return;
            }

            if (!$this->validateEmail($email)) {
                $_SESSION['error'] = 'Invalid email format';
                return;
            }

            if (!$this->validatePassword($password)) {
                $_SESSION['error'] = 'Password must be at least 8 characters';
                return;
            }

            if ($password !== $confirmPassword) {
                $_SESSION['error'] = 'Passwords do not match';
                return;
            }

            // Check if email already exists
            $existing = $this->userModel->getByEmail($email);
            if ($existing) {
                $_SESSION['error'] = 'Email already registered';
                return;
            }

            $token = bin2hex(random_bytes(32));

            error_log("[REGISTER] Attempting to register: $email");
            
            if ($this->userModel->register($fname, $lname, $mname, $email, $password, $token)) {
                error_log("[REGISTER] User record created, now sending verification email");
                
                // Send verification email
                $emailResult = $this->sendVerificationEmail($email, $fname, $token);
                
                error_log("[REGISTER] Email send result: " . ($emailResult ? 'success' : 'failed'));
                
                if ($emailResult) {
                    error_log("[REGISTER] Registration complete, redirecting to verification pending");
                    $_SESSION['success'] = 'Registration successful! Check your email to verify your account.';
                } else {
                    error_log("[REGISTER] Email failed but user was created. This is a problem!");
                    $_SESSION['warning'] = 'Registration successful but email could not be sent. Check your spam folder or try again.';
                }
                
                header('Location: index.php?action=verification-pending');
                exit;
            } else {
                error_log("[REGISTER] User registration failed");
                $_SESSION['error'] = 'Registration failed. Please try again.';
            }
        }
    }

    public function verifyEmail() {
        $token = $_GET['token'] ?? '';

        if (empty($token)) {
            $_SESSION['error'] = 'Invalid verification link';
            header('Location: index.php?action=login');
            exit;
        }

        if ($this->userModel->verifyEmail($token)) {
            $_SESSION['success'] = 'Email verified successfully! You can now log in.';
            header('Location: index.php?action=login');
            exit;
        } else {
            $_SESSION['error'] = 'Verification link is invalid or expired';
            header('Location: index.php?action=register');
            exit;
        }
    }

    public function login() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = trim($_POST['email'] ?? '');
            $password = $_POST['password'] ?? '';

            if (empty($email) || empty($password)) {
                $_SESSION['login_error'] = 'Email and password are required';
                return;
            }

            $user = $this->userModel->getByEmail($email);

            if (!$user) {
                $_SESSION['login_error'] = 'Invalid email or password';
                return;
            }

            // Check if email is verified
            if (!$user['is_verified']) {
                $_SESSION['login_error'] = 'Please verify your email before logging in';
                return;
            }

            if (!password_verify($password, $user['password'])) {
                $_SESSION['login_error'] = 'Invalid email or password';
                return;
            }

            // Generate and send OTP
            error_log("[LOGIN] OTP generation and sending for user: " . $user['email']);
            $otp = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
            $this->userModel->saveOTP($user['id'], $otp);
            error_log("[LOGIN] OTP saved to database: $otp");
            
            $emailResult = $this->sendOTPEmail($user['email'], $user['first_name'], $otp);
            error_log("[LOGIN] OTP email send result: " . ($emailResult ? 'success' : 'failed'));

            // Store user ID temporarily for OTP verification
            $_SESSION['temp_user_id'] = $user['id'];
            $_SESSION['temp_user_data'] = [
                'id' => $user['id'],
                'first_name' => $user['first_name'],
                'last_name' => $user['last_name'],
                'email' => $user['email'],
            ];

            error_log("[LOGIN] Redirecting to OTP verification page");
            header('Location: index.php?action=verify-otp');
            exit;
        }
    }

    public function verifyOTP() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $otp = trim($_POST['otp'] ?? '');
            $userId = $_SESSION['temp_user_id'] ?? null;

            if (!$userId) {
                $_SESSION['otp_error'] = 'Session expired. Please login again.';
                return;
            }

            if (empty($otp)) {
                $_SESSION['otp_error'] = 'OTP is required';
                return;
            }

            if ($this->userModel->verifyOTP($userId, $otp)) {
                // OTP verified, set proper session
                $_SESSION['user_data'] = $_SESSION['temp_user_data'];
                unset($_SESSION['temp_user_id']);
                unset($_SESSION['temp_user_data']);
                
                // Redirect to home
                header('Location: index.php?action=home');
                exit;
            } else {
                $_SESSION['otp_error'] = 'Invalid or expired OTP';
            }
        }
    }


    private function sendVerificationEmail($email, $name, $token) {
        error_log("[VERIFY_EMAIL] Function called for $email");
        
        require_once __DIR__ . '/../config/email.php';
        
        $verificationLink = "http://" . $_SERVER['HTTP_HOST'] . "/heartifact-mid/?action=verify-email&token=" . $token;
        $subject = "Verify Your Email - Heartifact";
        
        $body = "
        <html>
        <body style='font-family: Arial, sans-serif;'>
            <h2>Welcome to Heartifact!</h2>
            <p>Hi {$name},</p>
            <p>Click the link below to verify your email address:</p>
            <p><a href='{$verificationLink}' style='background-color: #007bff; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>Verify Email</a></p>
            <p>Or copy this link: <br><small>{$verificationLink}</small></p>
            <p>This link expires in 24 hours.</p>
            <p>Best regards,<br>Heartifact Team</p>
        </body>
        </html>
        ";

        error_log("[VERIFY_EMAIL] Calling sendEmail function");
        $result = sendEmail($email, $name, $subject, $body);
        error_log("[VERIFY_EMAIL] sendEmail returned: " . ($result ? 'true' : 'false'));
        
        return $result;
    }

    private function sendOTPEmail($email, $name, $otp) {
        error_log("[OTP_EMAIL] Function called for $email");
        
        require_once __DIR__ . '/../config/email.php';
        
        $subject = "Your OTP Code - Heartifact";
        
        $body = "
        <html>
        <body style='font-family: Arial, sans-serif;'>
            <h2>OTP Verification</h2>
            <p>Hi {$name},</p>
            <p>Your OTP code is:</p>
            <h1 style='color: #007bff;'>{$otp}</h1>
            <p>This code expires in 10 minutes.</p>
            <p>If you didn't request this, please ignore this email.</p>
            <p>Best regards,<br>Heartifact Team</p>
        </body>
        </html>
        ";

        error_log("[OTP_EMAIL] Calling sendEmail function");
        $result = sendEmail($email, $name, $subject, $body);
        error_log("[OTP_EMAIL] sendEmail returned: " . ($result ? 'true' : 'false'));
        
        return $result;
    }
}
