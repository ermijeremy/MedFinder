<?php
/**
 * Customer Authentication Guard
 * Checks if customer is logged in and has valid session.
 * Redirects to login if not authenticated.
 */

// Start session if not started
if (session_status() === PHP_SESSION_NONE) {
    session_name(defined('SESSION_NAME') ? SESSION_NAME : 'mf_session');
    session_start();
}

// Check if customer is logged in
if (empty($_SESSION['customer_id'])) {
    flash('error', 'Please log in to continue');
    redirect('customer/login.php');
}

// Optional: Check if account is still active
// This could be extended to check the database
