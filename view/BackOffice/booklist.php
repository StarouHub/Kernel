<?php require '../../Verification.php'; require '../../Controller/ReclamationController.php'; $reclamations = getAllReclamations(); ?>
<?php include '../template/header.php'; ?>
<div class="d-flex">
    <?php include '../template/sidebar.php'; ?>
    <div class="content ms-5 ps-5 py-4 flex-grow-1" style="margin-left: 260px;">
        <h1 class="display-5 fw-bold mb-4">Gestion des Réclamations</h1>
        <div class="table-responsive">
            <table class="table table-striped table-hover">
                <thead class="table-dark">
                    <tr>
                        <th>ID</th><th>Titre</th><th>Utilisateur</th><th>Priorité</th><th>Statut</th><th>Date</th><th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($reclamations as $r): ?>
                    <tr>
                        <td>#REC-<?= str_pad($r['id'],4,'0',STR_PAD_LEFT) ?></td>
                        <td><?= htmlspecialchars($r['titre']) ?></td>
                        <td><?= htmlspecialchars($r['nom']) ?></td>
                        <td><span class="badge bg-<?= $r['priorite']=='Urgente'?'danger':($r['priorite']=='Haute'?'warning':'secondary') ?>"><?= $r['priorite'] ?></span></td>
                        <td><span class="badge bg-<?= $r['statut']=='resolue'?'success':($r['statut']=='en-cours'?'info':'warning') ?>"><?= ucfirst(str_replace('-',' ',$r['statut'])) ?></span></td>
                        <td><?= date('d/m/Y', strtotime($r['date_creation'])) ?></td>
                        <td>
                            <a href="showReclamation.php?id=<?= $r['id'] ?>" class="btn btn-sm btn-primary">Voir</a>
                            <a href="deleteReclamation.php?id=<?= $r['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Supprimer ?')">Supprimer</a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php include '../template/footer.php'; ?>