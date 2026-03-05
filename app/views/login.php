<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Login</title>
    <link rel="stylesheet" href="public/style.css">
</head>
<body>
<div class="auth-container">
    <form action="index.php?action=login" method="POST">
        <h2>Login</h2>
        
        <?php if (!empty($_SESSION['login_error'])): ?>
            <div style="color: #d32f2f; font-size: 0.9rem; margin-bottom: 1rem; padding: 0.75rem; background: #ffebee; border-radius: 6px;">
                <?php echo htmlspecialchars($_SESSION['login_error']); unset($_SESSION['login_error']); ?>
            </div>
        <?php endif; ?>

        <?php if (!empty($_SESSION['success'])): ?>
            <div style="color: #2e7d32; font-size: 0.9rem; margin-bottom: 1rem; padding: 0.75rem; background: #e8f5e9; border-radius: 6px;">
                <?php echo htmlspecialchars($_SESSION['success']); unset($_SESSION['success']); ?>
            </div>
        <?php endif; ?>

        <div class="input-group">
            <input type="email" name="email" placeholder="Email Address" required autofocus>
            <input type="password" name="password" placeholder="Password" required>
        </div>
        <button class="btn-primary" type="submit">Login</button>
    </form>
    <?php include __DIR__ . '/partials/google-button.php'; ?>
    <p style="margin-top: 1rem;">Don't have an account? <a href="?action=register">Register</a></p>
</div>
</body>
</html>