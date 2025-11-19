<?php

class Actualite
{
    private ?int $id;
    private string $titre;
    private string $contenu;
    private DateTime $date_publication;
    private string $type;
    private int $projet_id;

    // Constructor
    public function __construct(
        ?int $id,
        string $titre,
        string $contenu,
        DateTime $date_publication,
        string $type,
        int $projet_id
    ) {
        $this->id = $id;
        $this->titre = $titre;
        $this->contenu = $contenu;
        $this->date_publication = $date_publication;
        $this->type = $type;
        $this->projet_id = $projet_id;
    }

    // Getters
    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitre(): string
    {
        return $this->titre;
    }

    public function getContenu(): string
    {
        return $this->contenu;
    }

    public function getDatePublication(): DateTime
    {
        return $this->date_publication;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function getProjetId(): int
    {
        return $this->projet_id;
    }

    // Setters
    public function setId(?int $id): void
    {
        $this->id = $id;
    }

    public function setTitre(string $titre): void
    {
        $this->titre = $titre;
    }

    public function setContenu(string $contenu): void
    {
        $this->contenu = $contenu;
    }

    public function setDatePublication(DateTime $date_publication): void
    {
        $this->date_publication = $date_publication;
    }

    public function setType(string $type): void
    {
        $this->type = $type;
    }

    public function setProjetId(int $projet_id): void
    {
        $this->projet_id = $projet_id;
    }
}
?>
