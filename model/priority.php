<?php
// model/priority.php
class Priority {
    private $id;
    private $reclamation_id;
    private $priority_level;     // critique, haute, normale, basse
    private $score;              // 0 à 100
    private $reason;             // texte expliquant pourquoi
    private $confidence;         // 0.00 à 1.00
    private $analyzed_at;
    private $is_manual = false;  // true si l'admin a forcé la priorité

    // --- Getters ---
    public function getId() { return $this->id; }
    public function getReclamationId() { return $this->reclamation_id; }
    public function getPriorityLevel() { return $this->priority_level; }
    public function getScore() { return $this->score; }
    public function getReason() { return $this->reason; }
    public function getConfidence() { return $this->confidence; }
    public function getAnalyzedAt() { return $this->analyzed_at; }
    public function isManual() { return $this->is_manual; }

    // --- Setters avec validation ---
    public function setReclamationId($id) {
        $id = (int)$id;
        if ($id <= 0) throw new InvalidArgumentException("ID réclamation invalide");
        $this->reclamation_id = $id;
    }

    public function setPriorityLevel($level) {
        $levels = ['critique', 'haute', 'normale', 'basse'];
        if (!in_array($level, $levels)) {
            throw new InvalidArgumentException("Niveau de priorité invalide");
        }
        $this->priority_level = $level;
    }

    public function setScore($score) {
        $score = (int)$score;
        if ($score < 0 || $score > 100) {
            throw new InvalidArgumentException("Score doit être entre 0 et 100");
        }
        $this->score = $score;
    }

    public function setReason($reason) {
        $this->reason = trim($reason);
    }

    public function setConfidence($confidence) {
        $confidence = (float)$confidence;
        if ($confidence < 0 || $confidence > 1) {
            throw new InvalidArgumentException("Confiance doit être entre 0 et 1");
        }
        $this->confidence = $confidence;
    }

    public function setManual($bool) {
        $this->is_manual = (bool)$bool;
    }

    public function setAnalyzedAt($date = null) {
        $this->analyzed_at = $date ?: date('Y-m-d H:i:s');
    }

    // --- Méthode utilitaire : couleur Bootstrap ---
    public function getBootstrapColor() {
        return match($this->priority_level) {
            'critique' => 'danger',
            'haute'    => 'warning',
            'normale'  => 'primary',
            'basse'    => 'secondary',
            default    => 'secondary'
        };
    }

    // --- Méthode utilitaire : icône ---
    public function getIcon() {
        return match($this->priority_level) {
            'critique' => 'bi-exclamation-triangle-fill',
            'haute'    => 'bi-exclamation-circle-fill',
            'normale'  => 'bi-clock-fill',
            'basse'    => 'bi-check-circle-fill',
            default    => 'bi-question-circle-fill'
        };
    }
}
?>