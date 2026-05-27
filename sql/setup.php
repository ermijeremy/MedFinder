<?php
// Load config
define('ROOT', dirname(__DIR__));
require_once ROOT . '/includes/config.php';

// Credentials for seed accounts
$adminPassword    = 'admin123';
$pharmacyPassword = 'pharmacy123';

// Connect
try {
    $dsn = 'mysql:host=' . DB_HOST . ';charset=utf8mb4';
    $pdo = new PDO($dsn, DB_USER, DB_PASS, [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);
} catch (PDOException $e) {
    die("[ERROR] Cannot connect to MySQL: " . $e->getMessage() . "\n");
}

// Read and split the SQL file
$sqlFile = __DIR__ . '/database.sql';
if (!file_exists($sqlFile)) {
    die("[ERROR] Cannot find sql/database.sql\n");
}

$sql = file_get_contents($sqlFile);

// Split on semicolons, ignore empty statements
$statements = array_filter(
    array_map('trim', explode(';', $sql)),
    fn($s) => $s !== ''
);

// bcrypt hashes
$adminHash    = password_hash($adminPassword,    PASSWORD_BCRYPT, ['cost' => 12]);
$pharmacyHash = password_hash($pharmacyPassword, PASSWORD_BCRYPT, ['cost' => 12]);

echo "Generated hashes:\n";
echo "  Admin hash    : $adminHash\n";
echo "  Pharmacy hash : $pharmacyHash\n\n";

// Execute statements
$success = 0;
$errors  = 0;

foreach ($statements as $stmt) {
    // Replace placeholder admin hash
    $stmt = str_replace(
        "'PLACEHOLDER_ADMIN_HASH'",
        $pdo->quote($adminHash),
        $stmt
    );

    // Replace placeholder pharmacy hashes
    $stmt = str_replace(
        ["'PLACEHOLDER_UNITY_HASH'",
         "'PLACEHOLDER_ETHIOCARE_HASH'",
         "'PLACEHOLDER_BLUECROSS_HASH'",
         "'PLACEHOLDER_GREENMED_HASH'"],
        $pdo->quote($pharmacyHash),
        $stmt
    );

    try {
        $pdo->exec($stmt);
        $success++;
    } catch (PDOException $e) {
        // Skip "already exists" notices silently
        if (strpos($e->getMessage(), 'already exists') !== false ||
            strpos($e->getMessage(), 'Duplicate entry') !== false) {
            echo "[SKIP] Duplicate/existing: " . substr($stmt, 0, 60) . "...\n";
        } else {
            echo "[ERROR] " . $e->getMessage() . "\n";
            echo "        Statement: " . substr($stmt, 0, 80) . "\n";
            $errors++;
        }
    }
}

echo "\n";
echo "===================================\n";
echo " Setup complete!\n";
echo "  Statements OK : $success\n";
echo "  Errors        : $errors\n";
echo "===================================\n\n";

echo "Seed credentials:\n";
echo "  Admin login   : username=admin       password=$adminPassword\n";
echo "  Pharmacy login: email=unity@pharmacy.et password=$pharmacyPassword\n";
echo "\n";
echo "IMPORTANT: Delete sql/setup.php after running it in production.\n";
