<link rel="stylesheet" href="/css/auth.css">
<div class="auth-container">
    <div class="verify-result <?= $success ? 'success' : 'error' ?>">

        <?php if ($success): ?>
            <div class="icon success-icon">✓</div>
            <h1>Email Verified!</h1>
        <?php else: ?>
            <div class="icon error-icon">✗</div>
            <h1>Verification Failed</h1>
        <?php endif; ?>

        <p class="message"><?= htmlspecialchars($message) ?></p>

        <div class="actions">
            <?php if ($success): ?>
                <a href="/auth/login" class="btn btn-link">Log in</a>
            <?php else: ?>
                <a href="/" class="btn btn-link">Go to Home</a>
            <?php endif; ?>
        </div>
    </div>
</div>