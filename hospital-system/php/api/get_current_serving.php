<?php
header('Content-Type: application/json');
require_once '../db.php';

$window = $_GET['window'] ?? 1;

try {
    $stmt = $pdo->prepare("SELECT current_token FROM counters WHERE window_number = ?");
    $stmt->execute([$window]);
    $token = $stmt->fetchColumn();

    echo json_encode([
        'success' => true,
        'token' => $token ?: '---'
    ]);

} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>
