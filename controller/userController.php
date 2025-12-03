<?php
// projetweb/controller/userController.php

require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../model/user.php';

// Importez PHPMailer
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Chargez l'autoloader de Composer
require_once __DIR__ . '/../vendor/autoload.php';

class userController
{
    private $pdo;

    public function __construct()
    {
        $this->pdo = Config::getConnexion();
    }

    // ============ MÉTHODES ORIGINALES ============

    // 1. Add user (inscription)
    public function addUser(User $user): bool
    {
        try {
            $sql = "INSERT INTO users (nom, prenom, email, telephone, mdp, role) 
                    VALUES (:nom, :prenom, :email, :telephone, :mdp, :role)";

            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([
                ':nom'       => $user->getNom(),
                ':prenom'    => $user->getPrenom(),
                ':email'     => $user->getEmail(),
                ':telephone' => $user->getTelephone(),
                ':mdp'       => $user->getMdp(),        
                ':role'      => $user->getRole()
            ]);

            $user->setId($this->pdo->lastInsertId());
            return true;
        } catch (PDOException $e) {
            error_log("Add user error: " . $e->getMessage());
            return false;
        }
    }

    // 2. Update user (admin modify)
    public function updateUser(User $user): bool
    {
        try {
            if (empty($user->getMdp())) {
                // No password change
                $sql = "UPDATE users 
                        SET nom = :nom, prenom = :prenom, email = :email, 
                            telephone = :telephone, role = :role 
                        WHERE id = :id";
                $params = [
                    ':nom'       => $user->getNom(),
                    ':prenom'    => $user->getPrenom(),
                    ':email'     => $user->getEmail(),
                    ':telephone' => $user->getTelephone(),
                    ':role'      => $user->getRole(),
                    ':id'        => $user->getId()
                ];
            } else {
                $sql = "UPDATE users 
                        SET nom = :nom, prenom = :prenom, email = :email, 
                            telephone = :telephone, mdp = :mdp, role = :role 
                        WHERE id = :id";
                $params = [
                    ':nom'       => $user->getNom(),
                    ':prenom'    => $user->getPrenom(),
                    ':email'     => $user->getEmail(),
                    ':telephone' => $user->getTelephone(),
                    ':mdp'       => $user->getMdp(),
                    ':role'      => $user->getRole(),
                    ':id'        => $user->getId()
                ];
            }

            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($params);
            return true;
        } catch (PDOException $e) {
            error_log("Update user error: " . $e->getMessage());
            return false;
        }
    }

    // 3. Delete user
    public function deleteUser(int $id): bool
    {
        try {
            $sql = "DELETE FROM users WHERE id = :id";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([':id' => $id]);
            return true;
        } catch (PDOException $e) {
            error_log("Delete user error: " . $e->getMessage());
            return false;
        }
    }

    // 4. Get user by email (for login)
    public function getUserByEmail(string $email): ?User
    {
        $sql = "SELECT * FROM users WHERE email = :email LIMIT 1";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':email' => $email]);
        $data = $stmt->fetch();

        if (!$data) return null;

        $user = new User(
            $data['nom'],
            $data['prenom'],
            $data['email'],
            $data['telephone'],
            $data['mdp'],
            $data['role'],
            (int)$data['id']
        );
        return $user;
    }

    // 5. Get user by ID (for modify)
    public function getUserById(int $id): ?User
    {
        $sql = "SELECT * FROM users WHERE id = :id LIMIT 1";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':id' => $id]);
        $data = $stmt->fetch();

        if (!$data) return null;

        $user = new User(
            $data['nom'],
            $data['prenom'],
            $data['email'],
            $data['telephone'],
            $data['mdp'],
            $data['role'],
            (int)$data['id']
        );
        return $user;
    }

    // 6. Get all users (admin list)
    public function getAllUsers(): array
    {
        $sql = "SELECT * FROM users ORDER BY id DESC";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        $results = $stmt->fetchAll();

        $users = [];
        foreach ($results as $row) {
            $user = new User(
                $row['nom'],
                $row['prenom'],
                $row['email'],
                $row['telephone'],
                $row['mdp'],
                $row['role'],
                (int)$row['id']
            );
            $users[] = $user;
        }
        return $users;
    }

    // ============ NOUVELLES MÉTHODES POUR MOT DE PASSE OUBLIÉ ============

    /**
     * Générer et envoyer un code de réinitialisation
     */
    public function sendResetCode(string $email): array
    {
        try {
            // Vérifier si l'email existe
            $user = $this->getUserByEmail($email);
            if (!$user) {
                return [
                    'success' => false,
                    'message' => 'Aucun compte associé à cet email.'
                ];
            }

            // Générer un code à 6 chiffres
            $code = str_pad(rand(0, 999999), 6, '0', STR_PAD_LEFT);

            // Supprimer les anciens codes non utilisés pour cet email
            $sqlDelete = "DELETE FROM password_resets WHERE email = :email AND is_used = 0";
            $stmtDelete = $this->pdo->prepare($sqlDelete);
            $stmtDelete->execute([':email' => $email]);

            // Insérer le nouveau code (expire dans 30 minutes)
            $sqlInsert = "INSERT INTO password_resets (email, code, expires_at) 
                         VALUES (:email, :code, DATE_ADD(NOW(), INTERVAL 30 MINUTE))";
            $stmtInsert = $this->pdo->prepare($sqlInsert);
            $stmtInsert->execute([
                ':email' => $email,
                ':code' => $code
            ]);

            // Envoyer l'email
            $emailSent = $this->sendResetEmail($email, $code, $user->getPrenom());

            if ($emailSent) {
                return [
                    'success' => true,
                    'message' => 'Code envoyé avec succès.'
                ];
            } else {
                return [
                    'success' => false,
                    'message' => 'Erreur lors de l\'envoi de l\'email.'
                ];
            }

        } catch (PDOException $e) {
            error_log("Send reset code error: " . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Erreur serveur.'
            ];
        }
    }

    /**
     * Vérifier le code de réinitialisation
     */
    public function verifyResetCode(string $email, string $code): array
    {
        try {
            $sql = "SELECT * FROM password_resets 
                    WHERE email = :email 
                    AND code = :code 
                    AND is_used = 0 
                    AND expires_at > NOW()
                    ORDER BY created_at DESC 
                    LIMIT 1";
            
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([
                ':email' => $email,
                ':code' => $code
            ]);
            
            $result = $stmt->fetch();

            if ($result) {
                return [
                    'success' => true,
                    'message' => 'Code valide.'
                ];
            } else {
                return [
                    'success' => false,
                    'message' => 'Code incorrect ou expiré.'
                ];
            }

        } catch (PDOException $e) {
            error_log("Verify reset code error: " . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Erreur serveur.'
            ];
        }
    }

    /**
     * Réinitialiser le mot de passe
     */
    public function resetPassword(string $email, string $code, string $newPassword): array
    {
        try {
            // Vérifier d'abord que le code est valide
            $verification = $this->verifyResetCode($email, $code);
            if (!$verification['success']) {
                return $verification;
            }

            // Hasher le nouveau mot de passe
           // ✅ NOUVEAU CODE (sans hachage - EN CLAIR)
            // Mettre à jour le mot de passe EN CLAIR
            $sqlUpdate = "UPDATE users SET mdp = :mdp WHERE email = :email";
            $stmtUpdate = $this->pdo->prepare($sqlUpdate);
            $stmtUpdate->execute([
                ':mdp' => $newPassword,  // ← Directement sans hachage
                ':email' => $email
            ]);

            // Marquer le code comme utilisé
            $sqlMarkUsed = "UPDATE password_resets SET is_used = 1 WHERE email = :email AND code = :code";
            $stmtMarkUsed = $this->pdo->prepare($sqlMarkUsed);
            $stmtMarkUsed->execute([
                ':email' => $email,
                ':code' => $code
            ]);

            return [
                'success' => true,
                'message' => 'Mot de passe réinitialisé avec succès.'
            ];

        } catch (PDOException $e) {
            error_log("Reset password error: " . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Erreur lors de la réinitialisation.'
            ];
        }
    }

    /**
     * Envoyer l'email avec le code
     */
    private function sendResetEmail(string $email, string $code, string $prenom): bool
    {
        try {
            $mail = new PHPMailer(true);

            // ========================================
            // CHOISISSEZ UNE CONFIGURATION CI-DESSOUS
            // ========================================

            // ===== OPTION 1: MAILTRAP (RECOMMANDÉ POUR LES TESTS) =====
            // Inscrivez-vous sur https://mailtrap.io/ et récupérez vos identifiants
            /*
            $mail->isSMTP();
            $mail->Host = 'sandbox.smtp.mailtrap.io';
            $mail->SMTPAuth = true;
            $mail->Username = 'VOTRE_USERNAME_MAILTRAP'; // ⚠️ À changer
            $mail->Password = 'VOTRE_PASSWORD_MAILTRAP'; // ⚠️ À changer
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = 2525;
            */

            // ===== OPTION 2: GMAIL (POUR LA PRODUCTION) =====
            // Obtenez un mot de passe d'application: https://myaccount.google.com/apppasswords
            
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'awissem349@gmail.com'; // ⚠️ CHANGEZ ICI - Votre email Gmail
            $mail->Password = 'umat bwep dbrq mcre'; // ⚠️ CHANGEZ ICI - Mot de passe d'application (16 caractères)
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = 587;
            
            // Options SSL pour éviter les erreurs
            $mail->SMTPOptions = array(
                'ssl' => array(
                    'verify_peer' => false,
                    'verify_peer_name' => false,
                    'allow_self_signed' => true
                )
            );
            
            // Activer le débogage (à retirer en production)
            // $mail->SMTPDebug = 2; // Décommentez cette ligne pour voir les erreurs détaillées

            // Paramètres de l'email
            $mail->setFrom('noreply@kernel.tn', 'Kernel');
            $mail->addAddress($email);
            $mail->CharSet = 'UTF-8';

            // Contenu de l'email
            $mail->isHTML(true);
            $mail->Subject = 'Code de réinitialisation - Kernel';
            $mail->Body = "
                <div style='font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto;'>
                    <h2 style='color: #2563EB;'>Réinitialisation de mot de passe</h2>
                    <p>Bonjour <strong>{$prenom}</strong>,</p>
                    <p>Vous avez demandé la réinitialisation de votre mot de passe Kernel.</p>
                    <div style='background: #F3F4F6; padding: 20px; border-radius: 10px; text-align: center; margin: 20px 0;'>
                        <p style='margin: 0; color: #6B7280;'>Votre code de vérification :</p>
                        <h1 style='color: #2563EB; font-size: 36px; margin: 10px 0; letter-spacing: 5px;'>{$code}</h1>
                    </div>
                    <p style='color: #EF4444;'><strong>Ce code expire dans 30 minutes.</strong></p>
                    <p>Si vous n'avez pas demandé cette réinitialisation, ignorez cet email.</p>
                    <hr style='border: none; border-top: 1px solid #E5E7EB; margin: 20px 0;'>
                    <p style='color: #6B7280; font-size: 12px;'>© 2024 Kernel. Tous droits réservés.</p>
                </div>
            ";

            $mail->send();
            return true;

        } catch (Exception $e) {
            // Afficher l'erreur pour le débogage (à retirer en production)
            echo "Erreur d'envoi: " . $mail->ErrorInfo . "<br>";
            error_log("Email error: " . $mail->ErrorInfo);
            return false;
        }
    }
}