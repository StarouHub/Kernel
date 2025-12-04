<?php
require_once __DIR__ . '/../models/Reponse.php';
require_once __DIR__ . '/../models/Sujet.php';
require_once __DIR__ . '/../helpers/auth.php';

class ReponseController {
    private $model;
    private $sujetModel;
    
    public function __construct() {
        $this->model = new Reponse();
        $this->sujetModel = new Sujet();
        switchRole();
    }
    
    public function index() {
        requireAdmin();
        $sujet_id = $_GET['sujet_id'] ?? null;
        
        if ($sujet_id) {
            $reponses = $this->model->getBySujet($sujet_id);
        } else {
            $reponses = $this->model->getAll();
        }
        
        require_once __DIR__ . '/../views/reponse/index.php';
    }
    
    public function create() {
        $sujet_id = $_GET['sujet_id'] ?? 0;
        $sujet = $this->sujetModel->getById($sujet_id);
        
        if (!$sujet) {
            header('Location: index.php?controller=sujet&action=index');
            exit;
        }
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $contenu = trim($_POST['contenu'] ?? '');
            $sujet_id = $_POST['sujet_id'] ?? 0;
            
            if (empty($contenu)) {
                $error = "Le contenu est requis";
            } else {
                $user_id = getUserId();
                if ($this->model->create($contenu, $sujet_id, $user_id)) {
                    header('Location: index.php?controller=sujet&action=show&id=' . $sujet_id);
                    exit;
                } else {
                    $error = "Erreur lors de la création";
                }
            }
        }
        
        require_once __DIR__ . '/../views/reponse/create.php';
    }
    
    public function edit() {
        $id = $_GET['id'] ?? 0;
        $reponse = $this->model->getById($id);
        
        if (!$reponse) {
            if (isAdmin()) {
                header('Location: index.php?controller=admin&action=reponses');
            } else {
                header('Location: index.php?controller=sujet&action=index');
            }
            exit;
        }
        
        // Check permissions
        $reponse_user_id = isset($_SESSION['reponse_owners'][$id]) ? $_SESSION['reponse_owners'][$id] : ($reponse['user_id'] ?? null);
        if (!canEditReponse($reponse_user_id)) {
            $_SESSION['error'] = "Vous n'avez pas la permission de modifier cette réponse.";
            header('Location: index.php?controller=sujet&action=show&id=' . $reponse['sujet_id']);
            exit;
        }
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $contenu = trim($_POST['contenu'] ?? '');
            
            if (empty($contenu)) {
                $error = "Le contenu est requis";
            } else {
                if ($this->model->update($id, $contenu)) {
                    header('Location: index.php?controller=sujet&action=show&id=' . $reponse['sujet_id']);
                    exit;
                } else {
                    $error = "Erreur lors de la mise à jour";
                }
            }
        }
        
        require_once __DIR__ . '/../views/reponse/edit.php';
    }
    
    public function delete() {
        $id = $_GET['id'] ?? 0;
        $reponse = $this->model->getById($id);
        
        if (!$reponse) {
            header('Location: index.php?controller=reponse&action=index');
            exit;
        }
        
        // Check permissions
        $reponse_user_id = isset($_SESSION['reponse_owners'][$id]) ? $_SESSION['reponse_owners'][$id] : ($reponse['user_id'] ?? null);
        if (!canEditReponse($reponse_user_id)) {
            $_SESSION['error'] = "Vous n'avez pas la permission de supprimer cette réponse.";
            header('Location: index.php?controller=sujet&action=show&id=' . $reponse['sujet_id']);
            exit;
        }
        
        if ($id > 0) {
            $this->model->delete($id);
            // Remove from session mapping
            if (isset($_SESSION['reponse_owners'][$id])) {
                unset($_SESSION['reponse_owners'][$id]);
            }
        }
        
        if ($reponse && isset($reponse['sujet_id'])) {
            header('Location: index.php?controller=sujet&action=show&id=' . $reponse['sujet_id']);
        } else {
            if (isAdmin()) {
                header('Location: index.php?controller=admin&action=reponses');
            } else {
                header('Location: index.php?controller=sujet&action=index');
            }
        }
        exit;
    }
}
?>
