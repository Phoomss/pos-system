<?php
$host = "localhost";
$user = "root";
$password = "";
$dbName = "pos_system";

$conn = new mysqli($host, $user, $password, $dbName);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// echo "Connected successfully";
