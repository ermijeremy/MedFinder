<?php
require_once '../includes/config.php';
require_once '../includes/db.php';
require_once '../includes/functions.php';
require_once '../includes/customer-auth.php';

db_query(
    "DELETE FROM search_logs WHERE customer_id = :cid",
    [':cid' => $_SESSION['customer_id']]
);

flash('success', 'Search history cleared.');
redirect('customer/dashboard.php');
