<?php
session_start();
header('Content-Type: application/json');
require_once '../db.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);

if (!isset($data['patient_id']) || !isset($data['action'])) {
    echo json_encode(['success' => false, 'message' => 'Missing data']);
    exit;
}

$status = ($data['action'] === 'approve') ? 'approved' : 'rejected';

try {
    $stmt = $pdo->prepare("UPDATE patients SET status = ? WHERE patient_id = ?");
    $stmt->execute([$status, $data['patient_id']]);

    echo json_encode(['success' => true]);

} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>
