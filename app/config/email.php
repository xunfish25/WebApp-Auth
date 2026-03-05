<?php
// Email Configuration for Gmail
// You may need to update these values as described in SETUP.md

define('SMTP_HOST', 'smtp.gmail.com');
define('SMTP_PORT', 587);
define('SMTP_USER', 'michaelanicolefalcatan@gmail.com'); // Replace with your email
define('SMTP_PASS', 'ryjyyesgsdapounk');     // Replace with Gmail App Password
define('FROM_EMAIL', 'michaelanicolefalcatan@gmail.com');
define('FROM_NAME', 'Heartifact');

// Function to send email via SMTP
function sendEmail($toEmail, $toName, $subject, $body, $isHtml = true) {
    error_log("[EMAIL] ========== START EMAIL SEND ==========");
    error_log("[EMAIL] To: $toEmail | Subject: $subject");
    
    // Check if vendor exists
    $vendorPath = __DIR__ . '/../../vendor';
    if (!file_exists($vendorPath)) {
        error_log("[EMAIL] ERROR: Vendor directory not found at $vendorPath");
        return false;
    }
    
    if (!file_exists($vendorPath . '/autoload.php')) {
        error_log("[EMAIL] ERROR: vendor/autoload.php not found");
        return false;
    }
    
    require_once $vendorPath . '/autoload.php';
    
    // Using PHPMailer
    error_log("[EMAIL] Using PHPMailer for SMTP");
    
    // Capture output
    ob_start();
    
    $mail = new \PHPMailer\PHPMailer\PHPMailer(true);
    
    try {
        // Configure SMTP
        $mail->isSMTP();
        $mail->Host       = SMTP_HOST;
        $mail->SMTPAuth   = true;
        $mail->Username   = SMTP_USER;
        $mail->Password   = SMTP_PASS;
        $mail->SMTPSecure = \PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = SMTP_PORT;
        $mail->SMTPDebug  = 3;  // Full SMTP debug
        
        error_log("[EMAIL] SMTP Config: Host=" . SMTP_HOST . ", Port=" . SMTP_PORT . ", User=" . SMTP_USER);
        
        // Set message content
        $mail->setFrom(FROM_EMAIL, FROM_NAME);
        $mail->addAddress($toEmail, $toName);
        
        $mail->isHTML($isHtml);
        $mail->Subject = $subject;
        $mail->Body    = $body;
        
        // Send
        error_log("[EMAIL] Sending email...");
        $result = $mail->send();
        
        $output = ob_get_clean();
        if (!empty($output)) {
            error_log("[EMAIL] SMTP Output: " . substr($output, 0, 500));
        }
        
        error_log("[EMAIL] Successfully sent to $toEmail");
        error_log("[EMAIL] ========== END EMAIL SEND SUCCESS ==========");
        return true;
        
    } catch (\PHPMailer\PHPMailer\Exception $e) {
        $output = ob_get_clean();
        error_log('[EMAIL] PHPMailer Error: ' . $e->getMessage());
        if (!empty($output)) {
            error_log("[EMAIL] Debug Output: " . $output);
        }
        error_log("[EMAIL] ========== END EMAIL SEND FAILED (PHPMailer Exception) ==========");
        return false;
    } catch (\Exception $e) {
        $output = ob_get_clean();
        error_log('[EMAIL] General Error: ' . $e->getMessage());
        if (!empty($output)) {
            error_log("[EMAIL] Debug Output: " . $output);
        }
        error_log("[EMAIL] ========== END EMAIL SEND FAILED (General Exception) ==========");
        return false;
    }
}
?>