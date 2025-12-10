<?php
/**
 * Email Service
 * Gère l'envoi d'emails de confirmation avec QR code
 */

require_once __DIR__ . '/../models/Evenement.php';

class EmailService {
    
    /**
     * Génère un QR code en utilisant une API en ligne
     * 
     * @param string $data Les données à encoder dans le QR code
     * @return string URL de l'image du QR code
     */
    private function generateQRCode(string $data): string {
        // Utilisation de l'API QR Server pour générer le QR code
        $encodedData = urlencode($data);
        $size = 200; // Taille du QR code en pixels
        return "https://api.qrserver.com/v1/create-qr-code/?size={$size}x{$size}&data={$encodedData}";
    }
    
    /**
     * Crée le contenu HTML de l'email de confirmation
     * 
     * @param array $inscriptionData Données de l'inscription
     * @param array $evenementData Données de l'événement
     * @return string Contenu HTML de l'email
     */
    private function createEmailContent(array $inscriptionData, array $evenementData): string {
        // Créer les données pour le QR code
        $qrData = json_encode([
            'nom' => $inscriptionData['nom'],
            'prenom' => $inscriptionData['prenom'],
            'email' => $inscriptionData['adresse_mail'],
            'id_evenement' => $inscriptionData['id_evenement'],
            'date_inscription' => $inscriptionData['date_inscription'],
            'titre_evenement' => $evenementData['titre']
        ]);
        
        $qrCodeUrl = $this->generateQRCode($qrData);
        
        // Formater la date d'inscription
        $dateInscriptionFormatted = date('d/m/Y', strtotime($inscriptionData['date_inscription']));
        $dateEvenementFormatted = Evenement::formatDateForDisplay($evenementData['date']);
        
        $html = '
        <!DOCTYPE html>
        <html lang="fr">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Confirmation d\'inscription</title>
            <style>
                body {
                    font-family: Arial, sans-serif;
                    line-height: 1.6;
                    color: #333;
                    max-width: 600px;
                    margin: 0 auto;
                    padding: 20px;
                    background-color: #f4f4f4;
                }
                .container {
                    background-color: #ffffff;
                    border-radius: 10px;
                    padding: 30px;
                    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
                }
                .header {
                    background: linear-gradient(135deg, #2563EB, #7C3AED);
                    color: white;
                    padding: 20px;
                    border-radius: 10px 10px 0 0;
                    text-align: center;
                    margin: -30px -30px 30px -30px;
                }
                .header h1 {
                    margin: 0;
                    font-size: 24px;
                }
                .content {
                    margin: 20px 0;
                }
                .info-box {
                    background-color: #f9fafb;
                    border-left: 4px solid #2563EB;
                    padding: 15px;
                    margin: 20px 0;
                    border-radius: 5px;
                }
                .info-row {
                    margin: 10px 0;
                    display: flex;
                    justify-content: space-between;
                }
                .info-label {
                    font-weight: bold;
                    color: #374151;
                }
                .info-value {
                    color: #6B7280;
                }
                .qr-code-section {
                    text-align: center;
                    margin: 30px 0;
                    padding: 20px;
                    background-color: #f9fafb;
                    border-radius: 10px;
                }
                .qr-code-section img {
                    max-width: 200px;
                    height: auto;
                    border: 3px solid #2563EB;
                    border-radius: 10px;
                    padding: 10px;
                    background-color: white;
                }
                .qr-code-section p {
                    margin-top: 15px;
                    color: #6B7280;
                    font-size: 14px;
                }
                .footer {
                    margin-top: 30px;
                    padding-top: 20px;
                    border-top: 1px solid #E5E7EB;
                    text-align: center;
                    color: #6B7280;
                    font-size: 12px;
                }
                .button {
                    display: inline-block;
                    padding: 12px 24px;
                    background-color: #2563EB;
                    color: white;
                    text-decoration: none;
                    border-radius: 5px;
                    margin: 20px 0;
                }
            </style>
        </head>
        <body>
            <div class="container">
                <div class="header">
                    <h1>✓ Confirmation d\'inscription</h1>
                </div>
                
                <div class="content">
                    <p>Bonjour <strong>' . htmlspecialchars($inscriptionData['prenom'] . ' ' . $inscriptionData['nom']) . '</strong>,</p>
                    
                    <p>Nous vous confirmons que votre inscription à l\'événement <strong>' . htmlspecialchars($evenementData['titre']) . '</strong> a bien été enregistrée.</p>
                    
                    <div class="info-box">
                        <div class="info-row">
                            <span class="info-label">Nom complet :</span>
                            <span class="info-value">' . htmlspecialchars($inscriptionData['prenom'] . ' ' . $inscriptionData['nom']) . '</span>
                        </div>
                        <div class="info-row">
                            <span class="info-label">Date d\'inscription :</span>
                            <span class="info-value">' . htmlspecialchars($dateInscriptionFormatted) . '</span>
                        </div>
                        <div class="info-row">
                            <span class="info-label">Événement :</span>
                            <span class="info-value">' . htmlspecialchars($evenementData['titre']) . '</span>
                        </div>
                        <div class="info-row">
                            <span class="info-label">Date de l\'événement :</span>
                            <span class="info-value">' . htmlspecialchars($dateEvenementFormatted) . '</span>
                        </div>
                        <div class="info-row">
                            <span class="info-label">Lieu :</span>
                            <span class="info-value">' . htmlspecialchars($evenementData['lieu']) . '</span>
                        </div>
                        <div class="info-row">
                            <span class="info-label">ID de l\'événement :</span>
                            <span class="info-value">#' . htmlspecialchars($inscriptionData['id_evenement']) . '</span>
                        </div>
                    </div>
                    
                    <div class="qr-code-section">
                        <h3 style="color: #2563EB; margin-bottom: 15px;">Votre QR Code d\'inscription</h3>
                        <img src="' . $qrCodeUrl . '" alt="QR Code d\'inscription" />
                        <p>Présentez ce QR code à l\'entrée de l\'événement pour valider votre inscription.</p>
                    </div>
                    
                    <p>Nous vous remercions de votre participation et nous avons hâte de vous accueillir !</p>
                    
                    <p>Cordialement,<br><strong>L\'équipe TAKTAK</strong></p>
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
     * Envoie un email de confirmation d'inscription
     * 
     * @param array $inscriptionData Données de l'inscription (nom, prenom, adresse_mail, id_evenement, date_inscription)
     * @param array $evenementData Données de l'événement (titre, date, lieu, etc.)
     * @return bool True si l'email a été envoyé avec succès, false sinon
     */
    public function sendConfirmationEmail(array $inscriptionData, array $evenementData): bool {
        $to = $inscriptionData['adresse_mail'];
        $subject = "Confirmation de votre inscription à l'événement " . $evenementData['titre'];
        
        $message = $this->createEmailContent($inscriptionData, $evenementData);
        
        // Headers pour un email HTML
        $headers = "MIME-Version: 1.0" . "\r\n";
        $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
        $headers .= "From: TAKTAK Events <noreply@taktak.com>" . "\r\n";
        $headers .= "Reply-To: noreply@taktak.com" . "\r\n";
        
        // Envoyer l'email
        $result = @mail($to, $subject, $message, $headers);
        
        // Log pour le débogage (en production, utiliser un système de logging)
        if (!$result) {
            error_log("Erreur lors de l'envoi de l'email à : " . $to);
        }
        
        return $result;
    }
}
