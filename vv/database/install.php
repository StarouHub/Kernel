<?php
/**
 * Database Installation Script
 * Run this script once to set up the database schema
 */

// Connect to MySQL without selecting a database first
try {
    $dsn = "mysql:host=" . DB_HOST . ";port=" . DB_PORT . ";charset=" . DB_CHARSET;
    $options = [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
    ];
    $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
} catch (PDOException $e) {
    die("Error: Cannot connect to MySQL server. Please check your configuration in config/database.php\n" . $e->getMessage() . "\n");
}

echo "=== Database Installation Script ===\n\n";

try {
    // Read schema file
    $schemaFile = __DIR__ . '/schema.sql';
    if (!file_exists($schemaFile)) {
        die("Error: schema.sql file not found!\n");
    }
    
    $schema = file_get_contents($schemaFile);
    
    // Remove comments and clean up
    $schema = preg_replace('/--.*$/m', '', $schema);
    
    // Handle DELIMITER commands for stored procedures and triggers
    $schema = preg_replace('/DELIMITER\s+[^\s]+/i', '', $schema);
    
    // Split by semicolons, but be careful with stored procedures
    $statements = [];
    $currentStatement = '';
    $inProcedure = false;
    
    $lines = explode("\n", $schema);
    foreach ($lines as $line) {
        $line = trim($line);
        if (empty($line)) continue;
        
        $currentStatement .= $line . "\n";
        
        // Check if we're entering or leaving a procedure/trigger
        if (preg_match('/CREATE\s+(PROCEDURE|TRIGGER)/i', $line)) {
            $inProcedure = true;
        }
        
        // Check for end of statement
        if (preg_match('/END\s*$/i', $line) && $inProcedure) {
            $inProcedure = false;
            if (!empty(trim($currentStatement))) {
                $statements[] = trim($currentStatement);
            }
            $currentStatement = '';
        } elseif (!$inProcedure && substr(rtrim($line), -1) === ';') {
            if (!empty(trim($currentStatement))) {
                $statements[] = trim($currentStatement);
            }
            $currentStatement = '';
        }
    }
    
    // Add any remaining statement
    if (!empty(trim($currentStatement))) {
        $statements[] = trim($currentStatement);
    }
    
    // Filter out empty statements and comments
    $statements = array_filter($statements, function($stmt) {
        $stmt = trim($stmt);
        return !empty($stmt) && 
               strlen($stmt) > 10 &&
               !preg_match('/^\/\*/', $stmt) &&
               !preg_match('/^\*/', $stmt);
    });
    
    echo "Found " . count($statements) . " SQL statements to execute...\n\n";
    
    $pdo->beginTransaction();
    
    foreach ($statements as $index => $statement) {
        $statement = trim($statement);
        if (empty($statement)) {
            continue;
        }
        
        // Remove trailing semicolon if present
        $statement = rtrim($statement, ';');
        
        try {
            $pdo->exec($statement);
            // Extract table/view name for better feedback
            if (preg_match('/CREATE\s+(TABLE|VIEW|PROCEDURE|TRIGGER)\s+(?:IF\s+NOT\s+EXISTS\s+)?`?(\w+)`?/i', $statement, $matches)) {
                $type = strtolower($matches[1]);
                $name = $matches[2];
                echo "✓ Created $type: $name\n";
            } else if (preg_match('/INSERT\s+INTO\s+`?(\w+)`?/i', $statement, $matches)) {
                echo "✓ Inserted data into: {$matches[1]}\n";
            } else {
                echo "✓ Executed statement " . ($index + 1) . "\n";
            }
        } catch (PDOException $e) {
            // Ignore "already exists" errors
            $errorMsg = $e->getMessage();
            if (strpos($errorMsg, 'already exists') !== false || 
                strpos($errorMsg, 'Duplicate') !== false ||
                strpos($errorMsg, 'Table') !== false && strpos($errorMsg, "doesn't exist") === false) {
                echo "ℹ Skipped (already exists): " . substr($statement, 0, 50) . "...\n";
            } else {
                echo "⚠ Warning: " . $errorMsg . "\n";
                echo "   Statement: " . substr($statement, 0, 100) . "...\n";
            }
        }
    }
    
    $pdo->commit();
    
    echo "\n✓ Database installation completed successfully!\n";
    echo "\nYou can now use the forum application.\n";
    echo "\nDatabase: " . DB_NAME . "\n";
    echo "Tables created: categories, sujets, reponses, users\n";
    
} catch (Exception $e) {
    if (isset($pdo)) {
        $pdo->rollBack();
    }
    die("\n✗ Error: " . $e->getMessage() . "\n");
}
?>

