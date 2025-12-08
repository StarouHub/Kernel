<?php
/**
 * Evenement Model
 * Représente une entité Événement avec ses propriétés
 * Les opérations CRUD sont gérées dans le contrôleur
 */

class Evenement {
    // Propriétés de l'événement
    private $id;
    private $titre;
    private $type;
    private $date;
    private $lieu;
    private $capacite;
    private $user_id;
    private $description;
    private $created_at;
    
    /**
     * Constructor
     * 
     * @param array|null $data Données pour initialiser l'objet
     */
    public function __construct(?array $data = null) {
        if ($data !== null) {
            $this->hydrate($data);
        }
    }
    
    /**
     * Hydrate l'objet avec des données
     * 
     * @param array $data
     * @return void
     */
    public function hydrate(array $data): void {
        if (isset($data['id'])) {
            $this->setId((int)$data['id']);
        }
        if (isset($data['titre'])) {
            $this->setTitre($data['titre']);
        }
        if (isset($data['type'])) {
            $this->setType($data['type']);
        }
        if (isset($data['date'])) {
            $this->setDate($data['date']);
        }
        if (isset($data['lieu'])) {
            $this->setLieu($data['lieu']);
        }
        if (isset($data['capacite'])) {
            $this->setCapacite((int)$data['capacite']);
        }
        if (isset($data['user_id'])) {
            $this->setUserId((int)$data['user_id']);
        }
        if (isset($data['description'])) {
            $this->setDescription($data['description']);
        }
        if (isset($data['created_at'])) {
            $this->setCreatedAt($data['created_at']);
        }
    }
    
    /**
     * Convertit l'objet en tableau
     * 
     * @return array
     */
    public function toArray(): array {
        return [
            'id' => $this->id,
            'titre' => $this->titre,
            'type' => $this->type,
            'date' => $this->date,
            'lieu' => $this->lieu,
            'capacite' => $this->capacite,
            'user_id' => $this->user_id,
            'description' => $this->description,
            'created_at' => $this->created_at,
        ];
    }
    
    // Getters and Setters
    
    /**
     * @return int|null
     */
    public function getId(): ?int {
        return $this->id;
    }
    
    /**
     * @param int $id
     * @return self
     */
    public function setId(int $id): self {
        $this->id = $id;
        return $this;
    }
    
    /**
     * @return string|null
     */
    public function getTitre(): ?string {
        return $this->titre;
    }
    
    /**
     * @param string $titre
     * @return self
     */
    public function setTitre(string $titre): self {
        $this->titre = trim($titre);
        return $this;
    }
    
    /**
     * @return string|null
     */
    public function getType(): ?string {
        return $this->type;
    }
    
    /**
     * @param string $type
     * @return self
     */
    public function setType(string $type): self {
        $this->type = $type;
        return $this;
    }
    
    /**
     * @return string|null
     */
    public function getDate(): ?string {
        return $this->date;
    }
    
    /**
     * @param string $date
     * @return self
     */
    public function setDate(string $date): self {
        $this->date = $date;
        return $this;
    }
    
    /**
     * @return string|null
     */
    public function getLieu(): ?string {
        return $this->lieu;
    }
    
    /**
     * @param string $lieu
     * @return self
     */
    public function setLieu(string $lieu): self {
        $this->lieu = trim($lieu);
        return $this;
    }
    
    /**
     * @return int|null
     */
    public function getCapacite(): ?int {
        return $this->capacite;
    }
    
    /**
     * @param int $capacite
     * @return self
     */
    public function setCapacite(int $capacite): self {
        $this->capacite = $capacite;
        return $this;
    }
    
    /**
     * @return int|null
     */
    public function getUserId(): ?int {
        return $this->user_id;
    }
    
    /**
     * @param int $user_id
     * @return self
     */
    public function setUserId(int $user_id): self {
        $this->user_id = $user_id;
        return $this;
    }
    
    /**
     * @return string|null
     */
    public function getDescription(): ?string {
        return $this->description;
    }
    
    /**
     * @param string $description
     * @return self
     */
    public function setDescription(string $description): self {
        $this->description = trim($description);
        return $this;
    }
    
    /**
     * @return string|null
     */
    public function getCreatedAt(): ?string {
        return $this->created_at;
    }
    
    /**
     * @param string $created_at
     * @return self
     */
    public function setCreatedAt(string $created_at): self {
        $this->created_at = $created_at;
        return $this;
    }
    
    /**
     * Format date from YYYY-MM-DD to DD/MM/YYYY for display
     * 
     * @param string $date
     * @return string
     */
    public static function formatDateForDisplay(string $date): string {
        if (empty($date)) {
            return '';
        }
        
        $timestamp = strtotime($date);
        if ($timestamp === false) {
            return $date;
        }
        
        return date('d/m/Y', $timestamp);
    }
    
    /**
     * Get formatted date with day name
     * 
     * @param string $date
     * @return string
     */
    public static function formatDateWithDay(string $date): string {
        if (empty($date)) {
            return '';
        }
        
        $timestamp = strtotime($date);
        if ($timestamp === false) {
            return $date;
        }
        
        $days = ['Dimanche', 'Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi', 'Samedi'];
        $months = ['', 'Janvier', 'Février', 'Mars', 'Avril', 'Mai', 'Juin', 
                   'Juillet', 'Août', 'Septembre', 'Octobre', 'Novembre', 'Décembre'];
        
        $dayName = $days[date('w', $timestamp)];
        $day = date('d', $timestamp);
        $month = $months[(int)date('m', $timestamp)];
        $year = date('Y', $timestamp);
        
        return "$dayName $day $month $year";
    }
}
