<?php
session_start();

// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Database connection
    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "recipe_website";

    try {
        $conn = new mysqli($servername, $username, $password, $dbname);

        // Check connection
        if ($conn->connect_error) {
            throw new Exception("Connection failed: " . $conn->connect_error);
        }

        // Get and sanitize input
        $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
        $password = $_POST['password'];

        // Validate input
        if (empty($email) || empty($password)) {
            throw new Exception("Email and password are required");
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new Exception("Invalid email format");
        }

        // Prepare and execute query using prepared statements
        $stmt = $conn->prepare("SELECT id, username, email, password, is_admin FROM user WHERE email = ?");
        if (!$stmt) {
            throw new Exception("Query preparation failed");
        }

        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 0) {
            throw new Exception("No account found with this email");
        }

        $user = $result->fetch_assoc();

        // Verify password
        if (!password_verify($password, $user['password'])) {
            throw new Exception("Invalid password");
        }

        // Set session variables
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['email'] = $user['email'];
        $_SESSION['is_admin'] = $user['is_admin'];

        // Log the login attempt
        $login_stmt = $conn->prepare("INSERT INTO login (user_id, login_time) VALUES (?, NOW())");
        if (!$login_stmt) {
            throw new Exception("Failed to log login attempt");
        }

        $login_stmt->bind_param("i", $user['id']);
        $login_stmt->execute();
        $login_stmt->close();

        // Redirect based on user type
        if ($user['is_admin'] == 1) {
            header("Location: admin/dashboard.php");
        } else {
            header("Location: homepage.php");
        }
        exit();

    } catch (Exception $e) {
        // Set error message in session
        $_SESSION['login_error'] = $e->getMessage();
        header("Location: index.html");
        exit();
    } finally {
        // Close database connection
        if (isset($stmt)) {
            $stmt->close();
        }
        if (isset($conn)) {
            $conn->close();
        }
    }
} else {
    // If someone tries to access this file directly
    header("Location: index.html");
    exit();
}
?>