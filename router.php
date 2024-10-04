<?php

require 'config.php';
require 'controller/reglog.php';
require 'controller/chat.php';
require 'controller/sse.php';

$chatController = new ChatController();
$RegLogController = new RegLogController();
$sseController = new SSEController();

$action = $_GET['action'] ?? '';

switch($action) {
    case 'register':
        $RegLogController->register();
        break;
    case 'login':
        $RegLogController->login();
        break;
    case 'create':
        $chatController->create_chat();
        break;
    case 'get':
        $chatController->get_chats();
        break;  
    case 'stream':
        $sseController->stream_messages();
        break;  
    default: 
    echo json_encode(['success' => false, 'message' => 'Invalid Action']);
    break;
}

?>