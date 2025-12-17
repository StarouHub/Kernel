<?php
/**
 * Inscription Model
 * Représente une entité Inscription avec ses propriétés
 * Les opérations CRUD sont gérées dans le contrôleur
 *
 * IMPORTANT :
 * - Assure-toi que le nom de la table et les colonnes correspondent bien
 *   à ta base de données dans phpMyAdmin.
 * - Ici nous supposons une table `inscription` avec les colonnes :
 *   id_inscription (PK, auto-incrément), nom, prenom, adresse_mail,
 *   id_evenement, statut, date_inscription.
 */

class Inscription {
    // Propriétés de l'inscription
    private $id_inscription;
    private $nom;
    private $prenom;
    private $adresse_mail;
    private $id_evenement;
    private $statut;
    private $date_inscription;
    
    // Statut constants
    const STATUT_EN_ATTENTE = 'en_attente';
    const STATUT_CONFIRME = 'confirme';
    const STATUT_ANNULE = 'annule';
    
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
        if (isset($data['id_inscription'])) {
            $this->setIdInscription((int)$data['id_inscription']);
        }
        if (isset($data['nom'])) {
            $this->setNom($data['nom']);
        }
        if (isset($data['prenom'])) {
            $this->setPrenom($data['prenom']);
        }
        if (isset($data['adresse_mail'])) {
            $this->setAdresseMail($data['adresse_mail']);
        }
        if (isset($data['id_evenement'])) {
            $this->setIdEvenement((int)$data['id_evenement']);
        }
        if (isset($data['statut'])) {
            $this->setStatut($data['statut']);
        }
        if (isset($data['date_inscription'])) {
            $this->setDateInscription($data['date_inscription']);
        }
    }
    
    /**
     * Convertit l'objet en tableau
     * 
     * @return array
     */
    public function toArray(): array {
        return [
            'id_inscription' => $this->id_inscription,
            'nom' => $this->nom,
            'prenom' => $this->prenom,
            'adresse_mail' => $this->adresse_mail,
            'id_evenement' => $this->id_evenement,
            'statut' => $this->statut,
            'date_inscription' => $this->date_inscription,
        ];
    }
    
    // Getters and Setters
    
    /**
     * @return int|null
     */
    public function getIdInscription(): ?int {
        return $this->id_inscription;
    }
    
    /**
     * @param int $id_inscription
     * @return self
     */
    public function setIdInscription(int $id_inscription): self {
        $this->id_inscription = $id_inscription;
        return $this;
    }
    
    /**
     * @return string|null
     */
    public function getNom(): ?string {
        return $this->nom;
    }
    
    /**
     * @param string $nom
     * @return self
     */
    public function setNom(string $nom): self {
        $this->nom = trim($nom);
        return $this;
    }
    
    /**
     * @return string|null
     */
    public function getPrenom(): ?string {
        return $this->prenom;
    }
    
    /**
     * @param string $prenom
     * @return self
     */
    public function setPrenom(string $prenom): self {
        $this->prenom = trim($prenom);
        return $this;
    }
    
    /**
     * @return string|null
     */
    public function getAdresseMail(): ?string {
        return $this->adresse_mail;
    }
    
    /**
     * @param string $adresse_mail
     * @return self
     */
    public function setAdresseMail(string $adresse_mail): self {
        $this->adresse_mail = strtolower(trim($adresse_mail));
        return $this;
    }
    
    /**
     * @return int|null
     */
    public function getIdEvenement(): ?int {
        return $this->id_evenement;
    }
    
    /**
     * @param int $id_evenement
     * @return self
     */
    public function setIdEvenement(int $id_evenement): self {
        $this->id_evenement = $id_evenement;
        return $this;
    }
    
    /**
     * @return string|null
     */
    public function getStatut(): ?string {
        return $this->statut;
    }
    
    /**
     * @param string $statut
     * @return self
     */
    public function setStatut(string $statut): self {
        $validStatuts = [self::STATUT_EN_ATTENTE, self::STATUT_CONFIRME, self::STATUT_ANNULE];
        if (in_array($statut, $validStatuts)) {
            $this->statut = $statut;
        }
        return $this;
    }
    
    /**
     * @return string|null
     */
    public function getDateInscription(): ?string {
        return $this->date_inscription;
    }
    
    /**
     * @param string $date_inscription
     * @return self
     */
    public function setDateInscription(string $date_inscription): self {
        $this->date_inscription = $date_inscription;
        return $this;
    }
}
