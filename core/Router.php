<?php
/**
 * Router Class
 * Handles routing logic for the application
 */
class Router {
    private $auth;

    public function __construct($authController) {
        $this->auth = $authController;
    }

    /**
     * Dispatch the request to the appropriate handler
     */
    public function dispatch() {
        $action = $_GET['action'] ?? '';

        switch ($action) {
            case 'register':
                $this->auth->register();
                include __DIR__ . '/../app/views/register.php';
                break;

            case 'verify-email':
                $this->auth->verifyEmail();
                break;

            case 'verification-pending':
                include __DIR__ . '/../app/views/verify_email.php';
                break;

            case 'login':
                $this->auth->login();
                include __DIR__ . '/../app/views/login.php';
                break;

            case 'verify-otp':
                $this->auth->verifyOTP();
                include __DIR__ . '/../app/views/otp.php';
                break;

            case 'home':
                if (isset($_SESSION['user_data'])) {
                    include __DIR__ . '/../app/views/home.php';
                } else {
                    header('Location: index.php?action=login');
                    exit;
                }
                break;

            default:
                header('Location: index.php?action=login');
                exit;
        }
    }
}
?>