<?php
include_once(__DIR__ . '/../config.php');
include_once(__DIR__ . '/../model/categorie.php');

class CategorieController {

    // Lister toutes les catégories
    public function listCategories() {
        $sql = "SELECT * FROM categorie ORDER BY nom ASC";
        $db = config::getConnexion();
        try {
            $list = $db->query($sql);
            return $list->fetchAll();
        } catch (Exception $e) {
            die('Error: ' . $e->getMessage());
        }
    }

    // Ajouter une catégorie
    public function addCategorie(Categorie $categorie) {
        $sql = "INSERT INTO categorie VALUES (NULL, :nom, :icon, :description)";
        $db = config::getConnexion();
        try {
            $query = $db->prepare($sql);
            $query->execute([
                'nom' => $categorie->getNom(),
                'icon' => $categorie->getIcon(),
                'description' => $categorie->getDescription()
            ]);
            return true;
        } catch (Exception $e) {
            echo 'Error: ' . $e->getMessage();
            return false;
        }
    }

    // Mettre à jour une catégorie
    public function updateCategorie(Categorie $categorie, $id) {
        try {
            $db = config::getConnexion();
            $query = $db->prepare(
                'UPDATE categorie SET 
                    nom = :nom,
                    icon = :icon,
                    description = :description
                WHERE id = :id'
            );
            $query->execute([
                'id' => $id,
                'nom' => $categorie->getNom(),
                'icon' => $categorie->getIcon(),
                'description' => $categorie->getDescription()
            ]);
            return true;
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
            return false;
        }
    }

    // Supprimer une catégorie
    public function deleteCategorie($id) {
        $db = config::getConnexion();
        try {
            // Supprimer d'abord les relations
            $sqlRelations = "DELETE FROM projet_categorie WHERE categorie_id = :id";
            $reqRelations = $db->prepare($sqlRelations);
            $reqRelations->bindValue(':id', $id);
            $reqRelations->execute();
            
            // Puis supprimer la catégorie
            $sql = "DELETE FROM categorie WHERE id = :id";
            $req = $db->prepare($sql);
            $req->bindValue(':id', $id);
            $req->execute();
            return true;
        } catch (Exception $e) {
            die('Error: ' . $e->getMessage());
        }
    }

    // Afficher une catégorie par ID
    public function showCategorie($id) {
        $sql = "SELECT * FROM categorie WHERE id = :id";
        $db = config::getConnexion();
        $query = $db->prepare($sql);
        $query->bindValue(':id', $id);
        
        try {
            $query->execute();
            $categorie = $query->fetch();
            return $categorie;
        } catch (Exception $e) {
            die('Error: ' . $e->getMessage());
        }
    }

    // Compter les projets par catégorie
    public function countProjetsByCategorie($categorieId) {
        $sql = "SELECT COUNT(*) as total FROM projet_categorie WHERE categorie_id = :categorie_id";
        $db = config::getConnexion();
        $query = $db->prepare($sql);
        $query->bindValue(':categorie_id', $categorieId);
        
        try {
            $query->execute();
            $result = $query->fetch();
            return $result['total'];
        } catch (Exception $e) {
            die('Error: ' . $e->getMessage());
        }
    }
}
?>