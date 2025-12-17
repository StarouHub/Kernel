<?php
class Projet {
    private ?int $id;
    private ?string $titre;
    private ?string $description;
    private ?float $budget_requis;
    private ?float $budget_actuel;
    private ?string $statut;
    private ?DateTime $date_creation;
    private ?int $user_id;

    public function __construct(
        ?int $id = null,
        ?string $titre = null,
        ?string $description = null,
        ?float $budget_requis = null,
        ?float $budget_actuel = null,
        ?string $statut = null,
        ?DateTime $date_creation = null,
        ?int $user_id = null
    ) {
        $this->id = $id;
        $this->titre = $titre;
        $this->description = $description;
        $this->budget_requis = $budget_requis;
        $this->budget_actuel = $budget_actuel;
        $this->statut = $statut;
        $this->date_creation = $date_creation;
        $this->user_id = $user_id;
    }

    // Getters
    public function getId(): ?int {
        return $this->id;
    }

    public function getTitre(): ?string {
        return $this->titre;
    }

    public function getDescription(): ?string {
        return $this->description;
    }

    public function getBudgetRequis(): ?float {
        return $this->budget_requis;
    }

    public function getBudgetActuel(): ?float {
        return $this->budget_actuel;
    }

    public function getStatut(): ?string {
        return $this->statut;
    }

    public function getDateCreation(): ?DateTime {
        return $this->date_creation;
    }

    public function getUserId(): ?int {
        return $this->user_id;
    }

    // Setters
    public function setId(?int $id): void {
        $this->id = $id;
    }

    public function setTitre(?string $titre): void {
        $this->titre = $titre;
    }

    public function setDescription(?string $description): void {
        $this->description = $description;
    }

    public function setBudgetRequis(?float $budget_requis): void {
        $this->budget_requis = $budget_requis;
    }

    public function setBudgetActuel(?float $budget_actuel): void {
        $this->budget_actuel = $budget_actuel;
    }

    public function setStatut(?string $statut): void {
        $this->statut = $statut;
    }

    public function setDateCreation(?DateTime $date_creation): void {
        $this->date_creation = $date_creation;
    }

    public function setUserId(?int $user_id): void {
        $this->user_id = $user_id;
    }
}
?>