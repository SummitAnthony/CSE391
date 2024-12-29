<?php
session_start();
require_once 'db_config.php';

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $car_license = $_POST['car_license'];
    $car_engine = $_POST['car_engine'];
    $address = $_POST['address'];
    $appointment_date = $_POST['appointment_date'];
    $mechanic_id = $_POST['mechanic_id'];
    $user_id = $_SESSION['user_id'];

    // Fetch user's phone number
    $stmt = $pdo->prepare("SELECT phone FROM users WHERE id = ?");
    $stmt->execute([$user_id]);
    $user = $stmt->fetch();
    $user_phone = $user['phone'];

    try {
        // Insert appointment into database
        $stmt = $pdo->prepare("INSERT INTO appointments (user_id, phone, car_license, car_engine, address, appointment_date, mechanic_id, created_at)
                               VALUES (?, ?, ?, ?, ?, ?, ?, NOW())");
        $stmt->execute([$user_id, $user_phone, $car_license, $car_engine, $address, $appointment_date, $mechanic_id]);

        // Redirect to success page
        $_SESSION['message'] = "Appointment booked successfully!";
        header('Location: user_dashboard.php');
        exit;
    } catch (Exception $e) {
        $_SESSION['message'] = "Failed to book appointment: " . $e->getMessage();
        header('Location: user_dashboard.php');
        exit;
    }
} else {
    // Redirect back if accessed directly
    header('Location: user_dashboard.php');
    exit;
}
?>
