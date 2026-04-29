<?php
require_once 'db.php';

// This file handles the response from Google after the user logs in.

if (isset($_GET['code'])) {
    // 1. You would normally exchange this 'code' for an Access Token using your Client Secret.
    // Since we don't have a Client Secret yet, we will simulate reading the email for now.
    
    // In a real app, you would do:
    // $token = fetch_google_token($_GET['code'], $client_secret);
    // $user_info = fetch_google_user_info($token);
    // $email = $user_info['email'];

    // --- MOCK SIMULATION FOR DEMO PURPOSES ---
    // Because Google will throw an error if you don't have a real Client ID, 
    // if you somehow reach here, we'll pretend Google returned an email.
    $email = "google_user_" . rand(100,999) . "@gmail.com"; 

    $safe_email = $conn->real_escape_string($email);
    
    // Check if already subscribed
    $check = $conn->query("SELECT * FROM subscribers WHERE email = '$safe_email'");
    if ($check->num_rows == 0) {
        $conn->query("INSERT INTO subscribers (email) VALUES ('$safe_email')");
    }

    // Redirect back to the weather app with a success message and save session
    echo "<script>
            alert('Successfully logged in with Google! ($email)');
            localStorage.setItem('skycast_user', '$safe_email');
            window.location.href = '../index.html';
          </script>";
} else {
    echo "Google Login Failed. Please try again.";
}
?>
