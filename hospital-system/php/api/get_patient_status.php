<?php
header('Content-Type: application/json');
require_once '../db.php';

$patient_id = $_GET['id'] ?? '';

if (empty($patient_id)) {
    echo json_encode(['success' => false, 'message' => 'No ID provided']);
    exit();
}

try {
    // 1. Get patient's current active queue info
    $stmt = $pdo->prepare("
        SELECT q.*, p.full_name, p.phone, p.age, p.gender, p.patient_id as actual_id
        FROM queue q
        JOIN patients p ON q.patient_id = p.patient_id
        WHERE q.patient_id = ? AND q.status != 'completed' AND q.status != 'skipped'
        ORDER BY q.created_at DESC LIMIT 1
    ");
    $stmt->execute([$patient_id]);
    $myQueue = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$myQueue) {
        echo json_encode(['success' => false, 'message' => 'No active queue found']);
        exit();
    }

    // 2. Get currently serving number
    // First, check if the patient is already assigned to a window (being called)
    if (!empty($myQueue['window_number'])) {
        $stmt = $pdo->prepare("SELECT current_token FROM counters WHERE window_number = ?");
        $stmt->execute([$myQueue['window_number']]);
        $currentServing = $stmt->fetchColumn() ?: '---';
    } else {
        // Fallback: look at the latest token in their department
        $stmt = $pdo->prepare("SELECT current_token FROM counters WHERE counter_name LIKE ? ORDER BY window_number ASC LIMIT 1");
        $stmt->execute(['%' . $myQueue['department'] . '%']);
        $currentServing = $stmt->fetchColumn() ?: '---';
    }

    // 3. Count people ahead in the same department
    $stmt = $pdo->prepare("
        SELECT COUNT(*) FROM queue 
        WHERE department = ? AND status = 'waiting' AND id < ?
    ");
    $stmt->execute([$myQueue['department'], $myQueue['id']]);
    $peopleAhead = $stmt->fetchColumn();

    // 4. Count total people remaining in the same department
    $stmt = $pdo->prepare("
        SELECT COUNT(*) FROM queue 
        WHERE department = ? AND status = 'waiting'
    ");
    $stmt->execute([$myQueue['department']]);
    $totalRemaining = $stmt->fetchColumn();

    // 5. Get status of all depts for the sidebar
    $stmt = $pdo->query("SELECT counter_name as name, current_token as current FROM counters WHERE counter_name NOT LIKE '%Reception%'");
    $allDepts = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode([
        'success' => true,
        'patient_name' => $myQueue['full_name'],
        'patient_phone' => $myQueue['phone'],
        'patient_age' => $myQueue['age'],
        'patient_gender' => $myQueue['gender'],
        'patient_id_display' => $myQueue['actual_id'],
        'token' => $myQueue['token_number'],
        'department' => $myQueue['department'],
        'current_serving' => $currentServing,
        'people_ahead' => $peopleAhead,
        'total_remaining' => $totalRemaining,
        'all_depts' => $allDepts
    ]);

} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>
