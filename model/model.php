<?php
// C:\xampp\htdocs\projetweb\Kernel\model\model.php

require_once __DIR__ . '/../config.php';

class InvestmentModel {
    private $db;
    
    public function __construct() {
        $this->db = DatabaseConfig::getConnection();
    }
    
    // Get user portfolio stats
    public function getPortfolio($userId = 1) {
        try {
            // Total invested
            $stmt = $this->db->prepare("SELECT COALESCE(SUM(amount), 0) as total FROM investments WHERE user_id = ?");
            $stmt->execute([$userId]);
            $totalInvested = $stmt->fetch()['total'] ?: 125500;
            
            // Active projects
            $stmt = $this->db->prepare("SELECT COUNT(*) as count FROM investments WHERE user_id = ? AND status = 'active'");
            $stmt->execute([$userId]);
            $activeProjects = $stmt->fetch()['count'] ?: 3;
            
            // Calculate gains (15% of total invested)
            $totalGains = $totalInvested * 0.15;
            
            return [
                'totalInvested' => $totalInvested,
                'totalGains' => $totalGains,
                'activeProjects' => $activeProjects,
                'investorScore' => 4.8,
                'monthlyChange' => 12.5,
                'gainsChange' => 8.3,
                'projectsChange' => 3
            ];
            
        } catch (Exception $e) {
            // Return default values if error
            return [
                'totalInvested' => 125500,
                'totalGains' => 18825,
                'activeProjects' => 3,
                'investorScore' => 4.8,
                'monthlyChange' => 12.5,
                'gainsChange' => 8.3,
                'projectsChange' => 3
            ];
        }
    }
    
    // Get all investments for user
    public function getInvestments($userId = 1) {
        try {
            $stmt = $this->db->prepare("SELECT * FROM investments WHERE user_id = ? ORDER BY investment_date DESC");
            $stmt->execute([$userId]);
            $investments = $stmt->fetchAll();
            
            // Add thumbnail URLs and format data
            foreach ($investments as &$investment) {
                $investment['thumbnail'] = $this->generateThumbnail($investment['sector']);
                $investment['projectName'] = $investment['project_name'];
                $investment['date'] = $investment['investment_date'];
                $investment['statusText'] = $this->getStatusText($investment['status']);
            }
            
            return $investments;
        } catch (Exception $e) {
            return [];
        }
    }
    
    // Get all active tenders
    public function getTenders($filters = []) {
        try {
            $sql = "SELECT * FROM tenders WHERE status = 'open'";
            $params = [];
            
            // Filter by sector if specified
            if (isset($filters['sector']) && $filters['sector'] !== 'all') {
                $sql .= " AND sector = ?";
                $params[] = $filters['sector'];
            }
            
            // Sort by deadline if requested
            if (isset($filters['sort']) && $filters['sort'] === 'deadline') {
                $sql .= " ORDER BY deadline ASC";
            } else {
                $sql .= " ORDER BY created_at DESC";
            }
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute($params);
            $tenders = $stmt->fetchAll();
            
            // Format data
            foreach ($tenders as &$tender) {
                $tender['id'] = (int)$tender['id'];
                $tender['projectName'] = $tender['project_name'];
                $tender['shortPitch'] = $tender['short_pitch'];
                $tender['fundingTarget'] = (float)$tender['funding_target'];
                $tender['raised'] = (float)$tender['raised'];
                $tender['minInvestment'] = (float)$tender['min_investment'];
                $tender['maxInvestment'] = $tender['max_investment'] ? (float)$tender['max_investment'] : null;
                $tender['expectedROI'] = (float)$tender['expected_roi'];
                $tender['offerType'] = $tender['offer_type'];
                $tender['daysLeft'] = $this->calculateDaysLeft($tender['deadline']);
                $tender['progress'] = $this->calculateProgress($tender);
            }
            
            return $tenders;
        } catch (Exception $e) {
            return [];
        }
    }
    
    // Get transaction history - FIXED
    public function getTransactions($userId = 1, $limit = 50) {
        try {
            // Use intval to ensure proper integer casting for LIMIT
            $limit = intval($limit);
            $userId = intval($userId);
            
            $stmt = $this->db->prepare("SELECT * FROM transactions WHERE user_id = ? ORDER BY transaction_date DESC LIMIT $limit");
            $stmt->execute([$userId]);
            $transactions = $stmt->fetchAll();
            
            // Format transaction data
            foreach ($transactions as &$transaction) {
                $transaction['formatted_date'] = $this->formatDate($transaction['transaction_date']);
                $transaction['formatted_amount'] = $this->formatCurrency(abs($transaction['amount']));
                $transaction['amount_color'] = $transaction['amount'] < 0 ? '#EF4444' : '#10B981';
                $transaction['amount_sign'] = $transaction['amount'] < 0 ? '-' : '+';
                $transaction['type_text'] = $this->getTransactionTypeText($transaction['type']);
                $transaction['status_text'] = $this->getStatusText($transaction['status']);
            }
            
            return $transactions;
        } catch (Exception $e) {
            error_log("Transaction error: " . $e->getMessage());
            return [];
        }
    }
    
    // Get tender by ID
    public function getTenderById($tenderId) {
        try {
            $stmt = $this->db->prepare("SELECT * FROM tenders WHERE id = ?");
            $stmt->execute([$tenderId]);
            $tender = $stmt->fetch();
            
            if ($tender) {
                $tender['projectName'] = $tender['project_name'];
                $tender['shortPitch'] = $tender['short_pitch'];
                $tender['fundingTarget'] = (float)$tender['funding_target'];
                $tender['minInvestment'] = (float)$tender['min_investment'];
                $tender['maxInvestment'] = $tender['max_investment'] ? (float)$tender['max_investment'] : null;
                $tender['expectedROI'] = (float)$tender['expected_roi'];
                $tender['offerType'] = $tender['offer_type'];
                $tender['raised'] = (float)$tender['raised'];
            }
            
            return $tender;
        } catch (Exception $e) {
            return null;
        }
    }
    
    // Create new tender
    public function createTender($userId, $tenderData) {
        try {
            $sql = "INSERT INTO tenders (
                user_id, project_name, short_pitch, sector, 
                funding_target, min_investment, max_investment, 
                offer_type, expected_roi, deadline
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute([
                $userId,
                $tenderData['projectName'],
                $tenderData['shortPitch'],
                $tenderData['sector'],
                $tenderData['fundingTarget'],
                $tenderData['minInvestment'],
                $tenderData['maxInvestment'] ?? null,
                $tenderData['offerType'],
                $tenderData['expectedROI'],
                $tenderData['deadline']
            ]);
            
            $tenderId = $this->db->lastInsertId();
            
            // Also create a transaction for creating the tender
            $this->createTransaction($userId, [
                'type' => 'investment',
                'project' => $tenderData['projectName'],
                'amount' => 0,
                'status' => 'confirmed',
                'notes' => 'Appel d\'offres créé'
            ]);
            
            return $tenderId;
        } catch (Exception $e) {
            return false;
        }
    }
    
    // Create new investment - UPDATED WITH BETTER TRANSACTION HANDLING
    public function createInvestment($userId, $investmentData) {
        try {
            // Start transaction
            $this->db->beginTransaction();
            
            // 1. Insert investment record
            $sql = "INSERT INTO investments (
                user_id, tender_id, project_name, amount, 
                roi, sector, status, investment_date
            ) VALUES (?, ?, ?, ?, ?, ?, 'active', CURDATE())";
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute([
                $userId,
                $investmentData['tenderId'],
                $investmentData['projectName'],
                $investmentData['amount'],
                $investmentData['roi'],
                $investmentData['sector']
            ]);
            
            $investmentId = $this->db->lastInsertId();
            
            // 2. Update tender raised amount
            $sql = "UPDATE tenders SET raised = raised + ? WHERE id = ?";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$investmentData['amount'], $investmentData['tenderId']]);
            
            // 3. Create transaction record - IMPORTANT: Negative amount for investment
            $transactionData = [
                'type' => 'investment',
                'project' => $investmentData['projectName'],
                'amount' => -$investmentData['amount'], // Negative for investment
                'status' => 'confirmed',
                'notes' => 'Investissement dans ' . $investmentData['projectName']
            ];
            
            $this->createTransaction($userId, $transactionData);
            
            // 4. Check if tender is now fully funded
            $sql = "UPDATE tenders SET status = 'closed' WHERE id = ? AND raised >= funding_target";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$investmentData['tenderId']]);
            
            // Commit transaction
            $this->db->commit();
            return $investmentId;
            
        } catch (Exception $e) {
            $this->db->rollBack();
            error_log("Create investment error: " . $e->getMessage());
            return false;
        }
    }
    
    // Create transaction helper method
    private function createTransaction($userId, $transactionData) {
        try {
            $sql = "INSERT INTO transactions (
                user_id, type, project, amount, status, transaction_date, created_at
            ) VALUES (?, ?, ?, ?, ?, CURDATE(), NOW())";
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute([
                $userId,
                $transactionData['type'],
                $transactionData['project'],
                $transactionData['amount'],
                $transactionData['status']
            ]);
            
            return $this->db->lastInsertId();
        } catch (Exception $e) {
            error_log("Create transaction error: " . $e->getMessage());
            return false;
        }
    }
    
    // Validate investment amount
    public function validateInvestment($amount, $tender) {
        $errors = [];
        
        if ($amount < $tender['min_investment']) {
            $errors[] = "Minimum investment is " . $this->formatCurrency($tender['min_investment']);
        }
        
        if ($tender['max_investment'] && $amount > $tender['max_investment']) {
            $errors[] = "Maximum investment is " . $this->formatCurrency($tender['max_investment']);
        }
        
        $remaining = $tender['funding_target'] - $tender['raised'];
        if ($amount > $remaining) {
            $errors[] = "Only " . $this->formatCurrency($remaining) . " remaining";
        }
        
        return $errors;
    }
    
    // Validate tender data
    public function validateTender($tenderData) {
        $errors = [];
        
        if (empty($tenderData['projectName']) || strlen(trim($tenderData['projectName'])) < 3) {
            $errors['projectName'] = "Project name must be at least 3 characters";
        }
        
        if (empty($tenderData['shortPitch']) || strlen(trim($tenderData['shortPitch'])) < 10) {
            $errors['shortPitch'] = "Short pitch must be at least 10 characters";
        }
        
        if (empty($tenderData['fundingTarget']) || $tenderData['fundingTarget'] < 1000) {
            $errors['fundingTarget'] = "Funding target must be at least 1,000 TND";
        }
        
        if (empty($tenderData['expectedROI']) || $tenderData['expectedROI'] < 1 || $tenderData['expectedROI'] > 100) {
            $errors['expectedROI'] = "ROI must be between 1% and 100%";
        }
        
        if (empty($tenderData['minInvestment']) || $tenderData['minInvestment'] < 100) {
            $errors['minInvestment'] = "Minimum investment must be at least 100 TND";
        }
        
        if (!empty($tenderData['maxInvestment']) && $tenderData['maxInvestment'] < $tenderData['minInvestment']) {
            $errors['maxInvestment'] = "Maximum cannot be less than minimum";
        }
        
        if (empty($tenderData['deadline'])) {
            $errors['deadline'] = "Deadline is required";
        } else {
            $deadline = new DateTime($tenderData['deadline']);
            $today = new DateTime();
            if ($deadline <= $today) {
                $errors['deadline'] = "Deadline must be in the future";
            }
        }
        
        return $errors;
    }
    
    // Helper: Generate thumbnail
    private function generateThumbnail($sector) {
        $sectorCode = substr($sector, 0, 3);
        return "data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='100' height='80' viewBox='0 0 100 80'%3E%3Crect fill='%232563EB' width='100' height='80'/%3E%3Ctext x='50%25' y='50%25' font-size='12' fill='white' text-anchor='middle' dy='.3em'%3E$sectorCode%3C/text%3E%3C/svg%3E";
    }
    
    // Helper: Format currency
    public function formatCurrency($amount) {
        return number_format($amount, 0, ',', ' ') . ' TND';
    }
    
    // Helper: Format date
    public function formatDate($dateString) {
        if (!$dateString) return 'Date inconnue';
        $date = new DateTime($dateString);
        return $date->format('d/m/Y');
    }
    
    // Helper: Calculate progress percentage
    public function calculateProgress($tender) {
        if (!$tender || !isset($tender['funding_target']) || $tender['funding_target'] == 0) return 0;
        $raised = $tender['raised'] ?? 0;
        return min(100, round(($raised / $tender['funding_target']) * 100));
    }
    
    // Helper: Calculate days left
    public function calculateDaysLeft($deadline) {
        if (!$deadline) return 0;
        $deadlineDate = new DateTime($deadline);
        $today = new DateTime();
        $interval = $today->diff($deadlineDate);
        return $interval->invert ? 0 : $interval->days;
    }
    
    // Helper: Get status text
    public function getStatusText($status) {
        $statusMap = [
            'active' => 'En cours',
            'completed' => 'Financé',
            'pending' => 'En attente',
            'confirmed' => 'Confirmé',
            'received' => 'Reçu',
            'open' => 'Ouvert',
            'closed' => 'Fermé'
        ];
        return $statusMap[$status] ?? $status;
    }
    
    // Helper: Get transaction type text
    public function getTransactionTypeText($type) {
        $typeMap = [
            'investment' => 'Investissement',
            'return' => 'Retour',
            'withdrawal' => 'Retrait'
        ];
        return $typeMap[$type] ?? $type;
    }

    // Clear transaction history for a user
    public function clearTransactions($userId = 1) {
        try {
            $stmt = $this->db->prepare("DELETE FROM transactions WHERE user_id = ?");
            $stmt->execute([$userId]);
            return ['success' => true, 'message' => 'Historique effacé'];
        } catch (Exception $e) {
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }
}
?>