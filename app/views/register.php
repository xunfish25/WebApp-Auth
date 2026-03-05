<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Register</title>
    <link rel="stylesheet" href="public/style.css">
</head>
<body>
<div class="auth-container">
    <form action="index.php?action=register" method="POST">
        <h2>Create Account</h2>
        
        <?php if (!empty($_SESSION['error'])): ?>
            <div style="color: #d32f2f; font-size: 0.9rem; margin-bottom: 1rem; padding: 0.75rem; background: #ffebee; border-radius: 6px;">
                <?php echo htmlspecialchars($_SESSION['error']); unset($_SESSION['error']); ?>
            </div>
        <?php endif; ?>

        <?php if (!empty($_SESSION['success'])): ?>
            <div style="color: #2e7d32; font-size: 0.9rem; margin-bottom: 1rem; padding: 0.75rem; background: #e8f5e9; border-radius: 6px;">
                <?php echo htmlspecialchars($_SESSION['success']); unset($_SESSION['success']); ?>
            </div>
        <?php endif; ?>

        <div class="input-group">
            <input type="text" name="first_name" placeholder="First Name" required>
            <input type="text" name="last_name" placeholder="Last Name" required>
            <input type="text" name="middle_name" placeholder="Middle Name (Optional)">
            <input type="email" name="email" placeholder="Email Address" required>
            <input type="password" name="password" placeholder="Password (min. 8 characters)" required>
            <input type="password" name="confirm_password" placeholder="Confirm Password" required>
        </div>
        <button class="btn-primary" type="submit">Register</button>
    </form>
    <?php include __DIR__ . '/partials/google-button.php'; ?>
    <p style="margin-top: 1rem;">Already have an account? <a href="?action=login">Log in</a></p>
</div>
</body>
</html>