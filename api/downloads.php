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
    $action = $_GET['action'] ?? 'list';

    switch ($action) {
        case 'list':
            getDownloadsList($pdo, $user_id);
            break;
        case 'stats':
            getDownloadStats($pdo, $user_id);
            break;
        case 'storage':
            getStorageInfo($pdo, $user_id);
            break;
        default:
            echo json_encode(['success' => false, 'message' => 'Invalid action']);
    }
}

function getDownloadsList($pdo, $user_id) {
    $sql = "
        SELECT 
            udt.*,
            lc.title,
            lc.description,
            lc.category,
            lc.author,
            lc.rating,
            lc.image_url,
            lc.duration_minutes,
            lc.difficulty_level
        FROM user_downloads_tracking udt
        JOIN library_content lc ON udt.content_id = lc.id
        WHERE udt.user_id = ?
        ORDER BY udt.completed_at DESC, udt.created_at DESC
    ";

    $stmt = $pdo->prepare($sql);
    $stmt->execute([$user_id]);
    $downloads = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Group by category
    $grouped = [];
    foreach ($downloads as $download) {
        $category = $download['category'];
        if (!isset($grouped[$category])) {
            $grouped[$category] = [];
        }
        $grouped[$category][] = $download;
    }

    echo json_encode([
        'success' => true,
        'downloads' => $downloads,
        'grouped' => $grouped,
        'total_downloads' => count($downloads)
    ]);
}

function getDownloadStats($pdo, $user_id) {
    // ... (rest of the functions in this file remain the same)
    // Get storage usage by category
    $sql = "
        SELECT 
            lc.category,
            COUNT(*) as item_count,
            SUM(udt.file_size_mb) as total_size_mb,
            AVG(lc.rating) as avg_rating
        FROM user_downloads_tracking udt
        JOIN library_content lc ON udt.content_id = lc.id
        WHERE udt.user_id = ? AND udt.download_status = 'completed'
        GROUP BY lc.category
    ";

    $stmt = $pdo->prepare($sql);
    $stmt->execute([$user_id]);
    $stats = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Calculate totals
    $totalSize = 0;
    $totalItems = 0;
    $categoryStats = [];

    foreach ($stats as $stat) {
        $totalSize += $stat['total_size_mb'];
        $totalItems += $stat['item_count'];
        
        $categoryStats[$stat['category']] = [
            'count' => $stat['item_count'],
            'size_mb' => round($stat['total_size_mb'], 2),
            'size_formatted' => formatFileSize($stat['total_size_mb'] * 1024 * 1024),
            'avg_rating' => round($stat['avg_rating'], 1)
        ];
    }

    // Storage limits (can be configured)
    $storageLimit = 1024; // 1GB in MB
    $storageUsed = $totalSize;
    $storageAvailable = $storageLimit - $storageUsed;

    echo json_encode([
        'success' => true,
        'stats' => [
            'total_items' => $totalItems,
            'total_size_mb' => round($totalSize, 2),
            'total_size_formatted' => formatFileSize($totalSize * 1024 * 1024),
            'storage_limit_mb' => $storageLimit,
            'storage_used_mb' => round($storageUsed, 2),
            'storage_available_mb' => round($storageAvailable, 2),
            'storage_used_formatted' => formatFileSize($storageUsed * 1024 * 1024),
            'storage_available_formatted' => formatFileSize($storageAvailable * 1024 * 1024),
            'storage_usage_percentage' => round(($storageUsed / $storageLimit) * 100, 1),
            'categories' => $categoryStats
        ]
    ]);
}

function getStorageInfo($pdo, $user_id) {
    $sql = "
        SELECT 
            SUM(file_size_mb) as total_used_mb,
            COUNT(*) as total_files
        FROM user_downloads_tracking 
        WHERE user_id = ? AND download_status = 'completed'
    ";

    $stmt = $pdo->prepare($sql);
    $stmt->execute([$user_id]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    $usedMB = $result['total_used_mb'] ?? 0;
    $totalFiles = $result['total_files'] ?? 0;
    $limitMB = 1024; // 1GB limit

    echo json_encode([
        'success' => true,
        'storage' => [
            'used_mb' => round($usedMB, 2),
            'used_formatted' => formatFileSize($usedMB * 1024 * 1024),
            'available_mb' => round($limitMB - $usedMB, 2),
            'available_formatted' => formatFileSize(($limitMB - $usedMB) * 1024 * 1024),
            'limit_mb' => $limitMB,
            'limit_formatted' => formatFileSize($limitMB * 1024 * 1024),
            'usage_percentage' => round(($usedMB / $limitMB) * 100, 1),
            'total_files' => $totalFiles
        ]
    ]);
}

function handlePost($pdo, $user_id, $input) {
    $action = $input['action'] ?? '';

    switch ($action) {
        case 'download':
            startDownload($pdo, $user_id, $input);
            break;
        case 'update_progress':
            updateDownloadProgress($pdo, $user_id, $input);
            break;
        default:
            echo json_encode(['success' => false, 'message' => 'Invalid action']);
    }
}

function startDownload($pdo, $user_id, $input) {
    $content_id = $input['content_id'] ?? null;

    if (!$content_id) {
        echo json_encode(['success' => false, 'message' => 'Content ID required']);
        return;
    }

    // Check if content exists
    $contentStmt = $pdo->prepare("
        SELECT id, title, file_size, category 
        FROM library_content 
        WHERE id = ? AND status = 'active'
    ");
    $contentStmt->execute([$content_id]);
    $content = $contentStmt->fetch(PDO::FETCH_ASSOC);

    if (!$content) {
        echo json_encode(['success' => false, 'message' => 'Content not found']);
        return;
    }

    // Check if already downloaded
    $existingStmt = $pdo->prepare("
        SELECT id FROM user_downloads_tracking 
        WHERE user_id = ? AND content_id = ? AND download_status = 'completed'
    ");
    $existingStmt->execute([$user_id, $content_id]);
    
    if ($existingStmt->fetch()) {
        echo json_encode(['success' => false, 'message' => 'Content already downloaded']);
        return;
    }

    // Create download record
    $downloadPath = "/downloads/{$content['category']}/{$content['title']}.pdf";
    $fileSizeMB = ($content['file_size'] ?? 1024000) / (1024 * 1024); // Convert bytes to MB

    $insertStmt = $pdo->prepare("
        INSERT INTO user_downloads_tracking 
        (user_id, content_id, download_path, file_size_mb, download_status, progress_percentage) 
        VALUES (?, ?, ?, ?, 'downloading', 0)
    ");
    $insertStmt->execute([$user_id, $content_id, $downloadPath, $fileSizeMB]);
    $downloadId = $pdo->lastInsertId();

    // Record interaction
    $interactionStmt = $pdo->prepare("
        INSERT INTO user_library_interactions (user_id, content_id, interaction_type) 
        VALUES (?, ?, 'download')
        ON DUPLICATE KEY UPDATE created_at = NOW()
    ");
    $interactionStmt->execute([$user_id, $content_id]);

    // Update download count
    $updateStmt = $pdo->prepare("
        UPDATE library_content 
        SET downloads_count = downloads_count + 1 
        WHERE id = ?
    ");
    $updateStmt->execute([$content_id]);

    echo json_encode([
        'success' => true,
        'message' => 'Download started',
        'download_id' => $downloadId,
        'file_size_mb' => round($fileSizeMB, 2)
    ]);
}

function updateDownloadProgress($pdo, $user_id, $input) {
    $download_id = $input['download_id'] ?? null;
    $progress = $input['progress'] ?? null;

    if (!$download_id || $progress === null) {
        echo json_encode(['success' => false, 'message' => 'Download ID and progress required']);
        return;
    }

    $status = $progress >= 100 ? 'completed' : 'downloading';
    $completedAt = $progress >= 100 ? 'NOW()' : 'NULL';

    $updateStmt = $pdo->prepare("
        UPDATE user_downloads_tracking 
        SET progress_percentage = ?, download_status = ?, completed_at = $completedAt
        WHERE id = ? AND user_id = ?
    ");
    $updateStmt->execute([$progress, $status, $download_id, $user_id]);

    echo json_encode([
        'success' => true,
        'message' => 'Progress updated',
        'status' => $status
    ]);
}

function handleDelete($pdo, $user_id) {
    $download_id = $_GET['id'] ?? null;

    if (!$download_id) {
        echo json_encode(['success' => false, 'message' => 'Download ID required']);
        return;
    }

    $deleteStmt = $pdo->prepare("
        DELETE FROM user_downloads_tracking 
        WHERE id = ? AND user_id = ?
    ");
    $deleteStmt->execute([$download_id, $user_id]);

    if ($deleteStmt->rowCount() > 0) {
        echo json_encode(['success' => true, 'message' => 'Download removed successfully']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Download not found']);
    }
}

function formatFileSize($bytes) {
    if ($bytes >= 1073741824) {
        return number_format($bytes / 1073741824, 2) . ' GB';
    } elseif ($bytes >= 1048576) {
        return number_format($bytes / 1048576, 2) . ' MB';
    } elseif ($bytes >= 1024) {
        return number_format($bytes / 1024, 2) . ' KB';
    } else {
        return $bytes . ' bytes';
    }
}
?>
