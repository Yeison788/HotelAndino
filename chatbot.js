// Objeto con respuestas en español para el hotel
const responses = {
    "hola": "¡Hola! Bienvenido a Blue Bird Hotel. ¿En qué puedo ayudarte hoy?",
    "buenos días": "¡Buenos días! ¿Quieres hacer una reserva o necesitas información sobre nuestros servicios?",
    "reservar": "Para hacer una reserva, por favor indícame la fecha de llegada y salida.",
    "precios": "Nuestros precios varían según la temporada y el tipo de habitación. ¿Quieres que te envíe una lista?",
    "servicios": "Ofrecemos WiFi gratis, desayuno incluido, piscina y gimnasio. ¿Quieres saber más sobre algún servicio?",
    "habitaciones": "Disponemos de habitaciones sencillas, dobles y suites. ¿Cuál prefieres?",
    "ubicación": "Estamos ubicados en el centro de la ciudad, cerca de los principales atractivos turísticos.",
    "contacto": "Puedes contactarnos al teléfono +123 456 7890 o al correo contacto@bluebirdhotel.com.",
    "gracias": "¡Con gusto! Si tienes más preguntas, aquí estaré para ayudarte.",
    "adiós": "¡Gracias por visitarnos! Que tengas un excelente día.",
    "default": "Lo siento, no entendí eso. ¿Podrías reformular tu pregunta o elegir una de las opciones? 😊",
    "expert": "¡Perfecto! Un experto te atenderá en breve.",
    "no": "Está bien, si cambias de opinión, aquí estaré."
};

// Event listeners para botones e input
document.getElementById('chatbot-toggle-btn').addEventListener('click', toggleChatbot);
document.getElementById('close-btn').addEventListener('click', toggleChatbot);
document.getElementById('send-btn').addEventListener('click', sendMessage);
document.getElementById('user-input').addEventListener('keypress', function(e) {
    if (e.key === 'Enter') {
        sendMessage();
    }
});

// Mostrar u ocultar chatbot
function toggleChatbot() {
    const chatbotPopup = document.getElementById('chatbot-popup');
    chatbotPopup.style.display = chatbotPopup.style.display === 'none' ? 'block' : 'none';
}

// Enviar mensaje escrito por usuario
function sendMessage() {
    const userInput = document.getElementById('user-input').value.trim().toLowerCase();
    if (userInput !== '') {
        appendMessage('user', userInput);
        respondToUser(userInput);
        document.getElementById('user-input').value = '';
    }
}

// Responder al usuario buscando palabra clave en el input
function respondToUser(userInput) {
    let response = responses["default"];
    for (const key in responses) {
        if (key !== "default" && userInput.includes(key)) {
            response = responses[key];
            break;
        }
    }
    setTimeout(() => {
        appendMessage('bot', response);
    }, 500);
}

// Mostrar mensaje en la pantalla
function appendMessage(sender, message) {
    const chatBox = document.getElementById('chat-box');
    const messageElement = document.createElement('div');
    messageElement.classList.add(sender === 'user' ? 'user-message' : 'bot-message');
    messageElement.innerHTML = message;
    chatBox.appendChild(messageElement);
    chatBox.scrollTop = chatBox.scrollHeight;

    // Mostrar botones solo si el bot responde con el mensaje por defecto
    if (sender === 'bot' && message === responses["default"]) {
        const buttonYes = document.createElement('button');
        buttonYes.textContent = '✔ Sí';
        buttonYes.onclick = function() {
            appendMessage('bot', responses["expert"]);
        };
        const buttonNo = document.createElement('button');
        buttonNo.textContent = '✖ No';
        buttonNo.onclick = function() {
            appendMessage('bot', responses["no"]);
        };
        const buttonContainer = document.createElement('div');
        buttonContainer.classList.add('button-container');
        buttonContainer.appendChild(buttonYes);
        buttonContainer.appendChild(buttonNo);
        chatBox.appendChild(buttonContainer);
    }
}
