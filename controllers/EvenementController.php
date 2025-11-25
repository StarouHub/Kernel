<?php
/**
 * Evenement Controller
 * Handles all requests related to events
 */

require_once __DIR__ . '/../models/Evenement.php';

class EvenementController {
    private $model;
    
    public function __construct() {
        $this->model = new Evenement();
    }
    
    /**
     * Check if user is admin
     * @return bool
     */
    private function isAdmin() {
        return isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin';
    }
    
    /**
     * Require admin access, redirect if not admin
     */
    private function requireAdmin() {
        if (!$this->isAdmin()) {
            $_SESSION['error'] = 'Accès refusé. Vous devez être administrateur pour effectuer cette action.';
            header('Location: index.php');
            exit;
        }
    }
    
    /**
     * Show login page
     */
    public function login() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $role = $_POST['role'] ?? '';
            
            if (in_array($role, ['admin', 'user'])) {
                $_SESSION['user_role'] = $role;
                $_SESSION['success'] = 'Connexion réussie! Bienvenue en tant que ' . ($role === 'admin' ? 'Administrateur' : 'Utilisateur');
                header('Location: index.php');
                exit;
            } else {
                $_SESSION['error'] = 'Rôle invalide.';
            }
        }
        
        require_once __DIR__ . '/../views/auth/login.php';
    }
    
    /**
     * Logout user
     */
    public function logout() {
        session_destroy();
        session_start();
        $_SESSION['success'] = 'Déconnexion réussie!';
        header('Location: index.php?action=login');
        exit;
    }
    
    /**
     * List all events
     */
    public function index() {
        $type = $_GET['type'] ?? 'Tous';
        $search = $_GET['search'] ?? null;
        
        $evenements = $this->model->getAll($type, $search);
        
        require_once __DIR__ . '/../views/evenements/list.php';
    }
    
    /**
     * Show create form
     */
    public function create() {
        // Both admin and user can create events
        $evenement = null; // New event
        require_once __DIR__ . '/../views/evenements/create.php';
    }
    
    /**
     * Show edit form
     */
    public function edit() {
        $this->requireAdmin();
        $id = $_GET['id'] ?? null;
        
        if (!$id) {
            header('Location: index.php');
            exit;
        }
        
        $evenement = $this->model->getById($id);
        
        if (!$evenement) {
            header('Location: index.php');
            exit;
        }
        
        require_once __DIR__ . '/../views/evenements/edit.php';
    }
    
    /**
     * Show event details
     */
    public function details() {
        $id = $_GET['id'] ?? null;
        
        if (!$id) {
            header('Location: index.php');
            exit;
        }
        
        $evenement = $this->model->getById($id);
        
        if (!$evenement) {
            header('Location: index.php');
            exit;
        }
        
        require_once __DIR__ . '/../views/evenements/details.php';
    }
    
    /**
     * Save event (create or update)
     */
    public function save() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: index.php');
            exit;
        }
        
        $id = $_POST['id'] ?? null;
        
        // Only admin can update/delete, but anyone can create
        if ($id && !$this->isAdmin()) {
            $_SESSION['error'] = 'Seuls les administrateurs peuvent modifier des événements.';
            header('Location: index.php');
            exit;
        }
        $data = [
            'titre' => $_POST['titre'] ?? '',
            'type' => $_POST['type'] ?? '',
            'date' => $_POST['date'] ?? '',
            'lieu' => $_POST['lieu'] ?? '',
            'capacite' => $_POST['capacite'] ?? 0,
            'user_id' => $_POST['user_id'] ?? 1,
            'description' => $_POST['description'] ?? ''
        ];
        
        // La validation est maintenant gérée côté client en JavaScript
        // On garde une validation minimale côté serveur pour la sécurité
        if (empty($data['titre']) || empty($data['type']) || empty($data['date']) || 
            empty($data['lieu']) || empty($data['description'])) {
            $_SESSION['error'] = 'Tous les champs obligatoires doivent être remplis.';
            if ($id) {
                header('Location: index.php?action=edit&id=' . $id);
            } else {
                header('Location: index.php?action=create');
            }
            exit;
        }
        
        if ($id) {
            // Update existing event
            $result = $this->model->update($id, $data);
            $message = $result ? 'Événement mis à jour avec succès!' : 'Erreur lors de la mise à jour.';
        } else {
            // Create new event
            $result = $this->model->create($data);
            $message = $result ? 'Événement créé avec succès!' : 'Erreur lors de la création.';
        }
        
        if ($result) {
            $_SESSION['success'] = $message;
            header('Location: index.php');
        } else {
            $_SESSION['error'] = $message;
            if ($id) {
                header('Location: index.php?action=edit&id=' . $id);
            } else {
                header('Location: index.php?action=create');
            }
        }
        exit;
    }
    
    /**
     * Delete event
     */
    public function delete() {
        $this->requireAdmin();
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: index.php');
            exit;
        }
        
        $id = $_POST['id'] ?? null;
        
        if (!$id) {
            $_SESSION['error'] = 'ID événement manquant.';
            header('Location: index.php');
            exit;
        }
        
        $result = $this->model->delete($id);
        
        if ($result) {
            $_SESSION['success'] = 'Événement supprimé avec succès!';
        } else {
            $_SESSION['error'] = 'Erreur lors de la suppression.';
        }
        
        header('Location: index.php');
        exit;
    }
    
}

