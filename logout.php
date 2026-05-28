<?php
/**
 * Unified Logout Handler (Phase 4)
 * Detects all session types (admin, pharmacy, customer) and clears appropriately.
 * Provides a centralized logout endpoint for all portals.
 */
require_once 'includes/config.php';
require_once 'includes/functions.php';

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_name(defined('SESSION_NAME') ? SESSION_NAME : 'mf_session');
    session_start();
}

// Log the logout for debugging (Phase 4)
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

// Flash success message and redirect to public home (Phase 4)
session_start();
flash('success', 'You have been logged out successfully.');

// Redirect to home page
redirect('index.php');
?>