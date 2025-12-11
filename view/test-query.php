<?php
define('ROOT_PATH', dirname(__FILE__));
require_once ROOT_PATH . '/config/database.php';

$db = Database::getInstance();

echo "<h2>TEST REQUETE STATS</h2>";

// Test la requête directement
$sql = "SELECT 
        COUNT(*) as total,
        SUM(CASE WHEN statut = 'en-attente' THEN 1 ELSE 0 END) as en_attente,
        SUM(CASE WHEN statut = 'en-cours' THEN 1 ELSE 0 END) as en_cours,
        SUM(CASE WHEN statut = 'resolue' THEN 1 ELSE 0 END) as resolues,
        SUM(CASE WHEN statut = 'fermee' THEN 1 ELSE 0 END) as fermees,
        SUM(CASE WHEN priorite = 'critique' OR priorite = 'urgente' THEN 1 ELSE 0 END) as urgentes
        FROM reclamations";

try {
    $stmt = $db->prepare($sql);
    $stmt->execute();
    $stats = $stmt->fetch();
    
    echo "<h3>Resultat:</h3>";
    echo "<pre>";
    print_r($stats);
    echo "</pre>";
    
    echo "<p>Total: " . $stats['total'] . "</p>";
    echo "<p>En attente: " . $stats['en_attente'] . "</p>";
    echo "<p>En cours: " . $stats['en_cours'] . "</p>";
    echo "<p>Resolues: " . $stats['resolues'] . "</p>";
    echo "<p>Fermees: " . $stats['fermees'] . "</p>";
    echo "<p>Urgentes: " . $stats['urgentes'] . "</p>";
    
} catch (Exception $e) {
    echo "<p style='color: red;'>ERREUR SQL: " . $e->getMessage() . "</p>";
}

?>
