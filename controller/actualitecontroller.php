<?php
include_once(__DIR__ . '/../config.php');
include_once(__DIR__ . '/../model/actualite.php');

class ActualiteController
{
    // ========== JOINTURE : Afficher les actualités d'un projet ==========
    public function afficherActualites($projet_id)
    {
        $sql = "SELECT a.*, p.titre as projet_titre 
                FROM actualite a 
                INNER JOIN projet p ON a.projet_id = p.id 
                WHERE a.projet_id = :projet_id 
                ORDER BY a.date_publication DESC";
        
        $db = config::getConnexion();
        try {
            $query = $db->prepare($sql);
            $query->bindValue(':projet_id', $projet_id, PDO::PARAM_INT);
            $query->execute();
            return $query->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            die('Error: ' . $e->getMessage());
        }
    }

    // ========== CREATE : Ajouter une actualité ==========
    public function addActualite(Actualite $actualite)
    {
        $sql = "INSERT INTO actualite (titre, contenu, date_publication, type, projet_id) 
                VALUES (:titre, :contenu, :date_publication, :type, :projet_id)";
        
        $db = config::getConnexion();
        try {
            $query = $db->prepare($sql);
            $query->execute([
                'titre' => $actualite->getTitre(),
                'contenu' => $actualite->getContenu(),
                'date_publication' => $actualite->getDatePublication()->format('Y-m-d H:i:s'),
                'type' => $actualite->getType(),
                'projet_id' => $actualite->getProjetId()
            ]);
            return true;
        } catch (Exception $e) {
            echo 'Error: ' . $e->getMessage();
            return false;
        }
    }

    // ========== READ : Liste toutes les actualités ==========
    public function listActualites()
    {
        $sql = "SELECT a.*, p.titre as projet_titre 
                FROM actualite a 
                INNER JOIN projet p ON a.projet_id = p.id 
                ORDER BY a.date_publication DESC";
        
        $db = config::getConnexion();
        try {
            $list = $db->query($sql);
            return $list->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            die('Error: ' . $e->getMessage());
        }
    }

    // ========== READ : Afficher une actualité spécifique ==========
    public function showActualite($id)
    {
        $sql = "SELECT a.*, p.titre as projet_titre 
                FROM actualite a 
                INNER JOIN projet p ON a.projet_id = p.id 
                WHERE a.id = :id";
        
        $db = config::getConnexion();
        $query = $db->prepare($sql);
        $query->bindValue(':id', $id, PDO::PARAM_INT);

        try {
            $query->execute();
            return $query->fetch(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            die('Error: ' . $e->getMessage());
        }
    }

    // ========== UPDATE : Modifier une actualité ==========
    public function updateActualite(Actualite $actualite, $id)
    {
        try {
            $db = config::getConnexion();
            $query = $db->prepare(
                'UPDATE actualite SET 
                    titre = :titre,
                    contenu = :contenu,
                    type = :type,
                    projet_id = :projet_id
                WHERE id = :id'
            );
            $query->execute([
                'id' => $id,
                'titre' => $actualite->getTitre(),
                'contenu' => $actualite->getContenu(),
                'type' => $actualite->getType(),
                'projet_id' => $actualite->getProjetId()
            ]);
            return true;
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
            return false;
        }
    }

    // ========== DELETE : Supprimer une actualité ==========
    public function deleteActualite($id)
    {
        $sql = "DELETE FROM actualite WHERE id = :id";
        $db = config::getConnexion();
        $req = $db->prepare($sql);
        $req->bindValue(':id', $id, PDO::PARAM_INT);
        
        try {
            $req->execute();
            return true;
        } catch (Exception $e) {
            die('Error: ' . $e->getMessage());
        }
    }

    // ========== Compter les actualités d'un projet ==========
    public function countActualitesByProjet($projet_id)
    {
        $sql = "SELECT COUNT(*) as total FROM actualite WHERE projet_id = :projet_id";
        $db = config::getConnexion();
        try {
            $query = $db->prepare($sql);
            $query->bindValue(':projet_id', $projet_id, PDO::PARAM_INT);
            $query->execute();
            $result = $query->fetch(PDO::FETCH_ASSOC);
            return $result['total'];
        } catch (Exception $e) {
            die('Error: ' . $e->getMessage());
        }
    }

    // ========== Rechercher des actualités ==========
    public function searchActualites($keyword)
    {
        $sql = "SELECT a.*, p.titre as projet_titre 
                FROM actualite a 
                INNER JOIN projet p ON a.projet_id = p.id 
                WHERE a.titre LIKE :keyword 
                OR a.contenu LIKE :keyword 
                ORDER BY a.date_publication DESC";
        
        $db = config::getConnexion();
        try {
            $query = $db->prepare($sql);
            $query->execute(['keyword' => '%' . $keyword . '%']);
            return $query->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            die('Error: ' . $e->getMessage());
        }
    }
}
?>
