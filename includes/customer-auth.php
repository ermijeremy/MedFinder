<?php


// Start session if not already started
start_session();

// Check if customer is logged in
if (empty($_SESSION['customer_id'])) {
    flash('error', 'Please log in to continue');
    redirect('customer/login.php');
}

