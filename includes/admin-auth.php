<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/functions.php';

start_session();

if (empty($_SESSION['admin_id'])) {
    // Not logged in ? redirect to login page
    header('Location: ' . base_url() . 'admin/login.php');
    exit;
}

// Load the admin record 
$current_admin = db_query(
    'SELECT admin_id, username, email FROM admins WHERE admin_id = :id LIMIT 1',
    [':id' => $_SESSION['admin_id']]
)->fetch();

if (!$current_admin) {
    session_destroy();
    header('Location: ' . base_url() . 'admin/login.php');
    exit;
}
