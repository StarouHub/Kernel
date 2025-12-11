<?php
define('ROOT_PATH', dirname(__FILE__));
require_once ROOT_PATH . '/config/database.php';

$db = Database::getInstance();

echo "<h2>TEST STATISTIQUES</h2>";

// Test 1: Vérifier si la table existe
echo "<h3>1. Vérifier la table reclamations</h3>";
try {
    $stmt = $db->prepare("DESCRIBE reclamations");
    $stmt->execute();
    $columns = $stmt->fetchAll();
    echo "<pre>";
    print_r($columns);
    echo "</pre>";
} catch (Exception $e) {
    echo "<p style='color: red;'>ERREUR: " . $e->getMessage() . "</p>";
}

// Test 2: Compter les réclamations
echo "<h3>2. Total des réclamations</h3>";
try {
    $stmt = $db->prepare("SELECT COUNT(*) as total FROM reclamations");
    $stmt->execute();
    $result = $stmt->fetch();
    echo "<p>Total: <strong>" . $result['total'] . "</strong></p>";
} catch (Exception $e) {
    echo "<p style='color: red;'>ERREUR: " . $e->getMessage() . "</p>";
}

// Test 3: Voir les priorités
echo "<h3>3. Réclamations par priorité</h3>";
try {
    $stmt = $db->prepare("SELECT priorite, COUNT(*) as count FROM reclamations GROUP BY priorite");
    $stmt->execute();
    $results = $stmt->fetchAll();
    echo "<pre>";
    print_r($results);
    echo "</pre>";
} catch (Exception $e) {
    echo "<p style='color: red;'>ERREUR: " . $e->getMessage() . "</p>";
}

// Test 4: Voir les statuts
echo "<h3>4. Réclamations par statut</h3>";
try {
    $stmt = $db->prepare("SELECT statut, COUNT(*) as count FROM reclamations GROUP BY statut");
    $stmt->execute();
    $results = $stmt->fetchAll();
    echo "<pre>";
    print_r($results);
    echo "</pre>";
} catch (Exception $e) {
    echo "<p style='color: red;'>ERREUR: " . $e->getMessage() . "</p>";
}

// Test 5: Voir les 5 dernières réclamations
echo "<h3>5. Dernières réclamations</h3>";
try {
    $stmt = $db->prepare("SELECT id, titre, priorite, statut, date_creation FROM reclamations ORDER BY id DESC LIMIT 5");
    $stmt->execute();
    $results = $stmt->fetchAll();
    echo "<pre>";
    print_r($results);
    echo "</pre>";
} catch (Exception $e) {
    echo "<p style='color: red;'>ERREUR: " . $e->getMessage() . "</p>";
}

?>
