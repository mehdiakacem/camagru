<?php

namespace Controllers;

class AuthController
{
    public function __construct(private \Core\Authentication $authentication) {}

    public function login()
    {
        return [
            'view' => '/auth/login.php',
            'title' => 'Login'
        ];
    }

    public function register()
    {
        return [
            'template' => '/auth/register.php',
            'title' => 'Register'
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
