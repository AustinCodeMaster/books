<?php
require_once 'conf.php';

$username = "Austin Maina";
$email = "austin.maina@strathmore.edu";
$password = "1234"; // You can change this if you want
$verification_code = ""; // Initially empty

$query = "INSERT INTO users (username, email, user_password, verification_code) VALUES (?, ?, ?, ?)";
$stmt = $conn->prepare($query);
$stmt->bind_param("ssss", $username, $email, $password, $verification_code);

if ($stmt->execute()) {
    echo "New user created successfully with:<br>";
    echo "Email: " . $email . "<br>";
    echo "Password: " . $password . "<br>";
    echo "<br>You can now use these credentials to log in.";
} else {
    echo "Error creating user: " . $stmt->error;
}
?>