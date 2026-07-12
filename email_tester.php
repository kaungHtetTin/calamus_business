<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require __DIR__ . '/vendor/autoload.php';
require __DIR__ . '/env_loader.php';

$mail = new PHPMailer(true);

try {
    $mail->isSMTP();
    $mail->Host = envValue('SMTP_HOST');
    $mail->SMTPAuth = true;
    $mail->Username = envValue('SMTP_USERNAME');
    $mail->Password = envValue('SMTP_PASSWORD');
    $mail->SMTPSecure = strtolower(envValue('SMTP_ENCRYPTION', 'ssl')) === 'tls'
        ? PHPMailer::ENCRYPTION_STARTTLS
        : PHPMailer::ENCRYPTION_SMTPS;
    $mail->Port = (int) envValue('SMTP_PORT', 465);

    //Recipients
    $mail->setFrom(
        envValue('EMAIL_FROM_ADDRESS'),
        envValue('EMAIL_FROM_NAME', 'Calamus Education')
    );
    $mail->addAddress(envValue('EMAIL_TEST_RECIPIENT'));

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
