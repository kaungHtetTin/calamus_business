<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php'; // if installed via composer
// or require 'PHPMailer/src/PHPMailer.php'; etc. if manual

$mail = new PHPMailer(true);

try {
    $mail->isSMTP();
    $mail->Host = 'smtp.hostinger.com';
    $mail->SMTPAuth = true;
    $mail->Username = 'business@calamuseducation.com'; // your Hostinger email
    $mail->Password = 'Wyne75707@@';       // your email password
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS; // for SSL (port 465)
    $mail->Port = 465;                       // or use 587 with STARTTLS

    //Recipients
    $mail->setFrom('business@calamuseducation.com', 'Kaung Htet Tin');
    $mail->addAddress('kaunghtettin17204@gmail.com');

    //Content
    $mail->isHTML(true);
    $mail->Subject = 'Test Email from Localhost using Hostinger';
    $mail->Body    = 'Hello! This email is sent from XAMPP using Hostinger SMTP.';

    $mail->send();
    echo '✅ Message has been sent';
} catch (Exception $e) {
    echo "❌ Message could not be sent. Error: {$mail->ErrorInfo}";
}
?>
