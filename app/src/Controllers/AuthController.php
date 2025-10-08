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

        // If there are no errors, proceed with saving the record in the database
        if (count($errors) === 0) {
            // Hash the password before saving it in the database
            $user['password'] = password_hash($user['password'], PASSWORD_DEFAULT);

            // When submitted, the $user variable now contains a lowercase value for email
            // and a hashed password
            $this->usersModel->save($user);

            header('Location: /auth/success');
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
