<?php
$host = "localhost";      // Host name (usually localhost)
$username = "root";       // MySQL username
$password = "123456789";           // MySQL password
$database = "agriconnect";    // Your database name

// Create connection
$conn = new mysqli($host, $username, $password, $database);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}else{
    echo "Connected successfully";
}

// Optional: set charset to utf8mb4
$conn->set_charset("utf8mb4");

// echo "Connected successfully"; // Uncomment for testing
?>
