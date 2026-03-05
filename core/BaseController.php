<?php
/**
 * Base Controller Class
 * Provides common functionality for all controllers
 */
abstract class BaseController {
    protected $conn;

    public function __construct($db) {
        $this->conn = $db;
    }

    /**
     * Redirect to a specific action
     */
    protected function redirect($action) {
        header('Location: index.php?action=' . $action);
        exit;
    }

    /**
     * Set a flash message in session
     */
    protected function setFlash($key, $message) {
        $_SESSION[$key] = $message;
    }

    /**
     * Get and clear a flash message
     */
    protected function getFlash($key) {
        $message = $_SESSION[$key] ?? null;
        unset($_SESSION[$key]);
        return $message;
    }
}
?>