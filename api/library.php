<?php
require_once '../config/database.php'; // Corrected path

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}

$database = new Database();
$pdo = $database->getConnection(); // Use 'pdo' as variable name to match rest of file

// Corrected authentication
$user_id = checkAuth($pdo);
if (!$user_id) {
    return; // checkAuth handles the error response and exit
}

$method = $_SERVER['REQUEST_METHOD'];
$input = json_decode(file_get_contents('php://input'), true);

try {
    switch ($method) {
        case 'GET':
            handleGet($pdo, $user_id);
            break;
        case 'POST':
            handlePost($pdo, $user_id, $input);
            break;
        case 'PUT':
            handlePut($pdo, $user_id, $input);
            break;
        case 'DELETE':
            handleDelete($pdo, $user_id);
            break;
        default:
            echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Server error: ' . $e->getMessage()]);
}

function handleGet($pdo, $user_id) {
    $category = $_GET['category'] ?? 'all';
    $search = $_GET['search'] ?? '';
    $page = (int)($_GET['page'] ?? 1);
    $limit = (int)($_GET['limit'] ?? 10);
    $offset = ($page - 1) * $limit;

    // Build query based on filters
    $whereClause = "WHERE lc.status = 'active'";
    $params = [];

    if ($category !== 'all') {
        $whereClause .= " AND lc.category = ?";
        $params[] = $category;
    }

    if (!empty($search)) {
        $whereClause .= " AND (lc.title LIKE ? OR lc.description LIKE ? OR lc.tags LIKE ?)";
        $searchTerm = "%$search%";
        $params[] = $searchTerm;
        $params[] = $searchTerm;
        $params[] = $searchTerm;
    }

    $sql = "
        SELECT 
            lc.*,
            COALESCE(uli_like.user_id IS NOT NULL, FALSE) as is_liked,
            COALESCE(uli_bookmark.user_id IS NOT NULL, FALSE) as is_bookmarked,
            COALESCE(uli_download.user_id IS NOT NULL, FALSE) as is_downloaded,
            COALESCE(uli_rating.rating_value, 0) as user_rating
        FROM library_content lc
        LEFT JOIN user_library_interactions uli_like ON lc.id = uli_like.content_id 
            AND uli_like.user_id = ? AND uli_like.interaction_type = 'like'
        LEFT JOIN user_library_interactions uli_bookmark ON lc.id = uli_bookmark.content_id 
            AND uli_bookmark.user_id = ? AND uli_bookmark.interaction_type = 'bookmark'
        LEFT JOIN user_library_interactions uli_download ON lc.id = uli_download.content_id 
            AND uli_download.user_id = ? AND uli_download.interaction_type = 'download'
        LEFT JOIN user_library_interactions uli_rating ON lc.id = uli_rating.content_id 
            AND uli_rating.user_id = ? AND uli_rating.interaction_type = 'rating'
        $whereClause
        ORDER BY lc.is_featured DESC, lc.rating DESC, lc.created_at DESC
        LIMIT ? OFFSET ?
    ";

    // Create the full parameters array for execution
    $exec_params = array_merge([$user_id, $user_id, $user_id, $user_id], $params, [$limit, $offset]);

    $stmt = $pdo->prepare($sql);
    $stmt->execute($exec_params);
    $content = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Get total count for pagination
    $countSql = "SELECT COUNT(*) FROM library_content lc $whereClause";
    $countStmt = $pdo->prepare($countSql);
    $countStmt->execute($params);
    $totalCount = $countStmt->fetchColumn();

    echo json_encode([
        'success' => true,
        'content' => $content,
        'pagination' => [
            'current_page' => $page,
            'total_pages' => ceil($totalCount / $limit),
            'total_items' => $totalCount,
            'items_per_page' => $limit
        ]
    ]);
}

function handlePost($pdo, $user_id, $input) {
    // ... (rest of the functions in this file remain the same)
    $action = $input['action'] ?? '';

    switch ($action) {
        case 'interact':
            handleInteraction($pdo, $user_id, $input);
            break;
        case 'search':
            handleSearch($pdo, $user_id, $input);
            break;
        default:
            echo json_encode(['success' => false, 'message' => 'Invalid action']);
    }
}

function handleInteraction($pdo, $user_id, $input) {
    $content_id = $input['content_id'] ?? null;
    $interaction_type = $input['interaction_type'] ?? null;
    $rating_value = $input['rating_value'] ?? null;

    if (!$content_id || !$interaction_type) {
        echo json_encode(['success' => false, 'message' => 'Missing required fields']);
        return;
    }

    // Check if content exists
    $checkStmt = $pdo->prepare("SELECT id FROM library_content WHERE id = ? AND status = 'active'");
    $checkStmt->execute([$content_id]);
    if (!$checkStmt->fetch()) {
        echo json_encode(['success' => false, 'message' => 'Content not found']);
        return;
    }

    if ($interaction_type === 'like' || $interaction_type === 'bookmark') {
        // Toggle like/bookmark
        $existingStmt = $pdo->prepare("
            SELECT id FROM user_library_interactions 
            WHERE user_id = ? AND content_id = ? AND interaction_type = ?
        ");
        $existingStmt->execute([$user_id, $content_id, $interaction_type]);
        
        if ($existingStmt->fetch()) {
            // Remove interaction
            $deleteStmt = $pdo->prepare("
                DELETE FROM user_library_interactions 
                WHERE user_id = ? AND content_id = ? AND interaction_type = ?
            ");
            $deleteStmt->execute([$user_id, $content_id, $interaction_type]);
            $action_performed = 'removed';
        } else {
            // Add interaction
            $insertStmt = $pdo->prepare("
                INSERT INTO user_library_interactions (user_id, content_id, interaction_type) 
                VALUES (?, ?, ?)
            ");
            $insertStmt->execute([$user_id, $content_id, $interaction_type]);
            $action_performed = 'added';
        }
    } elseif ($interaction_type === 'rating') {
        if (!$rating_value || $rating_value < 1 || $rating_value > 5) {
            echo json_encode(['success' => false, 'message' => 'Invalid rating value']);
            return;
        }

        // Insert or update rating
        $ratingStmt = $pdo->prepare("
            INSERT INTO user_library_interactions (user_id, content_id, interaction_type, rating_value) 
            VALUES (?, ?, 'rating', ?)
            ON DUPLICATE KEY UPDATE rating_value = VALUES(rating_value)
        ");
        $ratingStmt->execute([$user_id, $content_id, $rating_value]);
        
        // Update average rating in library_content
        $avgStmt = $pdo->prepare("
            UPDATE library_content 
            SET rating = (
                SELECT AVG(rating_value) 
                FROM user_library_interactions 
                WHERE content_id = ? AND interaction_type = 'rating'
            )
            WHERE id = ?
        ");
        $avgStmt->execute([$content_id, $content_id]);
        $action_performed = 'rated';
    } elseif ($interaction_type === 'view') {
        // Record view (always insert new)
        $viewStmt = $pdo->prepare("
            INSERT INTO user_library_interactions (user_id, content_id, interaction_type) 
            VALUES (?, ?, 'view')
        ");
        $viewStmt->execute([$user_id, $content_id]);
        
        // Update view count
        $updateViewsStmt = $pdo->prepare("
            UPDATE library_content 
            SET views_count = views_count + 1 
            WHERE id = ?
        ");
        $updateViewsStmt->execute([$content_id]);
        $action_performed = 'viewed';
    }

    echo json_encode([
        'success' => true, 
        'message' => ucfirst($interaction_type) . ' ' . $action_performed . ' successfully'
    ]);
}

function handleSearch($pdo, $user_id, $input) {
    $query = $input['query'] ?? '';
    $category = $input['category'] ?? 'all';
    
    if (empty($query)) {
        echo json_encode(['success' => false, 'message' => 'Search query required']);
        return;
    }

    $whereClause = "WHERE lc.status = 'active' AND (lc.title LIKE ? OR lc.description LIKE ? OR lc.content LIKE ? OR lc.tags LIKE ?)";
    $params = ["%$query%", "%$query%", "%$query%", "%$query%"];

    if ($category !== 'all') {
        $whereClause .= " AND lc.category = ?";
        $params[] = $category;
    }

    $sql = "
        SELECT lc.*, 
               COALESCE(uli_like.user_id IS NOT NULL, FALSE) as is_liked,
               COALESCE(uli_bookmark.user_id IS NOT NULL, FALSE) as is_bookmarked
        FROM library_content lc
        LEFT JOIN user_library_interactions uli_like ON lc.id = uli_like.content_id 
            AND uli_like.user_id = ? AND uli_like.interaction_type = 'like'
        LEFT JOIN user_library_interactions uli_bookmark ON lc.id = uli_bookmark.content_id 
            AND uli_bookmark.user_id = ? AND uli_bookmark.interaction_type = 'bookmark'
        $whereClause
        ORDER BY lc.rating DESC, lc.views_count DESC
        LIMIT 20
    ";
    
    $exec_params = array_merge([$user_id, $user_id], $params);
    $stmt = $pdo->prepare($sql);
    $stmt->execute($exec_params);
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode([
        'success' => true,
        'results' => $results,
        'query' => $query,
        'count' => count($results)
    ]);
}

function handlePut($pdo, $user_id, $input) {
    echo json_encode(['success' => false, 'message' => 'Update functionality not implemented']);
}

function handleDelete($pdo, $user_id) {
    echo json_encode(['success' => false, 'message' => 'Delete functionality not implemented']);
}
?>
