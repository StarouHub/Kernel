<?php require '../../Verification.php'; require '../../Controller/ReclamationController.php'; 
if($_POST){
    $data = [
        'user_id' => $_SESSION['user_id'],
        'titre' => $_POST['titre'],
        'description' => $_POST['description'],
        'type' => $_POST['type'],
        'priorite' => $_POST['priorite']
    ];
    $file = $_FILES['image'] ?? null;
    if(addReclamation($data, $file)){
        header("Location: bookList.php?success=1");
    }
}
?>
<?php include '../template/header.php'; ?>
<div class="container py-5">
    <h1 class="display-5 fw-bold mb-4">Nouvelle Réclamation</h1>
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow">
                <div class="card-body p-5">
                    <form method="post" enctype="multipart/form-data">
                        <div class="mb-3">
                            <label class="form-label">Titre</label>
                            <input type="text" name="titre" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Type de réclamation</label>
                            <select name="type" class="form-select" required>
                                <option value="Bug">Bug</option>
                                <option value="Suggestion">Suggestion</option>
                                <option value="Problème technique">Problème technique</option>
                                <option value="Paiement">Paiement</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Priorité</label>
                            <select name="priorite" class="form-select" required>
                                <option value="Basse">Basse</option>
                                <option value="Normale">Normale</option>
                                <option value="Haute">Haute</option>
                                <option value="Urgente">Urgente</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Description détaillée</label>
                            <textarea name="description" rows="6" class="form-control" required></textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Capture d’écran (optionnel)</label>
                            <input type="file" name="image" class="form-control" accept="image/*">
                        </div>
                        <div class="text-end">
                            <a href="bookList.php" class="btn btn-secondary me-2">Annuler</a>
                            <button type="submit" class="btn btn-primary btn-lg">Envoyer la réclamation</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<?php include '../template/footer.php'; ?>