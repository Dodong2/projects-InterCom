<?php
class SSEController {

    // SSE for real-time chat
    public function stream_messages() {
        global $conn;

        header('Content-Type: text/event-stream');
        header('Cache-Control: no-cache');
        header('Connection: keep-alive');

        // Loop to keep connection alive and send updates
        while (true) {
            $query = 'SELECT messages.id, messages.user_id, messages.message, messages.create_at, reglog.username
                      FROM messages
                      JOIN reglog ON messages.user_id = reglog.id
                      ORDER BY messages.create_at ASC';

            $result = $conn->query($query);
            $messages = [];

            while ($row = $result->fetch_assoc()) {
                $messages[] = $row;
            }

            echo "data: " . json_encode(['success' => true, 'messages' => $messages]) . "\n\n";
            ob_flush();
            flush();

            // Delay to reduce server load
            sleep(1);
        }
    }
}

?>
