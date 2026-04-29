<?php
header('Content-Type: application/json');
require_once '../db.php';

$data = json_decode(file_get_contents('php://input'), true);

if (!isset($data['window_number'])) {
    echo json_encode(['success' => false, 'message' => 'Specify window number']);
    exit;
}

$window = $data['window_number'];

try {
    // 0. Find the department/counter name for this window
    $stmt = $pdo->prepare("SELECT counter_name FROM counters WHERE window_number = ?");
    $stmt->execute([$window]);
    $counter = $stmt->fetch();
    
    if (!$counter) {
        echo json_encode(['success' => false, 'message' => 'Invalid window number']);
        exit;
    }
    
    $deptName = $counter['counter_name'];

    // 1. Find the oldest waiting patient who is APPROVED and in the SAME department
    $stmt = $pdo->prepare("
        SELECT q.id, q.token_number, q.patient_id
        FROM queue q 
        JOIN patients p ON q.patient_id = p.patient_id 
        WHERE q.status = 'waiting' 
        AND p.status = 'approved' 
        AND q.department = ?
        AND DATE(q.created_at) = CURDATE() 
        ORDER BY q.id ASC LIMIT 1
    ");
    $stmt->execute([$deptName]);
    $patient = $stmt->fetch();

    if (!$patient) {
        echo json_encode(['success' => false, 'message' => 'No one in queue']);
        exit;
    }

    $patientId = $patient['id'];
    $actualPatientId = $patient['patient_id'];
    $token = $patient['token_number'];

    // 2. Mark as calling and set window
    $stmt = $pdo->prepare("UPDATE queue SET status = 'calling', window_number = ?, called_at = NOW() WHERE id = ?");
    $stmt->execute([$window, $patientId]);

    // 3. Update the global Counter status for the TV to see
    $stmt = $pdo->prepare("UPDATE counters SET current_token = ? WHERE window_number = ?");
    $stmt->execute([$token, $window]);

    echo json_encode(['success' => true, 'token' => $token, 'patient_id' => $actualPatientId]);

} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>
