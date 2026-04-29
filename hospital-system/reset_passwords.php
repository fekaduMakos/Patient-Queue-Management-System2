<?php
require_once 'php/db.php';
$pass = password_hash('123456', PASSWORD_DEFAULT);
$users = ['triage_staff', 'opd_doctor', 'ped_doctor', 'lab_tech', 'pharmacist', 'radiology_tech', 'admin'];

foreach ($users as $user) {
    $stmt = $pdo->prepare("UPDATE users SET password = ? WHERE username = ?");
    $stmt->execute([$pass, $user]);
}
echo "All passwords reset to 123456";
?>
