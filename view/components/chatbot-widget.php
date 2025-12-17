<?php
/**
 * Widget Chatbot flottant
 * Usage: include_once(__DIR__ . '/../components/chatbot-widget.php');
 *        echo renderChatbotWidget();
 */
function renderChatbotWidget() {
    ob_start();
    ?>
    <style>
        .chatbot-widget {
            position: fixed;
            bottom: 20px;
            right: 20px;
            z-index: 9999;
            font-family: 'Roboto', sans-serif;
        }
        
        .chatbot-toggle {
            width: 60px;
            height: 60px;
            background: linear-gradient(135deg, #2563EB, #7C3AED);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            box-shadow: 0 4px 20px rgba(37, 99, 235, 0.3);
            transition: all 0.3s ease;
            border: none;
            color: white;
            font-size: 24px;
        }
        
        .chatbot-toggle:hover {
            transform: scale(1.1);
            box-shadow: 0 6px 25px rgba(37, 99, 235, 0.4);
        }
        
        .chatbot-toggle.active {
            background: #EF4444;
        }
        
        .chatbot-container {
            position: absolute;
            bottom: 80px;
            right: 0;
            width: 350px;
            height: 500px;
            background: white;
            border-radius: 20px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.15);
            display: none;
            flex-direction: column;
            overflow: hidden;
            animation: slideUp 0.3s ease;
        }
        
        .chatbot-container.active {
            display: flex;
        }
        
        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .chatbot-header {
            background: linear-gradient(135deg, #2563EB, #7C3AED);
            color: white;
            padding: 20px;
            text-align: center;
        }
        
        .chatbot-header h3 {
            margin: 0;
            font-size: 18px;
            font-weight: 600;
        }
        
        .chatbot-header p {
            margin: 5px 0 0 0;
            font-size: 12px;
            opacity: 0.9;
        }
        
        .chatbot-messages {
            flex: 1;
            padding: 20px;
            overflow-y: auto;
            background: #F9FAFB;
        }
        
        .message {
            margin-bottom: 15px;
            display: flex;
            align-items: flex-start;
            gap: 10px;
        }
        
        .message.user {
            flex-direction: row-reverse;
        }
        
        .message-avatar {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 14px;
            flex-shrink: 0;
        }
        
        .message.bot .message-avatar {
            background: linear-gradient(135deg, #2563EB, #7C3AED);
            color: white;
        }
        
        .message.user .message-avatar {
            background: #F59E0B;
            color: white;
        }
        
        .message-content {
            max-width: 80%;
            padding: 12px 16px;
            border-radius: 18px;
            font-size: 14px;
            line-height: 1.4;
        }
        
        .message.bot .message-content {
            background: white;
            color: #374151;
            border-bottom-left-radius: 6px;
        }
        
        .message.user .message-content {
            background: #2563EB;
            color: white;
            border-bottom-right-radius: 6px;
        }
        
        .chatbot-input {
            padding: 20px;
            background: white;
            border-top: 1px solid #E5E7EB;
        }
        
        .input-group {
            display: flex;
            gap: 10px;
            align-items: center;
        }
        
        .input-group input {
            flex: 1;
            padding: 12px 16px;
            border: 2px solid #E5E7EB;
            border-radius: 25px;
            outline: none;
            font-size: 14px;
            transition: border-color 0.3s;
        }
        
        .input-group input:focus {
            border-color: #2563EB;
        }
        
        .send-btn {
            width: 40px;
            height: 40px;
            background: #2563EB;
            color: white;
            border: none;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.3s;
        }
        
        .send-btn:hover {
            background: #1D4ED8;
            transform: scale(1.1);
        }
        
        .send-btn:disabled {
            background: #9CA3AF;
            cursor: not-allowed;
            transform: none;
        }
        
        .typing-indicator {
            display: none;
            padding: 12px 16px;
            background: white;
            border-radius: 18px;
            border-bottom-left-radius: 6px;
            max-width: 80%;
        }
        
        .typing-dots {
            display: flex;
            gap: 4px;
        }
        
        .typing-dots span {
            width: 8px;
            height: 8px;
            background: #9CA3AF;
            border-radius: 50%;
            animation: typing 1.4s infinite;
        }
        
        .typing-dots span:nth-child(2) {
            animation-delay: 0.2s;
        }
        
        .typing-dots span:nth-child(3) {
            animation-delay: 0.4s;
        }
        
        @keyframes typing {
            0%, 60%, 100% {
                transform: translateY(0);
            }
            30% {
                transform: translateY(-10px);
            }
        }
        
        .quick-actions {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
            margin-top: 10px;
        }
        
        .quick-action {
            background: #E5E7EB;
            color: #374151;
            padding: 6px 12px;
            border-radius: 15px;
            font-size: 12px;
            cursor: pointer;
            transition: all 0.3s;
            border: none;
        }
        
        .quick-action:hover {
            background: #2563EB;
            color: white;
        }
        
        @media (max-width: 768px) {
            .chatbot-container {
                width: 300px;
                height: 450px;
            }
        }
    </style>
    
    <div class="chatbot-widget">
        <button class="chatbot-toggle" onclick="toggleChatbot()">
            <i class="bi bi-chat-dots" id="chatbot-icon"></i>
        </button>
        
        <div class="chatbot-container" id="chatbot-container">
            <div class="chatbot-header">
                <h3><i class="bi bi-robot"></i> Assistant Kernel</h3>
                <p>Posez-moi vos questions sur les projets !</p>
            </div>
            
            <div class="chatbot-messages" id="chatbot-messages">
                <div class="message bot">
                    <div class="message-avatar">🤖</div>
                    <div class="message-content">
                        Bonjour ! Je suis votre assistant virtuel. Comment puis-je vous aider aujourd'hui ?
                        <div class="quick-actions">
                            <button class="quick-action" onclick="sendQuickMessage('Combien de projets ?')">Nombre de projets</button>
                            <button class="quick-action" onclick="sendQuickMessage('Dernier projet')">Dernier projet</button>
                            <button class="quick-action" onclick="sendQuickMessage('Actualités')">Actualités</button>
                            <button class="quick-action" onclick="sendQuickMessage('aide')">Aide</button>
                        </div>
                    </div>
                </div>
                
                <div class="message bot typing-indicator" id="typing-indicator">
                    <div class="message-avatar">🤖</div>
                    <div class="typing-indicator">
                        <div class="typing-dots">
                            <span></span>
                            <span></span>
                            <span></span>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="chatbot-input">
                <div class="input-group">
                    <input type="text" id="chatbot-input" placeholder="Tapez votre message..." onkeypress="handleKeyPress(event)">
                    <button class="send-btn" onclick="sendMessage()" id="send-btn">
                        <i class="bi bi-send"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>
    
    <script>
        let chatbotOpen = false;
        
        function toggleChatbot() {
            const container = document.getElementById('chatbot-container');
            const toggle = document.querySelector('.chatbot-toggle');
            const icon = document.getElementById('chatbot-icon');
            
            chatbotOpen = !chatbotOpen;
            
            if (chatbotOpen) {
                container.classList.add('active');
                toggle.classList.add('active');
                icon.className = 'bi bi-x-lg';
                document.getElementById('chatbot-input').focus();
            } else {
                container.classList.remove('active');
                toggle.classList.remove('active');
                icon.className = 'bi bi-chat-dots';
            }
        }
        
        function handleKeyPress(event) {
            if (event.key === 'Enter') {
                sendMessage();
            }
        }
        
        function sendQuickMessage(message) {
            document.getElementById('chatbot-input').value = message;
            sendMessage();
        }
        
        async function sendMessage() {
            const input = document.getElementById('chatbot-input');
            const message = input.value.trim();
            
            if (!message) return;
            
            // Ajouter le message de l'utilisateur
            addMessage(message, 'user');
            input.value = '';
            
            // Désactiver le bouton d'envoi
            const sendBtn = document.getElementById('send-btn');
            sendBtn.disabled = true;
            
            // Afficher l'indicateur de frappe
            showTypingIndicator();
            
            try {
                // Envoyer la requête au chatbot
                const response = await fetch('../../api/chatbot.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({ question: message })
                });
                
                const data = await response.json();
                
                // Masquer l'indicateur de frappe
                hideTypingIndicator();
                
                // Ajouter la réponse du bot
                if (data.success) {
                    addMessage(data.message, 'bot');
                } else {
                    addMessage('Désolé, je n\'ai pas pu traiter votre demande.', 'bot');
                }
                
            } catch (error) {
                hideTypingIndicator();
                addMessage('Erreur de connexion. Veuillez réessayer.', 'bot');
            }
            
            // Réactiver le bouton d'envoi
            sendBtn.disabled = false;
        }
        
        function addMessage(content, sender) {
            const messagesContainer = document.getElementById('chatbot-messages');
            const messageDiv = document.createElement('div');
            messageDiv.className = `message ${sender}`;
            
            const avatar = sender === 'bot' ? '🤖' : '👤';
            
            messageDiv.innerHTML = `
                <div class="message-avatar">${avatar}</div>
                <div class="message-content">${content}</div>
            `;
            
            // Insérer avant l'indicateur de frappe
            const typingIndicator = document.getElementById('typing-indicator');
            messagesContainer.insertBefore(messageDiv, typingIndicator);
            
            // Faire défiler vers le bas
            messagesContainer.scrollTop = messagesContainer.scrollHeight;
        }
        
        function showTypingIndicator() {
            document.getElementById('typing-indicator').style.display = 'flex';
            const messagesContainer = document.getElementById('chatbot-messages');
            messagesContainer.scrollTop = messagesContainer.scrollHeight;
        }
        
        function hideTypingIndicator() {
            document.getElementById('typing-indicator').style.display = 'none';
        }
    </script>
    <?php
    return ob_get_clean();
}
?>