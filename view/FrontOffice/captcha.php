<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Vérification - Kernel</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Roboto', sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
        }
        .container {
            background: white;
            padding: 50px 40px;
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.2);
            text-align: center;
            max-width: 420px;
            width: 90%;
        }
        .logo {
            font-size: 42px;
            font-weight: 900;
            color: #5a67d8;
            margin-bottom: 10px;
        }
        h1 {
            font-size: 28px;
            color: #2d3748;
            margin-bottom: 15px;
        }
        p {
            color: #718096;
            margin-bottom: 35px;
            font-size: 16px;
        }
        .captcha-box {
            margin: 30px 0;
            display: flex;
            justify-content: center;
        }
        .btn {
            background: linear-gradient(to right, #667eea, #764ba2);
            color: white;
            border: none;
            padding: 14px 40px;
            font-size: 18px;
            border-radius: 50px;
            cursor: pointer;
            transition: 0.3s;
            margin-top: 10px;
        }
        .btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 20px rgba(102, 126, 234, 0.4);
        }
        .illustration {
            margin: 30px 0;
        }
    </style>
</head>
<body>

<div class="container">
    <div class="logo">Kernel</div>
    <h1>Bienvenue sur Kernel</h1>
    <p>Rejoignez la communauté des innovateurs et transformez vos idées en projets concrets.</p>

    <div class="illustration">
        <svg width="120" height="120" viewBox="0 0 300 250">
            <circle cx="150" cy="100" r="60" fill="#60A5FA" opacity="0.3"/>
            <circle cx="150" cy="100" r="40" fill="#60A5FA" opacity="0.5"/>
            <circle cx="150" cy="100" r="20" fill="#FFFFFF"/>
            <rect x="130" y="160" width="40" height="60" fill="#FFFFFF" rx="5"/>
            <circle cx="100" cy="200" r="15" fill="#F59E0B"/>
            <circle cx="200" cy="200" r="15" fill="#F59E0B"/>
            <line x1="100" y1="200" x2="150" y2="160" stroke="#FFFFFF" stroke-width="3"/>
            <line x1="200" y1="200" x2="150" y2="160" stroke="#FFFFFF" stroke-width="3"/>
        </svg>
    </div>

    <form method="post" onsubmit="return checkCaptcha()">
        <div class="captcha-box">
            <div class="g-recaptcha" data-sitekey="6LeIxAcTAAAAAJcZVRqyHh71UMIEGNQ_MXjiZKhI"></div>
        </div>
        <button type="submit" class="btn">
            Accéder à la connexion
        </button>
    </form>
</div>

<script src="https://www.google.com/recaptcha/api.js" async defer></script>
<script>
function checkCaptcha() {
    var response = grecaptcha.getResponse();
    if(response.length === 0) {
        alert("Coche la case 'Je ne suis pas un robot' avant de continuer");
        return false;
    }
    // Si coché → on va vers connexion.php
    window.location.href = "connexion.php";
    return false;
}
</script>

</body>
</html>