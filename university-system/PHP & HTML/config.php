<?php
// Database configuration
$servername = "localhost";
$username = "root"; // Change if needed
$password = ""; // Change if needed
$dbname = "university";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Set charset to utf8
$conn->set_charset("utf8");
?>