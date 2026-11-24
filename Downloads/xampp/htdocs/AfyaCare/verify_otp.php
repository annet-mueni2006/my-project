<?php
require 'config/db.php'; require 'includes/auth.php';
$id=(int)($_SESSION['pending_patient']??0); if (!$id) { header('Location: register.php'); exit; }
$error='';
if ($_SERVER['REQUEST_METHOD']==='POST') {
 check_csrf(); $code=trim($_POST['otp']??'');
 $pdo->beginTransaction();
 $stmt=$pdo->prepare('SELECT * FROM email_otps WHERE patient_id=? FOR UPDATE'); $stmt->execute([$id]); $otp=$stmt->fetch();
 if (!$otp || strtotime($otp['expires_at'])<=time()) $error='Code expired. Please request another.';
 elseif ((int)$otp['attempts']>=5) $error='Too many attempts. Request a new code.';
 elseif (!preg_match('/^[0-9]{6}$/',$code) || !password_verify($code,$otp['otp_hash'])) { $pdo->prepare('UPDATE email_otps SET attempts=attempts+1 WHERE patient_id=?')->execute([$id]); $error='Incorrect code.'; }
 else {
  $pdo->prepare('UPDATE patients SET is_verified=1 WHERE id=?')->execute([$id]);
  $pdo->prepare('DELETE FROM email_otps WHERE patient_id=?')->execute([$id]);
  $pdo->commit(); unset($_SESSION['pending_patient']); $_SESSION['flash']='Email verified. Please log in.'; header('Location: login.php'); exit;
 }
 $pdo->commit();
}
page('Verify your email'); if (!empty($_SESSION['otp_notice'])) { message($_SESSION['otp_notice']); unset($_SESSION['otp_notice']); } if ($error) message($error);
echo '<p class="text-sm mb-4">Enter the six-digit code sent to your email. It expires in five minutes.</p><form method="post"><input type="hidden" name="csrf" value="'.e(csrf()).'">'; field('otp','Verification code','text','one-time-code'); submit_button('Verify email'); echo '</form><form class="mt-4" method="post" action="resend_otp.php"><input type="hidden" name="csrf" value="'.e(csrf()).'"><button class="text-teal-700 underline" type="submit">Resend OTP</button></form>'; endpage();
