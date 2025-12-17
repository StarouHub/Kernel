<?php
// model/Reclamation.php
class Reclamation {
    private $id;
    private $utilisateur_id;
    private $titre;
    private $description;
    private $type;
    private $priorite;
    private $statut;
    private $date_creation;
    private $priority_score; // NOUVEAU: score de priorité calculé
    private $priority_reason; // NOUVEAU: raison de la priorité attribuée

    // Constantes de priorité
    const PRIORITY_CRITICAL = 'critique';
    const PRIORITY_HIGH = 'haute';
    const PRIORITY_MEDIUM = 'normale';
    const PRIORITY_LOW = 'basse';
    
    // Constantes de score
    const SCORE_CRITICAL = 90;
    const SCORE_HIGH = 70;
    const SCORE_MEDIUM = 40;
    const SCORE_LOW = 0;

    // Getters (ajouts)
    public function getPriorityScore() { return $this->priority_score; }
    public function getPriorityReason() { return $this->priority_reason; }
    
    public function getId() { return $this->id; }
    public function getUtilisateurId() { return $this->utilisateur_id; }
    public function getTitre() { return htmlspecialchars($this->titre ?? '', ENT_QUOTES, 'UTF-8'); }
    public function getDescription() { return htmlspecialchars($this->description ?? '', ENT_QUOTES, 'UTF-8'); }
    public function getType() { return $this->type; }
    public function getPriorite() { return $this->priorite; }
    public function getStatut() { return $this->statut; }
    public function getDateCreation() { return $this->date_creation; }

    // Setters avec validation
    public function setTitre($t) { 
        $t = trim($t);
        if (strlen($t) < 5 || strlen($t) > 100) {
            throw new InvalidArgumentException("Le titre doit contenir entre 5 et 100 caractères");
        }
        $this->titre = $t;
    }

    public function setDescription($d) { 
        $d = trim($d);
        if (strlen($d) < 10 || strlen($d) > 2000) {
            throw new InvalidArgumentException("La description doit contenir entre 10 et 2000 caractères");
        }
        $this->description = $d;
    }

    public function setType($t) { 
        $allowed = ['bug', 'technique', 'contenu', 'suggestion', 'autre'];
        if (!in_array($t, $allowed)) {
            throw new InvalidArgumentException("Type invalide. Choisissez parmi: " . implode(', ', $allowed));
        }
        $this->type = $t;
    }

    public function setPriorite($p) { 
        $allowed = ['critique', 'haute', 'normale', 'basse'];
        if (!in_array($p, $allowed)) {
            throw new InvalidArgumentException("Priorité invalide. Choisissez parmi: " . implode(', ', $allowed));
        }
        $this->priorite = $p;
    }

    public function setStatut($s) { 
        $allowed = ['en-attente', 'en-cours', 'resolue', 'fermee'];
        if (!in_array($s, $allowed)) {
            throw new InvalidArgumentException("Statut invalide. Choisissez parmi: " . implode(', ', $allowed));
        }
        $this->statut = $s;
    }

    public function setUtilisateurId($id) { 
        $this->utilisateur_id = (int)$id;
    }

    public function setId($id) { 
        $this->id = (int)$id;
    }

    public function setDateCreation($d) { 
        $this->date_creation = $d;
    }

    // NOUVEAU: Setter pour le score de priorité
    public function setPriorityScore($score) {
        $this->priority_score = min(100, max(0, (int)$score));
    }

    // NOUVEAU: Setter pour la raison de priorité
    public function setPriorityReason($reason) {
        $this->priority_reason = $reason;
    }

    // NOUVEAU: Méthode pour déterminer la priorité automatiquement
    public function calculateAutoPriority($title, $description) {
        $priorityManager = new PriorityManager();
        return $priorityManager->analyzeAndSetPriority($title, $description);
    }

    // NOUVEAU: Méthode pour obtenir le temps de traitement estimé
    public function getEstimatedResolutionTime() {
        switch ($this->priorite) {
            case self::PRIORITY_CRITICAL:
                return "Traitement immédiat (moins de 2 heures)";
            case self::PRIORITY_HIGH:
                return "Moins de 24 heures";
            case self::PRIORITY_MEDIUM:
                return "Moins de 48 heures";
            case self::PRIORITY_LOW:
                return "Moins de 5 jours";
            default:
                return "Moins de 48 heures";
        }
    }

    // NOUVEAU: Méthode pour vérifier si la priorité doit être escaladée
    public function shouldEscalatePriority() {
        if ($this->statut !== 'en-attente') {
            return false;
        }
        
        $creationDate = new DateTime($this->date_creation);
        $now = new DateTime();
        $interval = $now->diff($creationDate);
        $hours = ($interval->days * 24) + $interval->h;
        
        switch ($this->priorite) {
            case self::PRIORITY_CRITICAL:
                return $hours > 2; // Critique non traitée en 2h
            case self::PRIORITY_HIGH:
                return $hours > 24; // Haute non traitée en 24h
            case self::PRIORITY_MEDIUM:
                return $hours > 48; // Normale non traitée en 48h
            default:
                return false;
        }
    }
}
?>