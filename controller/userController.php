<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../model/user.php';

class UserController
{
    private $pdo;

    public function __construct()
    {
        $this->pdo = config::getConnexion();
    }

    // ============ AUTHENTICATION METHODS ============

    public function login(string $email, string $password): array
    {
        try {
            $user = $this->getUserByEmail($email);
            
            if (!$user) {
                return ['success' => false, 'message' => 'Email ou mot de passe incorrect.'];
            }

            if ($user->isBanned()) {
                return ['success' => false, 'message' => 'Votre compte est temporairement suspendu.'];
            }

            if (password_verify($password, $user->getPassword())) {
                if (session_status() === PHP_SESSION_NONE) {
                    session_start();
                }
                $_SESSION['user_id'] = $user->getId();
                $_SESSION['user_email'] = $user->getEmail();
                $_SESSION['user_role'] = $user->getRole();
                $_SESSION['user_nom'] = $user->getNom();
                $_SESSION['user_prenom'] = $user->getPrenom();
                
                return ['success' => true, 'message' => 'Connexion réussie.', 'user' => $user];
            } else {
                return ['success' => false, 'message' => 'Email ou mot de passe incorrect.'];
            }
        } catch (Exception $e) {
            error_log("Login error: " . $e->getMessage());
            return ['success' => false, 'message' => 'Erreur lors de la connexion.'];
        }
    }

    public function register(User $user): array
    {
        try {
            // Vérifier si l'email existe déjà
            if ($this->getUserByEmail($user->getEmail())) {
                return ['success' => false, 'message' => 'Cet email est déjà utilisé.'];
            }

            // Hasher le mot de passe
            $hashedPassword = password_hash($user->getPassword(), PASSWORD_DEFAULT);
            $user->setPassword($hashedPassword);
            
            // Définir la date d'inscription
            $user->setDateInscription(date('Y-m-d H:i:s'));

            if ($this->addUser($user)) {
                return ['success' => true, 'message' => 'Inscription réussie.'];
            } else {
                return ['success' => false, 'message' => 'Erreur lors de l\'inscription.'];
            }
        } catch (Exception $e) {
            error_log("Register error: " . $e->getMessage());
            return ['success' => false, 'message' => 'Erreur lors de l\'inscription.'];
        }
    }

    public function logout(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        session_unset();
        session_destroy();
        header('Location: connexion.php');
        exit();
    }

    // ============ CRUD METHODS ============

    public function addUser(User $user): bool
    {
        try {
            $sql = "INSERT INTO users (nom, prenom, email, telephone, mdp, role) 
                    VALUES (:nom, :prenom, :email, :telephone, :mdp, :role)";

            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([
                ':nom' => $user->getNom(),
                ':prenom' => $user->getPrenom(),
                ':email' => $user->getEmail(),
                ':telephone' => $user->getTelephone(),
                ':mdp' => $user->getPassword(),
                ':role' => $user->getRole()
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
            if (empty($user->getPassword())) {
                $sql = "UPDATE users 
                        SET nom = :nom, prenom = :prenom, email = :email, 
                            telephone = :telephone, role = :role 
                        WHERE id = :id";
                $params = [
                    ':nom' => $user->getNom(),
                    ':prenom' => $user->getPrenom(),
                    ':email' => $user->getEmail(),
                    ':telephone' => $user->getTelephone(),
                    ':role' => $user->getRole(),
                    ':id' => $user->getId()
                ];
            } else {
                $sql = "UPDATE users 
                        SET nom = :nom, prenom = :prenom, email = :email, 
                            telephone = :telephone, mdp = :mdp, role = :role 
                        WHERE id = :id";
                $params = [
                    ':nom' => $user->getNom(),
                    ':prenom' => $user->getPrenom(),
                    ':email' => $user->getEmail(),
                    ':telephone' => $user->getTelephone(),
                    ':mdp' => $user->getPassword(),
                    ':role' => $user->getRole(),
                    ':id' => $user->getId()
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
                $data['banned_until'], // banned_until
                $data['created_at']
            );
        } catch (PDOException $e) {
            error_log("Get user by email error: " . $e->getMessage());
            return null;
        }
    }

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
                $data['banned_until'], // banned_until
                $data['created_at']
            );
        } catch (PDOException $e) {
            error_log("Get user by ID error: " . $e->getMessage());
            return null;
        }
    }

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
                    $row['banned_until'], // banned_until
                    $row['created_at']
                );
            }
            return $users;
        } catch (PDOException $e) {
            error_log("Get all users error: " . $e->getMessage());
            return [];
        }
    }

    // ============ UTILITY METHODS ============

    public function isLoggedIn(): bool
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        return isset($_SESSION['user_id']);
    }

    public function getCurrentUser(): ?User
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        if (isset($_SESSION['user_id'])) {
            return $this->getUserById($_SESSION['user_id']);
        }
        return null;
    }

    public function requireLogin(): void
    {
        if (!$this->isLoggedIn()) {
            header('Location: connexion.php');
            exit();
        }
    }

    public function requireAdmin(): void
    {
        $user = $this->getCurrentUser();
        if (!$user || !$user->isAdmin()) {
            header('Location: index.php');
            exit();
        }
    }
}