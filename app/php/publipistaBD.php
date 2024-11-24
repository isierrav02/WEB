<?php
$servername = "db";
$username = "lamp";
$password = "lamp";
$dbname = "publipista";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}
