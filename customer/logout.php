<?php
/**
 * Customer Logout Handler
 * Clears customer session and redirects to home.
 */
require_once '../includes/config.php';
require_once '../includes/functions.php';

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_name(defined('SESSION_NAME') ? SESSION_NAME : 'mf_session');
    session_start();
}

// Log the logout for debugging
debug_request('customer_logout', array(
    'customer_id' => $_SESSION['customer_id'] ?? null,
));

// Clear customer session variables
unset($_SESSION['customer_id']);
unset($_SESSION['customer_email']);

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

// Flash success message and redirect to home
session_start();
flash('success', 'You have been logged out successfully.');

redirect('index.php');
