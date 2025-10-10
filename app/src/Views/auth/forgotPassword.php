<link rel="stylesheet" href="/css/auth.css">
<?php if (isset($success) && $success): ?>
    <div class="alert alert-success">
        <strong>✓ Email Sent!</strong>
        <p><?= htmlspecialchars($message) ?></p>
        <p style="margin-top: 10px; font-size: 13px;">
            Check your inbox for the reset link.
        </p>
    </div>
<?php else: ?>
    <?php if (!empty($errors)): ?>
        <div class="alert alert-error">
            <ul>
                <?php foreach ($errors as $error): ?>
                    <li><?= htmlspecialchars($error) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <form method="POST" action="">
        <div class="form-group">
            <label for="email">Email</label>
            <input
                type="email"
                id="email"
                name="email"
                value="<?= htmlspecialchars($email ?? '') ?>"
                required
                autofocus>

            <input type="submit" value="Send Reset Link"></button>
        </div>
        <div class="auth-links">
            <a href="/auth/login">← Back to Login</a>
            <a href="/auth/signup">Create Account</a>
        </div>
    </form>

<?php endif; ?>

<style>
    .alert {
        padding: 16px;
        /* border-radius: 8px; */
        margin-bottom: 20px;
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

    .alert ul {
        margin: 0;
        padding-left: 20px;
    }

    .form-group {
        margin-bottom: 50px;
    }

    .auth-links {
        margin-top: 20px;
        text-align: center;
        display: flex;
        justify-content: space-between;
        font-size: 14px;
    }

    .auth-links a {
        color: #2196f3;
        text-decoration: none;
    }

    .auth-links a:hover {
        text-decoration: underline;
    }

</style>