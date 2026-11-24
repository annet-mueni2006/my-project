<?php
require 'config/db.php'; require 'includes/auth.php';
if (empty($_SESSION['patient_id'])) { header('Location: login.php'); exit; }
$stmt=$pdo->prepare('SELECT full_name,email FROM patients WHERE id=? AND is_verified=1'); $stmt->execute([$_SESSION['patient_id']]); $patient=$stmt->fetch();
if (!$patient) { unset($_SESSION['patient_id']); header('Location: login.php'); exit; }
page('Patient dashboard'); echo '<p class="mb-2">Welcome, <strong>'.e($patient['full_name']).'</strong>!</p><p class="text-gray-600 mb-6">'.e($patient['email']).'</p><p class="mb-6">Your account is verified. You are securely logged in.</p><form method="post" action="logout.php"><input type="hidden" name="csrf" value="'.e(csrf()).'">'; submit_button('Log out'); echo '</form>'; endpage();
