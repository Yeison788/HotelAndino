<?php
declare(strict_types=1);

session_start();
header('Content-Type: application/json; charset=utf-8');

require_once __DIR__ . '/config.php';
require_once __DIR__ . '/includes/guest_portal.php';
require_once __DIR__ . '/includes/chatbot_responder.php';

$payload = json_decode(file_get_contents('php://input'), true);
$userMsg = is_array($payload) ? ($payload['message'] ?? '') : '';

$responder = new HotelChatbotResponder($conn);
$reply = $responder->handle(is_string($userMsg) ? $userMsg : '');

echo json_encode([
    'reply' => $reply,
], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
?>
