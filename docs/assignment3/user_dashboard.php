<?php
session_start();
require_once 'db_config.php';

// Redirect to login if not logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

// Fetch logged-in user's name
$user_id = $_SESSION['user_id'];
$stmt = $pdo->prepare("SELECT name FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch();

if (!$user) {
    header('Location: logout.php'); // Logout if user not found
    exit;
}

// Fetch list of mechanics
$mechanics = $pdo->query("SELECT * FROM mechanics;")->fetchAll(PDO::FETCH_ASSOC);

// Fetch user's appointments
$appointments = $pdo->prepare("SELECT a.*, m.name AS mechanic_name 
                               FROM appointments a 
                               JOIN mechanics m ON a.mechanic_id = m.id 
                               WHERE a.user_id = ?");
$appointments->execute([$user_id]);
$user_appointments = $appointments->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Dashboard - Car Workshop</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>
    <div class="container mt-5">
        <div class="text-end">
            <a href="logout.php" class="btn btn-danger">Logout</a>
        </div>
        <h1 class="text-center">Welcome, <?php echo htmlspecialchars($user['name']); ?>!</h1>
        
        <!-- Appointment Booking Form -->
        <div class="card mt-4">
            <div class="card-header text-center">
                <h2>Book an Appointment</h2>
            </div>
            <div class="card-body">
                <?php if (isset($_SESSION['message'])): ?>
                    <div class="alert alert-info"><?php echo htmlspecialchars($_SESSION['message']); unset($_SESSION['message']); ?></div>
                <?php endif; ?>
                
                <form method="POST" action="book_appointment.php" id="appointmentForm" class="needs-validation" novalidate>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="car_license" class="form-label">Car License Number</label>
                            <input type="text" class="form-control" id="car_license" name="car_license" required>
                        </div>
                        <div class="col-md-6">
                            <label for="car_engine" class="form-label">Car Engine Number</label>
                            <input type="text" class="form-control" id="car_engine" name="car_engine" required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="address" class="form-label">Address</label>
                        <textarea class="form-control" id="address" name="address" required></textarea>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="appointment_date" class="form-label">Appointment Date</label>
                            <input type="date" class="form-control" id="appointment_date" name="appointment_date" 
                                   min="<?php echo date('Y-m-d'); ?>" required>
                        </div>
                        <div class="col-md-6">
                            <label for="mechanic_id" class="form-label">Select Mechanic</label>
                            <select class="form-control" id="mechanic_id" name="mechanic_id" required>
                                <option value="">Choose a mechanic...</option>
                                <?php if (count($mechanics) > 0): ?>
                                    <?php foreach ($mechanics as $mechanic): ?>
                                        <option value="<?php echo $mechanic['id']; ?>">
                                            <?php echo htmlspecialchars($mechanic['name']); ?>
                                        </option>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <option value="">No mechanics available</option>
                                <?php endif; ?>
                            </select>
                        </div>
                    </div>

                    <div class="text-center">
                        <button type="submit" class="btn btn-primary">Book Appointment</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- List of User's Appointments -->
        <div class="card mt-5">
            <div class="card-header text-center">
                <h2>Your Appointments</h2>
            </div>
            <div class="card-body">
                <?php if (count($user_appointments) > 0): ?>
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Mechanic</th>
                                <th>Car License</th>
                                <th>Car Engine</th>
                                <th>Address</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($user_appointments as $appointment): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($appointment['appointment_date']); ?></td>
                                    <td><?php echo htmlspecialchars($appointment['mechanic_name']); ?></td>
                                    <td><?php echo htmlspecialchars($appointment['car_license']); ?></td>
                                    <td><?php echo htmlspecialchars($appointment['car_engine']); ?></td>
                                    <td><?php echo htmlspecialchars($appointment['address']); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <p class="text-center">No appointments booked yet.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script>
    $(document).ready(function() {
        // Form validation
        $('#appointmentForm').on('submit', function(event) {
            if (!this.checkValidity()) {
                event.preventDefault();
                event.stopPropagation();
            }
            $(this).addClass('was-validated');
        });

        // Update available mechanics when date changes
        $('#appointment_date').on('change', function() {
            const date = $(this).val();
            const mechanicSelect = $('#mechanic_id');
            
            $.ajax({
                url: 'get_available_mechanics.php',
                method: 'POST',
                data: { date: date },
                success: function(response) {
                    console.log(response);  // Log the response for debugging
                    if (response.trim() === "") {
                        mechanicSelect.html('<option value="">No mechanics available</option>');
                    } else {
                        mechanicSelect.html(response);
                    }
                },
                error: function() {
                    alert('Failed to fetch available mechanics.');
                }
            });
        });
    });
    </script>
</body>
</html>
