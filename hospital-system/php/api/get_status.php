<?php
header('Content-Type: application/json');
require_once '../db.php';

try {
    // 1. Get current status of all windows + waiting counts
    $stmt = $pdo->query("
        SELECT 
            c.window_number, 
            c.current_token, 
            c.counter_name,
            (SELECT COUNT(*) FROM queue WHERE department = c.counter_name AND status = 'waiting') as waiting_count
        FROM counters c 
        ORDER BY c.window_number ASC
    ");
    $windows = $stmt->fetchAll();

    // 2. Get the very last patient called (for the big display)
    $stmt = $pdo->query("SELECT token_number, window_number FROM queue WHERE status = 'calling' ORDER BY called_at DESC LIMIT 1");
    $latest = $stmt->fetch();

    echo json_encode([
        'success' => true,
        'windows' => $windows,
        'latest' => $latest
    ]);

} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>
