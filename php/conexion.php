<?php
$conn = new mysqli("localhost", "root", "", "security_hub");

if ($conn->connect_error) {
  die("Error de conexión");
}
?>