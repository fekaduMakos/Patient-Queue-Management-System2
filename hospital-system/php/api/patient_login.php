<?php
session_start();
header('Content-Type: application/json');
require_once '../db.php';

$data = json_decode(file_get_contents('php://input'), true);

if (!isset($data['phone']) || !isset($data['password'])) {
    echo json_encode(['success' => false, 'message' => 'Missing data']);
    exit;
}

try {
    $stmt = $pdo->prepare("SELECT * FROM patients WHERE phone = ?");
    $stmt->execute([$data['phone']]);
    $patient = $stmt->fetch();

    if ($patient && $data['password'] === $patient['password']) {
        if ($patient['status'] === 'pending') {
            echo json_encode(['success' => false, 'message' => 'የእርስዎ አካውንት ገና አልጸደቀም። እባክዎ አስተዳዳሪው እስኪያጸድቅ ድረስ ይጠብቁ። (Your account is pending approval. Please wait.)']);
            exit;
        } elseif ($patient['status'] === 'rejected') {
            echo json_encode(['success' => false, 'message' => 'የእርስዎ አካውንት ተሰርዟል። እባክዎ መስተንግዶውን ያነጋግሩ። (Your account has been rejected. Please contact reception.)']);
            exit;
        }
        $_SESSION['patient_id'] = $patient['patient_id'];
        $_SESSION['patient_name'] = $patient['full_name'];
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Invalid phone or password']);
    }

} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>
