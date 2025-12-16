<?php
// projetweb/controller/userController.php

require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../model/user.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require_once __DIR__ . '/../vendor/autoload.php';

class userController
{
    private $pdo;

    public function __construct()
    {
        $this->pdo = Config::getConnexion();
    }

    // ============ BAN METHODS ============

    /**
     * Ban a user until a specific date
     */
    public function banUser(int $userId, string $banUntil): bool
    {
        try {
            $sql = "UPDATE users SET banned_until = :banned_until WHERE id = :id";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([
                ':banned_until' => $banUntil,
                ':id' => $userId
            ]);
            return true;
        } catch (PDOException $e) {
            error_log("Ban user error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Unban a user (remove ban)
     */
    public function unbanUser(int $userId): bool
    {
        try {
            $sql = "UPDATE users SET banned_until = NULL WHERE id = :id";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([':id' => $userId]);
            return true;
        } catch (PDOException $e) {
            error_log("Unban user error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Check if user is currently banned
     */
    public function isUserBanned(int $userId): bool
    {
        $user = $this->getUserById($userId);
        return $user ? $user->isBanned() : false;
    }

    // ============ ORIGINAL METHODS (UPDATED) ============

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

    public function updateUser(User $user): bool
    {
        try {
            if (empty($user->getMdp())) {
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

    public function getUserByEmail(string $email): ?User
    {
        $sql = "SELECT * FROM users WHERE email = :email LIMIT 1";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':email' => $email]);
        $data = $stmt->fetch();

        if (!$data) return null;

        return new User(
            $data['nom'],
            $data['prenom'],
            $data['email'],
            $data['telephone'],
            $data['mdp'],
            $data['role'],
            (int)$data['id'],
            $data['banned_until']
        );
    }

    public function getUserById(int $id): ?User
    {
        $sql = "SELECT * FROM users WHERE id = :id LIMIT 1";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':id' => $id]);
        $data = $stmt->fetch();

        if (!$data) return null;

        return new User(
            $data['nom'],
            $data['prenom'],
            $data['email'],
            $data['telephone'],
            $data['mdp'],
            $data['role'],
            (int)$data['id'],
            $data['banned_until']
        );
    }

    public function getAllUsers(): array
    {
        $sql = "SELECT * FROM users ORDER BY id DESC";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        $results = $stmt->fetchAll();

        $users = [];
        foreach ($results as $row) {
            $users[] = new User(
                $row['nom'],
                $row['prenom'],
                $row['email'],
                $row['telephone'],
                $row['mdp'],
                $row['role'],
                (int)$row['id'],
                $row['banned_until']
            );
        }
        return $users;
    }

    // ============ PASSWORD RESET METHODS ============

    public function sendResetCode(string $email): array
    {
        try {
            $user = $this->getUserByEmail($email);
            if (!$user) {
                return ['success' => false, 'message' => 'Aucun compte associé à cet email.'];
            }

            $code = str_pad(rand(0, 999999), 6, '0', STR_PAD_LEFT);

            $sqlDelete = "DELETE FROM password_resets WHERE email = :email AND is_used = 0";
            $stmtDelete = $this->pdo->prepare($sqlDelete);
            $stmtDelete->execute([':email' => $email]);

            $sqlInsert = "INSERT INTO password_resets (email, code, expires_at) 
                         VALUES (:email, :code, DATE_ADD(NOW(), INTERVAL 30 MINUTE))";
            $stmtInsert = $this->pdo->prepare($sqlInsert);
            $stmtInsert->execute([':email' => $email, ':code' => $code]);

            $emailSent = $this->sendResetEmail($email, $code, $user->getPrenom());

            return $emailSent 
                ? ['success' => true, 'message' => 'Code envoyé avec succès.']
                : ['success' => false, 'message' => 'Erreur lors de l\'envoi de l\'email.'];

        } catch (PDOException $e) {
            error_log("Send reset code error: " . $e->getMessage());
            return ['success' => false, 'message' => 'Erreur serveur.'];
        }
    }

    public function verifyResetCode(string $email, string $code): array
    {
        try {
            $sql = "SELECT * FROM password_resets 
                    WHERE email = :email AND code = :code AND is_used = 0 
                    AND expires_at > NOW() ORDER BY created_at DESC LIMIT 1";
            
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([':email' => $email, ':code' => $code]);
            
            return $stmt->fetch() 
                ? ['success' => true, 'message' => 'Code valide.']
                : ['success' => false, 'message' => 'Code incorrect ou expiré.'];

        } catch (PDOException $e) {
            error_log("Verify reset code error: " . $e->getMessage());
            return ['success' => false, 'message' => 'Erreur serveur.'];
        }
    }

    public function resetPassword(string $email, string $code, string $newPassword): array
    {
        try {
            $verification = $this->verifyResetCode($email, $code);
            if (!$verification['success']) {
                return $verification;
            }

            $sqlUpdate = "UPDATE users SET mdp = :mdp WHERE email = :email";
            $stmtUpdate = $this->pdo->prepare($sqlUpdate);
            $stmtUpdate->execute([':mdp' => $newPassword, ':email' => $email]);

            $sqlMarkUsed = "UPDATE password_resets SET is_used = 1 WHERE email = :email AND code = :code";
            $stmtMarkUsed = $this->pdo->prepare($sqlMarkUsed);
            $stmtMarkUsed->execute([':email' => $email, ':code' => $code]);

            return ['success' => true, 'message' => 'Mot de passe réinitialisé avec succès.'];

        } catch (PDOException $e) {
            error_log("Reset password error: " . $e->getMessage());
            return ['success' => false, 'message' => 'Erreur lors de la réinitialisation.'];
        }
    }

    private function sendResetEmail(string $email, string $code, string $prenom): bool
    {
        try {
            $mail = new PHPMailer(true);
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'awissem349@gmail.com';
            $mail->Password = 'umat bwep dbrq mcre';
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = 587;
            $mail->SMTPOptions = ['ssl' => [
                'verify_peer' => false,
                'verify_peer_name' => false,
                'allow_self_signed' => true
            ]];
            
            $mail->setFrom('noreply@kernel.tn', 'Kernel');
            $mail->addAddress($email);
            $mail->CharSet = 'UTF-8';
            $mail->isHTML(true);
            $mail->Subject = 'Code de réinitialisation - Kernel';
            $mail->Body = "
                <div style='font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto;'>
                    <h2 style='color: #2563EB;'>Réinitialisation de mot de passe</h2>
                    <p>Bonjour <strong>{$prenom}</strong>,</p>
                    <div style='background: #F3F4F6; padding: 20px; border-radius: 10px; text-align: center; margin: 20px 0;'>
                        <h1 style='color: #2563EB; font-size: 36px; letter-spacing: 5px;'>{$code}</h1>
                    </div>
                    <p style='color: #EF4444;'><strong>Ce code expire dans 30 minutes.</strong></p>
                </div>
            ";

            $mail->send();
            return true;
        } catch (Exception $e) {
            error_log("Email error: " . $mail->ErrorInfo);
            return false;
        }
    }

    // ============ REMEMBER ME ============

    public function createRememberToken(int $userId): string
    {
        $selector = bin2hex(random_bytes(16));
        $validator = bin2hex(random_bytes(32));
        $token = $selector . ':' . $validator;
        $hashedValidator = password_hash($validator, PASSWORD_DEFAULT);

        $this->deleteRememberTokens($userId);

        $sql = "INSERT INTO remember_tokens (user_id, token, expires_at) 
                VALUES (:user_id, :token, DATE_ADD(NOW(), INTERVAL 30 DAY))";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            ':user_id' => $userId,
            ':token'   => $selector . ':' . $hashedValidator
        ]);

        return $token;
    }

    public function validateRememberToken(string $token): ?User
    {
        if (empty($token) || strpos($token, ':') === false) return null;

        [$selector, $validator] = explode(':', $token, 2);

        $sql = "SELECT rt.*, u.* FROM remember_tokens rt 
                JOIN users u ON rt.user_id = u.id 
                WHERE rt.token LIKE :selector AND rt.expires_at > NOW() LIMIT 1";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':selector' => $selector . '%']);
        $row = $stmt->fetch();

        if (!$row) return null;

        $storedHash = explode(':', $row['token'], 2)[1] ?? '';
        if (password_verify($validator, $storedHash)) {
            $this->createRememberToken($row['id']);
            
            return new User(
                $row['nom'],
                $row['prenom'],
                $row['email'],
                $row['telephone'],
                $row['mdp'],
                $row['role'],
                (int)$row['id'],
                $row['banned_until']
            );
        }
        return null;
    }

    public function deleteRememberTokens(int $userId): void
    {
        $sql = "DELETE FROM remember_tokens WHERE user_id = :user_id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':user_id' => $userId]);
    }
    /**
 * Get user registrations grouped by month for the last 12 months
 */
    public function getRegistrationsByMonth(): array
    {
        try {
            $sql = "SELECT 
                        DATE_FORMAT(created_at, '%b') as month,
                        COUNT(*) as count
                    FROM users 
                    WHERE created_at >= DATE_SUB(NOW(), INTERVAL 12 MONTH)
                    GROUP BY YEAR(created_at), MONTH(created_at)
                    ORDER BY YEAR(created_at), MONTH(created_at)";
            
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute();
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            // Create array with all 12 months
            $months = ['Jan', 'Fév', 'Mar', 'Avr', 'Mai', 'Juin', 'Juil', 'Août', 'Sep', 'Oct', 'Nov', 'Déc'];
            $data = array_fill(0, 12, 0);
            
            // Fill in actual data
            foreach ($results as $row) {
                $monthIndex = array_search($row['month'], $months);
                if ($monthIndex !== false) {
                    $data[$monthIndex] = (int)$row['count'];
                }
            }
            
            return [
                'labels' => $months,
                'data' => $data
            ];
        } catch (PDOException $e) {
            error_log("Get registrations error: " . $e->getMessage());
            return [
                'labels' => ['Jan', 'Fév', 'Mar', 'Avr', 'Mai', 'Juin', 'Juil', 'Août', 'Sep', 'Oct', 'Nov', 'Déc'],
                'data' => [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0]
            ];
        }
}
}