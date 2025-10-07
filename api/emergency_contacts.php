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
    // Get emergency contacts
    $stmt = $db->prepare("SELECT * FROM emergency_contacts WHERE user_id = ? ORDER BY is_primary DESC, name ASC");
    $stmt->execute([$user_id]);
    $contacts = $stmt->fetchAll();
    
    echo json_encode(['success' => true, 'contacts' => $contacts]);
    
} elseif ($method === 'POST') {
    // Create emergency contact
    $input = json_decode(file_get_contents('php://input'), true);
    
    $name = sanitizeInput($input['name']);
    $phone = sanitizeInput($input['phone']);
    $relationship = sanitizeInput($input['relationship']);
    $contact_type = sanitizeInput($input['contact_type']);
    $is_primary = isset($input['is_primary']) && $input['is_primary'] ? 1 : 0;
    
    $stmt = $db->prepare("INSERT INTO emergency_contacts (user_id, name, phone, relationship, contact_type, is_primary) VALUES (?, ?, ?, ?, ?, ?)");
    
    if ($stmt->execute([$user_id, $name, $phone, $relationship, $contact_type, $is_primary])) {
        echo json_encode(['success' => true, 'message' => 'Emergency contact added successfully']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to add emergency contact']);
    }
    
} elseif ($method === 'PUT') {
    // Update emergency contact
    $input = json_decode(file_get_contents('php://input'), true);
    
    $id = $input['id'];
    $name = sanitizeInput($input['name']);
    $phone = sanitizeInput($input['phone']);
    $relationship = sanitizeInput($input['relationship']);
    $contact_type = sanitizeInput($input['contact_type']);
    $is_primary = isset($input['is_primary']) && $input['is_primary'] ? 1 : 0;
    
    $stmt = $db->prepare("UPDATE emergency_contacts SET name = ?, phone = ?, relationship = ?, contact_type = ?, is_primary = ? WHERE id = ? AND user_id = ?");
    
    if ($stmt->execute([$name, $phone, $relationship, $contact_type, $is_primary, $id, $user_id])) {
        echo json_encode(['success' => true, 'message' => 'Emergency contact updated successfully']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to update emergency contact']);
    }
    
} elseif ($method === 'DELETE') {
    // Delete emergency contact
    $id = $_GET['id'];
    
    $stmt = $db->prepare("DELETE FROM emergency_contacts WHERE id = ? AND user_id = ?");
    
    if ($stmt->execute([$id, $user_id])) {
        echo json_encode(['success' => true, 'message' => 'Emergency contact deleted successfully']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to delete emergency contact']);
    }
}
?>
