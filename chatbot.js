// ===== Chatbot: abrir/cerrar desde FAB, saludo, persistencia, envío al responder interno =====

const DEFAULT_SUGGESTIONS = [
  { label: 'Reservar habitación', value: 'Reservar habitación' },
  { label: 'Solicitar servicio a la habitación', value: 'Solicitar servicio a la habitación' },
  { label: 'Ver estado de mi reserva', value: 'Estado de mi reserva' },
  { label: 'Ver servicios del hotel', value: 'Servicios del hotel' },
];

const DEFAULT_META = {
  inputType: 'text',
  placeholder: 'Escribe tu mensaje o elige una opción',
};

let activeSuggestionContainers = [];
let isSending = false;

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
<<<<<<< ours
<<<<<<< ours
    appendMessage('bot', "👋 Hola, soy el asistente virtual del Hotel Andino. Puedo darte información del hotel y ayudarte a gestionar reservas. ¿En qué puedo apoyarte?");
=======
    appendMessage('bot', '👋 Hola, soy el asistente virtual del Hotel Andino. Puedo darte información del hotel, ayudarte a reservar o solicitar algo a tu habitación. ¿Qué necesitas hoy?', DEFAULT_SUGGESTIONS);
    applyInputMeta(DEFAULT_META);
>>>>>>> theirs
=======
    appendMessage('bot', '👋 Hola, soy el asistente virtual del Hotel Andino. Puedo darte información del hotel, ayudarte a reservar o solicitar algo a tu habitación. ¿Qué necesitas hoy?', DEFAULT_SUGGESTIONS);
    applyInputMeta(DEFAULT_META);
>>>>>>> theirs
  }

  if (shouldOpen) setTimeout(() => document.getElementById('user-input')?.focus(), 0);
  try { localStorage.setItem('chatOpen', String(shouldOpen)); } catch (e) {}
}

async function sendMessage(forcedText) {
  const inputField = document.getElementById('user-input');
  if (!inputField || isSending) return;

  const raw = typeof forcedText === 'string' ? forcedText : inputField.value;
  const userInput = raw.trim();
  if (userInput === '') return;

  if (typeof forcedText !== 'string') {
    inputField.value = '';
  }

  clearPendingSuggestions();
  appendMessage('user', userInput);

  await dispatchMessage(userInput);
}

async function dispatchMessage(message) {
  const inputField = document.getElementById('user-input');
  const sendBtn = document.getElementById('send-btn');

  setSendingState(true, inputField, sendBtn);

  try {
    const response = await fetch('send_to_groq.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ message }),
    });

<<<<<<< ours
<<<<<<< ours
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
=======
    if (!response.ok) {
      appendMessage('bot', '⚠️ Error en el servidor, intenta más tarde.');
      applyInputMeta(DEFAULT_META);
      return;
    }

    const data = await response.json();
    const payload = data?.reply ?? {};
    const replyText = typeof payload.message === 'string' && payload.message.trim() !== ''
      ? payload.message
      : 'Aquí estoy para ayudarte con información del Hotel Andino.';
    const suggestions = Array.isArray(payload.suggestions) ? payload.suggestions : [];
    appendMessage('bot', replyText, suggestions);
    applyInputMeta(payload.meta);
  } catch (err) {
    console.error(err);
    appendMessage('bot', '⚠️ Error de conexión. Por favor revisa tu red.');
    applyInputMeta(DEFAULT_META);
  } finally {
    setSendingState(false, inputField, sendBtn);
  }
}

=======
    if (!response.ok) {
      appendMessage('bot', '⚠️ Error en el servidor, intenta más tarde.');
      applyInputMeta(DEFAULT_META);
      return;
    }

    const data = await response.json();
    const payload = data?.reply ?? {};
    const replyText = typeof payload.message === 'string' && payload.message.trim() !== ''
      ? payload.message
      : 'Aquí estoy para ayudarte con información del Hotel Andino.';
    const suggestions = Array.isArray(payload.suggestions) ? payload.suggestions : [];
    appendMessage('bot', replyText, suggestions);
    applyInputMeta(payload.meta);
  } catch (err) {
    console.error(err);
    appendMessage('bot', '⚠️ Error de conexión. Por favor revisa tu red.');
    applyInputMeta(DEFAULT_META);
  } finally {
    setSendingState(false, inputField, sendBtn);
  }
}

>>>>>>> theirs
function appendMessage(sender, message, suggestions = []) {
  const chatBox = document.getElementById('chat-box');
  if (!chatBox) return;

  const bubble = document.createElement('div');
  bubble.className = sender === 'user' ? 'user-message' : 'bot-message';
  bubble.innerHTML = formatMessage(message ?? '');
  chatBox.appendChild(bubble);

  if (Array.isArray(suggestions) && suggestions.length > 0) {
    const optionsWrap = document.createElement('div');
    optionsWrap.className = 'chat-options';

    suggestions.forEach((opt) => {
      const label = typeof opt.label === 'string' ? opt.label : '';
      const value = typeof opt.value === 'string' ? opt.value : '';
      if (!label || !value) return;
      const btn = document.createElement('button');
      btn.type = 'button';
      btn.className = 'chat-option-btn';
      btn.textContent = label;
      btn.addEventListener('click', () => sendMessage(value));
      optionsWrap.appendChild(btn);
    });

    if (optionsWrap.children.length > 0) {
      chatBox.appendChild(optionsWrap);
      activeSuggestionContainers.push(optionsWrap);
    }
  }

  chatBox.scrollTop = chatBox.scrollHeight;
}

function clearPendingSuggestions() {
  while (activeSuggestionContainers.length) {
    const container = activeSuggestionContainers.pop();
    if (!container) continue;
    container.classList.add('chat-options--disabled');
    container.querySelectorAll('button').forEach((btn) => {
      btn.disabled = true;
    });
  }
}

function applyInputMeta(meta = {}) {
  const inputField = document.getElementById('user-input');
  if (!inputField) return;

  const type = typeof meta.inputType === 'string' ? meta.inputType : DEFAULT_META.inputType;
  try {
    const previousType = inputField.getAttribute('data-last-type') || inputField.type;
    inputField.type = type;
    if (previousType !== type) {
      inputField.value = '';
    }
    inputField.setAttribute('data-last-type', type);
  } catch (e) {
    inputField.type = 'text';
  }

  const placeholder = typeof meta.placeholder === 'string' && meta.placeholder.trim() !== ''
    ? meta.placeholder
    : DEFAULT_META.placeholder;
  inputField.placeholder = placeholder;
}

function setSendingState(state, inputField, sendBtn) {
  isSending = state;
  if (inputField) {
    inputField.disabled = state;
  }
  if (sendBtn) {
    sendBtn.disabled = state;
  }
}

function formatMessage(text) {
  const safe = String(text)
    .replace(/&/g, '&amp;')
    .replace(/</g, '&lt;')
    .replace(/>/g, '&gt;');
  return safe.replace(/\n/g, '<br>');
}

// ===== Listeners y restauración =====
document.addEventListener('DOMContentLoaded', () => {
  const sendBtn = document.getElementById('send-btn');
  if (sendBtn) {
    sendBtn.addEventListener('click', () => sendMessage());
  }

  const userInput = document.getElementById('user-input');
  if (userInput) {
    userInput.addEventListener('keypress', (e) => {
      if (e.key === 'Enter') {
        e.preventDefault();
        sendMessage();
      }
    });
  }

  const fab = document.getElementById('chat-fab');
  if (fab) fab.addEventListener('click', () => toggleChatbot());

  const closeBtn = document.getElementById('close-btn');
  if (closeBtn) closeBtn.addEventListener('click', () => toggleChatbot(true));

  try {
    const saved = localStorage.getItem('chatOpen');
    if (saved === 'true') toggleChatbot(false);
  } catch (e) {}
});

// Cerrar con ESC
document.addEventListener('keydown', (e) => {
  if (e.key === 'Escape') {
    const popup = document.getElementById('chatbot-popup');
    const isOpen = popup && !(popup.style.display === 'none' || popup.style.display === '');
    if (isOpen) toggleChatbot(true);
  }
>>>>>>> theirs
});
