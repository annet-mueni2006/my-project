<?php
require 'config/db.php'; require 'includes/auth.php'; require 'includes/send_otp.php';
if ($_SERVER['REQUEST_METHOD']!=='POST') { header('Location: verify_otp.php'); exit; }
check_csrf(); $id=(int)($_SESSION['pending_patient']??0);
$stmt=$pdo->prepare('SELECT p.*,o.last_sent_at FROM patients p LEFT JOIN email_otps o ON o.patient_id=p.id WHERE p.id=? AND p.is_verified=0'); $stmt->execute([$id]); $patient=$stmt->fetch();
if (!$patient) { header('Location: register.php'); exit; }
if ($patient['last_sent_at'] && time()-strtotime($patient['last_sent_at'])<60) { $_SESSION['otp_notice']='Wait at least 60 seconds before requesting another code.'; header('Location: verify_otp.php'); exit; }
try { issue_otp($pdo,$id,$patient['email'],$patient['full_name']); $_SESSION['otp_notice']='A new code has been sent.'; }
catch (\PHPMailer\PHPMailer\Exception $ex) { $_SESSION['otp_notice']='Unable to send email. Check SMTP configuration.'; }
header('Location: verify_otp.php'); exit;
