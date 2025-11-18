<?php require '../../Verification.php'; require '../../Controller/ReclamationController.php'; $reclamations = getMyReclamations($_SESSION['user_id']); ?>
<?php include '../template/header.php'; ?>
<div class="container py-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="display-5 fw-bold">Mes Réclamations</h1>
        <a href="addBook.php" class="btn btn-primary btn-lg">Nouvelle Réclamation</a>
    </div>

    <div class="row g-4">
        <?php foreach($reclamations as $r): ?>
        <div class="col-md-6 col-lg-4">
            <div class="card shadow-sm h-100">
                <div class="card-body">
                    <h5 class="card-title"><?= htmlspecialchars($r['titre']) ?></h5>
                    <small class="text-muted">#REC-<?= str_pad($r['id'],4,'0',STR_PAD_LEFT) ?></small>
                    <p class="mt-2 text-muted"><?= substr(htmlspecialchars($r['description']),0,120) ?>...</p>
                    <div class="mt-3">
                        <span class="badge bg-<?= $r['priorite']=='Urgente'?'danger':'warning' ?> me-2"><?= $r['priorite'] ?></span>
                        <span class="badge bg-<?= $r['statut']=='resolue'?'success':'info' ?>"><?= ucfirst(str_replace('-',' ',$r['statut'])) ?></span>
                    </div>
                    <a href="showBook.php?id=<?= $r['id'] ?>" class="btn btn-outline-primary btn-sm mt-3">Voir détails →</a>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
</div>
<?php include '../template/footer.php'; ?>