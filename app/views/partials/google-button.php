<?php
// only show the google button if client ID is defined
$googleConfigured = false;
if (file_exists(__DIR__ . '/../../config/google.php')) {
    require_once __DIR__ . '/../../config/google.php';
    if (defined('GOOGLE_CLIENT_ID') && GOOGLE_CLIENT_ID && GOOGLE_CLIENT_ID !== 'YOUR_GOOGLE_CLIENT_ID') {
        $googleConfigured = true;
    }
}
if ($googleConfigured): ?>
    <div style="margin-top: 1rem;">
        <a href="/heartifact-mid/oauth2callback.php" style="display:inline-flex;align-items:center;gap:10px;padding:10px 14px;border-radius:6px;border:1px solid #ddd;background:#fff;color:#444;text-decoration:none;">
            <img src="https://www.gstatic.com/firebasejs/ui/2.0.0/images/auth/google.svg" alt="Google" style="width:20px;height:20px;">
            <span>Sign in with Google</span>
        </a>
    </div>
<?php endif; ?>