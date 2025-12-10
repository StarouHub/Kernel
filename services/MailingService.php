<?php
/**
 * Service de Mailing pour les Actualités
 * Envoie des notifications par email aux utilisateurs abonnés
 */

include_once(__DIR__ . '/../config.php');

class MailingService
{
    private $db;
    private $fromEmail = 'noreply@kernel.com';
    private $fromName = 'Kernel Platform';
    
    public function __construct()
    {
        $this->db = config::getConnexion();
    }
    
    /**
     * Envoie une notification pour une nouvelle actualité
     */
    public function notifyNewActualite($actualiteId, $projetId)
    {
        try {
            // Récupérer les détails de l'actualité
            $actualite = $this->getActualiteDetails($actualiteId);
            if (!$actualite) {
                return ['success' => false, 'message' => 'Actualité introuvable'];
            }
            
            // Récupérer les utilisateurs abonnés au projet
            $subscribers = $this->getProjectSubscribers($projetId);
            
            if (empty($subscribers)) {
                return [
                    'success' => true,
                    'message' => 'Aucun abonné à notifier',
                    'sent' => 0
                ];
            }
            
            $sentCount = 0;
            $errors = [];
            
            foreach ($subscribers as $subscriber) {
                $result = $this->sendActualiteEmail(
                    $subscriber['email'],
                    $subscriber['nom'] . ' ' . $subscriber['prenom'],
                    $actualite
                );
                
                if ($result['success']) {
                    $sentCount++;
                    // Enregistrer l'envoi dans la base
                    $this->logEmailSent($subscriber['id'], $actualiteId);
                } else {
                    $errors[] = $subscriber['email'];
                }
            }
            
            return [
                'success' => true,
                'message' => "$sentCount email(s) envoyé(s)",
                'sent' => $sentCount,
                'errors' => $errors
            ];
            
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'Erreur : ' . $e->getMessage()
            ];
        }
    }
    
    /**
     * Envoie un email pour une actualité modifiée
     */
    public function notifyUpdatedActualite($actualiteId, $projetId)
    {
        try {
            $actualite = $this->getActualiteDetails($actualiteId);
            if (!$actualite) {
                return ['success' => false, 'message' => 'Actualité introuvable'];
            }
            
            $subscribers = $this->getProjectSubscribers($projetId);
            
            if (empty($subscribers)) {
                return [
                    'success' => true,
                    'message' => 'Aucun abonné à notifier',
                    'sent' => 0
                ];
            }
            
            $sentCount = 0;
            
            foreach ($subscribers as $subscriber) {
                $result = $this->sendUpdateEmail(
                    $subscriber['email'],
                    $subscriber['nom'] . ' ' . $subscriber['prenom'],
                    $actualite
                );
                
                if ($result['success']) {
                    $sentCount++;
                }
            }
            
            return [
                'success' => true,
                'message' => "$sentCount notification(s) d'update envoyée(s)",
                'sent' => $sentCount
            ];
            
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'Erreur : ' . $e->getMessage()
            ];
        }
    }
    
    /**
     * Récupère les détails d'une actualité
     */
    private function getActualiteDetails($actualiteId)
    {
        $sql = "SELECT a.*, p.titre as projet_titre, p.description as projet_description
                FROM actualite a
                INNER JOIN projet p ON a.projet_id = p.id
                WHERE a.id = :id";
        
        $query = $this->db->prepare($sql);
        $query->bindValue(':id', $actualiteId, PDO::PARAM_INT);
        $query->execute();
        
        return $query->fetch(PDO::FETCH_ASSOC);
    }
    
    /**
     * Récupère les utilisateurs abonnés à un projet
     */
    private function getProjectSubscribers($projetId)
    {
        // Pour l'instant, on récupère tous les investisseurs du projet
        $sql = "SELECT DISTINCT u.id, u.email, u.nom, u.prenom
                FROM utilisateur u
                INNER JOIN investissement i ON u.id = i.user_id
                WHERE i.projet_id = :projet_id
                AND u.email IS NOT NULL
                AND u.email != ''";
        
        $query = $this->db->prepare($sql);
        $query->bindValue(':projet_id', $projetId, PDO::PARAM_INT);
        $query->execute();
        
        return $query->fetchAll(PDO::FETCH_ASSOC);
    }
    
    /**
     * Envoie un email pour une nouvelle actualité
     */
    private function sendActualiteEmail($toEmail, $toName, $actualite)
    {
        $subject = "📰 Nouvelle actualité : " . $actualite['titre'];
        
        $message = $this->buildEmailTemplate(
            $toName,
            $actualite['titre'],
            $actualite['contenu'],
            $actualite['projet_titre'],
            $actualite['type'],
            'nouvelle'
        );
        
        return $this->sendEmail($toEmail, $toName, $subject, $message);
    }
    
    /**
     * Envoie un email pour une actualité modifiée
     */
    private function sendUpdateEmail($toEmail, $toName, $actualite)
    {
        $subject = "🔄 Actualité mise à jour : " . $actualite['titre'];
        
        $message = $this->buildEmailTemplate(
            $toName,
            $actualite['titre'],
            $actualite['contenu'],
            $actualite['projet_titre'],
            $actualite['type'],
            'modifiee'
        );
        
        return $this->sendEmail($toEmail, $toName, $subject, $message);
    }
    
    /**
     * Construit le template HTML de l'email
     */
    private function buildEmailTemplate($userName, $titre, $contenu, $projetTitre, $type, $action)
    {
        $typeEmoji = [
            'milestone' => '🎯',
            'update' => '📢',
            'announcement' => '📣'
        ];
        
        $emoji = $typeEmoji[$type] ?? '📰';
        $actionText = ($action === 'nouvelle') ? 'Une nouvelle actualité a été publiée' : 'Une actualité a été mise à jour';
        
        $html = '
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background: linear-gradient(135deg, #2563EB, #7C3AED); color: white; padding: 30px; text-align: center; border-radius: 10px 10px 0 0; }
        .content { background: #f9f9f9; padding: 30px; border-radius: 0 0 10px 10px; }
        .actualite-box { background: white; padding: 20px; border-radius: 8px; margin: 20px 0; border-left: 4px solid #2563EB; }
        .badge { display: inline-block; padding: 5px 12px; background: #2563EB; color: white; border-radius: 20px; font-size: 12px; font-weight: bold; }
        .footer { text-align: center; padding: 20px; color: #666; font-size: 12px; }
        .btn { display: inline-block; padding: 12px 30px; background: #2563EB; color: white; text-decoration: none; border-radius: 5px; margin: 10px 0; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🚀 Kernel Platform</h1>
            <p>' . $actionText . '</p>
        </div>
        <div class="content">
            <p>Bonjour <strong>' . htmlspecialchars($userName) . '</strong>,</p>
            
            <div class="actualite-box">
                <span class="badge">' . $emoji . ' ' . strtoupper($type) . '</span>
                <h2>' . htmlspecialchars($titre) . '</h2>
                <p><strong>Projet :</strong> ' . htmlspecialchars($projetTitre) . '</p>
                <hr>
                <p>' . nl2br(htmlspecialchars($contenu)) . '</p>
            </div>
            
            <p>Restez informé des dernières évolutions de vos projets favoris !</p>
            
            <center>
                <a href="http://localhost/kernel/view/FrontOffice/listeActualite.php" class="btn">
                    Voir toutes les actualités
                </a>
            </center>
        </div>
        <div class="footer">
            <p>Vous recevez cet email car vous suivez ce projet sur Kernel.</p>
            <p>&copy; 2025 Kernel Platform - Tous droits réservés</p>
        </div>
    </div>
</body>
</html>';
        
        return $html;
    }
    
    /**
     * Envoie un email (simulation ou réel selon configuration)
     */
    private function sendEmail($toEmail, $toName, $subject, $htmlMessage)
    {
        // Configuration des headers
        $headers = "MIME-Version: 1.0\r\n";
        $headers .= "Content-Type: text/html; charset=UTF-8\r\n";
        $headers .= "From: " . $this->fromName . " <" . $this->fromEmail . ">\r\n";
        $headers .= "Reply-To: " . $this->fromEmail . "\r\n";
        
        // MODE SIMULATION (pour développement)
        // En production, décommenter la ligne mail() ci-dessous
        
        // Simulation : enregistrer dans un fichier log
        $logFile = __DIR__ . '/../logs/emails_sent.log';
        $logDir = dirname($logFile);
        
        if (!file_exists($logDir)) {
            mkdir($logDir, 0777, true);
        }
        
        $logEntry = "\n" . str_repeat('=', 80) . "\n";
        $logEntry .= "Date: " . date('Y-m-d H:i:s') . "\n";
        $logEntry .= "To: $toEmail ($toName)\n";
        $logEntry .= "Subject: $subject\n";
        $logEntry .= "Message:\n$htmlMessage\n";
        
        file_put_contents($logFile, $logEntry, FILE_APPEND);
        
        // Pour envoyer réellement l'email (nécessite configuration serveur SMTP) :
        // $sent = mail($toEmail, $subject, $htmlMessage, $headers);
        
        return [
            'success' => true,
            'message' => 'Email envoyé (mode simulation)',
            'to' => $toEmail
        ];
    }
    
    /**
     * Enregistre l'envoi d'un email dans la base
     */
    private function logEmailSent($userId, $actualiteId)
    {
        try {
            $sql = "INSERT INTO email_log (user_id, actualite_id, date_envoi, statut) 
                    VALUES (:user_id, :actualite_id, NOW(), 'sent')";
            
            $query = $this->db->prepare($sql);
            $query->execute([
                'user_id' => $userId,
                'actualite_id' => $actualiteId
            ]);
            
            return true;
        } catch (Exception $e) {
            // Table email_log n'existe pas encore, on ignore l'erreur
            return false;
        }
    }
    
    /**
     * Envoie un digest hebdomadaire des actualités
     */
    public function sendWeeklyDigest()
    {
        try {
            // Récupérer les actualités de la semaine
            $sql = "SELECT a.*, p.titre as projet_titre
                    FROM actualite a
                    INNER JOIN projet p ON a.projet_id = p.id
                    WHERE a.date_publication >= DATE_SUB(NOW(), INTERVAL 7 DAY)
                    ORDER BY a.date_publication DESC";
            
            $query = $this->db->query($sql);
            $actualites = $query->fetchAll(PDO::FETCH_ASSOC);
            
            if (empty($actualites)) {
                return [
                    'success' => true,
                    'message' => 'Aucune actualité cette semaine',
                    'sent' => 0
                ];
            }
            
            // Récupérer tous les utilisateurs actifs
            $users = $this->getAllActiveUsers();
            
            $sentCount = 0;
            foreach ($users as $user) {
                $result = $this->sendDigestEmail($user, $actualites);
                if ($result['success']) {
                    $sentCount++;
                }
            }
            
            return [
                'success' => true,
                'message' => "Digest envoyé à $sentCount utilisateur(s)",
                'sent' => $sentCount
            ];
            
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'Erreur : ' . $e->getMessage()
            ];
        }
    }
    
    /**
     * Récupère tous les utilisateurs actifs
     */
    private function getAllActiveUsers()
    {
        $sql = "SELECT id, email, nom, prenom 
                FROM utilisateur 
                WHERE email IS NOT NULL 
                AND email != ''
                AND role IN ('user', 'innovateur', 'Investisseur')";
        
        $query = $this->db->query($sql);
        return $query->fetchAll(PDO::FETCH_ASSOC);
    }
    
    /**
     * Envoie le digest hebdomadaire
     */
    private function sendDigestEmail($user, $actualites)
    {
        $subject = "📬 Votre résumé hebdomadaire Kernel";
        
        $message = $this->buildDigestTemplate(
            $user['nom'] . ' ' . $user['prenom'],
            $actualites
        );
        
        return $this->sendEmail($user['email'], $user['nom'], $subject, $message);
    }
    
    /**
     * Construit le template du digest
     */
    private function buildDigestTemplate($userName, $actualites)
    {
        $html = '
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background: linear-gradient(135deg, #2563EB, #7C3AED); color: white; padding: 30px; text-align: center; border-radius: 10px 10px 0 0; }
        .content { background: #f9f9f9; padding: 30px; }
        .actualite-item { background: white; padding: 15px; margin: 15px 0; border-radius: 8px; border-left: 4px solid #2563EB; }
        .footer { text-align: center; padding: 20px; color: #666; font-size: 12px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>📬 Résumé Hebdomadaire</h1>
            <p>' . count($actualites) . ' actualité(s) cette semaine</p>
        </div>
        <div class="content">
            <p>Bonjour <strong>' . htmlspecialchars($userName) . '</strong>,</p>
            <p>Voici les dernières actualités de la semaine sur Kernel :</p>';
        
        foreach ($actualites as $actu) {
            $html .= '
            <div class="actualite-item">
                <h3>' . htmlspecialchars($actu['titre']) . '</h3>
                <p><strong>Projet :</strong> ' . htmlspecialchars($actu['projet_titre']) . '</p>
                <p>' . substr(htmlspecialchars($actu['contenu']), 0, 150) . '...</p>
                <small>' . date('d/m/Y', strtotime($actu['date_publication'])) . '</small>
            </div>';
        }
        
        $html .= '
        </div>
        <div class="footer">
            <p>&copy; 2025 Kernel Platform</p>
        </div>
    </div>
</body>
</html>';
        
        return $html;
    }
}
?>
