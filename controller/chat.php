<?php
//step 2
class ChatController
{

    //Send Message ito
    public function send_message()
    {
        global $conn;

        $userId = $_POST['user_id'] ?? '';
        $message = $_POST['message'] ?? '';

        //Validate input
        if (empty($userId) || empty($message)) {
            $response = ['success' => false, 'message' => 'User ID and message cannot be empty'];
            echo json_encode($response);
            return;
        }
        // Insert data into the database
        $stmt = $conn->prepare('INSERT INTO messages (user_id, message) VALUES (?, ?)');
        $stmt->bind_param('is', $userId, $message);

        if ($stmt->execute()) {
            $response = ['success' => true, 'message' => 'Message sent successfully'];
        } else {
            $response = ['success' => false, 'messsage' => 'Failed to send message: ' . $stmt->error];
        }

        $stmt->close();
        echo json_encode($response);
    }

    //Get all messages
    public function get_messages()
{
    global $conn;

    // Execute the query
    $result = $conn->query('SELECT messages.*, reglog.username FROM messages JOIN reglog ON messages.user_id = reglog.id');

    // Check if the query was successful
    if ($result === false) {
        $response = ['success' => false, 'message' => 'Database query failed: ' . $conn->error];
        echo json_encode($response);
        return;
    }

    // Fetch messages
    $messages = [];
    while ($row = $result->fetch_assoc()) {
        $messages[] = $row;
    }

    // Send response
    $response = ['success' => true, 'messages' => $messages];
    echo json_encode($response);
}
    // public function get_messages()
    // {
    //     global $conn;

    //     $result = $conn->query('SELECT messages.*, reglog.username FROM messages JOIN reglog ON messages.user_id = reglog.id ORDER BY created_at ASC');
    //     $messages = [];

    //     while ($row = $result->fetch_assoc()) {
    //         $messages[] = $row;
    //     }
    //     $response = ['success' => true, 'message' => $messages];
    //     echo json_encode($response);
    // }

}
