<?php
// 1. Connect to the Database
$servername = "localhost";  // Replace with your server details
$username = "root";         // Replace with your DB username
$password = "";             // Replace with your DB password
$dbname = "recipe_website"; // Replace with your DB name

$conn = new mysqli($servername, $username, $password, $dbname);

// Check the connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// 2. Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // 3. Get the form data and sanitize it
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = mysqli_real_escape_string($conn, $_POST['password']);

    // 4. Validate the data (basic validation)
    if (empty($username) || empty($email) || empty($password)) {
        echo "All fields are required!";
        exit();
    }

    // 5. Check if the email is already taken
    $email_check_query = "SELECT * FROM user WHERE email='$email' LIMIT 1";
    $result = $conn->query($email_check_query);
    $user = $result->fetch_assoc();

    if ($user) {
        echo "Email is already registered!";
        exit();
    }

    // 6. Hash the password
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // 7. Insert the user data into the database
    $sql = "INSERT INTO user (username, email, password) 
            VALUES ('$username', '$email', '$hashed_password')";

    if ($conn->query($sql) === TRUE) {
        // Redirect to sample.html or the appropriate page
        header("Location: /dist/index.html"); // Assuming sample.html is in the same directory
        exit(); // Make sure the script stops after redirection
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
}

// Close the database connection
$conn->close();
?>
