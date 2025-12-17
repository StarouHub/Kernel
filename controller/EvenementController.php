<?php
/**
 * Evenement Controller
 * Contient toute la logique CRUD pour les événements et inscriptions
 * Les modèles sont uniquement des classes de données (propriétés + getters/setters)
 */

require_once __DIR__ . '/../configt/database.php';
require_once __DIR__ . '/../model/Evenement.php';
require_once __DIR__ . '/../model/Inscription.php';
require_once __DIR__ . '/../servicest/EmailService.php';

class EvenementController {
    private $db;
    private $tableEvenements = 'evenements';
    private $tableInscriptions = 'inscription';
    private $tableWaitlist = 'waitlist';
    private $defaultPrice = 0.00; // Prix par défaut si non spécifié
    
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
    
    // ==================== GESTION DES PLACES RESTANTES ====================
    
    /**
     * Vérifie si un événement est complet
     * 
     * @param int $eventId
     * @return bool
     */
    public function isEventFull(int $eventId): bool {
        $evenement = $this->getEvenementById($eventId);
        if (!$evenement) {
            return false;
        }
        
        $inscriptionsCount = $this->countInscriptionsByEventId($eventId);
        return $inscriptionsCount >= (int)$evenement['capacite'];
    }
    
    /**
     * Calcule le nombre de places restantes
     * 
     * @param int $eventId
     * @return int
     */
    public function getRemainingSpots(int $eventId): int {
        $evenement = $this->getEvenementById($eventId);
        if (!$evenement) {
            return 0;
        }
        
        $inscriptionsCount = $this->countInscriptionsByEventId($eventId);
        $remaining = (int)$evenement['capacite'] - $inscriptionsCount;
        
        return max(0, $remaining);
    }
    
    // ==================== GESTION DE LA LISTE D'ATTENTE ====================
    
    /**
     * Créer une entrée dans la liste d'attente
     * 
     * @param array $data
     * @return int|false ID de l'entrée en cas de succès, false sinon
     */
    public function addToWaitlist(array $data) {
        // Créer la table si elle n'existe pas
        $this->createWaitlistTableIfNotExists();
        
        $sql = "INSERT INTO {$this->tableWaitlist} (nom, prenom, adresse_mail, id_evenement, date_inscription)
                VALUES (:nom, :prenom, :adresse_mail, :id_evenement, :date_inscription)";

        $stmt = $this->db->prepare($sql);

        $dateInscription = $data['date_inscription'] ?? date('Y-m-d');

        $result = $stmt->execute([
            ':nom' => trim($data['nom']),
            ':prenom' => trim($data['prenom']),
            ':adresse_mail' => strtolower(trim($data['adresse_mail'])),
            ':id_evenement' => (int)$data['id_evenement'],
            ':date_inscription' => $dateInscription,
        ]);

        return $result ? (int)$this->db->lastInsertId() : false;
    }
    
    /**
     * Vérifie si un email est déjà dans la liste d'attente
     * 
     * @param string $email
     * @param int $eventId
     * @return bool
     */
    public function isEmailInWaitlist(string $email, int $eventId): bool {
        $this->createWaitlistTableIfNotExists();
        
        $sql = "SELECT COUNT(*) as total FROM {$this->tableWaitlist} 
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
     * Récupère toutes les entrées de la liste d'attente pour un événement
     * 
     * @param int $eventId
     * @return array
     */
    public function getWaitlistByEventId(int $eventId): array {
        $this->createWaitlistTableIfNotExists();
        
        $sql = "SELECT * FROM {$this->tableWaitlist} WHERE id_evenement = :event_id ORDER BY date_inscription ASC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':event_id' => $eventId]);
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    /**
     * Crée la table waitlist si elle n'existe pas
     */
    private function createWaitlistTableIfNotExists() {
        $sql = "CREATE TABLE IF NOT EXISTS {$this->tableWaitlist} (
            id_waitlist INT AUTO_INCREMENT PRIMARY KEY,
            nom VARCHAR(100) NOT NULL,
            prenom VARCHAR(100) NOT NULL,
            adresse_mail VARCHAR(255) NOT NULL,
            id_evenement INT NOT NULL,
            date_inscription DATE NOT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            INDEX idx_event (id_evenement),
            INDEX idx_email (adresse_mail)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";
        
        try {
            $this->db->exec($sql);
        } catch (PDOException $e) {
            // La table existe peut-être déjà, on ignore l'erreur
            error_log("Erreur lors de la création de la table waitlist: " . $e->getMessage());
        }
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
        
        require_once __DIR__ . '/../view/auth/login.php';
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
        
        require_once __DIR__ . '/../view/evenements/list.php';
    }
    
    /**
     * Show create form
     */
    public function create() {
        // Both admin and user can create events
        $evenement = null; // New event
        require_once __DIR__ . '/../view/evenements/create.php';
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
        
        require_once __DIR__ . '/../view/evenements/edit.php';
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
        
        // Calculer les places restantes
        $remainingSpots = $this->getRemainingSpots((int)$id);
        $isFull = $this->isEventFull((int)$id);
        
        require_once __DIR__ . '/../view/evenements/details.php';
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
        
        // Vérifier si l'événement est complet
        $isFull = $this->isEventFull((int)$id);
        $remainingSpots = $this->getRemainingSpots((int)$id);

        require_once __DIR__ . '/../view/evenements/inscription.php';
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

        // Vérifier si l'événement est complet
        $isFull = $this->isEventFull($evenementId);
        $isWaitlist = isset($_POST['waitlist']) && $_POST['waitlist'] === '1';

        // Si l'événement est complet et que ce n'est pas une demande de liste d'attente
        if ($isFull && !$isWaitlist) {
            // Vérifier si l'email est déjà dans la liste d'attente
            if ($this->isEmailInWaitlist($data['adresse_mail'], $evenementId)) {
                $_SESSION['error'] = "Vous êtes déjà inscrit sur la liste d'attente pour cet événement.";
            } else {
                // Proposer la liste d'attente
                $_SESSION['info'] = "Cet événement est complet. Souhaitez-vous vous inscrire sur la liste d'attente ?";
                $_SESSION['waitlist_event_id'] = $evenementId;
                $_SESSION['waitlist_data'] = $data;
            }
            header('Location: index.php?action=inscription&id=' . $evenementId);
            exit;
        }

        // Si c'est une inscription sur la liste d'attente
        if ($isWaitlist || ($isFull && $isWaitlist)) {
            $result = $this->addToWaitlist($data);
            
            if ($result) {
                $_SESSION['success'] = "Vous avez été ajouté à la liste d'attente. Vous serez notifié si une place se libère.";
            } else {
                $_SESSION['error'] = "Une erreur est survenue lors de l'ajout à la liste d'attente.";
            }
            
            header('Location: index.php?action=details&id=' . $evenementId);
            exit;
        }

        // Inscription normale
        $result = $this->createInscription($data);

        if ($result) {
            // Vérifier si l'événement nécessite un paiement
            $eventPrice = $this->getEventPrice($evenementId);
            
            if ($eventPrice > 0) {
                // Rediriger vers le paiement
                $_SESSION['pending_inscription_id'] = $result;
                $_SESSION['pending_inscription_data'] = $data;
                header('Location: index.php?action=payment_checkout&inscription_id=' . $result . '&event_id=' . $evenementId);
                exit;
            } else {
                // Événement gratuit - procéder normalement
                $this->completeFreeRegistration($result, $data, $evenement, $evenementId);
            }
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
    
    /**
     * Récupère le prix d'un événement
     * 
     * @param int $eventId
     * @return float
     */
    private function getEventPrice(int $eventId): float {
        // Pour l'instant, on utilise un prix par défaut
        // En production, vous pouvez ajouter un champ 'prix' dans la table evenements
        // ou utiliser une table de tarification séparée
        
        // Exemple : prix basé sur le type d'événement
        $evenement = $this->getEvenementById($eventId);
        if (!$evenement) {
            return $this->defaultPrice;
        }
        
        // Prix par défaut selon le type (vous pouvez personnaliser)
        $prices = [
            'Workshop' => 50.00,
            'Hackathon' => 0.00, // Gratuit
            'Conférence' => 75.00,
            'Meetup' => 0.00, // Gratuit
            'Webinaire' => 25.00
        ];
        
        return $prices[$evenement['type']] ?? $this->defaultPrice;
    }
    
    /**
     * Complète l'inscription pour un événement gratuit
     * 
     * @param int $inscriptionId
     * @param array $data
     * @param array $evenement
     * @param int $evenementId
     */
    private function completeFreeRegistration(int $inscriptionId, array $data, array $evenement, int $evenementId) {
        // Vérifier si l'événement est maintenant complet après cette inscription
        $isNowFull = $this->isEventFull($evenementId);
        
        // Envoyer l'email de confirmation avec QR code
        try {
            require_once __DIR__ . '/../servicest/EmailService.php';
            $emailService = new EmailService();
            $emailSent = $emailService->sendConfirmationEmail($data, $evenement);
            
            if ($emailSent) {
                $_SESSION['success'] = "Votre inscription a été enregistrée avec succès ! Un email de confirmation avec votre QR code vous a été envoyé.";
            } else {
                $_SESSION['success'] = "Votre inscription a été enregistrée avec succès ! (Note: L'envoi de l'email de confirmation a échoué, mais votre inscription est bien enregistrée.)";
            }
        } catch (Exception $e) {
            error_log("Erreur lors de l'envoi de l'email de confirmation: " . $e->getMessage());
            $_SESSION['success'] = "Votre inscription a été enregistrée avec succès !";
        }
        
        if ($isNowFull) {
            $_SESSION['info'] = "Cet événement est maintenant complet.";
        }
        
        header('Location: index.php?action=details&id=' . $evenementId);
        exit;
    }
    
    /**
     * Affiche la page de checkout pour le paiement
     */
    public function paymentCheckout() {
        $inscriptionId = $_GET['inscription_id'] ?? null;
        $eventId = $_GET['event_id'] ?? null;
        
        if (!$inscriptionId || !$eventId) {
            $_SESSION['error'] = "Informations de paiement manquantes.";
            header('Location: index.php');
            exit;
        }
        
        // Récupérer les données depuis la session ou la base
        $inscriptionData = $_SESSION['pending_inscription_data'] ?? null;
        $evenement = $this->getEvenementById((int)$eventId);
        
        if (!$evenement || !$inscriptionData) {
            $_SESSION['error'] = "Données d'inscription introuvables.";
            header('Location: index.php');
            exit;
        }
        
        $eventPrice = $this->getEventPrice((int)$eventId);
        
        require_once __DIR__ . '/../view/payment/checkout.php';
    }
    
    /**
     * Traite le paiement
     */
    public function processPayment() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: index.php');
            exit;
        }
        
        $inscriptionId = $_POST['inscription_id'] ?? null;
        $eventId = $_POST['event_id'] ?? null;
        $paymentMethod = $_POST['payment_method'] ?? 'stripe';
        
        if (!$inscriptionId || !$eventId) {
            $_SESSION['error'] = "Informations de paiement manquantes.";
            header('Location: index.php');
            exit;
        }
        
        $inscriptionData = $_SESSION['pending_inscription_data'] ?? null;
        $evenement = $this->getEvenementById((int)$eventId);
        
        if (!$evenement || !$inscriptionData) {
            $_SESSION['error'] = "Données d'inscription introuvables.";
            header('Location: index.php');
            exit;
        }
        
        $eventPrice = $this->getEventPrice((int)$eventId);
        
        require_once __DIR__ . '/../servicest/PaymentService.php';
        $paymentService = new PaymentService();
        
        // Créer la session de paiement
        $paymentData = [
            'inscription_id' => (int)$inscriptionId,
            'event_id' => (int)$eventId,
            'amount' => $eventPrice,
            'currency' => 'EUR',
            'payment_method' => $paymentMethod
        ];
        
        // Créer la session selon la méthode de paiement
        if ($paymentMethod === 'paypal') {
            $checkoutSession = $paymentService->createPayPalSession($paymentData);
        } else {
            $checkoutSession = $paymentService->createStripeCheckoutSession($paymentData);
        }
        
        if ($checkoutSession && $checkoutSession['success']) {
            // Simuler le paiement réussi (en production, cela viendrait du webhook Stripe/PayPal)
            // Pour la démo, on simule directement
            $this->completePayment($checkoutSession['payment_id'], $inscriptionId, $inscriptionData, $evenement, $eventId);
        } else {
            $_SESSION['error'] = "Erreur lors de l'initialisation du paiement.";
            header('Location: index.php?action=payment_checkout&inscription_id=' . $inscriptionId . '&event_id=' . $eventId);
            exit;
        }
    }
    
    /**
     * Complète le paiement et envoie la facture
     * 
     * @param int $paymentId
     * @param int $inscriptionId
     * @param array $inscriptionData
     * @param array $evenement
     * @param int $eventId
     */
    private function completePayment(int $paymentId, int $inscriptionId, array $inscriptionData, array $evenement, int $eventId) {
        require_once __DIR__ . '/../servicest/PaymentService.php';
        require_once __DIR__ . '/../servicest/InvoiceService.php';
        
        $paymentService = new PaymentService();
        $invoiceService = new InvoiceService();
        
        // Marquer le paiement comme réussi
        $payment = $paymentService->getPayment($paymentId);
        if ($payment) {
            // Mettre à jour le statut du paiement
            $sql = "UPDATE payments SET payment_status = 'succeeded' WHERE id_payment = :id";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([':id' => $paymentId]);
        }
        
        // Envoyer la facture par email
        try {
            $invoiceSent = $invoiceService->sendInvoiceByEmail($paymentId, $inscriptionData, $evenement);
            
            // Envoyer aussi l'email de confirmation avec QR code
            require_once __DIR__ . '/../servicest/EmailService.php';
            $emailService = new EmailService();
            $emailService->sendConfirmationEmail($inscriptionData, $evenement);
            
            if ($invoiceSent) {
                $_SESSION['success'] = "Paiement effectué avec succès ! Votre facture et votre confirmation d'inscription ont été envoyées par email.";
            } else {
                $_SESSION['success'] = "Paiement effectué avec succès ! Votre confirmation d'inscription a été envoyée par email.";
                $_SESSION['info'] = "Note: L'envoi de la facture a échoué, mais votre paiement est bien enregistré.";
            }
        } catch (Exception $e) {
            error_log("Erreur lors de l'envoi de la facture: " . $e->getMessage());
            $_SESSION['success'] = "Paiement effectué avec succès !";
        }
        
        // Nettoyer la session
        unset($_SESSION['pending_inscription_id']);
        unset($_SESSION['pending_inscription_data']);
        
        header('Location: index.php?action=payment_success&payment_id=' . $paymentId);
        exit;
    }
    
    /**
     * Affiche la page de succès du paiement
     */
    public function paymentSuccess() {
        $paymentId = $_GET['payment_id'] ?? null;
        
        if (!$paymentId) {
            header('Location: index.php');
            exit;
        }
        
        require_once __DIR__ . '/../servicest/PaymentService.php';
        $paymentService = new PaymentService();
        $payment = $paymentService->getPayment((int)$paymentId);
        
        if (!$payment) {
            $_SESSION['error'] = "Paiement introuvable.";
            header('Location: index.php');
            exit;
        }
        
        $evenement = $this->getEvenementById($payment['id_evenement']);
        
        require_once __DIR__ . '/../view/payment/success.php';
    }
}
