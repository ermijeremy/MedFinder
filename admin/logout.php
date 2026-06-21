<?php

require_once '../includes/config.php';
require_once '../includes/functions.php';

start_session();
debug_request('admin/logout.php');

// Wipe all session data
$_SESSION = [];

// Expire the session cookie immediately
if (ini_get('session.use_cookies')) {
    $params = session_get_cookie_params();
    setcookie(
        session_name(), '', time() - 42000,
        $params['path'], $params['domain'],
        $params['secure'], $params['httponly']
    );
}

session_destroy();

header('Location: ' . base_url() . 'admin/login.php');
exit;
