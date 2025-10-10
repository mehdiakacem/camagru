<link rel="stylesheet" href="/css/auth.css">

<div class="auth-container">
    <div class="auth-box profile-box">
        <h1>Edit Profile</h1>
        <p class="subtitle">Update your account information</p>

        <?php if (isset($success) && $success): ?>
            <div class="alert alert-success">
                <strong>✓ Success!</strong>
                <p>Your profile has been updated successfully.</p>
            </div>
        <?php endif; ?>

        <?php if (!empty($errors)): ?>
            <div class="alert alert-error">
                <strong>Please fix the following errors:</strong>
                <ul>
                    <?php foreach ($errors as $error): ?>
                        <li><?= htmlspecialchars($error) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <form method="POST" action="">
            <div class="form-section">
                <h3>Account Information</h3>

                <div class="form-group">
                    <label for="username">Username</label>
                    <input
                        type="text"
                        id="username"
                        name="username"
                        value="<?= htmlspecialchars($user->name) ?>"
                        required>
                </div>

                <div class="form-group">
                    <label for="email">Email</label>
                    <input
                        type="email"
                        id="email"
                        name="email"
                        value="<?= htmlspecialchars($user->email) ?>"
                        required>
                </div>
            </div>

            <div class="form-section">
                <h3>Change Password</h3>
                <p class="help-text">Leave blank if you don't want to change your password</p>

                <div class="form-group">
                    <label for="new_password">New Password</label>
                    <input
                        type="password"
                        id="new_password"
                        name="new_password"
                        placeholder="Enter new password (optional)">
                    <small class="help-text">
                        Must be at least 8 characters with uppercase, lowercase, number and special character
                    </small>
                </div>

                <div class="form-group">
                    <label for="confirm_password">Confirm New Password</label>
                    <input
                        type="password"
                        id="confirm_password"
                        name="confirm_password"
                        placeholder="Confirm new password">
                </div>
            </div>

            <div class="form-section password-verify">
                <h3>Verify Your Identity</h3>
                <p class="help-text required-note">Current password is required to save any changes</p>

                <div class="form-group">
                    <label for="current_password">Current Password *</label>
                    <input
                        type="password"
                        id="current_password"
                        name="current_password"
                        placeholder="Enter your current password"
                        required>
                </div>
            </div>

            <button type="submit" class="btn btn-primary">Save Changes</button>

            <div class="form-actions">
                <a href="/" class="btn-link">Cancel</a>
            </div>
        </form>
    </div>
</div>

<style>
    .profile-box {
        max-width: 600px;
    }

    .profile-box h1 {
        font-size: 28px;
        margin-bottom: 10px;
        color: #333;
        text-align: center;
    }

    .subtitle {
        text-align: center;
        color: #666;
        margin-bottom: 30px;
        font-size: 14px;
    }

    .form-section {
        margin-bottom: 30px;
        padding-bottom: 20px;
        border-bottom: 1px solid #e0e0e0;
    }

    .form-section:last-of-type {
        border-bottom: none;
    }

    .form-section h3 {
        font-size: 18px;
        color: #333;
        margin-bottom: 15px;
    }

    .password-verify {
        background: #f8f9fa;
        padding: 20px;
        margin-left: -40px;
        margin-right: -40px;
        margin-bottom: 20px;
        border-bottom: none;
    }

    .required-note {
        color: #d32f2f;
        font-weight: 600;
    }

    .alert {
        padding: 16px;
        margin-bottom: 20px;
        border-radius: 4px;
    }

    .alert-success {
        background: #d4edda;
        color: #155724;
        border: 1px solid #c3e6cb;
    }

    .alert-success strong {
        display: block;
        margin-bottom: 8px;
    }

    .alert-error {
        background: #f8d7da;
        color: #721c24;
        border: 1px solid #f5c6cb;
    }

    .alert-error strong {
        display: block;
        margin-bottom: 8px;
    }

    .alert ul {
        margin: 8px 0 0 0;
        padding-left: 20px;
    }

    .form-group {
        margin-bottom: 20px;
    }

    .form-group label {
        display: block;
        margin-bottom: 8px;
        font-weight: 600;
        color: #333;
    }

    .form-group input {
        width: 100%;
        padding: 12px;
        border: 2px solid #e0e0e0;
        font-size: 14px;
        transition: border-color 0.3s;
        box-sizing: border-box;
    }

    .form-group input:focus {
        outline: none;
        border-color: #2196f3;
    }

    .help-text {
        display: block;
        margin-top: 6px;
        font-size: 12px;
        color: #666;
    }

    .btn {
        width: 100%;
        padding: 14px;
        border: none;
        font-size: 16px;
        font-weight: 600;
        cursor: pointer;
        transition: background-color 0.2s;
        box-sizing: border-box;
    }

    .btn-primary {
        background: #2196f3;
        color: white;
    }

    .btn-primary:hover {
        background-color: #1976d2;
    }

    .form-actions {
        margin-top: 15px;
        text-align: center;
    }

    .btn-link {
        color: #2196f3;
        text-decoration: none;
        font-size: 14px;
    }

    .btn-link:hover {
        text-decoration: underline;
    }
</style>