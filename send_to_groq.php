<?php
header('Content-Type: application/json');

$data = json_decode(file_get_contents("php://input"), true);
$userMsg = $data['message'] ?? '';

$apiKey = "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"; // <-- pega aquí tu key de Groq

$ch = curl_init("https://api.groq.com/openai/v1/chat/completions");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    "Content-Type: application/json",
    "Authorization: Bearer $apiKey"
]);

curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode([
    "model" => "llama-3.1-8b-instant", // modelo recomendado (rápido y gratis)
    "messages" => [
        ["role" => "system", "content" => "Eres el asistente virtual del Hotel Andino, responde en español de manera amable, breve y clara."],
        ["role" => "user", "content" => $userMsg]
    ],
    "max_tokens" => 200,
    "temperature" => 0.7
]));

$response = curl_exec($ch);

if (curl_errno($ch)) {
    echo json_encode(["reply" => "⚠️ Error cURL: " . curl_error($ch)]);
    curl_close($ch);
    exit;
}

curl_close($ch);

$result = json_decode($response, true);

if (isset($result['error'])) {
    echo json_encode(["reply" => "⚠️ Error API: " . $result['error']['message']]);
    exit;
}

$reply = $result['choices'][0]['message']['content'] ?? "Lo siento, no entendí eso.";
echo json_encode(["reply" => $reply]);
?>
