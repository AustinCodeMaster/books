<?php
require_once 'conf.php';

$query = "SELECT id, username, email, user_password FROM users";
$result = $conn->query($query);

echo "<h2>Users in Database:</h2>";
if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        echo "ID: " . $row["id"] . "<br>";
        echo "Username: " . $row["username"] . "<br>";
        echo "Email: " . $row["email"] . "<br>";
        echo "Password: " . $row["user_password"] . "<br>";
        echo "----------------------<br>";
    }
} else {
    echo "No users found in database";
}

// Also show the SQL table structure
echo "<h2>Table Structure:</h2>";
$result = $conn->query("DESCRIBE users");
while($row = $result->fetch_assoc()) {
    echo "Field: " . $row["Field"] . 
         " | Type: " . $row["Type"] . 
         " | Null: " . $row["Null"] . 
         " | Key: " . $row["Key"] . "<br>";
}
?>