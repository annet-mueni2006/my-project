<?php
require_once __DIR__ . '/includes/functions.php';
requireLogin();

if ($_SESSION['role'] === 'doctor') {
    redirect('/annet-healthcare/doctor/dashboard.php');
} else {
    redirect('/annet-healthcare/patient/dashboard.php');
}
?>