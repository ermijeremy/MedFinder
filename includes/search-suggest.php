<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/functions.php';
require_once __DIR__ . '/services/MedicineService.php';

start_session();
debug_request('includes/search-suggest.php');

header('Content-Type: application/json; charset=utf-8');

// Only accept POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
    exit;
}

$query   = sanitize($_POST['query'] ?? '');
$results = MedicineService::suggest($query);

echo json_encode($results);
