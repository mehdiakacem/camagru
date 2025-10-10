<div class="auth-container">
    <div class="auth-box success-box">
        <div class="success-icon">✓</div>
        <h1>Password Reset Successful!</h1>
        <p class="message">Your password has been successfully changed.</p>

        <div class="info-box">
            <p><strong>Account:</strong> <?= htmlspecialchars($email) ?></p>
            <p>You can now log in with your new password.</p>
        </div>

        <a href="/auth/login" class="btn btn-primary">Login Now</a>

        <div class="auth-links">
            <a href="/">← Go to Home</a>
        </div>
    </div>
</div>

<style>
    .auth-box {
        background: white;
        padding: 40px;
        max-width: 450px;
        width: 100%;
        box-shadow: 0 10px 40px rgba(0, 0, 0, 0.2);
        text-align: center;
    }

    .success-icon {
        width: 80px;
        height: 80px;
        background: #4caf50;
        color: white;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 48px;
        font-weight: bold;
        margin: 0 auto 20px;
    }

    h1 {
        font-size: 28px;
        margin-bottom: 10px;
        color: #333;
    }

    .message {
        color: #666;
        margin-bottom: 30px;
        font-size: 16px;
    }

    .info-box {
        border: 1px solid #b3d9ff;
        padding: 20px;
        margin-bottom: 30px;
        text-align: left;
    }

    .info-box p {
        margin: 8px 0;
        color: #333;
    }

    .info-box strong {
        color: #1976d2;
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


    .auth-links {
        margin-top: 20px;
        font-size: 14px;
    }

    .auth-links a {
        color: #1976d2;
        text-decoration: none;
    }

    .auth-links a:hover {
        text-decoration: underline;
    }
</style>