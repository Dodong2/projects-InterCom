<?php
//Step 1
class RegLogController
{

    //Register ka muna
    public function register()
    {
        global $conn;

        $username = $_POST['username'] ?? '';
        $email = $_POST['email'] ?? '';
        $password = $_POST['password'] ?? '';

        $response = [];

        //Validate Input if mali
        if (empty($username) || empty($email) || empty($password)) {
            $response = ['success' => false, 'message' => 'username, email, password cannot be empty'];
            echo json_encode($response);
            return;
        }

        //check if the email already exists na daw
        $stmt = $conn->prepare('SELECT * FROM reglog WHERE email = ?');
        $stmt->bind_param('s', $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $response = ['success' => false, 'message' => 'Email already exist'];
            echo json_encode($response);
            return;
        }

        // Hash password daw
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        //Insert into Database par
        $stmt = $conn->prepare('INSERT INTO reglog (username, email, password) VALUES (?, ?, ?)');
        if ($stmt === false) {
            $response = ['success' => false, 'message' => 'SQL prepare error: ' . $conn->error];
            echo json_encode($response);
            return;
        }

        $stmt->bind_param('sss', $username, $email, $password);

        if ($stmt->execute()) {
            $response = ['success' => true, 'message' => 'User registered successfully'];
        } else {
            $response = ['success' => false, 'message' => 'Failed to register user' . $stmt->error];
        }

        $stmt->close();
        echo json_encode($response);
    }

    //User login par
    public function login()
    {
        global $conn;

        $email = $_POST['email'] ?? '';
        $password = $_POST['password'] ?? '';

        //Validate input par
        if (empty($email) || empty($password)) {
            $response = ['success' => false, 'message' => 'Email and password cannot be empty'];
            echo json_encode($response);
            return;
        }

        // Check if the email exists par
        $stmt = $conn->prepare('SELECT * FROM reglog WHERE email = ?');
        $stmt->bind_param('s', $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 0) {
            $response = ['success' => false, 'message' => 'Invalid email or password'];
            echo json_encode($response);
            return;
        }

        $user = $result->fetch_assoc();

        //Verify password par
        // if (password_verify($password, $user['password'])) for password if hash
        if ($password === $user['password']) {
            $response = ['success' => true, 'message' => 'Login Successful'];
        } else {
            $response = ['success' => false, 'message' => 'Invalid email or password'];
        }

        echo json_encode($response);
    }
}
