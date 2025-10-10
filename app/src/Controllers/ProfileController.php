<?php

namespace Controllers;

use Core\Model;

class ProfileController
{
    public function __construct(
        private \Core\Authentication $authentication,
        private Model $usersModel
    ) {}

    public function edit()
    {
        // Check if user is logged in
        if (!$this->authentication->isLoggedIn()) {
            header('Location: /auth/login');
            exit();
        }

        $user = $this->authentication->getUser();

        return [
            'view' => 'profile/edit.php',
            'title' => 'Edit Profile',
            'variables' => [
                'user' => $user
            ]
        ];
    }

    public function editSubmit()
    {
        // Check if user is logged in
        if (!$this->authentication->isLoggedIn()) {
            header('Location: /auth/login');
            exit();
        }

        $currentUser = $this->authentication->getUser();
        $errors = [];
        $success = false;

        // Get form data
        $newUsername = trim($_POST['username'] ?? '');
        $newEmail = trim($_POST['email'] ?? '');
        $currentPassword = $_POST['current_password'] ?? '';
        $newPassword = $_POST['new_password'] ?? '';
        $confirmPassword = $_POST['confirm_password'] ?? '';

        // Validate current password if user wants to change anything
        if (!empty($currentPassword)) {
            if (!password_verify($currentPassword, $currentUser->password)) {
                $errors[] = 'Current password is incorrect';
            }
        } else {
            // Current password is required for any changes
            if (
                $newUsername !== $currentUser->name ||
                $newEmail !== $currentUser->email ||
                !empty($newPassword)
            ) {
                $errors[] = 'Current password is required to make changes';
            }
        }

        // Validate username
        if (empty($newUsername)) {
            $errors[] = 'Username cannot be blank';
        } elseif ($newUsername !== $currentUser->name) {
            // Check if username is already taken
            $existingUsers = $this->usersModel->find('name', $newUsername);
            if (!empty($existingUsers)) {
                $errors[] = 'Username is already taken';
            }
        }

        // Validate email
        if (empty($newEmail)) {
            $errors[] = 'Email cannot be blank';
        } elseif (!filter_var($newEmail, FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'Invalid email address';
        } elseif ($newEmail !== $currentUser->email) {
            // Check if email is already taken
            $existingUsers = $this->usersModel->find('email', strtolower($newEmail));
            if (!empty($existingUsers)) {
                $errors[] = 'Email is already registered';
            }
        }

        // Validate new password if provided
        if (!empty($newPassword)) {
            if (empty($currentPassword)) {
                $errors[] = 'Current password is required to change password';
            }

            $passwordValidation = \Core\PasswordValidator::validate($newPassword);
            if ($passwordValidation['valid'] !== true) {
                $errors = array_merge($errors, $passwordValidation['errors']);
            }

            if ($newPassword !== $confirmPassword) {
                $errors[] = 'New passwords do not match';
            }
        }

        // If no errors, update user
        if (empty($errors)) {
            $updateData = [
                'id' => $currentUser->id,
                'name' => $newUsername,
                'email' => strtolower($newEmail),
                'is_verified' => $currentUser->is_verified,
                'verification_token' => $currentUser->verification_token,
                'reset_token' => $currentUser->reset_token,
            ];

            // Update password if new one provided
            if (!empty($newPassword)) {
                $updateData['password'] = password_hash($newPassword, PASSWORD_DEFAULT);
            } else {
                $updateData['password'] = $currentUser->password;
            }

            try {
                $this->usersModel->save($updateData);

                // If username changed, update session
                if ($newUsername !== $currentUser->name) {
                    $_SESSION['username'] = $newUsername;
                }

                if (!empty($newPassword)) {
                    $_SESSION['password'] = $updateData['password'];
                }

                $success = true;

                // Get updated user data
                $currentUser = $this->authentication->getUser();

            } catch (\Exception $e) {
                $errors[] = 'Failed to update profile. Please try again.';
            }
        }
        return [
            'view' => 'profile/edit.php',
            'title' => 'Edit Profile',
            'variables' => [
                'user' => $currentUser,
                'errors' => $errors,
                'success' => $success
            ]
        ];
    }
}
