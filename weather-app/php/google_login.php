<?php
// SkyCast - Real Google OAuth 2.0 Login
// You need to get your Client ID from Google Cloud Console (https://console.cloud.google.com/)

// 1. Paste your Google Client ID here
$client_id = 'YOUR_GOOGLE_CLIENT_ID_HERE'; // <-- CHANGE THIS LATER

// 2. The URL Google will send the user back to after login
$redirect_uri = 'http://localhost/New%20folder/weather-app/php/google_callback.php';

// 3. Generate the Google Login URL
$auth_url = 'https://accounts.google.com/o/oauth2/v2/auth?' . http_build_query([
    'client_id' => $client_id,
    'redirect_uri' => $redirect_uri,
    'response_type' => 'code',
    'scope' => 'email profile',
    'prompt' => 'select_account' // Forces Google to show the account selection screen
]);

// Redirect the user to the real Google login page
header('Location: ' . filter_var($auth_url, FILTER_SANITIZE_URL));
exit;
?>
