<?php
require_once __DIR__ . '/../model/Reponse.php';
require_once __DIR__ . '/../model/Sujet.php'; // adapte le chemin/nom si besoin

class ReponseController
{
    private $reponseModel;
    private $sujetModel;

    public function __construct()
    {
        $this->reponseModel = new Reponse();
        $this->sujetModel   = new Sujet();
    }

    public function index()
    {
        $reponses = $this->reponseModel->getAll();
        require __DIR__ . '/../view/reponse/index.php';
    }

    public function create()
    {
        if (!isset($_GET['sujet_id'])) {
            $error    = "Sujet introuvable.";
            $reponses = [];
            require __DIR__ . '/../view/reponse/index.php';
            return;
        }

        $sujet_id = (int) $_GET['sujet_id'];
        $sujet    = $this->sujetModel->getById($sujet_id);

        if (!$sujet) {
            $error    = "Sujet introuvable.";
            $reponses = [];
            require __DIR__ . '/../view/reponse/index.php';
            return;
        }

        $contenu = '';

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $contenu = trim($_POST['contenu'] ?? '');

            // Blocage si au moins un * ou # ou "fruit"
            if (strpos($contenu, '*') !== false ||
                strpos($contenu, '#') !== false ||
                stripos($contenu, 'fruit') !== false) {

                $error = "Vous ne pouvez pas publier cette réponse . Veuillez les supprimer ou les modifiers.";
                require __DIR__ . '/../view/reponse/create.php';
                return;
            }

            // Aucun mot interdit → on crée
            $this->reponseModel->create($contenu, $sujet_id);
            header("Location: index.php?controller=sujet&action=show&id=" . $sujet_id);
            exit;
        }

        require __DIR__ . '/../view/reponse/create.php';
    }

    public function edit()
    {
        if (!isset($_GET['id'])) {
            $error    = "Réponse introuvable.";
            $reponses = $this->reponseModel->getAll();
            require __DIR__ . '/../view/reponse/index.php';
            return;
        }

        $id      = (int) $_GET['id'];
        $reponse = $this->reponseModel->getById($id);

        if (!$reponse) {
            $error    = "Réponse introuvable.";
            $reponses = $this->reponseModel->getAll();
            require __DIR__ . '/../view/reponse/index.php';
            return;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $contenu = trim($_POST['contenu'] ?? '');

            // Blocage si au moins un * ou # ou "fruit"
            if (strpos($contenu, '*') !== false ||
                strpos($contenu, '#') !== false ||
                stripos($contenu, 'fruit') !== false) {

                $error = "Vous ne pouvez pas publier cette réponse . Veuillez les supprimer ou les modifiers.";
                $reponse['contenu'] = $contenu;
                require __DIR__ . '/../view/reponse/edit.php';
                return;
            }

            // Aucun mot interdit → on met à jour
            $this->reponseModel->update($id, $contenu);
            header("Location: index.php?controller=sujet&action=show&id=" . $reponse['sujet_id']);
            exit;
        }

        require __DIR__ . '/../view/reponse/edit.php';
    }

    public function delete()
    {
        if (!isset($_GET['id'])) {
            $error    = "Réponse introuvable.";
            $reponses = $this->reponseModel->getAll();
            require __DIR__ . '/../view/reponse/index.php';
            return;
        }

        $id      = (int) $_GET['id'];
        $reponse = $this->reponseModel->getById($id);

        if (!$reponse) {
            $error    = "Réponse introuvable.";
            $reponses = $this->reponseModel->getAll();
            require __DIR__ . '/../view/reponse/index.php';
            return;
        }

        $this->reponseModel->delete($id);
        header("Location: index.php?controller=sujet&action=show&id=" . $reponse['sujet_id']);
        exit;
    }
}
