<?php
require_once '../config/database.php';

header('Content-Type: application/json');

$database = new Database();
$db = $database->getConnection();

$user_id = checkAuth($db);
if (!$user_id) {
    echo json_encode(['success' => false, 'message' => 'Not authenticated']);
    exit;
}

function formatBytes($bytes, $precision = 2) { 
    $units = array('B', 'KB', 'MB', 'GB', 'TB'); 

    $bytes = max($bytes, 0); 
    $pow = floor(($bytes ? log($bytes) : 0) / log(1024)); 
    $pow = min($pow, count($units) - 1); 
    $bytes /= (1 << (10 * $pow)); 

    return round($bytes, $precision) . ' ' . $units[$pow]; 
} 

try {
    // Total storage used by user
    $sql_total = "SELECT SUM(lc.file_size) as total_bytes FROM user_downloads_tracking udt JOIN library_content lc ON udt.content_id = lc.id WHERE udt.user_id = ?";
    $stmt_total = $db->prepare($sql_total);
    $stmt_total->execute([$user_id]);
    $total_bytes = $stmt_total->fetch(PDO::FETCH_ASSOC)['total_bytes'] ?? 0;
    
    // Total available storage is conceptual, let's set it to 5GB
    $total_available_bytes = 5 * 1024 * 1024 * 1024;

    // Stats per category
    $sql_category = "SELECT lc.category, COUNT(udt.id) as modules, SUM(lc.file_size) as size_bytes FROM user_downloads_tracking udt JOIN library_content lc ON udt.content_id = lc.id WHERE udt.user_id = ? GROUP BY lc.category";
    $stmt_category = $db->prepare($sql_category);
    $stmt_category->execute([$user_id]);
    $category_stats = $stmt_category->fetchAll(PDO::FETCH_ASSOC | PDO::FETCH_GROUP);
    
    $stats = [
        'storage_used' => formatBytes($total_bytes),
        'storage_available' => formatBytes($total_available_bytes),
        'mental_health_modules' => $category_stats['mental-health'][0]['modules'] ?? 0,
        'mental_health_size' => isset($category_stats['mental-health']) ? formatBytes($category_stats['mental-health'][0]['size_bytes']) : '0 MB',
        'exercise_modules' => $category_stats['exercise'][0]['modules'] ?? 0,
        'exercise_size' => isset($category_stats['exercise']) ? formatBytes($category_stats['exercise'][0]['size_bytes']) : '0 MB',
        'recipe_modules' => $category_stats['recipes'][0]['modules'] ?? 0,
        'recipe_size' => isset($category_stats['recipes']) ? formatBytes($category_stats['recipes'][0]['size_bytes']) : '0 MB'
    ];
    
    echo json_encode([
        'success' => true,
        'stats' => $stats
    ]);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Error loading download stats: ' . $e->getMessage()
    ]);
}
?>
