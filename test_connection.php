<?php
require_once 'conf.php';

if (isset($conn)) {
    if ($conn->connect_error) {
        echo "Connection failed: " . $conn->connect_error;
    } else {
        echo "Connection successful! Connected to database: " . $conf['db_name'];
        echo "\nServer info: " . $conn->server_info;
    }
} else {
    echo "Database connection variable (\$conn) not found!";
}
?>