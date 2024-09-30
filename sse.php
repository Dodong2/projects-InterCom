<?php

require 'config.php';

header('Content-Type: text/event-stream');
header('Cache-Control: no-cache');
header('Connection: keep-alive');

// Allow the script to run indefinitely
set_time_limit(0);

$lastEventId = isset($_GET['lastEventId']) ? (int)$_GET['lastEventId'] : 0;

// Function to fetch new messages from the database
function fetchNewMessages($lastEventId) {
    global $conn;

    $stmt = $conn->prepare('SELECT messages.*, reglog.username FROM messages JOIN reglog ON messages.user_id = reglog.id WHERE messages.id > ? ORDER BY messages.id ASC');
    
    // Check if prepare was successful
    if ($stmt === false) {
        error_log('SQL prepare error: ' . $conn->error);
        return [];
    }

    $stmt->bind_param('i', $lastEventId);
    $stmt->execute();
    $result = $stmt->get_result();

    $messages = [];
    while ($row = $result->fetch_assoc()) {
        $messages[] = $row;
    }
    $stmt->close();
    return $messages;
}

$startTime = time();
$timeout = 30; // Set a timeout for 30 seconds

// Loop to keep the connection open for a limited time
while (true) {
    // Break the loop after the timeout period
    if ((time() - $startTime) > $timeout) {
        break;
    }

    $messages = fetchNewMessages($lastEventId);

    if (!empty($messages)) {
        foreach ($messages as $message) {
            $lastEventId = $message['id'];
            echo "id: {$lastEventId}\n";
            echo "data: " . json_encode($message) . "\n\n";
        }
        flush();
    }

    // Send a comment to keep the connection alive
    echo ": keep-alive\n\n";
    flush();

    // Wait for 1 second before checking for new messages
    sleep(1);
}

echo "retry: 1000\n"; // Suggest the client to retry after 10 seconds
?>
