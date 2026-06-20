<?php
require_once 'includes/config.php';
require_once 'includes/db.php';
require_once 'includes/functions.php';
require_once 'includes/services/SearchService.php';
require_once 'includes/services/NeighborhoodService.php';
require_once 'includes/services/CustomerService.php';
require_once 'includes/services/ReviewService.php';

session_start();
$_SESSION['customer_id'] = 1;
$_SESSION['customer_email'] = 'customer@test.et';

ob_start();
try {
    $_SERVER['REQUEST_METHOD'] = 'GET';
    $_GET['q'] = 'test';
    
    // Bypass auth redirect
    $is_customer = true;
    
    $results = SearchService::search('test', 0, '', 'nearest', 1);
    echo "SUCCESS: Found " . count($results['rows']) . " results\n";
} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
} catch (Error $e) {
    echo "FATAL: " . $e->getMessage() . "\n";
}
$out = ob_get_clean();
echo substr($out, 0, 500);
