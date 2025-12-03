<?php
/**
 * Inscription Model
 * Gère les inscriptions des participants aux événements
 *
 * IMPORTANT :
 * - Assure-toi que le nom de la table et les colonnes correspondent bien
 *   à ta base de données dans phpMyAdmin.
 * - Ici nous supposons une table `inscription` avec les colonnes :
 *   id_inscription (PK, auto-incrément), nom, prenom, adresse_mail,
 *   id_evenement, statut, date_inscription.
 */

require_once __DIR__ . '/../config/database.php';

class Inscription {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    /**
     * Créer une nouvelle inscription
     *
     * @param array $data
     * @return int|false ID de l'inscription en cas de succès, false sinon
     */
    public function create(array $data) {
        // IMPORTANT : la requête utilise les noms EXACTS de ta base :
        // table `inscription` et colonnes nom, prenom, adresse_mail,
        // id_evenement, statut, date_inscription.
        $sql = "INSERT INTO inscription (nom, prenom, adresse_mail, id_evenement, statut, date_inscription)
                VALUES (:nom, :prenom, :adresse_mail, :id_evenement, :statut, :date_inscription)";

        $stmt = $this->db->prepare($sql);

        // Si aucune date fournie, on met la date du jour (format YYYY-MM-DD)
        $dateInscription = $data['date_inscription'] ?? date('Y-m-d');

        $result = $stmt->execute([
            ':nom'             => $data['nom'],
            ':prenom'          => $data['prenom'],
            ':adresse_mail'    => $data['adresse_mail'],
            ':id_evenement'    => (int)$data['id_evenement'],
            ':statut'          => $data['statut'],
            ':date_inscription'=> $dateInscription,
        ]);

        return $result ? $this->db->lastInsertId() : false;
    }
}


