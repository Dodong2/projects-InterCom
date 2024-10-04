<?php

class ChatController {

    // Create a chat message
    public function create_chat() {
        global $conn;

        $userId = $_SESSION['user_id'] ?? null;

        if ($userId === null) {
            $response = ['success' => false, 'message' => 'User not logged in'];
            echo json_encode($response);
            return;
        }

        $message = $_POST['message'] ?? '';

        // Validate input
        if (empty($message)) {
            $response = ['success' => false, 'message' => 'Message cannot be empty'];
            echo json_encode($response);
            return;
        }

        // Insert data into the database
        $stmt = $conn->prepare('INSERT INTO messages (user_id, message) VALUES (?, ?)');
        if ($stmt === false) {
            $response = ['success' => false, 'message' => 'SQL prepare error: ' . $conn->error];
            echo json_encode($response);
            return;
        }

        $stmt->bind_param('is', $userId, $message);

        if ($stmt->execute()) {
            $response = ['success' => true, 'message' => 'Message sent successfully'];
        } else {
            $response = ['success' => false, 'message' => 'Failed to send message: ' . $stmt->error];
        }

        $stmt->close();
        echo json_encode($response);
    }

    // Get all messages with username
    public function get_chats() {
        global $conn;
    
        $query = 'SELECT messages.id, messages.user_id, messages.message, messages.create_at, reglog.username
                  FROM messages
                  JOIN reglog ON messages.user_id = reglog.id
                  ORDER BY messages.create_at ASC';
    
        $result = $conn->query($query);
        
        // Check if the query was successful
        if ($result === false) {
            $response = ['success' => false, 'message' => 'Failed to fetch messages: ' . $conn->error];
            echo json_encode($response);
            return;
        }
    
        $messages = [];
        while ($row = $result->fetch_assoc()) {
            $messages[] = $row;
        }
    
        $response = ['success' => true, 'messages' => $messages];
        echo json_encode($response);
    }
    
}

?>
