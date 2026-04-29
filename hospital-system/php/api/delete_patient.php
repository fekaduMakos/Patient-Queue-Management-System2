<?php
session_start();
header('Content-Type: application/json');
require_once '../db.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);

if (!isset($data['patient_id'])) {
    echo json_encode(['success' => false, 'message' => 'Missing Patient ID']);
    exit;
}

try {
    $pdo->beginTransaction();

    $p_id = $data['patient_id'];

    // 1. Delete from queue first (foreign key constraint)
    $stmt = $pdo->prepare("DELETE FROM queue WHERE patient_id = ?");
    $stmt->execute([$p_id]);

    // 2. Delete from patients
    $stmt = $pdo->prepare("DELETE FROM patients WHERE patient_id = ?");
    $stmt->execute([$p_id]);

    $pdo->commit();
    echo json_encode(['success' => true]);

} catch (Exception $e) {
    $pdo->rollBack();
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>