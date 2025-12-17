<?php
class PieceJointe {
    private $id;
    private $reclamation_id;
    private $reponse_id;
    private $nom_original;
    private $chemin;
    private $taille_octets;
    private $date_upload;
    private $type_mime;

    // Getters
    public function getId() { return $this->id; }
    public function getReclamationId() { return $this->reclamation_id; }
    public function getReponseId() { return $this->reponse_id; }
    public function getNomOriginal() { return $this->nom_original; }
    public function getChemin() { return $this->chemin; }
    public function getTailleOctets() { return $this->taille_octets; }
    public function getDateUpload() { return $this->date_upload; }
    public function getTypeMime() { return $this->type_mime; }

    // Setters
    public function setNomOriginal($n) { $this->nom_original = $n; }
    public function setChemin($c) { $this->chemin = $c; }
    public function setTailleOctets($t) { $this->taille_octets = $t; }
    public function setTypeMime($t) { $this->type_mime = $t; }
    public function setReclamationId($id) { $this->reclamation_id = $id; }
    public function setReponseId($id) { $this->reponse_id = $id; }
}
?>