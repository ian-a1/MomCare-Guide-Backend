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
$action = $_REQUEST['action'] ?? '';

try {
    if ($method === 'GET') {
        if ($action === 'posts') {
            getPosts($db);
        } elseif ($action === 'post_details') {
            getPostDetails($db, $user_id);
        } else {
             echo json_encode(['success' => false, 'message' => 'Invalid GET action']);
        }
    } elseif ($method === 'POST') {
        $input = json_decode(file_get_contents('php://input'), true);
        if ($action === 'create_post') {
            createPost($db, $user_id, $input);
        } elseif ($action === 'create_reply') {
            createReply($db, $user_id, $input);
        } elseif ($action === 'like_post') {
            likePost($db, $user_id, $input);
        } else {
             echo json_encode(['success' => false, 'message' => 'Invalid POST action']);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Server error: ' . $e->getMessage()]);
}

function getPosts($db) {
    $tag = isset($_GET['tag']) ? sanitizeInput($_GET['tag']) : 'all';
    $search = isset($_GET['search']) ? sanitizeInput($_GET['search']) : '';
    
    $sql = "SELECT fp.*, u.name as author_name FROM forum_posts fp JOIN users u ON fp.user_id = u.id WHERE fp.status = 'active'";
    $params = [];

    if ($tag !== 'all') {
        $sql .= " AND FIND_IN_SET(?, fp.tags)";
        $params[] = $tag;
    }
    if (!empty($search)) {
        $sql .= " AND (fp.title LIKE ? OR fp.content LIKE ?)";
        $searchTerm = "%$search%";
        $params[] = $searchTerm;
        $params[] = $searchTerm;
    }
    $sql .= " ORDER BY fp.created_at DESC";
    
    $stmt = $db->prepare($sql);
    $stmt->execute($params);
    $posts = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode(['success' => true, 'posts' => $posts]);
}

function getPostDetails($db, $user_id) {
    $post_id = $_GET['post_id'];
    
    $post_sql = "SELECT fp.*, u.name as author_name FROM forum_posts fp JOIN users u ON fp.user_id = u.id WHERE fp.id = ?";
    $stmt_post = $db->prepare($post_sql);
    $stmt_post->execute([$post_id]);
    $post = $stmt_post->fetch(PDO::FETCH_ASSOC);

    $replies_sql = "SELECT fr.*, u.name as author_name FROM forum_replies fr JOIN users u ON fr.user_id = u.id WHERE fr.post_id = ? ORDER BY fr.created_at ASC";
    $stmt_replies = $db->prepare($replies_sql);
    $stmt_replies->execute([$post_id]);
    $replies = $stmt_replies->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode(['success' => true, 'post' => $post, 'replies' => $replies]);
}

function createPost($db, $user_id, $data) {
    $title = sanitizeInput($data['title']);
    $content = sanitizeInput($data['content']);
    $tags = sanitizeInput($data['tags']);

    $sql = "INSERT INTO forum_posts (user_id, title, content, tags) VALUES (?, ?, ?, ?)";
    $stmt = $db->prepare($sql);
    $stmt->execute([$user_id, $title, $content, $tags]);
    echo json_encode(['success' => true, 'message' => 'Post created successfully']);
}

function createReply($db, $user_id, $data) {
    $post_id = $data['post_id'];
    $content = sanitizeInput($data['content']);

    $sql = "INSERT INTO forum_replies (post_id, user_id, content) VALUES (?, ?, ?)";
    $stmt = $db->prepare($sql);
    $stmt->execute([$post_id, $user_id, $content]);

    $update_sql = "UPDATE forum_posts SET replies_count = replies_count + 1 WHERE id = ?";
    $stmt_update = $db->prepare($update_sql);
    $stmt_update->execute([$post_id]);

    echo json_encode(['success' => true, 'message' => 'Reply added successfully']);
}

function likePost($db, $user_id, $data) {
    $post_id = $data['post_id'];
    
    $check_sql = "SELECT id FROM forum_post_likes WHERE post_id = ? AND user_id = ?";
    $stmt_check = $db->prepare($check_sql);
    $stmt_check->execute([$post_id, $user_id]);
    
    if ($stmt_check->fetch()) {
        // Unlike
        $delete_sql = "DELETE FROM forum_post_likes WHERE post_id = ? AND user_id = ?";
        $stmt_delete = $db->prepare($delete_sql);
        $stmt_delete->execute([$post_id, $user_id]);
        
        $update_sql = "UPDATE forum_posts SET likes_count = likes_count - 1 WHERE id = ?";
        $stmt_update = $db->prepare($update_sql);
        $stmt_update->execute([$post_id]);
        
        echo json_encode(['success' => true, 'message' => 'Post unliked']);
    } else {
        // Like
        $insert_sql = "INSERT INTO forum_post_likes (post_id, user_id) VALUES (?, ?)";
        $stmt_insert = $db->prepare($insert_sql);
        $stmt_insert->execute([$post_id, $user_id]);

        $update_sql = "UPDATE forum_posts SET likes_count = likes_count + 1 WHERE id = ?";
        $stmt_update = $db->prepare($update_sql);
        $stmt_update->execute([$post_id]);
        
        echo json_encode(['success' => true, 'message' => 'Post liked']);
    }
}
?>
