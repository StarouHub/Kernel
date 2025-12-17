<?php
include_once(__DIR__ . '/../../services/ChatbotService.php');
include_once(__DIR__ . '/../components/office-switch.php');

$chatbotService = new ChatbotService();
$response = null;

// Traiter la question si envoyée
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['question'])) {
    $question = trim($_POST['question']);
    if (!empty($question)) {
        $response = $chatbotService->processQuestion($question);
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">
  <title>Chatbot Kernel - Assistant Virtuel</title>
  
  <link href="https://fonts.googleapis.com" rel="preconnect">
  <link href="https://fonts.gstatic.com" rel="preconnect" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700;900&family=Raleway:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
  
  <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.10.0/font/bootstrap-icons.min.css" rel="stylesheet">
  
  <style>
    :root {
      --primary-color: #2563EB;
      --secondary-color: #7C3AED;
      --dark-color: #1F2937;
      --light-bg: #F9FAFB;
    }
    
    body {
      font-family: 'Roboto', sans-serif;
      background: var(--light-bg);
      padding-top: 80px;
      padding-bottom: 50px;
    }
    
    .header {
      background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
      padding: 15px 0;
      position: fixed;
      width: 100%;
      top: 0;
      z-index: 1000;
      box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    }
    
    .logo {
      font-size: 28px;
      font-weight: 700;
      color: white;
      text-decoration: none;
      font-family: 'Raleway', sans-serif;
    }
    
    .chat-container {
      max-width: 800px;
      margin: 0 auto;
      background: white;
      border-radius: 15px;
      box-shadow: 0 2px 20px rgba(0,0,0,0.1);
      overflow: hidden;
    }
    
    .chat-header {
      background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
      color: white;
      padding: 25px;
      text-align: center;
    }
    
    .chat-header h1 {
      font-size: 28px;
      margin: 0;
      font-weight: 700;
    }
    
    .chat-messages {
      padding: 30px;
      min-height: 400px;
      max-height: 500px;
      overflow-y: auto;
    }
    
    .message {
      margin-bottom: 20px;
      animation: fadeIn 0.3s;
    }
    
    @keyframes fadeIn {
      from { opacity: 0; transform: translateY(10px); }
      to { opacity: 1; transform: translateY(0); }
    }
    
    .message.user {
      text-align: right;
    }
    
    .message-bubble {
      display: inline-block;
      padding: 15px 20px;
      border-radius: 20px;
      max-width: 70%;
      word-wrap: break-word;
    }
    
    .message.user .message-bubble {
      background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
      color: white;
      border-bottom-right-radius: 5px;
    }
    
    .message.bot .message-bubble {
      background: #F3F4F6;
      color: var(--dark-color);
      border-bottom-left-radius: 5px;
    }
    
    .message.bot .message-bubble.success {
      border-left: 4px solid #10B981;
    }
    
    .message.bot .message-bubble.warning {
      border-left: 4px solid #F59E0B;
    }
    
    .message.bot .message-bubble.info {
      border-left: 4px solid #3B82F6;
    }
    
    .chat-input-container {
      padding: 20px;
      background: #F9FAFB;
      border-top: 1px solid #E5E7EB;
    }
    
    .chat-input {
      display: flex;
      gap: 10px;
    }
    
    .chat-input input {
      flex: 1;
      padding: 15px;
      border: 2px solid #E5E7EB;
      border-radius: 25px;
      font-size: 16px;
      transition: all 0.3s;
    }
    
    .chat-input input:focus {
      outline: none;
      border-color: var(--primary-color);
      box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
    }
    
    .chat-input button {
      padding: 15px 30px;
      background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
      color: white;
      border: none;
      border-radius: 25px;
      font-weight: 600;
      cursor: pointer;
      transition: all 0.3s;
    }
    
    .chat-input button:hover {
      transform: translateY(-2px);
      box-shadow: 0 5px 15px rgba(37, 99, 235, 0.3);
    }
    
    .suggestions {
      display: flex;
      flex-wrap: wrap;
      gap: 10px;
      margin-top: 15px;
    }
    
    .suggestion-btn {
      padding: 8px 15px;
      background: white;
      border: 2px solid #E5E7EB;
      border-radius: 20px;
      font-size: 14px;
      cursor: pointer;
      transition: all 0.3s;
    }
    
    .suggestion-btn:hover {
      border-color: var(--primary-color);
      color: var(--primary-color);
      transform: translateY(-2px);
    }
    
    .welcome-message {
      text-align: center;
      padding: 40px 20px;
      color: #6B7280;
    }
    
    .welcome-message i {
      font-size: 64px;
      color: var(--primary-color);
      margin-bottom: 20px;
    }
  </style>
</head>

<body>
  <?php echo renderOfficeSwitch('front', 'actualite'); ?>
  
  <header class="header d-flex align-items-center">
    <div class="container d-flex align-items-center justify-content-between">
      <a href="index.html" class="logo">
        <i class="bi bi-hexagon-fill"></i> Kernel
      </a>
      <a class="btn btn-light" href="listeprojet.php">
        <i class="bi bi-arrow-left me-2"></i> Retour
      </a>
    </div>
  </header>

  <div class="container mt-5">
    <div class="chat-container">
      <div class="chat-header">
        <h1><i class="bi bi-robot"></i> Assistant Virtuel Kernel</h1>
        <p class="mb-0">Posez-moi vos questions sur les projets et actualités</p>
      </div>
      
      <div class="chat-messages" id="chatMessages">
        <?php if ($response === null): ?>
          <div class="welcome-message">
            <i class="bi bi-chat-dots"></i>
            <h3>Bonjour ! 👋</h3>
            <p>Je suis votre assistant virtuel Kernel.<br>
            Posez-moi des questions sur les projets, actualités, budgets...</p>
            <p><small>Tapez "aide" pour voir ce que je peux faire</small></p>
          </div>
        <?php else: ?>
          <div class="message user">
            <div class="message-bubble">
              <?php echo htmlspecialchars($_POST['question']); ?>
            </div>
          </div>
          
          <div class="message bot">
            <div class="message-bubble <?php echo $response['type']; ?>">
              <strong><i class="bi bi-robot me-2"></i>Assistant :</strong><br><br>
              <?php echo $response['message']; ?>
            </div>
          </div>
        <?php endif; ?>
      </div>
      
      <div class="chat-input-container">
        <form method="POST" action="" class="chat-input">
          <input type="text" name="question" placeholder="Posez votre question..." 
                 autocomplete="off" required autofocus>
          <button type="submit">
            <i class="bi bi-send"></i> Envoyer
          </button>
        </form>
        
        <div class="suggestions">
          <button class="suggestion-btn" onclick="askQuestion('Combien de projets ?')">
            💼 Nombre de projets
          </button>
          <button class="suggestion-btn" onclick="askQuestion('Dernier projet')">
            🆕 Dernier projet
          </button>
          <button class="suggestion-btn" onclick="askQuestion('Actualités')">
            📰 Actualités
          </button>
          <button class="suggestion-btn" onclick="askQuestion('Catégories')">
            📂 Catégories
          </button>
          <button class="suggestion-btn" onclick="askQuestion('Budget')">
            💰 Budget
          </button>
          <button class="suggestion-btn" onclick="askQuestion('Aide')">
            ❓ Aide
          </button>
        </div>
      </div>
    </div>
  </div>

  <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
  <script>
    function askQuestion(question) {
      document.querySelector('input[name="question"]').value = question;
      document.querySelector('form').submit();
    }
    
    // Auto-scroll vers le bas des messages
    const chatMessages = document.getElementById('chatMessages');
    if (chatMessages) {
      chatMessages.scrollTop = chatMessages.scrollHeight;
    }
  </script>
</body>
</html>
