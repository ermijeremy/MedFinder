<?php

require_once 'includes/config.php';
require_once 'includes/functions.php';


if (session_status() === PHP_SESSION_NONE) {
    session_name(defined('SESSION_NAME') ? SESSION_NAME : 'mf_session');
    session_start();
}


debug_request('logout', array(
    'admin_id' => $_SESSION['admin_id'] ?? null,
    'pharmacy_id' => $_SESSION['pharmacy_id'] ?? null,
));

// Clear session data for all role types
unset($_SESSION['admin_id']);
unset($_SESSION['admin_username']);
unset($_SESSION['pharmacy_id']);
unset($_SESSION['pharmacy_name']);
unset($_SESSION['pharmacy_status']);
unset($_SESSION['customer_id']);
unset($_SESSION['customer_email']);
unset($_SESSION['_csrf_token']);
unset($_SESSION['_flash']);

// Clear session cookie
if (ini_get('session.use_cookies')) {
    $cookie_params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000, 
        $cookie_params['path'],
        $cookie_params['domain'],
        $cookie_params['secure'],
        $cookie_params['httponly']
    );
}

// Regenerate and destroy session
session_regenerate_id(true);
session_destroy();


session_start();
flash('success', 'You have been logged out successfully.');


redirect('index.php');
?>