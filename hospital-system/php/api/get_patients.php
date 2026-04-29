<?php
session_start();
header('Content-Type: application/json');
require_once '../db.php';

// Security check: Only Admins can access full lists
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

try {
    $window = $_GET['window'] ?? null;
    $deptFilter = "";
    $params = [];

    if ($window) {
        // Get department name for this window
        $stmt = $pdo->prepare("SELECT counter_name FROM counters WHERE window_number = ?");
        $stmt->execute([$window]);
        $dept = $stmt->fetchColumn();
        if ($dept) {
            $deptFilter = " AND q.department = ? ";
            $params[] = $dept;
        }
    }

    $sql = "
        SELECT q.*, p.full_name as patient_name, p.age, p.gender, p.phone, p.status as account_status
        FROM queue q
        JOIN patients p ON q.patient_id = p.patient_id
        WHERE 1=1 $deptFilter
        ORDER BY q.created_at DESC 
        LIMIT 50
    ";
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $patients = $stmt->fetchAll();

    echo json_encode([
        'success' => true,
        'patients' => $patients
    ]);

} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>
