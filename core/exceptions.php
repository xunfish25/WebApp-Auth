<?php
/**
 * Custom Exception Classes
 */

/**
 * Authentication Exception
 */
class AuthenticationException extends Exception {
    public function __construct($message = "Authentication failed", $code = 0, Throwable $previous = null) {
        parent::__construct($message, $code, $previous);
    }
}

/**
 * Validation Exception
 */
class ValidationException extends Exception {
    public function __construct($message = "Validation failed", $code = 0, Throwable $previous = null) {
        parent::__construct($message, $code, $previous);
    }
}

/**
 * Database Exception
 */
class DatabaseException extends Exception {
    public function __construct($message = "Database error", $code = 0, Throwable $previous = null) {
        parent::__construct($message, $code, $previous);
    }
}
?>