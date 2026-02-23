<?php
// Database configuration
$host     = "localhost";   // usually 'localhost'
$username = "root";        // your MySQL username
$password = "";            // your MySQL password
$dbname   = "watatlas";      // your database name

// Create connection
$conn = new mysqli($host, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Optional: set charset to UTF-8 for proper encoding
$conn->set_charset("utf8");
?>
