<?php
// Enhanced error reporting for development
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Security and configuration settings
define('MAX_MESSAGE_LENGTH', 2000);
define('MAX_NAME_LENGTH', 100);
define('LOG_FILE', 'contact_log.txt');
define('ADMIN_EMAIL', 'iletisim@lezizköy.online');
define('SENDER_EMAIL', 'iletisim@lezizköy.online'); // Using same email for both admin and sender

// For international domain names (IDN), we should use Punycode encoding
if (!function_exists('idn_to_ascii')) {
    // Fallback if intl extension is not available
    function punycode_encode($email) {
        $parts = explode('@', $email);
        if (count($parts) === 2) {
            $domain = $parts[1];
            // Simple manual conversion for common Turkish characters
            $domain = str_replace(
                ['ö', 'ü', 'ı', 'ş', 'ç', 'ğ'],
                ['o', 'u', 'i', 's', 'c', 'g'],
                $domain
            );
            return $parts[0] . '@' . $domain;
        }
        return $email;
    }
} else {
    function punycode_encode($email) {
        $parts = explode('@', $email);
        if (count($parts) === 2) {
            $domain = idn_to_ascii($parts[1], IDNA_DEFAULT, INTL_IDNA_VARIANT_UTS46);
            return $parts[0] . '@' . $domain;
        }
        return $email;
    }
}

// Initialize logging
function logMessage($message) {
    $timestamp = date('Y-m-d H:i:s');
    file_put_contents(LOG_FILE, "[$timestamp] $message\n", FILE_APPEND);
}

logMessage('Script started');

// Only process POST requests
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    logMessage('Invalid request method');
    header('Location: index.html?status=error');
    exit();
}

logMessage('POST request received');

// Validate and sanitize input
$name = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW);
$email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
$message = filter_input(INPUT_POST, 'message', FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW);

// Additional validation
if (empty($name) || empty($email) || empty($message)) {
    logMessage('Validation failed: Empty fields');
    header('Location: index.html?status=error&reason=empty_fields');
    exit();
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    logMessage('Validation failed: Invalid email');
    header('Location: index.html?status=error&reason=invalid_email');
    exit();
}

if (strlen($name) > MAX_NAME_LENGTH) {
    logMessage('Validation failed: Name too long');
    header('Location: index.html?status=error&reason=name_too_long');
    exit();
}

if (strlen($message) > MAX_MESSAGE_LENGTH) {
    logMessage('Validation failed: Message too long');
    header('Location: index.html?status=error&reason=message_too_long');
    exit();
}

// Prepare email content with international domain support
$admin_email = punycode_encode(ADMIN_EMAIL);
$sender_email = punycode_encode(SENDER_EMAIL);

$subject = "Yeni İletişim Formu Mesajı: " . substr($name, 0, 30);
$headers = [
    'From' => $sender_email,
    'Reply-To' => $email,
    'MIME-Version' => '1.0',
    'Content-Type' => 'text/html; charset=UTF-8',
    'X-Mailer' => 'PHP/' . phpversion()
];

$emailBody = "
<html>
<head>
    <title>Yeni İletişim Formu Mesajı</title>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; }
        h2 { color: #333; }
        p { margin: 10px 0; }
        strong { color: #555; }
    </style>
</head>
<body>
    <h2>Yeni Mesaj</h2>
    <p><strong>İsim:</strong> " . htmlspecialchars($name) . "</p>
    <p><strong>Email:</strong> <a href=\"mailto:$email\">$email</a></p>
    <p><strong>Mesaj:</strong></p>
    <div style=\"background:#f9f9f9; padding:15px; border-left:4px solid #ddd;\">
        " . nl2br(htmlspecialchars($message)) . "
    </div>
    <p style=\"margin-top:20px; color:#777; font-size:0.9em;\">
        Bu mesaj lezizkoy.online iletişim formu aracılığıyla gönderilmiştir.
    </p>
</body>
</html>
";

// Send email using proper headers
$headersString = '';
foreach ($headers as $key => $value) {
    $headersString .= "$key: $value\r\n";
}

logMessage('Attempting to send email to: ' . $admin_email);
$mailSent = mail($admin_email, $subject, $emailBody, $headersString);

if ($mailSent) {
    logMessage('Email sent successfully');
    
    // Send confirmation to the user
    $confirmationSubject = "Mesajınız Alındı: LezizKöy";
    $confirmationBody = "
    <html>
    <body>
        <p>Merhaba $name,</p>
        <p>İletişim formunuzu başarıyla aldık. En kısa sürede size dönüş yapacağız.</p>
        <p><strong>Mesajınız:</strong></p>
        <div style=\"background:#f9f9f9; padding:15px; border-left:4px solid #ddd;\">
            " . nl2br(htmlspecialchars($message)) . "
        </div>
        <p style=\"margin-top:20px;\">Teşekkür ederiz,<br>LezizKöy Ekibi</p>
    </body>
    </html>";
    
    mail(punycode_encode($email), $confirmationSubject, $confirmationBody, $headersString);
    
    header('Location: index.html?status=success');
} else {
    logMessage('Failed to send email. Last error: ' . error_get_last()['message']);
    header('Location: index.html?status=error&reason=send_failed');
}

exit();