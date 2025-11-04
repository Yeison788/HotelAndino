// ===== Chatbot: abrir/cerrar desde FAB, saludo, persistencia, envío a Groq =====

// Toggle del popup. Si pasas true, fuerza cerrar.
function toggleChatbot(forceClose) {
  const chatbotPopup = document.getElementById('chatbot-popup');
  const fab = document.getElementById('chat-fab');
  if (!chatbotPopup) return;

  const isHidden = chatbotPopup.style.display === 'none' || chatbotPopup.style.display === '';
  const shouldOpen = (typeof forceClose === 'boolean') ? !forceClose : isHidden;

  chatbotPopup.style.display = shouldOpen ? 'block' : 'none';
  chatbotPopup.classList.toggle('open', shouldOpen);

  if (fab) {
    fab.setAttribute('data-open', String(shouldOpen));
    fab.setAttribute('aria-label', shouldOpen ? 'Cerrar chat' : 'Abrir chat');
    fab.title = shouldOpen ? 'Cerrar chat' : 'Chatear';
  }

  if (shouldOpen && document.getElementById('chat-box')?.children.length === 0) {
    appendMessage('bot', "👋 Hola, soy el asistente virtual del Hotel Andino. Puedo darte información del hotel y ayudarte a gestionar reservas. ¿En qué puedo apoyarte?");
  }

  if (shouldOpen) setTimeout(() => document.getElementById('user-input')?.focus(), 0);
  try { localStorage.setItem('chatOpen', String(shouldOpen)); } catch(e) {}
}

// Enviar mensaje (👉 usa Groq)
async function sendMessage() {
  const inputField = document.getElementById('user-input');
  if (!inputField) return;
  const userInput = inputField.value.trim();
  if (userInput === '') return;

  appendMessage('user', userInput);
  inputField.value = '';

  try {
    const r = await fetch('send_to_groq.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ message: userInput })
    });

    if (r.ok) {
      const data = await r.json();
      appendMessage('bot', data.reply || 'Lo siento, no entendí eso.');
    } else {
      appendMessage('bot', '⚠️ Ocurrió un problema al conectar con el servidor.');
    }
  } catch (err) {
    console.error(err);
    appendMessage('bot', '⚠️ Error de conexión.');
  }
}

// Agregar mensajes al chat
function appendMessage(sender, text) {
  const box = document.getElementById('chat-box');
  if (!box) return;
  const div = document.createElement('div');
  div.className = 'chat-message ' + sender;
  div.textContent = text;
  box.appendChild(div);
  box.scrollTop = box.scrollHeight;
}

// Inicializar estado al cargar
window.addEventListener('DOMContentLoaded', () => {
  const open = localStorage.getItem('chatOpen') === 'true';
  if (open) toggleChatbot(false);
});
