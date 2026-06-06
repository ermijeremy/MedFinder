<?php
require_once '../includes/config.php';
require_once '../includes/db.php';
require_once '../includes/functions.php';
require_once '../includes/customer-auth.php';
require_once '../includes/services/CustomerService.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['status' => 'error', 'message' => 'Method not allowed']);
    exit;
}

// Support both JSON and Form Data
$data = json_decode(file_get_contents('php://input'), true);
if (!$data) {
    $data = $_POST;
}

$pharmacy_id = (int)($data['pharmacy_id'] ?? 0);

if (!$pharmacy_id) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'Invalid pharmacy ID']);
    exit;
}

if (CustomerService::addFavorite($_SESSION['customer_id'], $pharmacy_id)) {
    echo json_encode(['status' => 'ok', 'message' => 'Added to favorites']);
} else {
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => 'Failed to add favorite']);
}
