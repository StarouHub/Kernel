<?php
// debug.php - Put this in C:\xampp\htdocs\projetweb\Kernel\debug.php
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);

echo "<h1>Debug Information</h1>";
echo "<pre>";

// Test database connection
echo "Testing Database Connection...\n";
try {
    $pdo = new PDO("mysql:host=localhost;dbname=kernel", "root", "");
    echo "✓ Database connected successfully!\n";
    
    // Check tables
    $tables = ['users', 'tenders', 'investments', 'transactions'];
    foreach ($tables as $table) {
        $stmt = $pdo->query("SELECT COUNT(*) as count FROM $table");
        $result = $stmt->fetch();
        echo "✓ Table '$table': " . $result['count'] . " rows\n";
    }
} catch (PDOException $e) {
    echo "✗ Database error: " . $e->getMessage() . "\n";
}

// Check file structure
echo "\nChecking File Structure...\n";
$files = [
    'config.php' => 'C:\xampp\htdocs\projetweb\Kernel\config.php',
    'controller/controller.php' => 'C:\xampp\htdocs\projetweb\Kernel\controller\controller.php',
    'model/modela.php' => 'C:\xampp\htdocs\projetweb\Kernel\model\modela.php',
    'view/Frontoffice/investissement.php' => 'C:\xampp\htdocs\projetweb\Kernel\view\Frontoffice\investissement.php'
];

foreach ($files as $name => $path) {
    if (file_exists($path)) {
        echo "✓ $name exists\n";
    } else {
        echo "✗ $name NOT FOUND at $path\n";
    }
}

echo "\nPHP Version: " . phpversion() . "\n";
echo "PHP Error Reporting: " . ini_get('error_reporting') . "\n";
echo "Display Errors: " . ini_get('display_errors') . "\n";

echo "</pre>";
?>