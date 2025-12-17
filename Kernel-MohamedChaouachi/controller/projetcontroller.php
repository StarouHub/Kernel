<?php
include_once(__DIR__ . '/../config.php');
include_once(__DIR__ . '/../model/projet.php');

class ProjetController {

    public function listProjets() {
        $sql = "SELECT p.*, 
                (SELECT COUNT(*) FROM projet_categorie pc WHERE pc.projet_id = p.id) as nb_categories
                FROM projet p 
                ORDER BY p.date_creation DESC";
        $db = config::getConnexion();
        try {
            $list = $db->query($sql);
            return $list->fetchAll();
        } catch (Exception $e) {
            die('Error:' . $e->getMessage());
        }
    }

    public function deleteProjet($id) {
        $sql = "DELETE FROM projet WHERE id = :id";
        $db = config::getConnexion();
        $req = $db->prepare($sql);
        $req->bindValue(':id', $id);
        try {
            // Supprimer d'abord les catégories associées
            $sqlCat = "DELETE FROM projet_categorie WHERE projet_id = :id";
            $reqCat = $db->prepare($sqlCat);
            $reqCat->bindValue(':id', $id);
            $reqCat->execute();
            
            // Puis supprimer le projet
            $req->execute();
        } catch (Exception $e) {
            die('Error:' . $e->getMessage());
        }
    }

    // MÉTHODE CORRIGÉE : accepte maintenant le tableau de catégories
    public function addProjet(Projet $projet, $selectedCategories = []) {
        $sql = "INSERT INTO projet (titre, description, budget_requis, budget_actuel, statut, date_creation, user_id) 
                VALUES (:titre, :description, :budget_requis, :budget_actuel, :statut, :date_creation, :user_id)";
        $db = config::getConnexion();
        try {
            $query = $db->prepare($sql);
            $query->execute([
                'titre' => $projet->getTitre(),
                'description' => $projet->getDescription(),
                'budget_requis' => $projet->getBudgetRequis(),
                'budget_actuel' => $projet->getBudgetActuel() ?? 0,
                'statut' => $projet->getStatut(),
                'date_creation' => $projet->getDateCreation() ? $projet->getDateCreation()->format('Y-m-d H:i:s') : date('Y-m-d H:i:s'),
                'user_id' => $projet->getUserId()
            ]);
            
            // Récupérer l'ID du projet créé
            $projet_id = $db->lastInsertId();
            
            // Ajouter les catégories associées
            if (!empty($selectedCategories)) {
                foreach ($selectedCategories as $categorie_id) {
                    $this->addProjetCategorie($projet_id, $categorie_id);
                }
            }
            
            return $projet_id;
        } catch (Exception $e) {
            echo 'Error: ' . $e->getMessage();
            return false;
        }
    }

    public function updateProjet(Projet $projet, $id) {
        try {
            $db = config::getConnexion();
            $query = $db->prepare(
                'UPDATE projet SET 
                    titre = :titre,
                    description = :description,
                    budget_requis = :budget_requis,
                    budget_actuel = :budget_actuel,
                    statut = :statut,
                    user_id = :user_id
                WHERE id = :id'
            );
            $query->execute([
                'id' => $id,
                'titre' => $projet->getTitre(),
                'description' => $projet->getDescription(),
                'budget_requis' => $projet->getBudgetRequis(),
                'budget_actuel' => $projet->getBudgetActuel(),
                'statut' => $projet->getStatut(),
                'user_id' => $projet->getUserId()
            ]);
            return true;
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
            return false;
        }
    }

    public function showProjet($id) {
        $sql = "SELECT * FROM projet WHERE id = :id";
        $db = config::getConnexion();
        $query = $db->prepare($sql);
        $query->bindValue(':id', $id);

        try {
            $query->execute();
            $projet = $query->fetch();
            return $projet;
        } catch(Exception $e) {
            die('Error: '. $e->getMessage());
        }
    }

    public function addProjetCategorie($projet_id, $categorie_id) {
        $sql = "INSERT INTO projet_categorie (projet_id, categorie_id) VALUES (:projet_id, :categorie_id)";
        $db = config::getConnexion();
        try {
            $query = $db->prepare($sql);
            $query->execute([
                'projet_id' => $projet_id,
                'categorie_id' => $categorie_id
            ]);
            return true;
        } catch (Exception $e) {
            echo 'Error: ' . $e->getMessage();
            return false;
        }
    }

    public function getProjetCategories($projet_id) {
        $sql = "SELECT c.* FROM categorie c 
                INNER JOIN projet_categorie pc ON c.id = pc.categorie_id 
                WHERE pc.projet_id = :projet_id";
        $db = config::getConnexion();
        try {
            $query = $db->prepare($sql);
            $query->bindValue(':projet_id', $projet_id);
            $query->execute();
            return $query->fetchAll();
        } catch (Exception $e) {
            die('Error:' . $e->getMessage());
        }
    }

    public function searchProjets($keyword) {
        $sql = "SELECT p.* FROM projet p 
                WHERE p.titre LIKE :keyword 
                OR p.description LIKE :keyword 
                ORDER BY p.date_creation DESC";
        $db = config::getConnexion();
        try {
            $query = $db->prepare($sql);
            $query->execute(['keyword' => '%' . $keyword . '%']);
            return $query->fetchAll();
        } catch (Exception $e) {
            die('Error:' . $e->getMessage());
        }
    }

    public function getProjetsByStatut($statut) {
        $sql = "SELECT * FROM projet WHERE statut = :statut ORDER BY date_creation DESC";
        $db = config::getConnexion();
        try {
            $query = $db->prepare($sql);
            $query->execute(['statut' => $statut]);
            return $query->fetchAll();
        } catch (Exception $e) {
            die('Error:' . $e->getMessage());
        }
    }

    public function countProjets() {
        $sql = "SELECT COUNT(*) as total FROM projet";
        $db = config::getConnexion();
        try {
            $result = $db->query($sql);
            return $result->fetch()['total'];
        } catch (Exception $e) {
            die('Error:' . $e->getMessage());
        }
    }
}
?>