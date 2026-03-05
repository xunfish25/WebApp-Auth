<?php
class User {
    private $conn;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function register($fname, $lname, $mname, $email, $password, $token) {
        // Hash password using password_hash() per requirement
        $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

        $stmt = $this->conn->prepare("INSERT INTO users (first_name, last_name, middle_name, email, password, is_verified) VALUES (?, ?, ?, ?, ?, 0)");
        $stmt->execute([$fname, $lname, $mname, $email, $hashedPassword]);
        
        $userId = $this->conn->lastInsertId();
        $expires = date("Y-m-d H:i:s", strtotime('+24 hours'));
        try {
            $stmtToken = $this->conn->prepare("INSERT INTO email_verifications (user_id, token, expires_at) VALUES (?, ?, ?)");
            return $stmtToken->execute([$userId, $token, $expires]);
        } catch (PDOException $e) {
            // if the column doesn't exist, attempt to add it automatically
            if (strpos($e->getMessage(), 'Unknown column') !== false && strpos($e->getMessage(), 'expires_at') !== false) {
                $this->conn->exec("ALTER TABLE email_verifications ADD expires_at TIMESTAMP NULL");
                $stmtToken = $this->conn->prepare("INSERT INTO email_verifications (user_id, token, expires_at) VALUES (?, ?, ?)");
                return $stmtToken->execute([$userId, $token, $expires]);
            }
            throw $e;
        }
    }

    public function getByEmail($email) {
        $stmt = $this->conn->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function isVerified($userId) {
        $stmt = $this->conn->prepare("SELECT is_verified FROM users WHERE id = ?");
        $stmt->execute([$userId]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result && $result['is_verified'] == 1;
    }

    public function verifyEmail($token) {
        $stmt = $this->conn->prepare("SELECT user_id FROM email_verifications WHERE token = ? AND expires_at > NOW()");
        $stmt->execute([$token]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($result) {
            $userId = $result['user_id'];
            $updateStmt = $this->conn->prepare("UPDATE users SET is_verified = 1 WHERE id = ?");
            $updateStmt->execute([$userId]);
            
            // Delete the token after verification
            $deleteStmt = $this->conn->prepare("DELETE FROM email_verifications WHERE user_id = ?");
            $deleteStmt->execute([$userId]);
            
            return true;
        }
        return false;
    }

    public function saveOTP($userId, $otp) {
        // compute expiration on the database side to avoid timezone mismatch
        error_log("[OTP] Saving OTP for user $userId: $otp (database will set expiry +10 minutes)");
        // Delete any existing OTP for this user
        $deleteStmt = $this->conn->prepare("DELETE FROM otp_codes WHERE user_id = ?");
        $deleteStmt->execute([$userId]);
        
        $stmt = $this->conn->prepare("INSERT INTO otp_codes (user_id, otp_code, expires_at) VALUES (?, ?, DATE_ADD(NOW(), INTERVAL 10 MINUTE))");
        $result = $stmt->execute([$userId, $otp]);
        error_log("[OTP] saveOTP query result: " . ($result ? 'true' : 'false'));
        
        // fetch back the stored expiry for debugging
        $check = $this->conn->prepare("SELECT expires_at FROM otp_codes WHERE user_id = ? AND otp_code = ?");
        $check->execute([$userId, $otp]);
        $row = $check->fetch(PDO::FETCH_ASSOC);
        if ($row) {
            error_log("[OTP] Stored expires_at from DB: " . $row['expires_at']);
        }
        
        return $result;
    }

    public function verifyOTP($userId, $otp) {
        error_log("[OTP] Verifying OTP for user $userId with code $otp");
        // also log current time and matching row if exists
        $stmt = $this->conn->prepare("SELECT id, expires_at FROM otp_codes WHERE user_id = ? AND otp_code = ? AND expires_at >= NOW()");
        $stmt->execute([$userId, $otp]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($result) {
            error_log("[OTP] OTP record found, expires_at=" . $result['expires_at']);
            // Delete the OTP after verification
            $deleteStmt = $this->conn->prepare("DELETE FROM otp_codes WHERE id = ?");
            $deleteStmt->execute([$result['id']]);
            return true;
        } else {
            // try to fetch the row even if expired to log its timestamp
            $stmt2 = $this->conn->prepare("SELECT id, expires_at FROM otp_codes WHERE user_id = ? AND otp_code = ?");
            $stmt2->execute([$userId, $otp]);
            $maybe = $stmt2->fetch(PDO::FETCH_ASSOC);
            if ($maybe) {
                error_log("[OTP] Found OTP but expired at " . $maybe['expires_at'] . " (now=" . date('Y-m-d H:i:s') . ")");
            } else {
                error_log("[OTP] No OTP record matched" );
            }
        }
        return false;
    }

    public function getVerificationToken($userId) {
        $stmt = $this->conn->prepare("SELECT token FROM email_verifications WHERE user_id = ?");
        $stmt->execute([$userId]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ? $result['token'] : null;
    }

    /**
     * Handle Google OAuth login/create logic.
     * Returns user array on success or false on failure.
     */
    public function googleLogin($email, $name, $googleId = null) {
        $user = $this->getByEmail($email);
        if ($user) {
            return $user;
        }
        // split full name into first/last
        $parts = explode(' ', $name, 2);
        $fname = $parts[0] ?? '';
        $lname = $parts[1] ?? '';
        // generate random password (never shown)
        $randomPass = bin2hex(random_bytes(8));
        $token = bin2hex(random_bytes(32));
        $this->register($fname, $lname, '', $email, $randomPass, $token);
        // mark account verified immediately
        $this->verifyEmail($token);
        return $this->getByEmail($email);
    }
}