<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

require_once '../config/database.php';

$database = new Database();
$db = $database->getConnection();

$method = $_SERVER['REQUEST_METHOD'];
$action = isset($_GET['action']) ? $_GET['action'] : '';

if ($method === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);
    
    if ($action === 'signup') {
        // User registration
        $name = sanitizeInput($input['name']);
        $email = sanitizeInput($input['email']);
        $password = $input['password'];
        $birthdate = $input['birthdate'];
        $sex = sanitizeInput($input['sex']);
        $address_line1 = sanitizeInput($input['address_line1']);
        $address_line2 = sanitizeInput($input['address_line2']);
        $barangay = sanitizeInput($input['barangay']);
        $city = sanitizeInput($input['city']);
        $zip_code = sanitizeInput($input['zip_code']);
        
        // Validate required fields
        if (empty($name) || empty($email) || empty($password)) {
            echo json_encode(['success' => false, 'message' => 'Required fields missing']);
            exit;
        }
        
        if (!validateEmail($email)) {
            echo json_encode(['success' => false, 'message' => 'Invalid email format']);
            exit;
        }
        
        // Check if email already exists
        $stmt = $db->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->execute([$email]);
        if ($stmt->rowCount() > 0) {
            echo json_encode(['success' => false, 'message' => 'Email already registered']);
            exit;
        }
        
        // Hash password
        $hashed_password = hashPassword($password);
        
        // Insert user
        $stmt = $db->prepare("INSERT INTO users (name, email, password, birthdate, sex, address_line1, address_line2, barangay, city, zip_code) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        
        if ($stmt->execute([$name, $email, $hashed_password, $birthdate, $sex, $address_line1, $address_line2, $barangay, $city, $zip_code])) {
            $user_id = $db->lastInsertId();
            
            // --- MODIFIED: Add 911 as a default emergency contact ---
            $stmt_emergency = $db->prepare("INSERT INTO emergency_contacts (user_id, name, phone, relationship, contact_type, is_primary) VALUES (?, 'Emergency Hotline', '911', 'National Emergency', 'emergency', 0)");
            $stmt_emergency->execute([$user_id]);
            
            // Create session
            $session_token = generateSessionToken();
            $expires_at = date('Y-m-d H:i:s', strtotime('+30 days'));
            
            $stmt = $db->prepare("INSERT INTO user_sessions (user_id, session_token, expires_at) VALUES (?, ?, ?)");
            $stmt->execute([$user_id, $session_token, $expires_at]);
            
            $_SESSION['user_id'] = $user_id;
            $_SESSION['session_token'] = $session_token;
            
            echo json_encode([
                'success' => true, 
                'message' => 'Registration successful',
                'user_id' => $user_id,
                'session_token' => $session_token
            ]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Registration failed']);
        }
        
    } elseif ($action === 'login') {
        // User login
        $email = sanitizeInput($input['email']);
        $password = $input['password'];
        
        if (empty($email) || empty($password)) {
            echo json_encode(['success' => false, 'message' => 'Email and password required']);
            exit;
        }
        
        // Get user
        $stmt = $db->prepare("SELECT id, name, email, password FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();
        
        if ($user && verifyPassword($password, $user['password'])) {
            // Create session
            $session_token = generateSessionToken();
            $expires_at = date('Y-m-d H:i:s', strtotime('+30 days'));
            
            $stmt = $db->prepare("INSERT INTO user_sessions (user_id, session_token, expires_at) VALUES (?, ?, ?)");
            $stmt->execute([$user['id'], $session_token, $expires_at]);
            
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['session_token'] = $session_token;
            
            echo json_encode([
                'success' => true,
                'message' => 'Login successful',
                'user' => [
                    'id' => $user['id'],
                    'name' => $user['name'],
                    'email' => $user['email']
                ],
                'session_token' => $session_token
            ]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Invalid credentials']);
        }
        
    } elseif ($action === 'logout') {
        // User logout
        if (isset($_SESSION['session_token'])) {
            $stmt = $db->prepare("DELETE FROM user_sessions WHERE session_token = ?");
            $stmt->execute([$_SESSION['session_token']]);
        }
        
        session_destroy();
        echo json_encode(['success' => true, 'message' => 'Logged out successfully']);
    }
}
?>
