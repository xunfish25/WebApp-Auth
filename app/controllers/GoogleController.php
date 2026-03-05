<?php
session_start();
require '../../vendor/autoload.php';
require_once '../config/db.php';
require_once '../models/user.php';

require_once '../config/google.php';
$client = getGoogleClient();

// if no code is present, begin OAuth flow
if (!isset($_GET['code'])) {
    $authUrl = $client->createAuthUrl();
    header('Location: ' . filter_var($authUrl, FILTER_SANITIZE_URL));
    exit();
}

// handle callback
$token = $client->fetchAccessTokenWithAuthCode($_GET['code']);
if (!isset($token['error'])) {
    $client->setAccessToken($token['access_token']);
    $google_oauth = new Google_Service_Oauth2($client);
    $google_account_info = $google_oauth->userinfo->get();
    
    // Use User model to handle Google login
    $user = new User($conn);
    $userResult = $user->googleLogin(
        $google_account_info->email,
        $google_account_info->name,
        $google_account_info->id
    );
    
    if ($userResult) {
        // set minimal session data
        unset($userResult['password']);
        $_SESSION['user_data'] = $userResult;
        header('Location: index.php?action=home');
        exit();
    } else {
        $_SESSION['login_error'] = "Error logging in with Google.";
        header('Location: index.php?action=login');
        exit();
    }
} else {
    $_SESSION['login_error'] = "Error logging in with Google.";
    header('Location: index.php?action=login');
    exit();
}
?>
