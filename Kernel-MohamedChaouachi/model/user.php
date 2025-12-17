<?php

class User
{
    private ?int $id = null;
    private string $nom;
    private string $prenom;
    private string $email;
    private string $telephone;
    private string $password;        
    private string $role;
    private ?string $banned_until = null;
    private ?string $date_inscription = null;

    public function __construct(
        string $nom = '',
        string $prenom = '',
        string $email = '',
        string $telephone = '',
        string $password = '',
        string $role = 'user',   
        ?int $id = null,
        ?string $banned_until = null,
        ?string $date_inscription = null
    ) {
        $this->id = $id;
        $this->nom = $nom;
        $this->prenom = $prenom;
        $this->email = $email;
        $this->telephone = $telephone;
        $this->password = $password;
        $this->role = $role;
        $this->banned_until = $banned_until;
        $this->date_inscription = $date_inscription;
    }

    // GETTERS
    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNom(): string
    {
        return $this->nom;
    }

    public function getPrenom(): string
    {
        return $this->prenom;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getTelephone(): string
    {
        return $this->telephone;
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    public function getRole(): string
    {
        return $this->role;
    }

    public function getBannedUntil(): ?string
    {
        return $this->banned_until;
    }

    public function getDateInscription(): ?string
    {
        return $this->date_inscription;
    }

    public function isAdmin(): bool
    {
        return in_array($this->role, ['Administrateur', 'admin']);
    }

    public function isUser(): bool
    {
        return $this->role === 'user';
    }

    public function isBanned(): bool
    {
        if ($this->banned_until === null) {
            return false;
        }
        return strtotime($this->banned_until) > time();
    }

    // SETTERS
    public function setId(?int $id): void
    {
        $this->id = $id;
    }

    public function setNom(string $nom): void
    {
        $this->nom = $nom;
    }

    public function setPrenom(string $prenom): void
    {
        $this->prenom = $prenom;
    }

    public function setEmail(string $email): void
    {
        $this->email = $email;
    }

    public function setTelephone(string $telephone): void
    {
        $this->telephone = $telephone;
    }

    public function setPassword(string $password): void
    {
        $this->password = $password;
    }

    public function setRole(string $role): void
    {
        $validRoles = ['visiteur', 'user', 'innovateur', 'Investisseur', 'Administrateur', 'admin'];
        if (in_array($role, $validRoles)) {
            $this->role = $role;
        } else {
            throw new InvalidArgumentException("Rôle invalide: " . $role);
        }
    }

    public function setBannedUntil(?string $banned_until): void
    {
        $this->banned_until = $banned_until;
    }

    public function setDateInscription(?string $date_inscription): void
    {
        $this->date_inscription = $date_inscription;
    }
}