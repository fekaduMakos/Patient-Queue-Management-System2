<?php
session_start();
header('Content-Type: application/json');
require_once '../db.php';

if (!isset($_SESSION['role'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);

if (!isset($data['patient_id']) || !isset($data['new_department'])) {
    echo json_encode(['success' => false, 'message' => 'Missing data']);
    exit;
}

try {
    $pid = $data['patient_id'];
    $newDept = $data['new_department'];
    $prefix = $data['prefix'] ?? 'G';

    // 1. Get the patient's existing details
    $stmt = $pdo->prepare("SELECT * FROM queue WHERE patient_id = ? AND (status = 'calling' OR status = 'waiting') ORDER BY id DESC LIMIT 1");
    $stmt->execute([$pid]);
    $currentQueue = $stmt->fetch();

    if (!$currentQueue) {
        echo json_encode(['success' => false, 'message' => 'Patient not active in queue']);
        exit;
    }

    // 2. Mark current session as completed
    $stmt = $pdo->prepare("UPDATE queue SET status = 'completed' WHERE id = ?");
    $stmt->execute([$currentQueue['id']]);

    // 3. Generate New Token for the new department
    $stmt = $pdo->prepare("SELECT token_number FROM queue WHERE token_number LIKE ? AND DATE(created_at) = CURDATE() ORDER BY id DESC LIMIT 1");
    $stmt->execute([$prefix . '-%']);
    $lastToken = $stmt->fetch();

    $nextNumber = 101;
    if ($lastToken) {
        $parts = explode('-', $lastToken['token_number']);
        $nextNumber = intval($parts[1]) + 1;
    }
    $newToken = $prefix . '-' . $nextNumber;

    // 4. Insert into new department
    $stmt = $pdo->prepare("INSERT INTO queue (token_number, patient_id, department, urgency, payment_status, status) VALUES (?, ?, ?, ?, ?, 'waiting')");
    $stmt->execute([$newToken, $pid, $newDept, $currentQueue['urgency'], $currentQueue['payment_status']]);

    echo json_encode(['success' => true, 'new_token' => $newToken]);

} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>
