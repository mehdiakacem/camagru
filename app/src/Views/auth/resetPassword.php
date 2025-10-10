<div class="auth-container">
    <div class="auth-box">
        <?php if (isset($invalidToken) || isset($expired)): ?>
            <!-- Invalid or Expired Token -->
            <h1>Invalid Reset Link</h1>
            <div class="alert alert-error">
                <?= htmlspecialchars($error) ?>
            </div>
            <div class="actions">
                <a href="/auth/forgotpassword" class="btn btn-primary">Request New Reset Link</a>
                <a href="/auth/login" class="btn btn-secondary">Back to Login</a>
            </div>

        <?php else: ?>
            <!-- Valid Token - Show Reset Form -->
            <h1>Create New Password</h1>
            <p class="subtitle">Enter a strong password for your account: <strong><?= htmlspecialchars($email) ?></strong></p>

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
                <input type="hidden" name="token" value="<?= htmlspecialchars($token) ?>">

                <div class="form-group">
                    <label for="password">New Password</label>
                    <input
                        type="password"
                        id="password"
                        name="password"
                        placeholder="Enter new password"
                        required
                        autofocus>
                    <small class="help-text">
                        Must be at least 8 characters with uppercase, lowercase, and number
                    </small>
                </div>

                <div class="form-group">
                    <label for="password_confirm">Confirm New Password</label>
                    <input
                        type="password"
                        id="password_confirm"
                        name="password_confirm"
                        placeholder="Confirm new password"
                        required>
                </div>

                <button type="submit" class="btn btn-primary">Reset Password</button>
            </form>

            <div class="auth-links">
                <a href="/auth/login">← Back to Login</a>
            </div>
        <?php endif; ?>
    </div>
</div>

<style>
    .auth-box h1 {
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
        line-height: 1.5;
    }

    .subtitle strong {
        color: #1976d2;
    }

    .alert {
        padding: 16px;
        margin-bottom: 20px;
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

    /* .form-group label {
        display: block;
        margin-bottom: 8px;
        font-weight: 600;
        color: #333;
    } */

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
        text-decoration: none;
        display: inline-block;
        text-align: center;
        box-sizing: border-box;
        margin-bottom: 10px;
        transition: background-color 0.2s;
    }

    .btn-primary {
        background: #2196f3;
        color: white;
    }

    .btn-primary:hover {
        background-color: #1976d2;
    }

    .btn-secondary {
        background: #f5f5f5;
        color: #333;
    }

    .btn-secondary:hover {
        background: #e0e0e0;
    }

    .auth-links {
        margin-top: 20px;
        text-align: center;
        font-size: 14px;
    }

    .auth-links a:hover {
        text-decoration: underline;
    }

    .actions {
        display: flex;
        flex-direction: column;
        gap: 10px;
    }
</style>