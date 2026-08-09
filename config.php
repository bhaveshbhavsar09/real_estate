<?php
/**
 * config.php
 * Central database connection for the EstateFolio website.
 * Edit the four constants below to match your hosting environment.
 */

// ---- EDIT THESE FOR YOUR SERVER -----------------------------------
define('DB_HOST', 'localhost');
define('DB_NAME', 'estatefolio');
define('DB_USER', 'root');
define('DB_PASS', '');
// ---------------------------------------------------------------------

// Allow the front-end (index.html) to call these PHP endpoints via fetch()
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

try {
    $pdo = new PDO(
        "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4",
        DB_USER,
        DB_PASS,
        [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]
    );
} catch (PDOException $e) {
    http_response_code(500);
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'error' => 'Database connection failed. Check config.php credentials.']);
    exit;
}