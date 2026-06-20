<?php
require_once 'includes/config.php';
require_once 'includes/db.php';
require_once 'includes/functions.php';

session_start();
$_SESSION['customer_id'] = 1;
$_SESSION['customer_email'] = 'customer@test.et';

ob_start();
try {
    include 'customer/dashboard.php';
    echo "SUCCESS\n";
} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
} catch (Error $e) {
    echo "FATAL: " . $e->getMessage() . "\n";
}
$out = ob_get_clean();
echo substr($out, 0, 500);
