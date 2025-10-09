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

    public function verify(string $token)
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
        $success = $this->authentication->login($_POST['email'], $_POST['password']);

        if ($success) {
            return [
                'view' => 'loginSuccess.html.php',
                'title' => 'Log In Successful'
            ];
        } else {
            return [
                'view' => 'loginForm.html.php',
                'title' => 'Log in',
                'variables' => [
                    'errorMessage' => true
                ]
            ];
        }
    }

    public function logout()
    {
        $this->authentication->logout();
        header('location: /');
    }
}
