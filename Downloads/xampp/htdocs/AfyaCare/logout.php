<?php
require 'includes/auth.php';
if ($_SERVER['REQUEST_METHOD']!=='POST') { header('Location: patient_dashboard.php'); exit; }
check_csrf(); $_SESSION=[]; session_regenerate_id(true); header('Location: login.php'); exit;
