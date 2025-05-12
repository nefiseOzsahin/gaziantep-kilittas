<?php
$to = "nefisevurur@gmail.com";
$from = $_REQUEST['email'];
$name = $_REQUEST['name'];
$subject = $_REQUEST['subject']; // Telefon numarası geliyor aslında
$message = $_REQUEST['message'];

$headers = "From: Website Contact Form <iletisim@lezizköy.online>\r\n";
$headers .= "Reply-To: $from\r\n";
$headers .= "MIME-Version: 1.0\r\n";
$headers .= "Content-Type: text/html; charset=UTF-8\r\n";

$email_subject = "Yeni mesaj - Web sitesi iletişim formu";

$body = "<!DOCTYPE html><html lang='tr'><head><meta charset='UTF-8'><title>İletişim Formu</title></head><body>";
$body .= "<h2>Yeni Mesaj</h2>";
$body .= "<p><strong>İsim Soyisim:</strong> {$name}</p>";
$body .= "<p><strong>Email:</strong> {$from}</p>";
$body .= "<p><strong>Telefon:</strong> {$subject}</p>";
$body .= "<p><strong>Mesaj:</strong><br>{$message}</p>";
$body .= "</body></html>";

if (mail($to, $email_subject, $body, $headers)) {
    echo "Mesajınız başarıyla gönderildi.";
} else {
    echo "Mesaj gönderilirken bir hata oluştu.";
}
?>
