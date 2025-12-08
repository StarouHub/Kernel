<?php
/**
 * Evenement Controller
 * Contient toute la logique CRUD pour les événements et inscriptions
 * Les modèles sont uniquement des classes de données (propriétés + getters/setters)
 */

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../models/Evenement.php';
require_once __DIR__ . '/../models/Inscription.php';

class EvenementController {
    private $db;
    private $tableEvenements = 'evenements';
    private $tableInscriptions = 'inscription';
    
    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
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
     * Format date from DD/MM/YYYY to YYYY-MM-DD
     * 
     * @param string $date
     * @return string
     */
    private function formatDate(string $date): string {
        if (empty($date)) {
            return date('Y-m-d');
        }
        
        // Check if already in YYYY-MM-DD format
        if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $date)) {
            return $date;
        }
        
        // Convert from DD/MM/YYYY to YYYY-MM-DD
        if (preg_match('/^(\d{2})\/(\d{2})\/(\d{4})$/', $date, $matches)) {
            return $matches[3] . '-' . $matches[2] . '-' . $matches[1];
        }
        
        return date('Y-m-d');
    }
    
    // ==================== CRUD ÉVÉNEMENTS ====================
    
    /**
     * READ - Get all events with optional filters
     * 
     * @param string|null $type Optional filter by type
     * @param string|null $search Optional search term
     * @return array
     */
    public function getAllEvenements(?string $type = null, ?string $search = null): array {
        $sql = "SELECT * FROM {$this->tableEvenements} WHERE 1=1";
        $params = [];
        
        if ($type && $type !== 'Tous') {
            $sql .= " AND type = :type";
            $params[':type'] = $type;
        }
        
        if ($search) {
            $sql .= " AND (titre LIKE :search OR description LIKE :search OR lieu LIKE :search)";
            $params[':search'] = "%{$search}%";
        }
        
        $sql .= " ORDER BY date ASC, created_at DESC";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    /**
     * READ - Get event by ID
     * 
     * @param int $id
     * @return array|false
     */
    public function getEvenementById(int $id) {
        $sql = "SELECT * FROM {$this->tableEvenements} WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id' => $id]);
        
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    /**
     * CREATE - Create a new event
     * 
     * @param array $data
     * @return int|false Event ID on success, false on failure
     */
    public function createEvenement(array $data) {
        // Format date
        $dateFormatted = $this->formatDate($data['date']);
        
        $sql = "INSERT INTO {$this->tableEvenements} (titre, type, date, lieu, capacite, user_id, description) 
                VALUES (:titre, :type, :date, :lieu, :capacite, :user_id, :description)";
        
        $stmt = $this->db->prepare($sql);
        
        $result = $stmt->execute([
            ':titre' => trim($data['titre']),
            ':type' => $data['type'],
            ':date' => $dateFormatted,
            ':lieu' => trim($data['lieu']),
            ':capacite' => (int)$data['capacite'],
            ':user_id' => (int)$data['user_id'],
            ':description' => trim($data['description'])
        ]);
        
        return $result ? (int)$this->db->lastInsertId() : false;
    }
    
    /**
     * UPDATE - Update an existing event
     * 
     * @param int $id
     * @param array $data
     * @return bool
     */
    public function updateEvenement(int $id, array $data): bool {
        // Format date
        $dateFormatted = $this->formatDate($data['date']);
        
        $sql = "UPDATE {$this->tableEvenements} 
                SET titre = :titre, type = :type, date = :date, lieu = :lieu, 
                    capacite = :capacite, user_id = :user_id, description = :description
                WHERE id = :id";
        
        $stmt = $this->db->prepare($sql);
        
        return $stmt->execute([
            ':id' => $id,
            ':titre' => trim($data['titre']),
            ':type' => $data['type'],
            ':date' => $dateFormatted,
            ':lieu' => trim($data['lieu']),
            ':capacite' => (int)$data['capacite'],
            ':user_id' => (int)$data['user_id'],
            ':description' => trim($data['description'])
        ]);
    }
    
    /**
     * DELETE - Delete an event
     * 
     * @param int $id
     * @return bool
     */
    public function deleteEvenement(int $id): bool {
        $sql = "DELETE FROM {$this->tableEvenements} WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        
        return $stmt->execute([':id' => $id]);
    }
    
    // ==================== CRUD INSCRIPTIONS ====================
    
    /**
     * READ - Get all inscriptions for an event
     * 
     * @param int $eventId
     * @return array
     */
    public function getInscriptionsByEventId(int $eventId): array {
        $sql = "SELECT * FROM {$this->tableInscriptions} WHERE id_evenement = :event_id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':event_id' => $eventId]);
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    /**
     * READ - Count inscriptions for an event
     * 
     * @param int $eventId
     * @return int
     */
    public function countInscriptionsByEventId(int $eventId): int {
        $sql = "SELECT COUNT(*) as total FROM {$this->tableInscriptions} WHERE id_evenement = :event_id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':event_id' => $eventId]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        return (int)$result['total'];
    }
    
    /**
     * READ - Check if email is already registered for an event
     * 
     * @param string $email
     * @param int $eventId
     * @return bool
     */
    public function isEmailRegisteredForEvent(string $email, int $eventId): bool {
        $sql = "SELECT COUNT(*) as total FROM {$this->tableInscriptions} 
                WHERE adresse_mail = :email AND id_evenement = :event_id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':email' => $email,
            ':event_id' => $eventId
        ]);
        
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return (int)$result['total'] > 0;
    }
    
    /**
     * CREATE - Create a new inscription
     * 
     * @param array $data
     * @return int|false ID de l'inscription en cas de succès, false sinon
     */
    public function createInscription(array $data) {
        $sql = "INSERT INTO {$this->tableInscriptions} (nom, prenom, adresse_mail, id_evenement, statut, date_inscription)
                VALUES (:nom, :prenom, :adresse_mail, :id_evenement, :statut, :date_inscription)";

        $stmt = $this->db->prepare($sql);

        // Si aucune date fournie, on met la date du jour (format YYYY-MM-DD)
        $dateInscription = $data['date_inscription'] ?? date('Y-m-d');

        $result = $stmt->execute([
            ':nom' => trim($data['nom']),
            ':prenom' => trim($data['prenom']),
            ':adresse_mail' => strtolower(trim($data['adresse_mail'])),
            ':id_evenement' => (int)$data['id_evenement'],
            ':statut' => $data['statut'] ?? 'En attente',
            ':date_inscription' => $dateInscription,
        ]);

        return $result ? (int)$this->db->lastInsertId() : false;
    }
    
    /**
     * UPDATE - Update an inscription
     * 
     * @param int $id
     * @param array $data
     * @return bool
     */
    public function updateInscription(int $id, array $data): bool {
        $sql = "UPDATE {$this->tableInscriptions} 
                SET nom = :nom, prenom = :prenom, adresse_mail = :adresse_mail, 
                    id_evenement = :id_evenement, statut = :statut, date_inscription = :date_inscription
                WHERE id_inscription = :id";
        
        $stmt = $this->db->prepare($sql);
        
        return $stmt->execute([
            ':id' => $id,
            ':nom' => trim($data['nom']),
            ':prenom' => trim($data['prenom']),
            ':adresse_mail' => strtolower(trim($data['adresse_mail'])),
            ':id_evenement' => (int)$data['id_evenement'],
            ':statut' => $data['statut'],
            ':date_inscription' => $data['date_inscription'] ?? date('Y-m-d'),
        ]);
    }
    
    /**
     * DELETE - Delete an inscription
     * 
     * @param int $id
     * @return bool
     */
    public function deleteInscription(int $id): bool {
        $sql = "DELETE FROM {$this->tableInscriptions} WHERE id_inscription = :id";
        $stmt = $this->db->prepare($sql);
        
        return $stmt->execute([':id' => $id]);
    }
    
    // ==================== ACTIONS DU CONTRÔLEUR ====================
    
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
        
        $evenements = $this->getAllEvenements($type, $search);
        
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
        
        $evenement = $this->getEvenementById((int)$id);
        
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
        
        $evenement = $this->getEvenementById((int)$id);
        
        if (!$evenement) {
            header('Location: index.php');
            exit;
        }
        
        require_once __DIR__ . '/../views/evenements/details.php';
    }

    /**
     * Afficher le formulaire d'inscription pour un événement
     * Accessible à tous les utilisateurs (même non connectés)
     */
    public function inscriptionForm() {
        $id = $_GET['id'] ?? null;

        if (!$id) {
            header('Location: index.php');
            exit;
        }

        $evenement = $this->getEvenementById((int)$id);

        if (!$evenement) {
            header('Location: index.php');
            exit;
        }

        require_once __DIR__ . '/../views/evenements/inscription.php';
    }

    /**
     * Enregistrer une inscription (POST)
     */
    public function inscriptionSave() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: index.php');
            exit;
        }

        $evenementId = $_POST['id_evenement'] ?? null;

        if (!$evenementId) {
            $_SESSION['error'] = "Événement introuvable pour l'inscription.";
            header('Location: index.php');
            exit;
        }

        $evenement = $this->getEvenementById((int)$evenementId);
        if (!$evenement) {
            $_SESSION['error'] = "Événement introuvable.";
            header('Location: index.php');
            exit;
        }

        $data = [
            'nom' => trim($_POST['nom'] ?? ''),
            'prenom' => trim($_POST['prenom'] ?? ''),
            'adresse_mail' => trim($_POST['adresse_mail'] ?? ''),
            'date_inscription' => $_POST['date_inscription'] ?? date('Y-m-d'),
            'statut' => $_POST['statut'] ?? 'En attente',
            'id_evenement' => (int)$evenementId,
        ];

        // Validation minimale côté serveur
        if (
            empty($data['nom']) ||
            empty($data['prenom']) ||
            empty($data['adresse_mail']) ||
            !filter_var($data['adresse_mail'], FILTER_VALIDATE_EMAIL)
        ) {
            $_SESSION['error'] = "Merci de vérifier les informations saisies.";
            header('Location: index.php?action=inscription&id=' . $evenementId);
            exit;
        }

        $result = $this->createInscription($data);

        if ($result) {
            $_SESSION['success'] = "Votre inscription a été enregistrée avec succès !";
        } else {
            $_SESSION['error'] = "Une erreur est survenue lors de l'enregistrement de votre inscription.";
        }

        // Après inscription, on renvoie vers la page de détails de l'événement
        header('Location: index.php?action=details&id=' . $evenementId);
        exit;
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
        
        // Validation minimale côté serveur
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
            $result = $this->updateEvenement((int)$id, $data);
            $message = $result ? 'Événement mis à jour avec succès!' : 'Erreur lors de la mise à jour.';
        } else {
            // Create new event
            $result = $this->createEvenement($data);
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
        
        $result = $this->deleteEvenement((int)$id);
        
        if ($result) {
            $_SESSION['success'] = 'Événement supprimé avec succès!';
        } else {
            $_SESSION['error'] = 'Erreur lors de la suppression.';
        }
        
        header('Location: index.php');
        exit;
    }
}
