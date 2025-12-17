<?php
require_once __DIR__ . '/../models/Categorie.php';
require_once __DIR__ . '/../helpers/auth.php';

class CategorieController {
    private $model;
    
    public function __construct() {
        $this->model = new Categorie();
        switchRole();
    }
    
    public function index() {
        requireAdmin();
        $categories = $this->model->getAll();
        require_once __DIR__ . '/../views/categorie/index.php';
    }
    
    public function create() {
        requireAdmin();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $name = trim($_POST['name'] ?? '');
            $color = $_POST['color'] ?? '#2563EB';
            
            if (empty($name)) {
                $error = "Le nom de la catégorie est requis";
            } else {
                if ($this->model->create($name, $color)) {
                    header('Location: index.php?controller=admin&action=categories');
                    exit;
                } else {
                    $error = "Erreur lors de la création";
                }
            }
        }
        require_once __DIR__ . '/../views/categorie/create.php';
    }
    
    public function edit() {
        requireAdmin();
        $id = $_GET['id'] ?? 0;
        $categorie = $this->model->getById($id);
        
        if (!$categorie) {
            header('Location: index.php?controller=admin&action=categories');
            exit;
        }
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $name = trim($_POST['name'] ?? '');
            $color = $_POST['color'] ?? ($categorie['color'] ?? '#2563EB');
            
            if (empty($name)) {
                $error = "Le nom de la catégorie est requis";
            } else {
                if ($this->model->update($id, $name, $color)) {
                    header('Location: index.php?controller=admin&action=categories');
                    exit;
                } else {
                    $error = "Erreur lors de la mise à jour";
                }
            }
        }
        
        require_once __DIR__ . '/../views/categorie/edit.php';
    }
    
    public function delete() {
        requireAdmin();
        $id = $_GET['id'] ?? 0;
        if ($id > 0) {
            $this->model->delete($id);
        }
        header('Location: index.php?controller=admin&action=categories');
        exit;
    }
}
?>
