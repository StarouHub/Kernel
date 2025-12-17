<?php
// model/Reponse.php
class Reponse {
    private $id;
    private $reclamation_id;
    private $utilisateur_id;
    private $message;
    private $est_admin = false;
    private $date_reponse;

    // Getters
    public function getId() { return $this->id; }
    public function getReclamationId() { return $this->reclamation_id; }
    public function getUtilisateurId() { return $this->utilisateur_id; }
    public function getMessage() { return htmlspecialchars($this->message ?? '', ENT_QUOTES, 'UTF-8'); }
    public function isAdmin() { return (bool)$this->est_admin; }
    public function getDateReponse() { return $this->date_reponse; }

    // Setters avec validation
    public function setMessage($m) { 
        $m = trim($m);
        if (empty($m)) {
            throw new InvalidArgumentException("Le message est obligatoire");
        }
        if (strlen($m) > 5000) {
            throw new InvalidArgumentException("Le message ne peut pas dépasser 5000 caractères");
        }
        $this->message = $m;
    }

    public function setEstAdmin($b) { 
        $this->est_admin = (bool)$b;
    }

    public function setReclamationId($id) { 
        $this->reclamation_id = (int)$id;
    }

    public function setUtilisateurId($id) { 
        $this->utilisateur_id = (int)$id;
    }

    public function setId($id) { 
        $this->id = (int)$id;
    }

    public function setDateReponse($d) { 
        $this->date_reponse = $d;
    }
}
?>