<?php
session_start();
require_once '../../config.php';
require_once '../../controller/userController.php';

$controller = new UserController();

// Vérifier les permissions d'admin
$controller->requireAdmin();

$message = '';
$user = null;

// Récupérer l'utilisateur à modifier
if (isset($_GET['id'])) {
    $user = $controller->getUserById((int)$_GET['id']);
    if (!$user) {
        header('Location: admin-users.php?error=user_not_found');
        exit();
    }
}

// Traitement du formulaire
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $user) {
    $nom = trim($_POST['nom'] ?? '');
    $prenom = trim($_POST['prenom'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $telephone = trim($_POST['telephone'] ?? '');
    $role = $_POST['role'] ?? '';
    $password = $_POST['password'] ?? '';

    // Validation
    if (empty($nom) || empty($prenom) || empty($email) || empty($role)) {
        $message = '<div class="alert alert-danger">Veuillez remplir tous les champs obligatoires.</div>';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $message = '<div class="alert alert-danger">Adresse email invalide.</div>';
    } else {
        // Vérifier si l'email existe déjà (sauf pour l'utilisateur actuel)
        $existingUser = $controller->getUserByEmail($email);
        if ($existingUser && $existingUser->getId() !== $user->getId()) {
            $message = '<div class="alert alert-danger">Cet email est déjà utilisé par un autre utilisateur.</div>';
        } else {
            // Mettre à jour les données
            $user->setNom($nom);
            $user->setPrenom($prenom);
            $user->setEmail($email);
            $user->setTelephone($telephone);
            $user->setRole($role);
            
            // Hasher le nouveau mot de passe si fourni
            if (!empty($password)) {
                $user->setPassword(password_hash($password, PASSWORD_DEFAULT));
            }

            if ($controller->updateUser($user)) {
                header('Location: admin-users.php?success=update');
                exit();
            } else {
                $message = '<div class="alert alert-danger">Erreur lors de la mise à jour.</div>';
            }
        }
    }
}

if (!$user) {
    header('Location: admin-users.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Modifier Utilisateur - Kernel</title>

    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">

    <style>
        body {
            background: #f5f7fa;
            font-family: 'Inter', sans-serif;
            color: #2d3748;
            min-height: 100vh;
        }

        .container {
            max-width: 800px;
            margin: 50px auto;
            padding: 0 20px;
        }

        .card {
            background: white;
            border-radius: 20px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.1);
            border: none;
            overflow: hidden;
        }

        .card-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px;
            border: none;
        }

        .card-header h2 {
            margin: 0;
            font-weight: 700;
            font-size: 28px;
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .card-header p {
            margin: 10px 0 0;
            opacity: 0.9;
            font-size: 16px;
        }

        .card-body {
            padding: 40px;
        }

        .form-label {
            font-weight: 600;
            color: #1e293b;
            margin-bottom: 8px;
            font-size: 14px;
        }

        .form-control, .form-select {
            border: 2px solid #e2e8f0;
            border-radius: 12px;
            padding: 14px 16px;
            font-size: 15px;
            transition: all 0.3s ease;
            background: #f8fafc;
        }

        .form-control:focus, .form-select:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 4px rgba(102, 126, 234, 0.1);
            outline: none;
            background: white;
        }

        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            border-radius: 12px;
            padding: 14px 30px;
            font-weight: 600;
            font-size: 16px;
            transition: all 0.3s ease;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(102, 126, 234, 0.3);
        }

        .btn-secondary {
            background: #6b7280;
            border: none;
            border-radius: 12px;
            padding: 14px 30px;
            font-weight: 600;
            font-size: 16px;
            transition: all 0.3s ease;
        }

        .btn-secondary:hover {
            background: #4b5563;
            transform: translateY(-2px);
        }

        .alert {
            border-radius: 12px;
            border: none;
            padding: 16px 20px;
            margin-bottom: 25px;
        }

        .alert-danger {
            background: #fef2f2;
            color: #dc2626;
            border-left: 4px solid #dc2626;
        }

        .user-info {
            background: #f8fafc;
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 30px;
            border-left: 4px solid #667eea;
        }

        .user-info h5 {
            color: #1e293b;
            font-weight: 700;
            margin-bottom: 10px;
        }

        .user-info p {
            color: #64748b;
            margin: 5px 0;
            font-size: 14px;
        }

        .password-note {
            background: #fef3c7;
            color: #92400e;
            padding: 12px 16px;
            border-radius: 8px;
            font-size: 13px;
            margin-top: 8px;
        }

        .form-actions {
            display: flex;
            gap: 15px;
            justify-content: flex-end;
            margin-top: 30px;
            padding-top: 30px;
            border-top: 1px solid #e2e8f0;
        }

        @media (max-width: 768px) {
            .container {
                margin: 20px auto;
                padding: 0 15px;
            }

            .card-body {
                padding: 30px 20px;
            }

            .form-actions {
                flex-direction: column;
            }

            .btn {
                width: 100%;
            }
        }
    </style>
</head>
<body>

<div class="container">
    <div class="card">
        <div class="card-header">
            <h2>
                <i class="bi bi-person-gear"></i>
                Modifier l'utilisateur
            </h2>
            <p>Modifiez les informations de l'utilisateur sélectionné</p>
        </div>
        
        <div class="card-body">
            <div class="user-info">
                <h5><i class="bi bi-info-circle me-2"></i>Informations actuelles</h5>
                <p><strong>ID:</strong> #<?php echo $user->getId(); ?></p>
                <p><strong>Nom complet:</strong> <?php echo htmlspecialchars($user->getPrenom() . ' ' . $user->getNom()); ?></p>
                <p><strong>Email:</strong> <?php echo htmlspecialchars($user->getEmail()); ?></p>
                <p><strong>Rôle actuel:</strong> <?php echo htmlspecialchars($user->getRole()); ?></p>
                <?php if ($user->getDateInscription()): ?>
                    <p><strong>Inscrit le:</strong> <?php echo date('d/m/Y à H:i', strtotime($user->getDateInscription())); ?></p>
                <?php endif; ?>
            </div>

            <?php echo $message; ?>

            <form method="POST" id="modifyForm">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Prénom *</label>
                        <input type="text" name="prenom" class="form-control" 
                               value="<?php echo htmlspecialchars($user->getPrenom()); ?>" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Nom *</label>
                        <input type="text" name="nom" class="form-control" 
                               value="<?php echo htmlspecialchars($user->getNom()); ?>" required>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label">Adresse Email *</label>
                    <input type="email" name="email" class="form-control" 
                           value="<?php echo htmlspecialchars($user->getEmail()); ?>" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Téléphone</label>
                    <input type="text" name="telephone" class="form-control" 
                           value="<?php echo htmlspecialchars($user->getTelephone()); ?>" 
                           placeholder="+216 XX XXX XXX">
                </div>

                <div class="mb-3">
                    <label class="form-label">Rôle *</label>
                    <select name="role" class="form-select" required>
                        <option value="">Sélectionner un rôle</option>
                        <option value="visiteur" <?php echo $user->getRole() === 'visiteur' ? 'selected' : ''; ?>>Visiteur</option>
                        <option value="user" <?php echo $user->getRole() === 'user' ? 'selected' : ''; ?>>Utilisateur</option>
                        <option value="innovateur" <?php echo $user->getRole() === 'innovateur' ? 'selected' : ''; ?>>Innovateur</option>
                        <option value="Investisseur" <?php echo $user->getRole() === 'Investisseur' ? 'selected' : ''; ?>>Investisseur</option>
                        <option value="Administrateur" <?php echo $user->getRole() === 'Administrateur' ? 'selected' : ''; ?>>Administrateur</option>
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label">Nouveau mot de passe</label>
                    <input type="password" name="password" class="form-control" 
                           placeholder="Laisser vide pour conserver le mot de passe actuel">
                    <div class="password-note">
                        <i class="bi bi-info-circle me-1"></i>
                        Laissez ce champ vide si vous ne souhaitez pas changer le mot de passe
                    </div>
                </div>

                <div class="form-actions">
                    <a href="admin-users.php" class="btn btn-secondary">
                        <i class="bi bi-arrow-left me-2"></i>Annuler
                    </a>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-check-lg me-2"></i>Enregistrer les modifications
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
// Validation du formulaire
document.getElementById('modifyForm').addEventListener('submit', function(e) {
    const nom = document.querySelector('input[name="nom"]').value.trim();
    const prenom = document.querySelector('input[name="prenom"]').value.trim();
    const email = document.querySelector('input[name="email"]').value.trim();
    const role = document.querySelector('select[name="role"]').value;
    
    if (!nom || !prenom || !email || !role) {
        e.preventDefault();
        alert('Veuillez remplir tous les champs obligatoires.');
        return false;
    }
    
    if (!email.includes('@')) {
        e.preventDefault();
        alert('Veuillez entrer une adresse email valide.');
        return false;
    }
    
    return confirm('Êtes-vous sûr de vouloir modifier cet utilisateur ?');
});
</script>

</body>
</html>