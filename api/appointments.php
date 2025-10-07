<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, GET, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

require_once '../config/database.php';

$database = new Database();
$db = $database->getConnection();

$method = $_SERVER['REQUEST_METHOD'];

// Corrected authentication: Use the global checkAuth function
$user_id = checkAuth($db);
if (!$user_id) {
    // The checkAuth function handles the error response and exits
    return;
}

if ($method === 'GET') {
    // Get appointments
    $stmt = $db->prepare("SELECT * FROM appointments WHERE user_id = ? ORDER BY appointment_date ASC");
    $stmt->execute([$user_id]);
    $appointments = $stmt->fetchAll();
    
    echo json_encode(['success' => true, 'appointments' => $appointments]);
    
} elseif ($method === 'POST') {
    // Create appointment
    $input = json_decode(file_get_contents('php://input'), true);
    
    $title = sanitizeInput($input['title']);
    $description = sanitizeInput($input['description']);
    $appointment_date = $input['appointment_date'];
    $doctor_name = sanitizeInput($input['doctor_name']);
    $location = sanitizeInput($input['location']);
    $type = sanitizeInput($input['type']);
    
    $stmt = $db->prepare("INSERT INTO appointments (user_id, title, description, appointment_date, doctor_name, location, type) VALUES (?, ?, ?, ?, ?, ?, ?)");
    
    if ($stmt->execute([$user_id, $title, $description, $appointment_date, $doctor_name, $location, $type])) {
        echo json_encode(['success' => true, 'message' => 'Appointment created successfully']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to create appointment']);
    }
    
} elseif ($method === 'PUT') {
    // Update appointment
    $input = json_decode(file_get_contents('php://input'), true);
    
    $id = $input['id'];
    $title = sanitizeInput($input['title']);
    $description = sanitizeInput($input['description']);
    $appointment_date = $input['appointment_date'];
    $doctor_name = sanitizeInput($input['doctor_name']);
    $location = sanitizeInput($input['location']);
    $type = sanitizeInput($input['type']);
    $status = sanitizeInput($input['status']);
    
    $stmt = $db->prepare("UPDATE appointments SET title = ?, description = ?, appointment_date = ?, doctor_name = ?, location = ?, type = ?, status = ? WHERE id = ? AND user_id = ?");
    
    if ($stmt->execute([$title, $description, $appointment_date, $doctor_name, $location, $type, $status, $id, $user_id])) {
        echo json_encode(['success' => true, 'message' => 'Appointment updated successfully']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to update appointment']);
    }
    
} elseif ($method === 'DELETE') {
    // Delete appointment
    $id = $_GET['id'];
    
    $stmt = $db->prepare("DELETE FROM appointments WHERE id = ? AND user_id = ?");
    
    if ($stmt->execute([$id, $user_id])) {
        echo json_encode(['success' => true, 'message' => 'Appointment deleted successfully']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to delete appointment']);
    }
}
?>
