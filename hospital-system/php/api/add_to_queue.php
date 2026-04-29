<?php
header('Content-Type: application/json');
require_once '../db.php';

$data = json_decode(file_get_contents('php://input'), true);

$requiredFields = ['patient_name', 'age', 'gender', 'phone', 'department', 'prefix', 'password'];
foreach ($requiredFields as $field) {
    if (empty($data[$field])) {
        echo json_encode(['success' => false, 'message' => "Missing field: $field"]);
        exit;
    }
}

// Phone Number Validation (Ethiopian standard: 10 digits)
if (!preg_match('/^[0-9]{10}$/', $data['phone'])) {
    echo json_encode(['success' => false, 'message' => 'Phone number must be exactly 10 digits (e.g., 0911223344)']);
    exit;
}

try {
    $pdo->beginTransaction();

    $name = $data['patient_name'];
    $phone = $data['phone'];
    $pass = $data['password'];
    $age = $data['age'];
    $gender = $data['gender'];
    $dept = $data['department'];
    $prefix = $data['prefix'];
    $urgency = $data['urgency'] ?? 'Normal';
    $payment = $data['payment_status'] ?? 'Pending';

    // 1. Check if patient already exists (by Phone)
    $stmt = $pdo->prepare("SELECT patient_id, full_name FROM patients WHERE phone = ?");
    $stmt->execute([$phone]);
    $existing = $stmt->fetch();

    if ($existing) {
        // If they didn't provide a Patient ID, but the phone exists, it's a duplicate registration attempt
        if (empty($data['patient_id'])) {
            echo json_encode(['success' => false, 'message' => "ይህ ስልክ ቁጥር ቀድሞ ተመዝግቧል። እባክዎ የቀድሞ መለያዎን ይጠቀሙ ወይም ይግቡ። (This phone number is already registered. Please login or use your Patient ID.)"]);
            exit;
        }
        $p_id = $existing['patient_id'];
    } else {
        // Create New Patient ID (e.g., NEH-12345)
        $p_id = 'NEH-' . mt_rand(10000, 99999);
        $sql = "INSERT INTO patients (patient_id, full_name, phone, password, age, gender) VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$p_id, $name, $phone, $pass, $age, $gender]);
    }

    // 2. Generate Token
    $stmt = $pdo->prepare("SELECT token_number FROM queue WHERE token_number LIKE ? AND DATE(created_at) = CURDATE() ORDER BY id DESC LIMIT 1");
    $stmt->execute([$prefix . '-%']);
    $lastToken = $stmt->fetch();

    $nextNumber = 101;
    if ($lastToken) {
        $parts = explode('-', $lastToken['token_number']);
        $nextNumber = intval($parts[1]) + 1;
    }
    $newToken = $prefix . '-' . $nextNumber;

    // 3. Add to Queue
    $sql = "INSERT INTO queue (token_number, patient_id, department, urgency, payment_status) VALUES (?, ?, ?, ?, ?)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$newToken, $p_id, $dept, $urgency, $payment]);

    // 4. Automatic "First Patient" logic
    // If the patient is already approved (returning) and the counter is empty, set them as current
    $stmt = $pdo->prepare("SELECT status FROM patients WHERE patient_id = ?");
    $stmt->execute([$p_id]);
    $p_status = $stmt->fetchColumn();

    if ($p_status === 'approved') {
        $stmt = $pdo->prepare("SELECT current_token FROM counters WHERE counter_name LIKE ? LIMIT 1");
        $stmt->execute(['%' . $dept . '%']);
        $current = $stmt->fetchColumn();

        if ($current === '---' || empty($current) || $current === '0') {
            $stmt = $pdo->prepare("UPDATE counters SET current_token = ? WHERE counter_name LIKE ?");
            $stmt->execute([$newToken, '%' . $dept . '%']);
            
            $stmt = $pdo->prepare("UPDATE queue SET status = 'calling', called_at = NOW() WHERE token_number = ? AND DATE(created_at) = CURDATE()");
            $stmt->execute([$newToken]);
        }
    }

    $pdo->commit();

    echo json_encode([
        'success' => true, 
        'token' => $newToken, 
        'name' => $name,
        'patient_id' => $p_id,
        'department' => $dept
    ]);

} catch (Exception $e) {
    $pdo->rollBack();
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>
