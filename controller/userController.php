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

    // ============ AUTHENTICATION METHODS ============

    /**
     * Check if a user is currently logged in
     * @return bool
     */
    public function isLoggedIn(): bool
    {
        return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
    }

    /**
     * Get the currently logged in user
     * @return User|null
     */
    public function getCurrentUser(): ?User
    {
        if (!$this->isLoggedIn()) {
            return null;
        }
        
        return $this->getUserById($_SESSION['user_id']);
    }

    /**
     * Login a user
     * @param string $email
     * @param string $password
     * @return bool
     */
    public function login(string $email, string $password): bool
    {
        try {
            $user = $this->getUserByEmail($email);
            
            if (!$user) {
                return false;
            }

            // Check if user is banned
            if ($user->isBanned()) {
                return false;
            }

            // Verify password
            if (password_verify($password, $user->getMdp())) {
                $_SESSION['user_id'] = $user->getId();
                $_SESSION['user_email'] = $user->getEmail();
                $_SESSION['user_name'] = $user->getPrenom() . ' ' . $user->getNom();
                $_SESSION['user_role'] = $user->getRole();
                return true;
            }

            return false;
        } catch (Exception $e) {
            error_log("Login error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Logout the current user
     */
    public function logout(): void
    {
        // Delete remember me token if exists
        if ($this->isLoggedIn()) {
            $this->deleteRememberTokens($_SESSION['user_id']);
        }

        // Clear session
        session_unset();
        session_destroy();

        // Delete remember me cookie
        if (isset($_COOKIE['remember_me'])) {
            setcookie('remember_me', '', time() - 3600, '/');
        }
    }

    // ============ BAN METHODS ============

    /**
     * Ban a user until a specific date
     * @param int $userId
     * @param string $banUntil
     * @return bool
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
     * @param int $userId
     * @return bool
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
     * @param int $userId
     * @return bool
     */
    public function isUserBanned(int $userId): bool
    {
        $user = $this->getUserById($userId);
        return $user ? $user->isBanned() : false;
    }

    // ============ USER CRUD METHODS ============

    /**
     * Add a new user
     * @param User $user
     * @return bool
     */
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

    /**
     * Update an existing user
     * @param User $user
     * @return bool
     */
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

    /**
     * Delete a user
     * @param int $id
     * @return bool
     */
    public function deleteUser(int $id): bool
    {
        try {
            // Delete remember tokens first
            $this->deleteRememberTokens($id);
            
            // Delete user
            $sql = "DELETE FROM users WHERE id = :id";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([':id' => $id]);
            return true;
        } catch (PDOException $e) {
            error_log("Delete user error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Get user by email
     * @param string $email
     * @return User|null
     */
    public function getUserByEmail(string $email): ?User
    {
        try {
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
                $data['banned_until'] ?? null
            );
        } catch (PDOException $e) {
            error_log("Get user by email error: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Get user by ID
     * @param int $id
     * @return User|null
     */
    public function getUserById(int $id): ?User
    {
        try {
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
                $data['banned_until'] ?? null
            );
        } catch (PDOException $e) {
            error_log("Get user by ID error: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Get all users
     * @return array
     */
    public function getAllUsers(): array
    {
        try {
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
                    $row['banned_until'] ?? null
                );
            }
            return $users;
        } catch (PDOException $e) {
            error_log("Get all users error: " . $e->getMessage());
            return [];
        }
    }

    // ============ PASSWORD RESET METHODS ============

    /**
     * Send password reset code via email
     * @param string $email
     * @return array
     */
    public function sendResetCode(string $email): array
    {
        try {
            $user = $this->getUserByEmail($email);
            if (!$user) {
                return ['success' => false, 'message' => 'Aucun compte associé à cet email.'];
            }

            // Generate 6-digit code
            $code = str_pad(rand(0, 999999), 6, '0', STR_PAD_LEFT);

            // Delete old unused codes
            $sqlDelete = "DELETE FROM password_resets WHERE email = :email AND is_used = 0";
            $stmtDelete = $this->pdo->prepare($sqlDelete);
            $stmtDelete->execute([':email' => $email]);

            // Insert new code
            $sqlInsert = "INSERT INTO password_resets (email, code, expires_at) 
                         VALUES (:email, :code, DATE_ADD(NOW(), INTERVAL 30 MINUTE))";
            $stmtInsert = $this->pdo->prepare($sqlInsert);
            $stmtInsert->execute([':email' => $email, ':code' => $code]);

            // Send email
            $emailSent = $this->sendResetEmail($email, $code, $user->getPrenom());

            return $emailSent 
                ? ['success' => true, 'message' => 'Code envoyé avec succès.']
                : ['success' => false, 'message' => 'Erreur lors de l\'envoi de l\'email.'];

        } catch (PDOException $e) {
            error_log("Send reset code error: " . $e->getMessage());
            return ['success' => false, 'message' => 'Erreur serveur.'];
        }
    }

    /**
     * Verify password reset code
     * @param string $email
     * @param string $code
     * @return array
     */
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

    /**
     * Reset user password
     * @param string $email
     * @param string $code
     * @param string $newPassword
     * @return array
     */
    public function resetPassword(string $email, string $code, string $newPassword): array
    {
        try {
            // Verify code first
            $verification = $this->verifyResetCode($email, $code);
            if (!$verification['success']) {
                return $verification;
            }

            // Update password
            $sqlUpdate = "UPDATE users SET mdp = :mdp WHERE email = :email";
            $stmtUpdate = $this->pdo->prepare($sqlUpdate);
            $stmtUpdate->execute([':mdp' => $newPassword, ':email' => $email]);

            // Mark code as used
            $sqlMarkUsed = "UPDATE password_resets SET is_used = 1 WHERE email = :email AND code = :code";
            $stmtMarkUsed = $this->pdo->prepare($sqlMarkUsed);
            $stmtMarkUsed->execute([':email' => $email, ':code' => $code]);

            return ['success' => true, 'message' => 'Mot de passe réinitialisé avec succès.'];

        } catch (PDOException $e) {
            error_log("Reset password error: " . $e->getMessage());
            return ['success' => false, 'message' => 'Erreur lors de la réinitialisation.'];
        }
    }

    /**
     * Send reset email with code
     * @param string $email
     * @param string $code
     * @param string $prenom
     * @return bool
     */
    private function sendResetEmail(string $email, string $code, string $prenom): bool
    {
        try {
            $mail = new PHPMailer(true);
            
            // SMTP Configuration
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'awissem349@gmail.com';
            $mail->Password = 'umat bwep dbrq mcre';
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = 587;
            $mail->SMTPOptions = [
                'ssl' => [
                    'verify_peer' => false,
                    'verify_peer_name' => false,
                    'allow_self_signed' => true
                ]
            ];
            
            // Email settings
            $mail->setFrom('noreply@kernel.tn', 'Kernel');
            $mail->addAddress($email);
            $mail->CharSet = 'UTF-8';
            $mail->isHTML(true);
            $mail->Subject = 'Code de réinitialisation - Kernel';
            
            // Email body
            $mail->Body = "
                <div style='font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto; padding: 20px;'>
                    <div style='text-align: center; margin-bottom: 30px;'>
                        <h1 style='color: #0A4FFF; margin: 0;'>Kernel</h1>
                    </div>
                    <div style='background: #F8FAFC; padding: 30px; border-radius: 10px;'>
                        <h2 style='color: #2563EB; margin-top: 0;'>Réinitialisation de mot de passe</h2>
                        <p style='color: #374151; font-size: 16px;'>Bonjour <strong>{$prenom}</strong>,</p>
                        <p style='color: #374151; font-size: 16px;'>Vous avez demandé à réinitialiser votre mot de passe. Voici votre code de vérification :</p>
                        <div style='background: white; padding: 25px; border-radius: 10px; text-align: center; margin: 25px 0; border: 2px solid #E5E7EB;'>
                            <h1 style='color: #0A4FFF; font-size: 42px; letter-spacing: 8px; margin: 0;'>{$code}</h1>
                        </div>
                        <p style='color: #EF4444; font-weight: 600; font-size: 14px;'>⚠️ Ce code expire dans 30 minutes.</p>
                        <p style='color: #6B7280; font-size: 14px; margin-top: 20px;'>Si vous n'avez pas demandé cette réinitialisation, veuillez ignorer cet email.</p>
                    </div>
                    <div style='text-align: center; margin-top: 20px; color: #9CA3AF; font-size: 12px;'>
                        <p>© 2025 Kernel - Plateforme d'Innovation Technologique</p>
                    </div>
                </div>
            ";

            $mail->send();
            return true;
        } catch (Exception $e) {
            error_log("Email error: " . $mail->ErrorInfo);
            return false;
        }
    }

    // ============ REMEMBER ME METHODS ============

    /**
     * Create remember me token
     * @param int $userId
     * @return string
     */
    public function createRememberToken(int $userId): string
    {
        $selector = bin2hex(random_bytes(16));
        $validator = bin2hex(random_bytes(32));
        $token = $selector . ':' . $validator;
        $hashedValidator = password_hash($validator, PASSWORD_DEFAULT);

        // Delete old tokens
        $this->deleteRememberTokens($userId);

        // Insert new token
        $sql = "INSERT INTO remember_tokens (user_id, token, expires_at) 
                VALUES (:user_id, :token, DATE_ADD(NOW(), INTERVAL 30 DAY))";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            ':user_id' => $userId,
            ':token'   => $selector . ':' . $hashedValidator
        ]);

        return $token;
    }

    /**
     * Validate remember me token
     * @param string $token
     * @return User|null
     */
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
            // Refresh token
            $this->createRememberToken($row['id']);
            
            return new User(
                $row['nom'],
                $row['prenom'],
                $row['email'],
                $row['telephone'],
                $row['mdp'],
                $row['role'],
                (int)$row['id'],
                $row['banned_until'] ?? null
            );
        }
        return null;
    }

    /**
     * Delete all remember tokens for a user
     * @param int $userId
     */
    public function deleteRememberTokens(int $userId): void
    {
        try {
            $sql = "DELETE FROM remember_tokens WHERE user_id = :user_id";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([':user_id' => $userId]);
        } catch (PDOException $e) {
            error_log("Delete remember tokens error: " . $e->getMessage());
        }
    }

    // ============ STATISTICS METHODS ============

    /**
     * Get user registrations grouped by month for the last 12 months
     * @return array
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

    /**
     * Get total number of users
     * @return int
     */
    public function getTotalUsers(): int
    {
        try {
            $sql = "SELECT COUNT(*) as total FROM users";
            $stmt = $this->pdo->query($sql);
            $result = $stmt->fetch();
            return (int)$result['total'];
        } catch (PDOException $e) {
            error_log("Get total users error: " . $e->getMessage());
            return 0;
        }
    }

    /**
     * Get number of active users (logged in within last 30 days)
     * @return int
     */
    public function getActiveUsers(): int
    {
        try {
            $sql = "SELECT COUNT(DISTINCT user_id) as total 
                    FROM remember_tokens 
                    WHERE created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)";
            $stmt = $this->pdo->query($sql);
            $result = $stmt->fetch();
            return (int)$result['total'];
        } catch (PDOException $e) {
            error_log("Get active users error: " . $e->getMessage());
            return 0;
        }
    }
}
?>