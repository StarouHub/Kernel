<?php 
require '../../Verification.php'; 
require '../../Controller/ReclamationController.php'; 
$id = $_GET['id'];
$rec = $pdo->query("SELECT r.*, u.nom, u.email FROM reclamations r JOIN users u ON r.user_id=u.id WHERE r.id=$id")->fetch();
$responses = getResponses($id);

if(isset($_POST['statut'])){
    updateReclamationStatus($id, $_POST['statut']);
    header("Location: showReclamation.php?id=$id");
}
if($_POST && isset($_POST['message'])){
    addResponse($id, $_POST['message'], true);
    header("Location: showReclamation.php?id=$id");
}
?>
<?php include '../template/header.php'; ?>
<div class="d-flex">
    <?php include '../template/sidebar.php'; ?>
    <div class="content p-5" style="margin-left:260px;">
        <h1><?= htmlspecialchars($rec['titre']) ?> #REC-<?= str_pad($rec['id'],4,'0',STR_PAD_LEFT) ?></h1>
        
        <form method="post" class="mb-4">
            <label>Changer le statut :</label>
            <select name="statut" onchange="this.form.submit()" class="form-select w-auto d-inline">
                <option value="en-attente" <?= $rec['statut']=='en-attente'?'selected':'' ?>>En attente</option>
                <option value="en-cours" <?= $rec['statut']=='en-cours'?'selected':'' ?>>En cours</option>
                <option value="resolue" <?= $rec['statut']=='resolue'?'selected':'' ?>>Résolue</option>
            </select>
        </form>

        <div class="card shadow mb-4"><div class="card-body">
            <p><strong>Utilisateur :</strong> <?= $rec['nom'] ?> (<?= $rec['email'] ?>)</p>
            <p><strong>Priorité :</strong> <span class="badge bg-danger"><?= $rec['priorite'] ?></span></p>
            <p><?= nl2br(htmlspecialchars($rec['description'])) ?></p>
            <?php if($rec['image']): ?><img src="../../uploads/<?= $rec['image'] ?>" class="img-fluid"><?php endif; ?>
        </div></div>

        <!-- Réponses + formulaire admin (même que front mais en violet) -->
        <!-- (copie le bloc réponses du showBook.php ici) -->

        <div class="card shadow border-primary">
            <div class="card-body bg-light">
                <h5 class="text-primary">Répondre en tant qu’administrateur</h5>
                <form method="post">
                    <textarea name="message" rows="4" class="form-control mb-3" required></textarea>
                    <button type="submit" class="btn btn-primary">Envoyer la réponse</button>
                </form>
            </div>
        </div>
    </div>
</div>
<?php include '../template/footer.php'; ?>