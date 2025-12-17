<?php
class Categorie {
    private ?int $id;
    private ?string $nom;
    private ?string $icon;
    private ?string $description;

    // Constructor
    public function __construct(
        ?int $id = null, 
        ?string $nom = null, 
        ?string $icon = null, 
        ?string $description = null
    ) {
        $this->id = $id;
        $this->nom = $nom;
        $this->icon = $icon;
        $this->description = $description;
    }

    // Getters
    public function getId(): ?int {
        return $this->id;
    }

    public function getNom(): ?string {
        return $this->nom;
    }

    public function getIcon(): ?string {
        return $this->icon;
    }

    public function getDescription(): ?string {
        return $this->description;
    }

    // Setters
    public function setId(?int $id): void {
        $this->id = $id;
    }

    public function setNom(?string $nom): void {
        $this->nom = $nom;
    }

    public function setIcon(?string $icon): void {
        $this->icon = $icon;
    }

    public function setDescription(?string $description): void {
        $this->description = $description;
    }
}
?>