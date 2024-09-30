<?php

define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'intercomm');

//connection
$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

if($conn->connect_error) {
    $response = array (
        'success' => 'true',
        'message' => 'Database connection failed: ' . $conn->connect_error 
    );
    echo json_encode($response);
    exit();
}


?>