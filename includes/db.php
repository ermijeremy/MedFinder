<?php
require_once __DIR__ . '/config.php';

function get_db(): PDO
{
    static $pdo = null; 
    if ($pdo === null) {
        $dsn = sprintf(
            'mysql:host=%s;dbname=%s;charset=%s',
            DB_HOST,
            DB_NAME,
            DB_CHARSET
        );

        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
            PDO::ATTR_PERSISTENT         => false,
        ];

        $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
    }

    return $pdo;
}

function db_query(string $sql, array $params = []): PDOStatement
{
    $stmt = get_db()->prepare($sql);
    $stmt->execute($params);
    return $stmt;
}

function db_last_id(): int
{
    return (int) get_db()->lastInsertId();
}
