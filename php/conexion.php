<?php
$conn = new mysqli("localhost", "root", "", "security_hub");
$conn->set_charset("utf8mb4");

if ($conn->connect_error) {
  die("Error de conexión");
}
?>