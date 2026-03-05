<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Home</title>
    <link rel="stylesheet" href="public/style.css">
</head>
<body>
<?php
$user = $_SESSION['user_data'];
$displayName = $user['first_name'] . ' ' . $user['last_name'];
?>
<div class="auth-container" style="max-width: 500px;">
    <h2>Welcome!</h2>
    <div style="text-align: left; background: #f5f6fa; padding: 1.5rem; border-radius: 8px; margin: 1.5rem 0;">
        <p style="margin: 0.5rem 0; color: #666;">
            <strong>Name:</strong> <?php echo htmlspecialchars($displayName); ?>
        </p>
        <p style="margin: 0.5rem 0; color: #666;">
            <strong>Email:</strong> <?php echo htmlspecialchars($user['email']); ?>
        </p>
    </div>
    <p style="text-align: center; color: #666;">You are successfully logged in!</p>
    <a href="app/logout.php" class="btn-primary" style="display: inline-block; text-align: center; width: 100%; margin-top: 1rem; text-decoration: none; color: white;">Logout</a>
</div>
</body>
</html>