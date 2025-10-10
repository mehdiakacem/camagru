<?php

namespace Controllers;

use Core\Model;
use PasswordValidator;

class AuthController
{
    public function __construct(
        private \Core\Authentication $authentication,
        private Model $usersModel
    ) {}

    public function login()
    {
        return [
            'view' => 'auth/login.php',
            'title' => 'Login'
        ];
    }

    public function signup()
    {
        return [
            'view' => 'auth/signup.php',
            'title' => 'Sign Up'
        ];
    }

    public function signupSubmit()
    {
        $user = $_POST['user'];
        $errors = [];

        if (empty($user['email'])) {
            $errors[] = 'Email cannot be blank';
        } else if (filter_var($user['email'], FILTER_VALIDATE_EMAIL) == false) {
            $errors[] = 'Invalid email address';
        } else {
            $user['email'] = strtolower($user['email']);
            if (count($this->usersModel->find('email', $user['email'])) > 0) {
                $errors[] = 'That email address is already registered';
            }
        }

        if (empty($user['name'])) {
            $errors[] = 'Username cannot be blank';
        } else {

            if (count($this->usersModel->find('name', $user['name'])) > 0) {
                $errors[] = 'That username is already registered';
            }
        }

        $passwordValidation = \Core\PasswordValidator::validate($user['password']);

        if ($passwordValidation['valid'] !== true) {
            $errors = array_merge($errors, $passwordValidation['errors']);
        }

        if (count($errors) === 0) {
            // Hash password
            $user['password'] = password_hash($user['password'], PASSWORD_DEFAULT);

            // Generate unique verification token
            $user['verification_token'] = bin2hex(random_bytes(32)); // 64 character token

            // Insert user into database (unverified)
            $this->usersModel->save($user);

            // Get base URL from environment or auto-detect
            $base_url = $_ENV['APP_URL'] ?? 'http://localhost';
            // Send verification email
            $verification_link = $base_url . "/auth/verify/"
                . $user['verification_token'];

            $to = $user['email'];
            $subject = "Verify your Camagru account";
            $message = "
                <html>
                <head>
                    <title>Verify Your Account</title>
                </head>
                <body>
                    <h2>Welcome to Camagru, {$user['name']}!</h2>
                    <p>Please click the link below to verify your email address:</p>
                    <p><a href='{$verification_link}'>Verify My Account</a></p>
                    <p>Or copy and paste this link into your browser:</p>
                    <p>{$verification_link}</p>
                    <p>This link will expire in 24 hours.</p>
                    <p>If you did not create this account, please ignore this email.</p>
                </body>
                </html>
            ";

            // Headers for HTML email
            $headers = "MIME-Version: 1.0" . "\r\n";
            $headers .= "Content-Type: text/html;charset=UTF-8" . "\r\n";
            $headers .= "From: noreply@camagru.com" . "\r\n";

            // Send email
            // mail($to, $subject, $message, $headers);
            if (mail($to, $subject, $message, $headers)) {
                header('Location: /auth/success');
                exit();
            } else {
                throw new \Exception("Failed to send verification email");
            }
        } else {
            // If the data is not valid, show the form again
            return [
                'view' => 'auth/signup.php',
                'title' => 'Sign Up',
                'variables' => [
                    'errors' => $errors,
                    'user' => $user
                ]
            ];
        }
    }

    public function success()
    {
        return [
            'view' => 'auth/success.php',
            'title' => 'Registration Successful'
        ];
    }

    public function verify(?string $token = null)
    {
        if (!$token) {
            return [
                'view' => 'auth/verify/result.php',
                'title' => 'Verification Failed',
                'variables' => [
                    'success' => false,
                    'message' => 'Invalid verification link. No token provided.'
                ]
            ];
        }

        $users = $this->usersModel->find('verification_token', $token);

        if (count($users) === 0) {
            return [
                'view' => 'auth/verify/result.php',
                'title' => 'Verification Failed',
                'variables' => [
                    'success' => false,
                    'message' => 'Invalid verification token. 
                                This link may have already been used.'
                ]
            ];
        }

        $user = $users[0];

        // Mark user as verified
        $user->is_verified = true;
        $user->verification_token = null; // Clear the token

        $this->usersModel->save((array)$user);

        return [
            'view' => 'auth/verify/result.php',
            'title' => 'Verification Successful',
            'variables' => [
                'success' => true,
                'message' => 'Your email has been verified successfully! 
                                You can now log in to your account.',
                'username' => $user->name
            ]
        ];
    }

    public function loginSubmit()
    {
        $success = $this->authentication->login($_POST['name'], $_POST['password']);

        if ($success) {
            $user = $this->usersModel->find('name', strtolower($_POST['name']))[0];
            if (!$user->is_verified) {
                $this->authentication->logout();
                return [
                    'view' => 'auth/login.php',
                    'title' => 'Log in',
                    'variables' => [
                        'errorMessage' => 'Please verify your email before logging in.'
                    ]
                ];
            }
            return [
                'view' => 'auth/success.php',
                'title' => 'Log In Successful',
                'variables' => [
                    'login' => true,
                ]
            ];
        } else {
            return [
                'view' => 'auth/login.php',
                'title' => 'Log in',
                'variables' => [
                    'errorMessage' => 'Sorry, your username and password could not be found.'
                ]
            ];
        }
    }

    public function logout()
    {
        $this->authentication->logout();
        header('location: /');
    }

    public function forgotPassword()
    {
        return [
            'view' => 'auth/forgotPassword.php',
            'title' => 'Forgot Password'
        ];
    }

    public function forgotPasswordSubmit()
    {
        $email = $_POST['email'] ?? '';
        $errors = [];

        if (empty($email)) {
            $errors[] = 'Please enter your email address';
        } else if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'Invalid email address';
        }

        if (!empty($errors)) {
            return [
                'view' => 'auth/forgot-password.php',
                'title' => 'Forgot Password',
                'variables' => [
                    'errors' => $errors,
                    'email' => $email
                ]
            ];
        }

        $users = $this->usersModel->find('email', strtolower($email));

        $successMessage = 'If an account exists with this email,
             you will receive password reset instructions shortly.';

        if (!empty($users)) {
            $user = $users[0];

            // Generate password reset token
            $resetToken = bin2hex(random_bytes(32));
            $user->reset_token = $resetToken;

            // Update user with reset token
            $this->usersModel->save((array)$user);

            // Send password reset email
            $base_url = $_ENV['APP_URL'] ?? 'http://localhost';
            $reset_link = $base_url . "/auth/resetpassword/" . $resetToken;

            $to = $user->email;
            $subject = "Reset your Camagru password";
            $message = "
                <html>
                <head>
                    <title>Reset Your Password</title>
                </head>
                <body>
                    <h2>Password Reset Request</h2>
                    <p>Hi {$user->name},</p>
                    <p>We received a request to reset your password. Click the link below to set a new password:</p>
                    <p><a href='{$reset_link}'>Reset My Password</a></p>
                    <p>Or copy and paste this link into your browser:</p>
                    <p>{$reset_link}</p>
                    <p>If you did not request a password reset, please ignore this email.</p>
                </body>
                </html>";

            $headers = "MIME-Version: 1.0" . "\r\n";
            $headers .= "Content-Type: text/html; charset=UTF-8" . "\r\n";
            $headers .= "From: Camagru <noreply@camagru.com>" . "\r\n";

            mail($to, $subject, $message, $headers);
        }

        return [
            'view' => 'auth/forgotPassword.php',
            'title' => 'Forgot Password',
            'variables' => [
                'success' => true,
                'message' => $successMessage
            ]
        ];
    }

    public function resetPassword(?string $token = null)
    {
        if (!$token) {
            return [
                'view' => 'auth/resetPassword.php',
                'title' => 'Reset Password',
                'variables' => [
                    'error' => 'Invalid reset link. No token provided.',
                    'invalidToken' => true
                ]
            ];
        }

        $users = $this->usersModel->find('reset_token', $token);
        if (empty($users)) {
            return [
                'view' => 'auth/resetPassword.php',
                'title' => 'Reset Password',
                'variables' => [
                    'error' => 'Invalid reset link. Please request a new one.',
                    'invalidToken' => true
                ]
            ];
        }

        $user = $users[0];

        // Token is valid - show reset form
        return [
            'view' => 'auth/resetPassword.php',
            'title' => 'Reset Password',
            'variables' => [
                'token' => $token,
                'email' => $user->email
            ]
        ];
    }

    public function resetPasswordSubmit(string $token)
    {
        $password = $_POST['password'] ?? '';
        $passwordConfirm = $_POST['password_confirm'] ?? '';
        $errors = [];

        // Validate token
        if (empty($token)) {
            return [
                'view' => 'auth/resetPassword.php',
                'title' => 'Reset Password',
                'variables' => [
                    'error' => 'Invalid request',
                    'invalidToken' => true
                ]
            ];
        }

        // Find user by token
        $users = $this->usersModel->find('reset_token', $token);

        if (empty($users)) {
            return [
                'view' => 'auth/resetPassword.php',
                'title' => 'Reset Password',
                'variables' => [
                    'error' => 'Invalid or expired reset link',
                    'invalidToken' => true
                ]
            ];
        }

        $user = $users[0];

        // Validate password
        if (empty($password)) {
            $errors[] = 'Password cannot be blank';
        } else {
            $passwordValidation = \Core\PasswordValidator::validate($password);
            if ($passwordValidation['valid'] !== true) {
                $errors = array_merge($errors, $passwordValidation['errors']);
            }
        }

        // Validate password confirmation
        if ($password !== $passwordConfirm) {
            $errors[] = 'Passwords do not match';
        }

        if (!empty($errors)) {
            return [
                'view' => 'auth/resetPassword.php',
                'title' => 'Reset Password',
                'variables' => [
                    'errors' => $errors,
                    'token' => $token,
                    'email' => $user->email
                ]
            ];
        }

        // All validations passed - update password
        try {
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

            $user->password = $hashedPassword;
            $user->reset_token = null;
            $this->usersModel->save((array)$user);

            return [
                'view' => 'auth/resetPassword-success.php',
                'title' => 'Password Reset Successful',
                'variables' => [
                    'email' => $user->email
                ]
            ];
        } catch (\Exception $e) {
            return [
                'view' => 'auth/resetPassword.php',
                'title' => 'Reset Password',
                'variables' => [
                    'errors' => ['An error occurred. Please try again.'],
                    'token' => $token,
                    'email' => $user->email
                ]
            ];
        }
    }
}
