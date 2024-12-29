<?php
require_once 'db_config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $date = $_POST['date'] ?? '';
    
    if (empty($date)) {
        echo '<option value="">Invalid date selected</option>';
        exit;
    }
    
    // Get mechanics with their appointment count
    $stmt = $pdo->prepare("
        SELECT 
            m.id,
            m.name,
            COUNT(a.id) as appointment_count
        FROM mechanics m
        LEFT JOIN appointments a ON m.id = a.mechanic_id 
            AND a.appointment_date = ?
        GROUP BY m.id
        ORDER BY m.name
    ");
    $stmt->execute([$date]);
    $mechanics = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Generate option elements for the select dropdown
    foreach ($mechanics as $mechanic) {
        $available_slots = 4 - $mechanic['appointment_count'];
        $disabled = $available_slots <= 0 ? 'disabled' : '';
        
        echo '<option value="' . $mechanic['id'] . '" ' . $disabled . '>';
        echo htmlspecialchars($mechanic['name']) . ' (' . $available_slots . ' slots available)';
        echo '</option>';
    }
}
?>
