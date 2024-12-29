<?php
session_start();

// Redirect if no success message is set
if (!isset($_SESSION['message'])) {
    header('Location: user_dashboard.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Success - Appointment</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container mt-5">
        <div class="card">
            <div class="card-header text-center bg-success text-white">
                <h2>Appointment Booked Successfully</h2>
            </div>
            <div class="card-body text-center">
                <p><?php echo htmlspecialchars($_SESSION['message']); ?></p>
                <a href="user_dashboard.php" class="btn btn-primary">Back to Dashboard</a>
            </div>
        </div>
    </div>
</body>
</html>
<?php
// Clear the success message
unset($_SESSION['message']);
?>
