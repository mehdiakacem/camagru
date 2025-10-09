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

<style>
    .auth-container {
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 20px;
    }

    .verify-result {
        background: white;
        padding: 40px;
        max-width: 500px;
        width: 100%;
        text-align: center;
        box-shadow: 0 10px 40px rgba(0, 0, 0, 0.2);
    }

    .icon {
        width: 80px;
        height: 80px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 48px;
        margin: 0 auto 20px;
        font-weight: bold;
    }

    .success-icon {
        background: #4caf50;
        color: white;
    }

    .error-icon {
        background: #f44336;
        color: white;
    }

    .verify-result h1 {
        font-size: 28px;
        margin-bottom: 15px;
        color: #333;
    }

    .message {
        font-size: 16px;
        color: #666;
        line-height: 1.6;
        margin-bottom: 30px;
    }

    .actions {
        display: flex;
        flex-direction: column;
        gap: 10px;
    }

    .btn {
        padding: 12px 24px;
        border-radius: 6px;
        text-decoration: none;
        font-weight: 600;
        transition: all 0.3s;
        display: inline-block;
    }

    .btn-link {
        background: transparent;
    }

    .btn-link:hover {
        text-decoration: underline;
    }
</style>