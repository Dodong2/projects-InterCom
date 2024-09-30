<?php

require 'config.php';
require 'controller/reglog.php';
require 'controller/chat.php';

$chatController = new ChatController();
$RegLogController = new RegLogController();

$action = $_GET['action'] ?? '';

switch($action) {
    case 'register':
        $RegLogController->register();
        break;
    case 'login':
        $RegLogController->login();
        break;
    case 'send':
        $chatController->send_message();
        break;
    case 'get':
        $chatController->get_messages();
        break;    
    default: 
    echo json_encode(['success' => false, 'message' => 'Invalid Action']);
    break;
}

?>