<?php
$host = "localhost";
$user = "root";
$pass = "";
$dbname = "friendify";

$conn = new mysqli($host, $user, $pass, $dbname);

if ($conn->connect_error) {
    die("Eroare la conectarea la baza de date: " . $conn->connect_error);
}
?>
