<?php
header('Content-Type: application/json');
require_once 'db.php';

$data = json_decode(file_get_contents('php://input'), true);

if (!isset($data['email']) || !isset($data['password'])) {
    echo json_encode(['success' => false, 'message' => 'Please provide email and password.']);
    exit;
}

$email = $conn->real_escape_string($data['email']);
$password = $data['password'];

$result = $conn->query("SELECT * FROM subscribers WHERE email = '$email'");
if ($result->num_rows == 0) {
    echo json_encode(['success' => false, 'message' => 'Account not found. Please subscribe first.']);
    exit;
}

$user = $result->fetch_assoc();

// If user registered with Google previously, they might not have a password
if (empty($user['password'])) {
    echo json_encode(['success' => false, 'message' => 'You registered with Google. Please use "Continue with Google" to login.']);
    exit;
}

// Verify the hashed password
if (password_verify($password, $user['password'])) {
    echo json_encode(['success' => true, 'message' => 'Successfully logged in! Welcome back.']);
} else {
    echo json_encode(['success' => false, 'message' => 'Incorrect password. Please try again.']);
}
?>
