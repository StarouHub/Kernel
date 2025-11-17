<?php
// projetweb/controllers/userController.php

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../models/user.php';

class userController
{
    private $pdo;

    public function __construct()
    {
        $this->pdo = Config::getConnexion();
    }


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
}