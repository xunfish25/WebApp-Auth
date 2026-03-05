<?php
/**
 * Helper Functions
 * Utility functions used throughout the application
 */

/**
 * Validate email format
 */
function validateEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL);
}

/**
 * Sanitize output for HTML
 */
function safe($string) {
    return htmlspecialchars($string, ENT_QUOTES, 'UTF-8');
}

/**
 * Check if user is logged in
 */
function isLoggedIn() {
    return isset($_SESSION['user_data']);
}

/**
 * Redirect to a specific action
 */
function redirect($action) {
    header('Location: index.php?action=' . $action);
    exit;
}

/**
 * Generate a random string
 */
function randomString($length = 32) {
    return bin2hex(random_bytes($length / 2));
}

/**
 * Get current user data
 */
function currentUser() {
    return $_SESSION['user_data'] ?? null;
}
?>