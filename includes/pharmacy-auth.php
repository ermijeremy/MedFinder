<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/functions.php';

start_session();

if (empty($_SESSION['pharmacy_id'])) {
    header('Location: ' . base_url() . 'pharmacy/login.php');
    exit;
}

$current_pharmacy = db_query(
    'SELECT pharmacy_id, pharmacy_name, owner_name, email, phone,
            address, neighborhood_id, logo, operating_hours, status
       FROM pharmacies
      WHERE pharmacy_id = :id
      LIMIT 1',
    [':id' => $_SESSION['pharmacy_id']]
)->fetch();

if (!$current_pharmacy) {
    session_destroy();
    header('Location: ' . base_url() . 'pharmacy/login.php');
    exit;
}

// Status checks
if ($current_pharmacy['status'] === 'pending') {
    session_destroy();
    header('Location: ' . base_url() . 'pharmacy/login.php?notice=pending');
    exit;
}

if ($current_pharmacy['status'] === 'suspended') {
    session_destroy();
    header('Location: ' . base_url() . 'pharmacy/login.php?notice=suspended');
    exit;
}
