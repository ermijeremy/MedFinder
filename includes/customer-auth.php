<?php
/**
 * Customer Authentication Guard
 * Checks if customer is logged in and has valid session.
 * Redirects to login if not authenticated.
 */

// Start session if not already started
start_session();

// Check if customer is logged in
if (empty($_SESSION['customer_id'])) {
    flash('error', 'Please log in to continue');
    redirect('customer/login.php');
}

// Optional: Check if account is still active
// This could be extended to check the database
