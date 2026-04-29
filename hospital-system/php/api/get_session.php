<?php
session_start();
header('Content-Type: application/json');

if (isset($_SESSION['user_id'])) {
    echo json_encode([
        'loggedIn' => true,
        'user' => [
            'name' => $_SESSION['full_name'],
            'role' => $_SESSION['role'],
            'window' => $_SESSION['window_number']
        ]
    ]);
} else {
    echo json_encode(['loggedIn' => false]);
}
?>
