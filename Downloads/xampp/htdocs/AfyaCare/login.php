<?php
require 'config/db.php'; require 'includes/auth.php';
if (isset($_SESSION['patient_id'])) { header('Location: patient_dashboard.php'); exit; }
$error='';
if ($_SERVER['REQUEST_METHOD']==='POST') {
 check_csrf(); $email=strtolower(trim($_POST['email']??'')); $pass=$_POST['password']??'';
 $stmt=$pdo->prepare('SELECT * FROM patients WHERE email=?'); $stmt->execute([$email]); $patient=$stmt->fetch();
 if (!$patient || !password_verify($pass,$patient['password'])) $error='Invalid email or password.';
 elseif (!$patient['is_verified']) { $_SESSION['pending_patient']=(int)$patient['id']; $error='Verify your email first. Use the verification page to resend your code.'; }
 else { session_regenerate_id(true); $_SESSION['patient_id']=(int)$patient['id']; header('Location: patient_dashboard.php'); exit; }
}
page('Patient login'); if (!empty($_SESSION['flash'])) { message($_SESSION['flash']); unset($_SESSION['flash']); } if ($error) message($error);
echo '<form method="post"><input type="hidden" name="csrf" value="'.e(csrf()).'">'; field('email','Email','email','email'); field('password','Password','password','current-password'); submit_button('Log in'); echo '</form><p class="mt-4 text-sm">New patient? <a class="text-teal-700 underline" href="register.php">Register</a></p>'; if (!empty($_SESSION['pending_patient'])) echo '<p class="mt-2"><a class="text-teal-700 underline" href="verify_otp.php">Verify email</a></p>'; endpage();
