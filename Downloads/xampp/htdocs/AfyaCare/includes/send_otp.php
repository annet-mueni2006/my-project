<?php
require_once __DIR__.'/../vendor/autoload.php';
require_once __DIR__.'/../config/mail.php';
use PHPMailer\PHPMailer\PHPMailer;
function send_otp(string $email, string $name, string $code): void {
    $mail = new PHPMailer(true);
    $mail->isSMTP(); $mail->Host='smtp.gmail.com'; $mail->SMTPAuth=true;
    $mail->Username=SMTP_USER; $mail->Password=SMTP_PASS;
    $mail->SMTPSecure=PHPMailer::ENCRYPTION_STARTTLS; $mail->Port=587;
    $mail->setFrom(SMTP_USER, 'AfyaCare'); $mail->addAddress($email, $name);
    $mail->isHTML(true); $mail->Subject='AfyaCare email verification';
    $mail->Body='<p>Your AfyaCare verification code is:</p><h2>'.htmlspecialchars($code, ENT_QUOTES, 'UTF-8').'</h2><p>Expires in 5 minutes. Do not share it.</p>';
    $mail->AltBody="Your AfyaCare verification code is $code. Expires in 5 minutes. Do not share it.";
    $mail->send();
}
function issue_otp(PDO $pdo, int $patientId, string $email, string $name): void {
    $code=(string)random_int(100000,999999);
    $hash=password_hash($code, PASSWORD_DEFAULT);
    $stmt=$pdo->prepare('INSERT INTO email_otps(patient_id,otp_hash,expires_at,attempts,last_sent_at) VALUES (?, ?, DATE_ADD(NOW(), INTERVAL 5 MINUTE), 0, NOW()) ON DUPLICATE KEY UPDATE otp_hash=VALUES(otp_hash), expires_at=VALUES(expires_at), attempts=0, last_sent_at=NOW()');
    $stmt->execute([$patientId,$hash]);
    send_otp($email,$name,$code);
}
