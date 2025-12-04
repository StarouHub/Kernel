<?php
require_once __DIR__ . '/../models/Sujet.php';
require_once __DIR__ . '/../models/Categorie.php';
require_once __DIR__ . '/../helpers/auth.php';

class SujetController {
    private $model;
    private $categorieModel;
    
    public function __construct() {
        $this->model = new Sujet();
        $this->categorieModel = new Categorie();
        switchRole();
    }
    
    public function index() {
        $categorie_id = $_GET['categorie_id'] ?? null;
        
        if ($categorie_id) {
            $sujets = $this->model->getByCategorie($categorie_id);
        } else {
            $sujets = $this->model->getAll();
        }
        
        $categories = $this->categorieModel->getAll();
        
        // Get count for each category
        $categoryCounts = [];
        foreach ($categories as $cat) {
            $categoryCounts[$cat['id']] = $this->model->getCountByCategorie($cat['id']);
        }
        
        require_once __DIR__ . '/../views/sujet/index.php';
    }
    
    public function show() {
        $id = $_GET['id'] ?? 0;
        $sujet = $this->model->getById($id);
        
        if (!$sujet) {
            header('Location: index.php?controller=sujet&action=index');
            exit;
        }
        
        require_once __DIR__ . '/../models/Reponse.php';
        $reponseModel = new Reponse();
        $reponses = $reponseModel->getBySujet($id);
        
        require_once __DIR__ . '/../views/sujet/show.php';
    }
    
    public function create() {
        $categories = $this->categorieModel->getAll();
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $titre = trim($_POST['titre'] ?? '');
            $contenu = trim($_POST['contenu'] ?? '');
            $categorie_id = $_POST['categorie_id'] ?? 0;
            
            $errors = [];
            if (empty($titre)) {
                $errors[] = "Le titre est requis";
            }
            if (empty($contenu)) {
                $errors[] = "Le contenu est requis";
            }
            if (empty($categorie_id) || $categorie_id == 0) {
                $errors[] = "La catégorie est requise";
            }
            
            if (empty($errors)) {
                $user_id = getUserId();
                if ($this->model->create($titre, $contenu, $categorie_id, $user_id)) {
                    header('Location: index.php?controller=sujet&action=index');
                    exit;
                } else {
                    $errors[] = "Erreur lors de la création";
                }
            }
        }
        
        require_once __DIR__ . '/../views/sujet/create.php';
    }
    
    public function edit() {
        $id = $_GET['id'] ?? 0;
        $sujet = $this->model->getById($id);
        
        if (!$sujet) {
            header('Location: index.php?controller=sujet&action=index');
            exit;
        }
        
        // Check permissions
        $sujet_user_id = isset($_SESSION['sujet_owners'][$id]) ? $_SESSION['sujet_owners'][$id] : ($sujet['user_id'] ?? null);
        if (!canEditSujet($sujet_user_id)) {
            $_SESSION['error'] = "Vous n'avez pas la permission de modifier ce sujet.";
            header('Location: index.php?controller=sujet&action=index');
            exit;
        }
        
        $categories = $this->categorieModel->getAll();
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $titre = trim($_POST['titre'] ?? '');
            $contenu = trim($_POST['contenu'] ?? '');
            $categorie_id = $_POST['categorie_id'] ?? 0;
            
            $errors = [];
            if (empty($titre)) {
                $errors[] = "Le titre est requis";
            }
            if (empty($contenu)) {
                $errors[] = "Le contenu est requis";
            }
            if (empty($categorie_id) || $categorie_id == 0) {
                $errors[] = "La catégorie est requise";
            }
            
            if (empty($errors)) {
                if ($this->model->update($id, $titre, $contenu, $categorie_id)) {
                    header('Location: index.php?controller=sujet&action=index');
                    exit;
                } else {
                    $errors[] = "Erreur lors de la mise à jour";
                }
            }
        }
        
        require_once __DIR__ . '/../views/sujet/edit.php';
    }
    
    public function delete() {
        $id = $_GET['id'] ?? 0;
        $sujet = $this->model->getById($id);
        
        if (!$sujet) {
            header('Location: index.php?controller=sujet&action=index');
            exit;
        }
        
        // Check permissions
        $sujet_user_id = isset($_SESSION['sujet_owners'][$id]) ? $_SESSION['sujet_owners'][$id] : ($sujet['user_id'] ?? null);
        if (!canEditSujet($sujet_user_id)) {
            $_SESSION['error'] = "Vous n'avez pas la permission de supprimer ce sujet.";
            header('Location: index.php?controller=sujet&action=index');
            exit;
        }
        
        // Remove from session mapping
        if (isset($_SESSION['sujet_owners'][$id])) {
            unset($_SESSION['sujet_owners'][$id]);
        }
        
        if ($id > 0) {
            $this->model->delete($id);
        }
        header('Location: index.php?controller=sujet&action=index');
        exit;
    }
}
?>
