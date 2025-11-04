<?php
require_once 'config/config.php';

// Redirect to login if not logged in
if (!isLoggedIn()) {
    redirect('/login.php');
}

// Redirect to appropriate dashboard based on role
$role = getUserRole();
if ($role === 'doctor') {
    redirect('/doctor/dashboard.php');
} elseif ($role === 'patient') {
    redirect('/patient/dashboard.php');
} elseif ($role === 'admin') {
    redirect('/admin/dashboard.php');
} else {
    redirect('/login.php');
}
?>
