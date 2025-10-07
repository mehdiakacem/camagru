<?php

namespace Controllers;

use Core\Model;

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

    public function register()
    {
        return [
            'view' => 'auth/register.php',
            'title' => 'Register'
        ];
    }

    public function registerSubmit()
    {
        $user = $_POST['user'];

        // Start with an empty array
        $errors = [];

        // But if any of the fields have been left blank, write an error to the array
        if (empty($user['name'])) {
            $errors[] = 'Username cannot be blank';
        }

        if (empty($user['email'])) {
            $errors[] = 'Email cannot be blank';
        } else if (filter_var($user['email'], FILTER_VALIDATE_EMAIL) == false) {
            $errors[] = 'Invalid email address';
        } else { // If the email is not blank and valid:
            // convert the email to lowercase
            $user['email'] = strtolower($user['email']);

            // Search for the lowercase version of $user['email']
            if (count($this->usersModel->find('email', $user['email'])) > 0) {
                $errors[] = 'That email address is already registered';
            }
        }

        if (empty($user['password'])) {
            $errors[] = 'Password cannot be blank';
        }

        // If there are no errors, proceed with saving the record in the database
        if (count($errors) === 0) {
            // Hash the password before saving it in the database
            $user['password'] = password_hash($user['password'], PASSWORD_DEFAULT);

            // When submitted, the $user variable now contains a lowercase value for email
            // and a hashed password
            $this->usersModel->save($user);

            header('Location: /user/success');
        } else {
            // If the data is not valid, show the form again
            return [
                'view' => 'auth/register.php',
                'title' => 'Register',
                'variables' => [
                    'errors' => $errors,
                    'user' => $user
                ]
            ];
        }
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
