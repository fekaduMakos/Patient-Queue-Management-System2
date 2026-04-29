<?php
header('Content-Type: application/json');
require_once 'db.php';

// Get the JSON POST data
$data = json_decode(file_get_contents('php://input'), true);

if (!isset($data['email']) || !filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
    echo json_encode(['success' => false, 'message' => 'Please enter a valid email address.']);
    exit;
}

$email = $conn->real_escape_string($data['email']);
$password = isset($data['password']) ? $data['password'] : '';

if (empty($password)) {
    echo json_encode(['success' => false, 'message' => 'Password is required.']);
    exit;
}

// Securely hash the password
$hashed_password = password_hash($password, PASSWORD_DEFAULT);

// Check if already subscribed
$check = $conn->query("SELECT * FROM subscribers WHERE email = '$email'");
if ($check->num_rows > 0) {
    echo json_encode(['success' => false, 'message' => 'This email is already subscribed!']);
    exit;
}

// Insert new subscriber with hashed password
if ($conn->query("INSERT INTO subscribers (email, password) VALUES ('$email', '$hashed_password')")) {
    echo json_encode(['success' => true, 'message' => 'Account created! Welcome to SkyCast.']);
} else {
    echo json_encode(['success' => false, 'message' => 'Database error occurred.']);
}
?>
