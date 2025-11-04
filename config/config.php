<?php
session_start();

// Base URL configuration for relative paths
define('BASE_URL', '/hms');

// Timezone
date_default_timezone_set('Asia/Kuala_Lumpur');

// Include database
require_once __DIR__ . '/database.php';

// Helper functions
function redirect($path) {
    header("Location: " . BASE_URL . $path);
    exit();
}

function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

function requireLogin() {
    if (!isLoggedIn()) {
        redirect('/login.php');
    }
}

function getUserRole() {
    return $_SESSION['role'] ?? null;
}

function formatDate($date) {
    return date('d/m/Y', strtotime($date));
}

function formatDateTime($datetime) {
    return date('d/m/Y H:i', strtotime($datetime));
}

function formatCurrency($amount) {
    return 'RM ' . number_format($amount, 2);
}
?>
