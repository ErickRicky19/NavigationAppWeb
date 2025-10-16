<?php
session_start();
header('Content-Type: application/json');

$HF_API_KEY = "ApiFromHuggingFace"; 

$input = json_decode(file_get_contents('php://input'), true);
$message = trim($input['question'] ?? '');

if (empty($message)) {
    echo json_encode(['error' => 'Mensaje vacío']);
    exit;
}

$payload = [
    "model" => "zai-org/GLM-4.6:novita",
    "messages" => [
        ["role" => "system", "content" => "You are a friendly travel assistant AI. Answer clearly and concisely."],
        ["role" => "user", "content" => $message]
    ],
    "temperature" => 0.7,
    "max_new_tokens" => 200
];

$huggingface_url = "https://router.huggingface.co/v1/chat/completions";

$ch = curl_init($huggingface_url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    "Authorization: Bearer $HF_API_KEY",
    "Content-Type: application/json"
]);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));

$response = curl_exec($ch);
if ($response === false) {
    echo json_encode(['error' => curl_error($ch)]);
    curl_close($ch);
    exit;
}
curl_close($ch);

$res = json_decode($response, true);

file_put_contents(__DIR__ . '/hf_log.txt', print_r($res, true));

$ai_message = $res['choices'][0]['message']['content'] ?? "Sorry, Cannot Found your query.";

echo json_encode(['ai_message' => $ai_message], JSON_UNESCAPED_UNICODE);
?>
