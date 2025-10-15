<?php
$host = "localhost";
$user = "root";
$pass = "";
$dbname = "friendify_db";

$conn = new mysqli($host, $user, $pass, $dbname);

if ($conn->connect_error) {
    die("Error connecting to database: " . $conn->connect_error);
}
?>
