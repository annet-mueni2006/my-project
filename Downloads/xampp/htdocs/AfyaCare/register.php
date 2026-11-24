<?php
require 'config/db.php'; require 'includes/auth.php'; require 'includes/send_otp.php';
$error='';
if ($_SERVER['REQUEST_METHOD']==='POST') {
 check_csrf();
 $name=trim($_POST['full_name']??''); $email=strtolower(trim($_POST['email']??'')); $phone=trim($_POST['phone']??''); $pass=$_POST['password']??'';
 if (strlen($name)<2 || strlen($name)>100 || !filter_var($email,FILTER_VALIDATE_EMAIL) || strlen($email)>150 || !preg_match('/^[0-9+() -]{7,20}$/',$phone) || strlen($pass)<8) $error='Enter valid details and a password of at least 8 characters.';
 else {
  $stmt=$pdo->prepare('SELECT id,is_verified FROM patients WHERE email=?'); $stmt->execute([$email]); $existing=$stmt->fetch();
  if ($existing) $error='This email is already registered. Please log in or contact support.';
  else {
   try {
    $stmt=$pdo->prepare('INSERT INTO patients(full_name,email,phone,password) VALUES (?,?,?,?)'); $stmt->execute([$name,$email,$phone,password_hash($pass,PASSWORD_DEFAULT)]);
    $id=(int)$pdo->lastInsertId(); $_SESSION['pending_patient']=$id;
    issue_otp($pdo,$id,$email,$name);
    header('Location: verify_otp.php'); exit;
   } catch (\PHPMailer\PHPMailer\Exception $ex) { $error='Email could not be sent. Check Gmail SMTP settings, then use Resend OTP.'; }
     catch (PDOException $ex) { $error='Registration could not be completed. Please try again.'; }
  }
 }
}
page('Patient registration'); if ($error) message($error);
echo '<form method="post"><input type="hidden" name="csrf" value="'.e(csrf()).'">';
field('full_name','Full name'); field('email','Email','email','email'); field('phone','Phone number','tel','tel'); field('password','Password (minimum 8 characters)','password','new-password'); submit_button('Register & send OTP'); echo '</form><p class="mt-4 text-sm">Already registered? <a class="text-teal-700 underline" href="login.php">Log in</a></p>'; endpage();
