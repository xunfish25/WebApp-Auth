<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Verify OTP</title>
    <link rel="stylesheet" href="public/style.css">
</head>
<body>
<div class="auth-container">
    <form action="index.php?action=verify-otp" method="POST">
        <h2>Enter OTP</h2>
        <p style="color: #666; font-size: 0.95rem;">We've sent a 6-digit code to your email</p>
        
        <?php if (!empty($_SESSION['otp_error'])): ?>
            <div style="color: #d32f2f; font-size: 0.9rem; margin-bottom: 1rem; padding: 0.75rem; background: #ffebee; border-radius: 6px;">
                <?php echo htmlspecialchars($_SESSION['otp_error']); unset($_SESSION['otp_error']); ?>
            </div>
        <?php endif; ?>

        <div class="input-group">
            <input type="text" name="otp" placeholder="000000" maxlength="6" pattern="[0-9]{6}" required autofocus>
        </div>
        <button class="btn-primary" type="submit">Verify OTP</button>
    </form>
    <p style="color: #666; font-size: 0.9rem; margin-top: 1.5rem;">
        Didn't receive the code? Check your spam folder or <a href="?action=login">log in again</a>
    </p>
</div>
</body>
</html>
