<?php
/**
 * Invoice Service
 * Génère et envoie des factures PDF par email
 */

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/PaymentService.php';

class InvoiceService {
    private $db;
    private $tableInvoices = 'invoices';
    private $paymentService;
    
    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
        $this->paymentService = new PaymentService();
        $this->createInvoicesTableIfNotExists();
    }
    
    /**
     * Crée la table invoices si elle n'existe pas
     */
    private function createInvoicesTableIfNotExists() {
        $sql = "CREATE TABLE IF NOT EXISTS {$this->tableInvoices} (
            id_invoice INT AUTO_INCREMENT PRIMARY KEY,
            payment_id INT NOT NULL,
            invoice_number VARCHAR(50) NOT NULL UNIQUE,
            invoice_date DATE NOT NULL,
            total_amount DECIMAL(10, 2) NOT NULL,
            tax_amount DECIMAL(10, 2) DEFAULT 0.00,
            pdf_path VARCHAR(500) NULL,
            sent_at TIMESTAMP NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            INDEX idx_payment (payment_id),
            INDEX idx_number (invoice_number)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";
        
        try {
            $this->db->exec($sql);
        } catch (PDOException $e) {
            error_log("Erreur lors de la création de la table invoices: " . $e->getMessage());
        }
    }
    
    /**
     * Génère un numéro de facture unique
     * 
     * @return string
     */
    private function generateInvoiceNumber(): string {
        $prefix = 'INV';
        $year = date('Y');
        $month = date('m');
        $random = strtoupper(substr(uniqid(), -6));
        
        return $prefix . '-' . $year . $month . '-' . $random;
    }
    
    /**
     * Génère une facture PDF
     * 
     * @param int $paymentId
     * @param array $inscriptionData
     * @param array $evenementData
     * @return string|false Chemin du fichier PDF ou false en cas d'erreur
     */
    public function generateInvoicePDF(int $paymentId, array $inscriptionData, array $evenementData): string|false {
        $payment = $this->paymentService->getPayment($paymentId);
        
        if (!$payment) {
            return false;
        }
        
        // Créer le dossier invoices s'il n'existe pas
        $invoicesDir = __DIR__ . '/../invoices';
        if (!is_dir($invoicesDir)) {
            mkdir($invoicesDir, 0755, true);
        }
        
        $invoiceNumber = $this->generateInvoiceNumber();
        $invoiceDate = date('Y-m-d');
        
        // Créer l'enregistrement de facture
        $invoiceId = $this->createInvoiceRecord($paymentId, $invoiceNumber, $invoiceDate, $payment['amount']);
        
        if (!$invoiceId) {
            return false;
        }
        
        // Générer le contenu HTML de la facture
        $htmlContent = $this->generateInvoiceHTML($invoiceNumber, $invoiceDate, $payment, $inscriptionData, $evenementData);
        
        // Convertir HTML en PDF (utiliser une bibliothèque comme TCPDF ou mPDF)
        // Pour la démo, on génère un HTML qui peut être converti en PDF
        $filename = 'invoice_' . $invoiceNumber . '_' . date('YmdHis') . '.html';
        $filepath = $invoicesDir . '/' . $filename;
        
        file_put_contents($filepath, $htmlContent);
        
        // En production, utilisez une bibliothèque PDF comme TCPDF ou mPDF
        // $pdf = new TCPDF();
        // $pdf->writeHTML($htmlContent);
        // $pdf->Output($filepath, 'F');
        
        // Mettre à jour le chemin du PDF
        $this->updateInvoicePDFPath($invoiceId, $filepath);
        
        return $filepath;
    }
    
    /**
     * Génère le contenu HTML de la facture
     * 
     * @param string $invoiceNumber
     * @param string $invoiceDate
     * @param array $payment
     * @param array $inscriptionData
     * @param array $evenementData
     * @return string
     */
    private function generateInvoiceHTML(string $invoiceNumber, string $invoiceDate, array $payment, array $inscriptionData, array $evenementData): string {
        $totalHT = $payment['amount'];
        $tva = $totalHT * 0.20; // TVA à 20%
        $totalTTC = $totalHT + $tva;
        
        $html = '<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Facture ' . htmlspecialchars($invoiceNumber) . '</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 40px;
            color: #333;
        }
        .invoice-container {
            max-width: 800px;
            margin: 0 auto;
            background: white;
            padding: 40px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        .header {
            display: flex;
            justify-content: space-between;
            margin-bottom: 40px;
            padding-bottom: 20px;
            border-bottom: 2px solid #2563EB;
        }
        .company-info {
            flex: 1;
        }
        .invoice-info {
            text-align: right;
        }
        .invoice-title {
            font-size: 32px;
            font-weight: bold;
            color: #2563EB;
            margin-bottom: 10px;
        }
        .invoice-number {
            font-size: 18px;
            color: #666;
        }
        .section {
            margin-bottom: 30px;
        }
        .section-title {
            font-size: 18px;
            font-weight: bold;
            color: #2563EB;
            margin-bottom: 15px;
            padding-bottom: 10px;
            border-bottom: 1px solid #E5E7EB;
        }
        .info-row {
            display: flex;
            margin-bottom: 8px;
        }
        .info-label {
            font-weight: bold;
            width: 150px;
        }
        .info-value {
            flex: 1;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #E5E7EB;
        }
        th {
            background: #F3F4F6;
            font-weight: bold;
            color: #374151;
        }
        .text-right {
            text-align: right;
        }
        .total-section {
            margin-top: 20px;
            padding-top: 20px;
            border-top: 2px solid #2563EB;
        }
        .total-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 10px;
            font-size: 16px;
        }
        .total-label {
            font-weight: bold;
        }
        .total-amount {
            font-size: 20px;
            font-weight: bold;
            color: #2563EB;
        }
        .footer {
            margin-top: 40px;
            padding-top: 20px;
            border-top: 1px solid #E5E7EB;
            text-align: center;
            color: #6B7280;
            font-size: 12px;
        }
    </style>
</head>
<body>
    <div class="invoice-container">
        <div class="header">
            <div class="company-info">
                <div class="invoice-title">TAKTAK Events</div>
                <div>123 Rue de l\'Événement</div>
                <div>75000 Paris, France</div>
                <div>Tél: +33 1 23 45 67 89</div>
                <div>Email: contact@taktak.com</div>
            </div>
            <div class="invoice-info">
                <div class="invoice-title">FACTURE</div>
                <div class="invoice-number">N° ' . htmlspecialchars($invoiceNumber) . '</div>
                <div style="margin-top: 10px;">Date: ' . date('d/m/Y', strtotime($invoiceDate)) . '</div>
            </div>
        </div>
        
        <div class="section">
            <div class="section-title">Facturé à</div>
            <div class="info-row">
                <div class="info-value">
                    <strong>' . htmlspecialchars($inscriptionData['prenom'] . ' ' . $inscriptionData['nom']) . '</strong><br>
                    ' . htmlspecialchars($inscriptionData['adresse_mail']) . '
                </div>
            </div>
        </div>
        
        <div class="section">
            <div class="section-title">Détails de la facture</div>
            <table>
                <thead>
                    <tr>
                        <th>Description</th>
                        <th class="text-right">Prix unitaire</th>
                        <th class="text-right">Total HT</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>
                            <strong>' . htmlspecialchars($evenementData['titre']) . '</strong><br>
                            <small>Type: ' . htmlspecialchars($evenementData['type']) . '</small><br>
                            <small>Date: ' . date('d/m/Y', strtotime($evenementData['date'])) . '</small>
                        </td>
                        <td class="text-right">' . number_format($totalHT, 2, ',', ' ') . ' €</td>
                        <td class="text-right">' . number_format($totalHT, 2, ',', ' ') . ' €</td>
                    </tr>
                </tbody>
            </table>
            
            <div class="total-section">
                <div class="total-row">
                    <span class="total-label">Total HT:</span>
                    <span>' . number_format($totalHT, 2, ',', ' ') . ' €</span>
                </div>
                <div class="total-row">
                    <span class="total-label">TVA (20%):</span>
                    <span>' . number_format($tva, 2, ',', ' ') . ' €</span>
                </div>
                <div class="total-row">
                    <span class="total-label total-amount">Total TTC:</span>
                    <span class="total-amount">' . number_format($totalTTC, 2, ',', ' ') . ' €</span>
                </div>
            </div>
        </div>
        
        <div class="section">
            <div class="section-title">Informations de paiement</div>
            <div class="info-row">
                <div class="info-label">Méthode:</div>
                <div class="info-value">' . strtoupper($payment['payment_method']) . '</div>
            </div>
            <div class="info-row">
                <div class="info-label">Statut:</div>
                <div class="info-value">Payé</div>
            </div>
            <div class="info-row">
                <div class="info-label">Transaction ID:</div>
                <div class="info-value">' . htmlspecialchars($payment['transaction_id'] ?? 'N/A') . '</div>
            </div>
        </div>
        
        <div class="footer">
            <p>Merci pour votre confiance !</p>
            <p>Cette facture a été générée automatiquement le ' . date('d/m/Y à H:i') . '</p>
        </div>
    </div>
</body>
</html>';
        
        return $html;
    }
    
    /**
     * Crée un enregistrement de facture
     * 
     * @param int $paymentId
     * @param string $invoiceNumber
     * @param string $invoiceDate
     * @param float $amount
     * @return int|false
     */
    private function createInvoiceRecord(int $paymentId, string $invoiceNumber, string $invoiceDate, float $amount): int|false {
        $sql = "INSERT INTO {$this->tableInvoices} 
                (payment_id, invoice_number, invoice_date, total_amount, tax_amount)
                VALUES (:payment_id, :invoice_number, :invoice_date, :total_amount, :tax_amount)";
        
        $stmt = $this->db->prepare($sql);
        
        $taxAmount = $amount * 0.20; // TVA à 20%
        
        $result = $stmt->execute([
            ':payment_id' => $paymentId,
            ':invoice_number' => $invoiceNumber,
            ':invoice_date' => $invoiceDate,
            ':total_amount' => $amount,
            ':tax_amount' => $taxAmount
        ]);
        
        return $result ? (int)$this->db->lastInsertId() : false;
    }
    
    /**
     * Met à jour le chemin du PDF de la facture
     * 
     * @param int $invoiceId
     * @param string $pdfPath
     * @return bool
     */
    private function updateInvoicePDFPath(int $invoiceId, string $pdfPath): bool {
        $sql = "UPDATE {$this->tableInvoices} SET pdf_path = :pdf_path WHERE id_invoice = :id";
        $stmt = $this->db->prepare($sql);
        
        return $stmt->execute([
            ':id' => $invoiceId,
            ':pdf_path' => $pdfPath
        ]);
    }
    
    /**
     * Envoie la facture par email
     * 
     * @param int $paymentId
     * @param array $inscriptionData
     * @param array $evenementData
     * @return bool
     */
    public function sendInvoiceByEmail(int $paymentId, array $inscriptionData, array $evenementData): bool {
        // Générer le PDF de la facture
        $pdfPath = $this->generateInvoicePDF($paymentId, $inscriptionData, $evenementData);
        
        if (!$pdfPath) {
            return false;
        }
        
        // Récupérer les informations de la facture
        $payment = $this->paymentService->getPayment($paymentId);
        $invoice = $this->getInvoiceByPaymentId($paymentId);
        
        if (!$invoice) {
            return false;
        }
        
        // Préparer l'email
        $to = $inscriptionData['adresse_mail'];
        $subject = "Facture " . $invoice['invoice_number'] . " - " . $evenementData['titre'];
        
        $message = $this->createInvoiceEmailContent($invoice, $inscriptionData, $evenementData, $payment);
        
        // Headers pour un email HTML avec pièce jointe
        $headers = "MIME-Version: 1.0" . "\r\n";
        $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
        $headers .= "From: TAKTAK Events <noreply@taktak.com>" . "\r\n";
        $headers .= "Reply-To: noreply@taktak.com" . "\r\n";
        
        // En production, utilisez PHPMailer ou SwiftMailer pour les pièces jointes
        // Pour la démo, on envoie juste l'email avec un lien vers la facture
        
        $result = @mail($to, $subject, $message, $headers);
        
        if ($result) {
            // Marquer la facture comme envoyée
            $this->markInvoiceAsSent($invoice['id_invoice']);
        }
        
        return $result;
    }
    
    /**
     * Crée le contenu HTML de l'email de facture
     * 
     * @param array $invoice
     * @param array $inscriptionData
     * @param array $evenementData
     * @param array $payment
     * @return string
     */
    private function createInvoiceEmailContent(array $invoice, array $inscriptionData, array $evenementData, array $payment): string {
        $html = '
        <!DOCTYPE html>
        <html lang="fr">
        <head>
            <meta charset="UTF-8">
            <style>
                body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
                .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                .header { background: linear-gradient(135deg, #2563EB, #7C3AED); color: white; padding: 20px; border-radius: 10px 10px 0 0; }
                .content { background: white; padding: 30px; border: 1px solid #E5E7EB; }
                .footer { background: #F9FAFB; padding: 20px; text-align: center; font-size: 12px; color: #6B7280; border-radius: 0 0 10px 10px; }
                .invoice-details { background: #F3F4F6; padding: 15px; border-radius: 5px; margin: 20px 0; }
                .btn { display: inline-block; padding: 12px 24px; background: #2563EB; color: white; text-decoration: none; border-radius: 5px; margin: 20px 0; }
            </style>
        </head>
        <body>
            <div class="container">
                <div class="header">
                    <h1>Votre facture</h1>
                </div>
                <div class="content">
                    <p>Bonjour <strong>' . htmlspecialchars($inscriptionData['prenom'] . ' ' . $inscriptionData['nom']) . '</strong>,</p>
                    
                    <p>Nous vous remercions pour votre paiement concernant l\'événement <strong>' . htmlspecialchars($evenementData['titre']) . '</strong>.</p>
                    
                    <div class="invoice-details">
                        <p><strong>Numéro de facture:</strong> ' . htmlspecialchars($invoice['invoice_number']) . '</p>
                        <p><strong>Date:</strong> ' . date('d/m/Y', strtotime($invoice['invoice_date'])) . '</p>
                        <p><strong>Montant total:</strong> ' . number_format($invoice['total_amount'] + $invoice['tax_amount'], 2, ',', ' ') . ' € TTC</p>
                    </div>
                    
                    <p>Votre facture est disponible en pièce jointe (format HTML).</p>
                    
                    <p>En cas de question, n\'hésitez pas à nous contacter.</p>
                    
                    <p>Cordialement,<br><strong>L\'équipe TAKTAK Events</strong></p>
                </div>
                <div class="footer">
                    <p>Cet email a été envoyé automatiquement. Merci de ne pas y répondre.</p>
                </div>
            </div>
        </body>
        </html>';
        
        return $html;
    }
    
    /**
     * Récupère une facture par payment ID
     * 
     * @param int $paymentId
     * @return array|false
     */
    private function getInvoiceByPaymentId(int $paymentId) {
        $sql = "SELECT * FROM {$this->tableInvoices} WHERE payment_id = :payment_id LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':payment_id' => $paymentId]);
        
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    /**
     * Marque une facture comme envoyée
     * 
     * @param int $invoiceId
     * @return bool
     */
    private function markInvoiceAsSent(int $invoiceId): bool {
        $sql = "UPDATE {$this->tableInvoices} SET sent_at = NOW() WHERE id_invoice = :id";
        $stmt = $this->db->prepare($sql);
        
        return $stmt->execute([':id' => $invoiceId]);
    }
}
