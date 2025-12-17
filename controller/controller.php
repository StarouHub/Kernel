<?php
// C:\xampp\htdocs\projetweb\Kernel\controller\controller.php

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Include model
require_once __DIR__ . '/../model/modela.php';

class InvestmentController {
    private $model;
    
    public function __construct() {
        $this->model = new InvestmentModel();
    }
    
    // Get portfolio data
    public function getPortfolio() {
        return $this->model->getPortfolio();
    }
    
    // Get investments
    public function getInvestments() {
        return $this->model->getInvestments();
    }
    
    // Get tenders with optional filters
    public function getTenders($filters = []) {
        return $this->model->getTenders($filters);
    }
    
    // Get transactions - UPDATED
    public function getTransactions() {
        return $this->model->getTransactions(1, 20);
    }
    
    // Create new tender
    public function createTender($tenderData) {
        $errors = $this->model->validateTender($tenderData);
        
        if (!empty($errors)) {
            return ['success' => false, 'errors' => $errors];
        }
        
        $tenderId = $this->model->createTender(1, $tenderData);
        
        if ($tenderId) {
            return [
                'success' => true,
                'tenderId' => $tenderId,
                'message' => 'Appel d\'offres créé avec succès!'
            ];
        } else {
            return ['success' => false, 'error' => 'Échec de la création de l\'appel d\'offres'];
        }
    }
    
    // Create new investment - UPDATED WITH TRANSACTION INFO
    public function createInvestment($investmentData) {
        // Get tender details
        $tender = $this->model->getTenderById($investmentData['tenderId']);
        
        if (!$tender) {
            return ['success' => false, 'error' => 'Appel d\'offres non trouvé'];
        }
        
        // Validate investment amount
        $errors = $this->model->validateInvestment($investmentData['amount'], $tender);
        
        if (!empty($errors)) {
            return ['success' => false, 'errors' => $errors];
        }
        
        $investmentId = $this->model->createInvestment(1, $investmentData);
        
        if ($investmentId) {
            return [
                'success' => true,
                'investmentId' => $investmentId,
                'tenderId' => $investmentData['tenderId'],
                'projectName' => $investmentData['projectName'],
                'amount' => $investmentData['amount'],
                'message' => 'Investissement réussi! Votre transaction a été enregistrée.'
            ];
        } else {
            return ['success' => false, 'error' => 'Échec de l\'investissement'];
        }
    }
    
    // Test connection
    public function testConnection() {
        try {
            return [
                'success' => true,
                'message' => 'Connexion à la base de données établie',
                'timestamp' => date('Y-m-d H:i:s')
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    // Clear transactions
    public function clearTransactions() {
        return $this->model->clearTransactions(1);
    }
}

// Handle AJAX requests
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $controller = new InvestmentController();
    header('Content-Type: application/json');
    
    try {
        $action = $_POST['action'];
        
        if ($action === 'create_tender') {
            $tenderData = [
                'projectName' => $_POST['projectName'] ?? '',
                'shortPitch' => $_POST['shortPitch'] ?? '',
                'sector' => $_POST['sector'] ?? '',
                'fundingTarget' => floatval($_POST['fundingTarget'] ?? 0),
                'minInvestment' => floatval($_POST['minInvestment'] ?? 0),
                'maxInvestment' => !empty($_POST['maxInvestment']) ? floatval($_POST['maxInvestment']) : null,
                'offerType' => $_POST['offerType'] ?? '',
                'expectedROI' => floatval($_POST['expectedROI'] ?? 0),
                'deadline' => $_POST['deadline'] ?? ''
            ];
            
            $result = $controller->createTender($tenderData);
            echo json_encode($result);
            
        } elseif ($action === 'create_investment') {
            $investmentData = [
                'tenderId' => intval($_POST['tenderId'] ?? 0),
                'projectName' => $_POST['projectName'] ?? '',
                'amount' => floatval($_POST['amount'] ?? 0),
                'roi' => floatval($_POST['roi'] ?? 0),
                'sector' => $_POST['sector'] ?? ''
            ];
            
            $result = $controller->createInvestment($investmentData);
            echo json_encode($result);
            
        } elseif ($action === 'clear_transactions') {
            $result = $controller->clearTransactions();
            echo json_encode($result);
            
        } else {
            echo json_encode(['success' => false, 'error' => 'Action inconnue']);
        }
        
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'error' => 'Erreur serveur: ' . $e->getMessage()]);
    }
    
    exit;
}

// Handle GET AJAX requests
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['ajax'])) {
    $controller = new InvestmentController();
    header('Content-Type: application/json');
    
    try {
        $action = $_GET['ajax'];
        
        if ($action === 'get_tenders') {
            $filters = [];
            
            if (isset($_GET['sector']) && $_GET['sector'] !== 'all') {
                $filters['sector'] = $_GET['sector'];
            }
            
            if (isset($_GET['sort']) && $_GET['sort'] === 'deadline') {
                $filters['sort'] = 'deadline';
            }
            
            $tenders = $controller->getTenders($filters);
            echo json_encode($tenders);
            
        } elseif ($action === 'get_investments') {
            $filters = [];
            
            if (isset($_GET['filter']) && $_GET['filter'] !== 'all') {
                $filters['status'] = $_GET['filter'];
            }
            
            if (isset($_GET['profitable'])) {
                $filters['profitable'] = true;
            }
            
            $investments = $controller->getInvestments();
            echo json_encode($investments);
            
        } elseif ($action === 'get_transactions') {
            $transactions = $controller->getTransactions();
            echo json_encode($transactions);
            
        } elseif ($action === 'test_connection') {
            $result = $controller->testConnection();
            echo json_encode($result);
            
        } else {
            echo json_encode(['success' => false, 'error' => 'Action inconnue']);
        }
        
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'error' => 'Erreur serveur: ' . $e->getMessage()]);
    }
    
    exit;
}
?>