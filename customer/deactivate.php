<?php
require_once '../includes/config.php';
require_once '../includes/db.php';
require_once '../includes/functions.php';
require_once '../includes/customer-auth.php';
require_once '../includes/services/CustomerService.php';

// Deactivate account
CustomerService::toggleActive($_SESSION['customer_id']);

// Log out
session_destroy();

// Start a new session for flash message
session_name(defined('SESSION_NAME') ? SESSION_NAME : 'mf_session');
session_start();
flash('info', 'Your account has been deactivated. Contact support to reactivate.');

redirect('../index.php');
