<!-- Chatbot d'assistance -->
<div id="chatbot-container" style="position: fixed; bottom: 20px; right: 20px; z-index: 1000; max-width: 350px;">
  <!-- Bouton pour ouvrir/fermer le chatbot -->
  <button id="chatbot-toggle" 
          style="width: 60px; height: 60px; border-radius: 50%; background: linear-gradient(135deg, #2563EB, #7C3AED); 
                 color: white; border: none; cursor: pointer; box-shadow: 0 4px 12px rgba(0,0,0,0.3);
                 display: flex; align-items: center; justify-content: center; font-size: 24px;
                 transition: all 0.3s;">
    <i class="bi bi-chat-dots"></i>
  </button>

  <!-- Fenêtre du chatbot -->
  <div id="chatbot-window" 
       style="display: none; background: white; border-radius: 15px; box-shadow: 0 8px 24px rgba(0,0,0,0.2);
              width: 100%; max-height: 500px; flex-direction: column; margin-top: 10px;">
    
    <!-- En-tête -->
    <div style="background: linear-gradient(135deg, #2563EB, #7C3AED); color: white; padding: 15px; 
                border-radius: 15px 15px 0 0; display: flex; justify-content: space-between; align-items: center;">
      <div>
        <strong style="font-size: 16px;"><i class="bi bi-robot"></i> Assistant</strong>
        <div style="font-size: 12px; opacity: 0.9;">En ligne</div>
      </div>
      <button id="chatbot-close" style="background: none; border: none; color: white; font-size: 20px; cursor: pointer;">
        <i class="bi bi-x"></i>
      </button>
    </div>

    <!-- Messages -->
    <div id="chatbot-messages" 
         style="padding: 15px; height: 300px; overflow-y: auto; flex: 1; background: #F9FAFB;">
      
      <!-- Message de bienvenue -->
      <div class="chatbot-message chatbot-bot" style="margin-bottom: 15px;">
        <div style="background: #E5E7EB; padding: 10px 15px; border-radius: 15px; display: inline-block; max-width: 80%;">
          <p style="margin: 0; color: #374151; font-size: 14px;">
            Bonjour ! 👋 Je suis votre assistant virtuel. Comment puis-je vous aider aujourd'hui ?
          </p>
        </div>
      </div>
    </div>

    <!-- Zone de saisie -->
    <div style="padding: 15px; border-top: 1px solid #E5E7EB; background: white; border-radius: 0 0 15px 15px;">
      <form id="chatbot-form" style="display: flex; gap: 8px;">
        <input type="text" id="chatbot-input" 
               placeholder="Posez votre question..." 
               style="flex: 1; padding: 10px 15px; border: 2px solid #E5E7EB; border-radius: 25px; 
                      font-size: 14px; outline: none; transition: all 0.3s;"
               onfocus="this.style.borderColor='#2563EB';"
               onblur="this.style.borderColor='#E5E7EB';">
        <button type="submit" 
                style="width: 40px; height: 40px; border-radius: 50%; background: linear-gradient(135deg, #2563EB, #7C3AED);
                       color: white; border: none; cursor: pointer; display: flex; align-items: center; justify-content: center;
                       transition: all 0.3s;"
                onmouseover="this.style.transform='scale(1.1)'"
                onmouseout="this.style.transform='scale(1)'">
          <i class="bi bi-send"></i>
        </button>
      </form>
      
      <!-- Suggestions rapides -->
      <div style="margin-top: 10px; display: flex; flex-wrap: gap: 5px; gap: 5px;">
        <button class="chatbot-suggestion" 
                style="padding: 5px 10px; background: #F3F4F6; border: 1px solid #E5E7EB; border-radius: 15px;
                       font-size: 11px; cursor: pointer; color: #374151; transition: all 0.2s;"
                onclick="sendQuickMessage('Où se déroule l\'événement ?')">
          Où se déroule l'événement ?
        </button>
        <button class="chatbot-suggestion" 
                style="padding: 5px 10px; background: #F3F4F6; border: 1px solid #E5E7EB; border-radius: 15px;
                       font-size: 11px; cursor: pointer; color: #374151; transition: all 0.2s;"
                onclick="sendQuickMessage('Comment m\'inscrire ?')">
          Comment m'inscrire ?
        </button>
      </div>
    </div>
  </div>
</div>

<style>
  .chatbot-message {
    margin-bottom: 15px;
    animation: fadeIn 0.3s;
  }
  
  .chatbot-user {
    text-align: right;
  }
  
  .chatbot-user > div {
    background: linear-gradient(135deg, #2563EB, #7C3AED) !important;
    color: white !important;
    margin-left: auto;
  }
  
  .chatbot-bot {
    text-align: left;
  }
  
  .chatbot-suggestion:hover {
    background: #E5E7EB !important;
    transform: translateY(-1px);
  }
  
  @keyframes fadeIn {
    from {
      opacity: 0;
      transform: translateY(10px);
    }
    to {
      opacity: 1;
      transform: translateY(0);
    }
  }
  
  #chatbot-messages::-webkit-scrollbar {
    width: 6px;
  }
  
  #chatbot-messages::-webkit-scrollbar-track {
    background: #F3F4F6;
    border-radius: 10px;
  }
  
  #chatbot-messages::-webkit-scrollbar-thumb {
    background: #D1D5DB;
    border-radius: 10px;
  }
  
  #chatbot-messages::-webkit-scrollbar-thumb:hover {
    background: #9CA3AF;
  }
</style>

<script>
  // État du chatbot
  let chatbotOpen = false;
  
  // Éléments DOM
  const chatbotToggle = document.getElementById('chatbot-toggle');
  const chatbotWindow = document.getElementById('chatbot-window');
  const chatbotClose = document.getElementById('chatbot-close');
  const chatbotForm = document.getElementById('chatbot-form');
  const chatbotInput = document.getElementById('chatbot-input');
  const chatbotMessages = document.getElementById('chatbot-messages');
  
  // Ouvrir/fermer le chatbot
  chatbotToggle.addEventListener('click', function() {
    chatbotOpen = !chatbotOpen;
    chatbotWindow.style.display = chatbotOpen ? 'flex' : 'none';
    if (chatbotOpen) {
      chatbotInput.focus();
    }
  });
  
  chatbotClose.addEventListener('click', function() {
    chatbotOpen = false;
    chatbotWindow.style.display = 'none';
  });
  
  // Détecter l'ID de l'événement depuis l'URL
  function getCurrentEventId() {
    const urlParams = new URLSearchParams(window.location.search);
    const id = urlParams.get('id');
    return id ? parseInt(id) : null;
  }
  
  // Envoyer un message
  function sendMessage(message) {
    if (!message.trim()) return;
    
    // Afficher le message de l'utilisateur
    addMessage(message, 'user');
    
    // Réinitialiser l'input
    chatbotInput.value = '';
    
    // Envoyer la requête au serveur
    const formData = new FormData();
    formData.append('message', message);
    
    // Ajouter l'ID de l'événement si disponible
    const eventId = getCurrentEventId();
    if (eventId) {
      formData.append('event_id', eventId);
    }
    
    fetch('index.php?action=chatbot', {
      method: 'POST',
      body: formData
    })
    .then(response => response.json())
    .then(data => {
      if (data.success) {
        addMessage(data.response, 'bot');
      } else {
        addMessage('Désolé, une erreur est survenue. Veuillez réessayer.', 'bot');
      }
    })
    .catch(error => {
      console.error('Erreur:', error);
      addMessage('Désolé, je ne peux pas répondre pour le moment. Veuillez réessayer plus tard.', 'bot');
    });
  }
  
  // Fonction pour envoyer un message rapide
  function sendQuickMessage(message) {
    chatbotInput.value = message;
    sendMessage(message);
  }
  
  // Ajouter un message à la conversation
  function addMessage(text, type) {
    const messageDiv = document.createElement('div');
    messageDiv.className = `chatbot-message chatbot-${type}`;
    
    const messageContent = document.createElement('div');
    messageContent.style.cssText = type === 'user' 
      ? 'background: linear-gradient(135deg, #2563EB, #7C3AED); padding: 10px 15px; border-radius: 15px; display: inline-block; max-width: 80%; margin-left: auto;'
      : 'background: #E5E7EB; padding: 10px 15px; border-radius: 15px; display: inline-block; max-width: 80%;';
    
    const messageText = document.createElement('p');
    messageText.style.cssText = 'margin: 0; color: ' + (type === 'user' ? 'white' : '#374151') + '; font-size: 14px; white-space: pre-wrap;';
    messageText.textContent = text;
    
    messageContent.appendChild(messageText);
    messageDiv.appendChild(messageContent);
    chatbotMessages.appendChild(messageDiv);
    
    // Scroll vers le bas
    chatbotMessages.scrollTop = chatbotMessages.scrollHeight;
  }
  
  // Gérer la soumission du formulaire
  chatbotForm.addEventListener('submit', function(e) {
    e.preventDefault();
    const message = chatbotInput.value.trim();
    if (message) {
      sendMessage(message);
    }
  });
  
  // Ouvrir le chatbot avec Enter
  document.addEventListener('keydown', function(e) {
    if (e.key === 'Enter' && chatbotInput === document.activeElement) {
      chatbotForm.dispatchEvent(new Event('submit'));
    }
  });
</script>
