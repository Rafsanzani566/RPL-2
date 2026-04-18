<?php
/**
 * config/database.php
 * Centralized PDO database connection with error handling.
 */

define('DB_HOST', 'localhost');
define('DB_NAME', 'webdb_rpl2');
define('DB_USER', 'root');       // Change to your MySQL user
define('DB_PASS', '');           // Change to your MySQL password
define('DB_CHAR', 'utf8mb4');

function getPDO(): PDO
{
    static $pdo = null;

    if ($pdo === null) {
        $dsn = sprintf(
            'mysql:host=%s;dbname=%s;charset=%s',
            DB_HOST,
            DB_NAME,
            DB_CHAR
        );

        $options = [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false, // WAJIB FALSE biar password hash tidak rusak saat ditarik
    ];

        try {
            $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
        } catch (PDOException $e) {
            // In production, log the error instead of displaying it
            error_log('[DB ERROR] ' . $e->getMessage());
            http_response_code(500);
            die(json_encode([
                'error' => 'Database connection failed. Please contact administrator.'
            ]));
        }
    }

    return $pdo;
}

// Expose $pdo globally for legacy-style scripts
$pdo = getPDO();
