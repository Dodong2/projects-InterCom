<?php

//Step 1
class RegLogController
{
    // Register function
    public function register()
    {
        global $conn;

        $username = $_POST['username'] ?? '';
        $email = $_POST['email'] ?? '';
        $password = $_POST['password'] ?? '';

        $response = [];

        // Validate input
        if (empty($username) || empty($email) || empty($password)) {
            $response = ['success' => false, 'message' => 'Username, email, password cannot be empty'];
            echo json_encode($response);
            return;
        }

        // Check if the email already exists
        $stmt = $conn->prepare('SELECT * FROM reglog WHERE email = ?');
        $stmt->bind_param('s', $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $response = ['success' => false, 'message' => 'Email already exists'];
            echo json_encode($response);
            return;
        }

        // Insert into Database
        $stmt = $conn->prepare('INSERT INTO reglog (username, email, password) VALUES (?, ?, ?)');
        if ($stmt === false) {
            $response = ['success' => false, 'message' => 'SQL prepare error: ' . $conn->error];
            echo json_encode($response);
            return;
        }

        // Here, consider hashing the password before storing it
        // $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        // $stmt->bind_param('sss', $username, $email, $hashedPassword);
        $stmt->bind_param('sss', $username, $email, $password); // Use hashed password in production!

        if ($stmt->execute()) {
            $response = ['success' => true, 'message' => 'User registered successfully'];
        } else {
            $response = ['success' => false, 'message' => 'Failed to register user: ' . $stmt->error];
        }

        $stmt->close();
        echo json_encode($response);
    }

    // User login function
    public function login()
    {
        global $conn;
    
        $email = $_POST['email'] ?? '';
        $password = $_POST['password'] ?? '';
    
        // Validate input
        if (empty($email) || empty($password)) {
            $response = ['success' => false, 'message' => 'Email and password cannot be empty'];
            echo json_encode($response);
            return;
        }
    
        // Check if the email exists
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
    
        // Here, check against the hashed password
        if ($password === $user['password']) { // Use hashed password comparison in production!
            $_SESSION['user_id'] = $user['id']; // Set session variable for user ID
            $response = ['success' => true, 'message' => 'Login Successful'];
        } else {
            $response = ['success' => false, 'message' => 'Invalid email or password'];
        }
    
        echo json_encode($response);
    }
    
}
