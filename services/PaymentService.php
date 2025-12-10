<?php
/**
 * Payment Service
 * Gère les paiements via Stripe, PayPal et autres passerelles
 */

require_once __DIR__ . '/../config/database.php';

class PaymentService {
    private $db;
    private $tablePayments = 'payments';
    
    // Configuration Stripe (à configurer dans un fichier de config séparé en production)
    private $stripeSecretKey = 'sk_test_...'; // À remplacer par votre clé secrète Stripe
    private $stripePublishableKey = 'pk_test_...'; // À remplacer par votre clé publique Stripe
    
    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
        $this->createPaymentsTableIfNotExists();
    }
    
    /**
     * Crée la table payments si elle n'existe pas
     */
    private function createPaymentsTableIfNotExists() {
        $sql = "CREATE TABLE IF NOT EXISTS {$this->tablePayments} (
            id_payment INT AUTO_INCREMENT PRIMARY KEY,
            inscription_id INT NOT NULL,
            id_evenement INT NOT NULL,
            amount DECIMAL(10, 2) NOT NULL,
            currency VARCHAR(3) DEFAULT 'EUR',
            payment_method VARCHAR(50) DEFAULT 'stripe',
            payment_intent_id VARCHAR(255) NULL,
            payment_status VARCHAR(50) DEFAULT 'pending',
            transaction_id VARCHAR(255) NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            INDEX idx_inscription (inscription_id),
            INDEX idx_event (id_evenement),
            INDEX idx_status (payment_status)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";
        
        try {
            $this->db->exec($sql);
        } catch (PDOException $e) {
            error_log("Erreur lors de la création de la table payments: " . $e->getMessage());
        }
    }
    
    /**
     * Crée une session de checkout Stripe
     * 
     * @param array $paymentData
     * @return array
     */
    public function createStripeCheckoutSession(array $paymentData): array {
        // En production, utilisez la vraie API Stripe
        // Pour la démo, on simule la création d'une session
        
        try {
            // Créer un enregistrement de paiement
            $paymentId = $this->createPaymentRecord($paymentData);
            
            if (!$paymentId) {
                return ['success' => false, 'error' => 'Erreur lors de la création du paiement'];
            }
            
            // En production, vous utiliseriez :
            // \Stripe\Stripe::setApiKey($this->stripeSecretKey);
            // $session = \Stripe\Checkout\Session::create([...]);
            
            // Pour la démo, on simule
            $sessionId = 'cs_test_' . uniqid();
            $paymentIntentId = 'pi_' . uniqid();
            
            // Mettre à jour l'enregistrement avec les IDs Stripe
            $this->updatePaymentRecord($paymentId, [
                'payment_intent_id' => $paymentIntentId,
                'transaction_id' => $sessionId
            ]);
            
            return [
                'success' => true,
                'session_id' => $sessionId,
                'payment_intent_id' => $paymentIntentId,
                'payment_id' => $paymentId,
                'url' => 'index.php?action=process_payment&payment_id=' . $paymentId
            ];
        } catch (Exception $e) {
            error_log("Erreur Stripe: " . $e->getMessage());
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }
    
    /**
     * Crée un enregistrement de paiement dans la base de données
     * 
     * @param array $paymentData
     * @return int|false
     */
    private function createPaymentRecord(array $paymentData): int|false {
        $sql = "INSERT INTO {$this->tablePayments} 
                (inscription_id, id_evenement, amount, currency, payment_method, payment_status)
                VALUES (:inscription_id, :id_evenement, :amount, :currency, :payment_method, 'pending')";
        
        $stmt = $this->db->prepare($sql);
        
        $result = $stmt->execute([
            ':inscription_id' => (int)$paymentData['inscription_id'],
            ':id_evenement' => (int)$paymentData['event_id'],
            ':amount' => (float)$paymentData['amount'],
            ':currency' => $paymentData['currency'] ?? 'EUR',
            ':payment_method' => $paymentData['payment_method'] ?? 'stripe'
        ]);
        
        return $result ? (int)$this->db->lastInsertId() : false;
    }
    
    /**
     * Met à jour un enregistrement de paiement
     * 
     * @param int $paymentId
     * @param array $data
     * @return bool
     */
    private function updatePaymentRecord(int $paymentId, array $data): bool {
        $fields = [];
        $params = [':id' => $paymentId];
        
        if (isset($data['payment_intent_id'])) {
            $fields[] = 'payment_intent_id = :payment_intent_id';
            $params[':payment_intent_id'] = $data['payment_intent_id'];
        }
        
        if (isset($data['transaction_id'])) {
            $fields[] = 'transaction_id = :transaction_id';
            $params[':transaction_id'] = $data['transaction_id'];
        }
        
        if (isset($data['payment_status'])) {
            $fields[] = 'payment_status = :payment_status';
            $params[':payment_status'] = $data['payment_status'];
        }
        
        if (empty($fields)) {
            return false;
        }
        
        $sql = "UPDATE {$this->tablePayments} SET " . implode(', ', $fields) . " WHERE id_payment = :id";
        $stmt = $this->db->prepare($sql);
        
        return $stmt->execute($params);
    }
    
    /**
     * Traite un paiement Stripe (webhook ou callback)
     * 
     * @param string $paymentIntentId
     * @param string $status
     * @return bool
     */
    public function processStripePayment(string $paymentIntentId, string $status): bool {
        $sql = "UPDATE {$this->tablePayments} 
                SET payment_status = :status 
                WHERE payment_intent_id = :payment_intent_id";
        
        $stmt = $this->db->prepare($sql);
        
        return $stmt->execute([
            ':status' => $status,
            ':payment_intent_id' => $paymentIntentId
        ]);
    }
    
    /**
     * Récupère un paiement par son ID
     * 
     * @param int $paymentId
     * @return array|false
     */
    public function getPayment(int $paymentId) {
        $sql = "SELECT * FROM {$this->tablePayments} WHERE id_payment = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id' => $paymentId]);
        
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    /**
     * Récupère un paiement par inscription ID
     * 
     * @param int $inscriptionId
     * @return array|false
     */
    public function getPaymentByInscriptionId(int $inscriptionId) {
        $sql = "SELECT * FROM {$this->tablePayments} WHERE inscription_id = :inscription_id ORDER BY created_at DESC LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':inscription_id' => $inscriptionId]);
        
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    /**
     * Crée une session de paiement PayPal
     * 
     * @param array $paymentData
     * @return array
     */
    public function createPayPalSession(array $paymentData): array {
        // En production, utilisez la vraie API PayPal
        // Pour la démo, on simule
        
        try {
            $paymentId = $this->createPaymentRecord($paymentData);
            
            if (!$paymentId) {
                return ['success' => false, 'error' => 'Erreur lors de la création du paiement'];
            }
            
            $orderId = 'PAYPAL_' . uniqid();
            
            $this->updatePaymentRecord($paymentId, [
                'transaction_id' => $orderId,
                'payment_method' => 'paypal'
            ]);
            
            return [
                'success' => true,
                'order_id' => $orderId,
                'payment_id' => $paymentId,
                'url' => 'index.php?action=process_payment&payment_id=' . $paymentId . '&method=paypal'
            ];
        } catch (Exception $e) {
            error_log("Erreur PayPal: " . $e->getMessage());
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }
    
    /**
     * Vérifie le statut d'un paiement
     * 
     * @param int $paymentId
     * @return string
     */
    public function getPaymentStatus(int $paymentId): string {
        $payment = $this->getPayment($paymentId);
        return $payment ? $payment['payment_status'] : 'unknown';
    }
}
