<?php
// Start session to check if user or admin is logged in
session_start();
if (isset($_SESSION['user_id'])) {
    header('Location: user_dashboard.php'); // Redirect logged-in users
    exit;
}
if (isset($_SESSION['admin_id'])) {
    header('Location: admin.php'); // Redirect logged-in admins
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Car Workshop - Welcome</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="welcome-container">
        <h1>Welcome to Car Workshop</h1>
        <p>Your trusted solution for car repair and servicing appointments.</p>
        
        <div class="buttons">
            <a href="login.php" class="btn">User Login</a>
            <a href="admin_login.php" class="btn btn-admin">Admin Login</a>
            <a href="signup.php" class="btn btn-secondary">Sign Up</a>
        </div>
    </div>
</body>
</html>
