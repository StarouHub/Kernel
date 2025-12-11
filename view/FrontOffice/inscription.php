<?php
session_start();
require_once '../../config.php';
require_once '../../controller/userController.php';

$controller = new UserController();
$message = '';
$success = false;

// Rediriger si déjà connecté
if ($controller->isLoggedIn()) {
    header('Location: index.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom = trim($_POST['nom'] ?? '');
    $prenom = trim($_POST['prenom'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $telephone = trim($_POST['telephone'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirmPassword = $_POST['confirmPassword'] ?? '';

    // Validation
    if (empty($nom) || empty($prenom) || empty($email) || empty($telephone) || empty($password)) {
        $message = '<div class="alert alert-danger">Veuillez remplir tous les champs.</div>';
    } elseif ($password !== $confirmPassword) {
        $message = '<div class="alert alert-danger">Les mots de passe ne correspondent pas.</div>';
    } elseif (strlen($password) < 8) {
        $message = '<div class="alert alert-danger">Le mot de passe doit contenir au moins 8 caractères.</div>';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $message = '<div class="alert alert-danger">Adresse email invalide.</div>';
    } else {
        $user = new User($nom, $prenom, $email, $telephone, $password, 'user');
        $result = $controller->register($user);
        
        if ($result['success']) {
            $message = '<div class="alert alert-success">Inscription réussie ! Vous pouvez maintenant vous connecter.</div>';
            $success = true;
        } else {
            $message = '<div class="alert alert-danger">' . htmlspecialchars($result['message']) . '</div>';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inscription - Kernel</title>

    <link href="https://fonts.googleapis.com" rel="preconnect">
    <link href="https://fonts.gstatic.com" rel="preconnect" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700;900&family=Raleway:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.10.0/font/bootstrap-icons.min.css" rel="stylesheet">

    <style>
        body {
            font-family: 'Roboto', sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px 0;
        }
        
        .register-container {
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
            padding: 40px;
            max-width: 600px;
            width: 100%;
            margin: 20px;
        }
        
        .logo {
            text-align: center;
            font-size: 2rem;
            font-weight: 700;
            color: #667eea;
            margin-bottom: 2rem;
        }
        
        .register-title {
            text-align: center;
            color: #333;
            margin-bottom: 10px;
            font-weight: 600;
        }
        
        .register-subtitle {
            text-align: center;
            color: #666;
            margin-bottom: 30px;
        }
        
        .form-control {
            border: 2px solid #e1e5e9;
            border-radius: 10px;
            padding: 12px 15px;
            font-size: 16px;
            transition: all 0.3s ease;
        }
        
        .form-control:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
        }
        
        .input-group-text {
            background: #f8f9fa;
            border: 2px solid #e1e5e9;
            border-right: none;
            border-radius: 10px 0 0 10px;
        }
        
        .btn-register {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            border-radius: 10px;
            color: white;
            font-weight: 600;
            padding: 12px 30px;
            width: 100%;
            font-size: 16px;
            transition: all 0.3s ease;
        }
        
        .btn-register:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(102, 126, 234, 0.3);
        }
        
        .login-link {
            text-align: center;
            margin-top: 30px;
            color: #666;
        }
        
        .login-link a {
            color: #667eea;
            text-decoration: none;
            font-weight: 600;
        }
        
        .login-link a:hover {
            text-decoration: underline;
        }
        
        .password-strength {
            height: 4px;
            background: #e1e5e9;
            border-radius: 2px;
            margin-top: 5px;
            overflow: hidden;
        }
        
        .password-strength-bar {
            height: 100%;
            width: 0%;
            transition: all 0.3s ease;
            border-radius: 2px;
        }
        
        .password-hint {
            font-size: 12px;
            margin-top: 5px;
            color: #666;
        }
        
        .strength-weak { background: #dc3545; }
        .strength-medium { background: #ffc107; }
        .strength-strong { background: #28a745; }
        
        @media (max-width: 768px) {
            .register-container {
                margin: 10px;
                padding: 30px 20px;
            }
        }
    </style>
</head>
<body>

<div class="register-container">
    <div class="logo">
        <i class="bi bi-hexagon-fill"></i> Kernel
    </div>

    <h3 class="register-title">Créer un compte</h3>
    <p class="register-subtitle">Rejoignez la communauté des innovateurs</p>

    <?php echo $message; ?>

    <?php if (!$success): ?>
    <form method="POST" id="registerForm" novalidate>
        <div class="row">
            <div class="col-md-6 mb-3">
                <label class="form-label">Prénom</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="bi bi-person"></i></span>
                    <input type="text" name="prenom" class="form-control" placeholder="Jean" 
                           value="<?= htmlspecialchars($_POST['prenom'] ?? '') ?>" required>
                </div>
            </div>
            <div class="col-md-6 mb-3">
                <label class="form-label">Nom</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="bi bi-person"></i></span>
                    <input type="text" name="nom" class="form-control" placeholder="Dupont" 
                           value="<?= htmlspecialchars($_POST['nom'] ?? '') ?>" required>
                </div>
            </div>
        </div>

        <div class="mb-3">
            <label class="form-label">Adresse Email</label>
            <div class="input-group">
                <span class="input-group-text"><i class="bi bi-envelope"></i></span>
                <input type="email" name="email" class="form-control" placeholder="votre@email.com" 
                       value="<?= htmlspecialchars($_POST['email'] ?? '') ?>" required>
            </div>
        </div>

        <div class="mb-3">
            <label class="form-label">Téléphone</label>
            <div class="input-group">
                <span class="input-group-text"><i class="bi bi-telephone"></i></span>
                <input type="text" name="telephone" class="form-control" placeholder="+216 XX XXX XXX" 
                       value="<?= htmlspecialchars($_POST['telephone'] ?? '') ?>" required>
            </div>
        </div>

        <div class="mb-3">
            <label class="form-label">Mot de passe</label>
            <div class="input-group">
                <span class="input-group-text"><i class="bi bi-lock"></i></span>
                <input type="password" name="password" class="form-control" placeholder="••••••••" 
                       id="password" oninput="checkPasswordStrength()" required>
                <span class="input-group-text" style="cursor: pointer;" onclick="togglePassword('password', 'toggleIcon1')">
                    <i class="bi bi-eye" id="toggleIcon1"></i>
                </span>
            </div>
            <div class="password-strength">
                <div class="password-strength-bar" id="strengthBar"></div>
            </div>
            <div class="password-hint" id="strengthText">Le mot de passe doit contenir au moins 8 caractères</div>
        </div>

        <div class="mb-4">
            <label class="form-label">Confirmer le mot de passe</label>
            <div class="input-group">
                <span class="input-group-text"><i class="bi bi-lock"></i></span>
                <input type="password" name="confirmPassword" class="form-control" placeholder="••••••••" 
                       id="confirmPassword" required>
                <span class="input-group-text" style="cursor: pointer;" onclick="togglePassword('confirmPassword', 'toggleIcon2')">
                    <i class="bi bi-eye" id="toggleIcon2"></i>
                </span>
            </div>
        </div>

        <button type="submit" class="btn-register">
            <i class="bi bi-person-plus me-2"></i> Créer mon compte
        </button>
    </form>
    <?php endif; ?>

    <div class="login-link">
        <?php if ($success): ?>
            <a href="connexion.php">Se connecter maintenant</a>
        <?php else: ?>
            Vous avez déjà un compte ? <a href="connexion.php">Se connecter</a>
        <?php endif; ?>
    </div>

    <div class="text-center mt-4">
        <a href="index.php" class="btn btn-outline-secondary">Retour à l'accueil</a>
    </div>
</div>

<script>
function togglePassword(inputId, iconId) {
    const password = document.getElementById(inputId);
    const toggleIcon = document.getElementById(iconId);
    
    if (password.type === 'password') {
        password.type = 'text';
        toggleIcon.classList.remove('bi-eye');
        toggleIcon.classList.add('bi-eye-slash');
    } else {
        password.type = 'password';
        toggleIcon.classList.remove('bi-eye-slash');
        toggleIcon.classList.add('bi-eye');
    }
}

function checkPasswordStrength() {
    const password = document.getElementById('password').value;
    const strengthBar = document.getElementById('strengthBar');
    const strengthText = document.getElementById('strengthText');
    
    let strength = 0;
    let feedback = [];
    
    if (password.length >= 8) strength += 25;
    else feedback.push('au moins 8 caractères');
    
    if (/[a-z]/.test(password)) strength += 25;
    else feedback.push('une minuscule');
    
    if (/[A-Z]/.test(password)) strength += 25;
    else feedback.push('une majuscule');
    
    if (/[0-9]/.test(password)) strength += 25;
    else feedback.push('un chiffre');
    
    strengthBar.style.width = strength + '%';
    
    if (strength < 50) {
        strengthBar.className = 'password-strength-bar strength-weak';
        strengthText.textContent = 'Faible - Ajoutez: ' + feedback.join(', ');
        strengthText.style.color = '#dc3545';
    } else if (strength < 100) {
        strengthBar.className = 'password-strength-bar strength-medium';
        strengthText.textContent = 'Moyen - Ajoutez: ' + feedback.join(', ');
        strengthText.style.color = '#ffc107';
    } else {
        strengthBar.className = 'password-strength-bar strength-strong';
        strengthText.textContent = 'Fort - Mot de passe sécurisé';
        strengthText.style.color = '#28a745';
    }
}

// Validation du formulaire
document.getElementById('registerForm').addEventListener('submit', function(e) {
    const password = document.getElementById('password').value;
    const confirmPassword = document.getElementById('confirmPassword').value;
    
    if (password !== confirmPassword) {
        e.preventDefault();
        alert('Les mots de passe ne correspondent pas.');
        return false;
    }
    
    if (password.length < 8) {
        e.preventDefault();
        alert('Le mot de passe doit contenir au moins 8 caractères.');
        return false;
    }
});
</script>

</body>
</html>