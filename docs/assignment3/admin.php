<?php
require_once 'db_config.php';

// Simple admin authentication (you should implement proper authentication)
session_start();
if (!isset($_SESSION['admin'])) {
    header('Location: admin_login.php');
    exit();
}

// Handle appointment updates or deletions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $appointment_id = $_POST['appointment_id'] ?? '';

    // Deleting an appointment
    if (isset($_POST['delete_appointment']) && !empty($appointment_id)) {
        $stmt = $pdo->prepare("DELETE FROM appointments WHERE id = ?");
        $stmt->execute([$appointment_id]);
        $message = "Appointment deleted successfully!";
    } 
    // Updating an appointment
    elseif (!empty($appointment_id) && (!empty($_POST['new_date']) || !empty($_POST['new_mechanic']))) {
        $new_date = $_POST['new_date'] ?? '';
        $new_mechanic = $_POST['new_mechanic'] ?? '';

        $updates = [];
        $params = [];

        if (!empty($new_date)) {
            $updates[] = "appointment_date = ?";
            $params[] = $new_date;
        }

        if (!empty($new_mechanic)) {
            $updates[] = "mechanic_id = ?";
            $params[] = $new_mechanic;
        }

        $params[] = $appointment_id;

        $sql = "UPDATE appointments SET " . implode(", ", $updates) . " WHERE id = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);

        $message = "Appointment updated successfully!";
    }
}

// Get all appointments with mechanic names
$stmt = $pdo->query("
    SELECT 
        a.*,
        m.name as mechanic_name 
    FROM appointments a 
    JOIN mechanics m ON a.mechanic_id = m.id 
    ORDER BY appointment_date DESC
");
$appointments = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Get all mechanics for the update form
$mechanics = $pdo->query("SELECT * FROM mechanics")->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel - Car Workshop</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>
    <div class="container mt-5">
        <div class="d-flex justify-content-between align-items-center">
            <h2>Appointment Management</h2>
            <a href="logout.php" class="btn btn-danger">Logout</a>
        </div>
        <?php if (isset($message)): ?>
            <div class="alert alert-success"><?php echo htmlspecialchars($message); ?></div>
        <?php endif; ?>
        
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Client Name</th>
                    <th>Phone</th>
                    <th>Car License</th>
                    <th>Appointment Date</th>
                    <th>Mechanic</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($appointments as $appointment): ?>
                <tr>
                    <td><?php echo htmlspecialchars($appointment['client_name']); ?></td>
                    <td><?php echo htmlspecialchars($appointment['phone']); ?></td>
                    <td><?php echo htmlspecialchars($appointment['car_license']); ?></td>
                    <td><?php echo htmlspecialchars($appointment['appointment_date']); ?></td>
                    <td><?php echo htmlspecialchars($appointment['mechanic_name']); ?></td>
                    <td>
                        <button type="button" class="btn btn-primary btn-sm" 
                                onclick="openEditModal(<?php echo $appointment['id']; ?>)">
                            Edit
                        </button>
                        <form method="POST" style="display:inline;" 
                              onsubmit="return confirm('Are you sure you want to delete this appointment?');">
                            <input type="hidden" name="appointment_id" value="<?php echo $appointment['id']; ?>">
                            <input type="hidden" name="delete_appointment" value="1">
                            <button type="submit" class="btn btn-danger btn-sm">Delete</button>
                        </form>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <!-- Edit Modal -->
    <div class="modal fade" id="editModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Appointment</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="editForm" method="POST">
                        <input type="hidden" name="appointment_id" id="appointment_id">
                        
                        <div class="mb-3">
                            <label for="new_date" class="form-label">New Date</label>
                            <input type="date" class="form-control" id="new_date" name="new_date" 
                                   min="<?php echo date('Y-m-d'); ?>">
                        </div>
                        
                        <div class="mb-3">
                            <label for="new_mechanic" class="form-label">New Mechanic</label>
                            <select class="form-control" id="new_mechanic" name="new_mechanic">
                                <option value="">Choose a mechanic...</option>
                                <?php foreach ($mechanics as $mechanic): ?>
                                    <option value="<?php echo $mechanic['id']; ?>">
                                        <?php echo htmlspecialchars($mechanic['name']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <button type="submit" class="btn btn-primary">Update Appointment</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
    function openEditModal(appointmentId) {
        $('#appointment_id').val(appointmentId);
        new bootstrap.Modal(document.getElementById('editModal')).show();
    }

    // Form validation
    $(document).ready(function() {
        $('#editForm').on('submit', function(event) {
            if (!$('#new_date').val() && !$('#new_mechanic').val()) {
                event.preventDefault();
                alert('Please select either a new date or a new mechanic');
            }
        });
    });
    </script>
</body>
</html>