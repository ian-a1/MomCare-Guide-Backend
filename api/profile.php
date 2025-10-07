<?php
require_once '../config/database.php';

header('Content-Type: application/json');

$database = new Database();
$db = $database->getConnection();

$user_id = checkAuth($db);
if (!$user_id) {
    echo json_encode(['success' => false, 'message' => 'Authentication required']);
    exit;
}

$method = $_SERVER['REQUEST_METHOD'];

try {
    if ($method === 'GET') {
        getProfile($db, $user_id);
    } elseif ($method === 'PUT') {
        $input = json_decode(file_get_contents('php://input'), true);
        updateProfile($db, $user_id, $input);
    } else {
        echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Server error: ' . $e->getMessage()]);
}

function getProfile($db, $user_id) {
    $sql = "
        SELECT 
            u.id, u.name, u.email, u.birthdate, u.sex, 
            u.address_line1, u.address_line2, u.barangay, u.city, u.zip_code,
            p.due_date, p.current_week, p.last_period_date, p.doctor_name, p.hospital,
            mi.blood_type, mi.allergies, mi.medical_conditions, mi.medications,
            ups.notification_appointments, ups.notification_milestones, 
            ups.notification_forum, ups.privacy_profile_public
        FROM users u
        LEFT JOIN pregnancy_info p ON u.id = p.user_id
        LEFT JOIN user_profile_settings ups ON u.id = ups.user_id
        LEFT JOIN user_medical_info mi ON u.id = mi.user_id
        WHERE u.id = ?
    ";

    $stmt = $db->prepare($sql);
    $stmt->execute([$user_id]);
    $profile = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($profile) {
        if ($profile['due_date']) {
            $dueDate = new DateTime($profile['due_date']);
            $today = new DateTime();
            $diff = $dueDate->diff($today);
            $daysPregnant = 280 - $diff->days;
            $currentWeek = floor($daysPregnant / 7);
            $profile['current_week'] = $currentWeek > 0 ? $currentWeek : 1;
        }
        echo json_encode(['success' => true, 'profile' => $profile]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Profile not found']);
    }
}

function updateProfile($db, $user_id, $input) {
    $section = $input['section'] ?? '';
    $data = $input['data'] ?? [];

    $db->beginTransaction();

    try {
        if ($section === 'basic-info') {
            $stmt = $db->prepare("UPDATE users SET name = ?, email = ?, birthdate = ?, sex = ?, address_line1 = ?, address_line2 = ?, barangay = ?, city = ?, zip_code = ? WHERE id = ?");
            $stmt->execute([
                sanitizeInput($data['name']),
                sanitizeInput($data['email']),
                $data['birthdate'],
                sanitizeInput($data['sex']),
                sanitizeInput($data['address_line1']),
                sanitizeInput($data['address_line2']),
                sanitizeInput($data['barangay']),
                sanitizeInput($data['city']),
                sanitizeInput($data['zip_code']),
                $user_id
            ]);

            $stmt_preg = $db->prepare("INSERT INTO pregnancy_info (user_id, due_date, doctor_name, hospital) VALUES (?, ?, ?, ?) ON DUPLICATE KEY UPDATE due_date = VALUES(due_date), doctor_name = VALUES(doctor_name), hospital = VALUES(hospital)");
            $stmt_preg->execute([
                $user_id,
                $data['due_date'],
                sanitizeInput($data['doctor_name']),
                sanitizeInput($data['hospital'])
            ]);
        } elseif ($section === 'medical-info') {
             $stmt = $db->prepare("INSERT INTO user_medical_info (user_id, blood_type, allergies, medical_conditions, medications) VALUES (?, ?, ?, ?, ?) ON DUPLICATE KEY UPDATE blood_type = VALUES(blood_type), allergies = VALUES(allergies), medical_conditions = VALUES(medical_conditions), medications = VALUES(medications)");
             $stmt->execute([
                $user_id,
                sanitizeInput($data['blood_type']),
                sanitizeInput($data['allergies']),
                sanitizeInput($data['medical_conditions']),
                sanitizeInput($data['medications'])
            ]);
        } elseif ($section === 'privacy-info') {
            $stmt = $db->prepare("INSERT INTO user_profile_settings (user_id, notification_appointments, notification_milestones, notification_forum, privacy_profile_public) VALUES (?, ?, ?, ?, ?) ON DUPLICATE KEY UPDATE notification_appointments = VALUES(notification_appointments), notification_milestones = VALUES(notification_milestones), notification_forum = VALUES(notification_forum), privacy_profile_public = VALUES(privacy_profile_public)");
            $stmt->execute([
                $user_id,
                isset($data['notification_appointments']) ? 1 : 0,
                isset($data['notification_milestones']) ? 1 : 0,
                isset($data['notification_forum']) ? 1 : 0,
                isset($data['privacy_profile_public']) ? 1 : 0,
            ]);
        } else {
            throw new Exception("Invalid profile section.");
        }
        
        $db->commit();
        echo json_encode(['success' => true, 'message' => 'Profile updated successfully']);

    } catch (Exception $e) {
        $db->rollBack();
        throw $e;
    }
}
?>
