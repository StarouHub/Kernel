<?php
// Database connection for your project
define('DB_HOST', 'localhost');
define('DB_NAME', 'kernel');
define('DB_USER', 'root');
define('DB_PASS', ''); // No password

try {
    $pdo = new PDO(
        'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8mb4',
        DB_USER,
        DB_PASS,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_OBJ // Fetch as objects for model compatibility
        ]
    );
} catch (PDOException $e) {
    die("Connexion à la base de données échouée : " . $e->getMessage());
}
?>
