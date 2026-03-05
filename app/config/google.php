<?php
// Google OAuth configuration
// Fill these with your Google OAuth credentials
// Create credentials at: https://console.developers.google.com/apis/credentials

define('GOOGLE_CLIENT_ID', '');
define('GOOGLE_CLIENT_SECRET', '');
// Redirect should match the one set in Google Console. This project uses a standalone callback file:
define('GOOGLE_REDIRECT_URI', '');

function getGoogleClient() {
    require_once __DIR__ . '/../../vendor/autoload.php';

    $client = new Google_Client();
    $client->setClientId(GOOGLE_CLIENT_ID);
    $client->setClientSecret(GOOGLE_CLIENT_SECRET);
    $client->setRedirectUri(GOOGLE_REDIRECT_URI);
    $client->addScope('email');
    $client->addScope('profile');
    $client->setAccessType('offline');

    // force Google to show account chooser each time
    // (prompt=select_account). Without this, Google may automatically
    // re-use the last logged‑in Google account in the browser.
    $client->setPrompt('select_account');

    return $client;
}
?>