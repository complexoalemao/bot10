<?php
// Inclui o arquivo de configuração
include 'config.php';

// Função para enviar mensagens para o Telegram
function sendMessage($chat_id, $text) {
    $url = API_URL . "sendMessage?chat_id=" . $chat_id . "&text=" . urlencode($text);
    file_get_contents($url);
}

// Obtém o conteúdo da requisição recebida do Telegram
$update = file_get_contents("php://input");
$update = json_decode($update, TRUE);

// Verifica se a requisição contém uma mensagem
if (isset($update["message"])) {
    $chat_id = $update["message"]["chat"]["id"];
    $message_text = $update["message"]["text"];

    // Comando /start
    if ($message_text == "/start") {
        sendMessage($chat_id, "Olá! Envie-me uma lista de cartões para verificar.");
        exit;
    }

    // Se não for um comando, trata como uma lista de cartões
    // Inclui e executa a lógica de verificação do 3ds.php
    $_GET['lista'] = $message_text; // Simula o GET para o 3ds.php
    
    ob_start();
    include '3ds.php';
    $response = ob_get_clean();
    
    sendMessage($chat_id, $response);
}
?>