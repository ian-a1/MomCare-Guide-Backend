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
    // Get milestones
    $stmt = $db->prepare("SELECT * FROM milestones WHERE user_id = ? ORDER BY week_number ASC");
    $stmt->execute([$user_id]);
    $milestones = $stmt->fetchAll();
    
    echo json_encode(['success' => true, 'milestones' => $milestones]);
    
} elseif ($method === 'POST') {
    // Create milestone
    $input = json_decode(file_get_contents('php://input'), true);
    
    $name = sanitizeInput($input['name']);
    $description = sanitizeInput($input['description']);
    $week_number = $input['week_number'];
    
    $stmt = $db->prepare("INSERT INTO milestones (user_id, name, description, week_number, status) VALUES (?, ?, ?, ?, ?)");
    
    if ($stmt->execute([$user_id, $name, $description, $week_number, 'pending'])) {
        echo json_encode(['success' => true, 'message' => 'Milestone created successfully']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to create milestone']);
    }
    
} elseif ($method === 'PUT') {
    // Update milestone
    $input = json_decode(file_get_contents('php://input'), true);
    
    $id = $input['id'];
    $name = sanitizeInput($input['name']);
    $description = sanitizeInput($input['description']);
    $week_number = $input['week_number'];
    $status = sanitizeInput($input['status']);
    $completed_date = ($status === 'complete') ? date('Y-m-d') : null;
    
    $stmt = $db->prepare("UPDATE milestones SET name = ?, description = ?, week_number = ?, status = ?, completed_date = ? WHERE id = ? AND user_id = ?");
    
    if ($stmt->execute([$name, $description, $week_number, $status, $completed_date, $id, $user_id])) {
        echo json_encode(['success' => true, 'message' => 'Milestone updated successfully']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to update milestone']);
    }
    
} elseif ($method === 'DELETE') {
    // Delete milestone
    $id = $_GET['id'];
    
    $stmt = $db->prepare("DELETE FROM milestones WHERE id = ? AND user_id = ?");
    
    if ($stmt->execute([$id, $user_id])) {
        echo json_encode(['success' => true, 'message' => 'Milestone deleted successfully']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to delete milestone']);
    }
}
?>
