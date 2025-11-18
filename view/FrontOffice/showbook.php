<?php 
require '../../Verification.php'; 
require '../../Controller/ReclamationController.php'; 
$id = $_GET['id'];
$rec = $pdo->prepare("SELECT r.*, u.nom FROM reclamations r JOIN users u ON r.user_id=u.id WHERE r.id=?")->execute([$id]);
$rec = $pdo->query("SELECT r.*, u.nom FROM reclamations r JOIN users u ON r.user_id=u.id WHERE r.id=$id")->fetch();
$responses = getResponses($id);

if($_POST && isset($_POST['message'])){
    addResponse($id, $_POST['message'], false);
    header("Location: showBook.php?id=$id");
}
?>
<?php include '../template/header.php'; ?>
<div class="container py-5">
    <h1><?= htmlspecialchars($rec['titre']) ?> <small class="text-muted">#REC-<?= str_pad($rec['id'],4,'0',STR_PAD_LEFT) ?></small></h1>
    <div class="row">
        <div class="col-lg-8">
            <div class="card shadow mb-4">
                <div class="card-body">
                    <p><strong>Type :</strong> <?= $rec['type'] ?> | 
                       <strong>Priorité :</strong> <span class="badge bg-<?= $rec['priorite']=='Urgente'?'danger':'warning' ?>"><?= $rec['priorite'] ?></span> | 
                       <strong>Statut :</strong> <span class="badge bg-<?= $rec['statut']=='resolue'?'success':'info' ?>"><?= ucfirst($rec['statut']) ?></span>
                    </p>
                    <?php if($rec['image']): ?>
                        <img src="../../uploads/<?= $rec['image'] ?>" class="img-fluid rounded mb-3" style="max-height:400px;">
                    <?php endif; ?>
                    <p class="lh-lg"><?= nl2br(htmlspecialchars($rec['description'])) ?></p>
                </div>
            </div>

            <h4>Réponses (<?= count($responses) ?>)</h4>
            <?php foreach($responses as $rep): ?>
            <div class="border-start border-4 border-<?= $rep['type']=='admin'?'primary':'secondary' ?> ps-3 mb-3">
                <strong><?= htmlspecialchars($rep['nom']) ?> <?= $rep['type']=='admin'?'<span class="badge bg-primary">ADMIN</span>':'' ?></strong>
                <small class="text-muted d-block"><?= date('d/m/Y H:i', strtotime($rep['date_envoi'])) ?></small>
                <p class="mt-2"><?= nl2br(htmlspecialchars($rep['message'])) ?></p>
            </div>
            <?php endforeach; ?>

            <div class="card shadow">
                <div class="card-body">
                    <h5>Ajouter une réponse</h5>
                    <form method="post">
                        <textarea name="message" rows="4" class="form-control mb-3" required placeholder="Écrivez votre message..."></textarea>
                        <button type="submit" class="btn btn-primary">Envoyer</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<?php include '../template/footer.php'; ?>