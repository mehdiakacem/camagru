<?php

namespace Core;

class PasswordValidator
{
    private const MIN_LENGTH = 8;
    private const MAX_LENGTH = 128;

    /**
     * Validates password complexity
     * Requirements:
     * - At least 8 characters
     * - At least one uppercase letter
     * - At least one lowercase letter
     * - At least one digit
     * - At least one special character
     * 
     * @param string $password The password to validate
     * @return array ['valid' => bool, 'errors' => array]
     */
    public static function validate($password)
    {
        $errors = [];

        // Check if password is empty
        if (empty($password)) {
            $errors[] = "Password is required";
            return ['valid' => false, 'errors' => $errors];
        }

        // Check minimum length
        if (strlen($password) < self::MIN_LENGTH) {
            $errors[] = "Password must be at least " . self::MIN_LENGTH . " characters long";
        }

        // Check maximum length
        if (strlen($password) > self::MAX_LENGTH) {
            $errors[] = "Password must not exceed " . self::MAX_LENGTH . " characters";
        }

        // Check for at least one uppercase letter
        if (!preg_match('/[A-Z]/', $password)) {
            $errors[] = "Password must contain at least one uppercase letter";
        }

        // Check for at least one lowercase letter
        if (!preg_match('/[a-z]/', $password)) {
            $errors[] = "Password must contain at least one lowercase letter";
        }

        // Check for at least one digit
        if (!preg_match('/\d/', $password)) {
            $errors[] = "Password must contain at least one number";
        }

        // Check for at least one special character
        if (!preg_match('/[^A-Za-z0-9]/', $password)) {
            $errors[] = "Password must contain at least one special character (!@#$%^&*()_+-=[]{}|;:,.<>?)";
        }

        return [
            'valid' => empty($errors),
            'errors' => $errors
        ];
    }
}