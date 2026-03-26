<?php
session_start();

if (empty($_SESSION['user_id'])) {
  http_response_code(401);
  header('Content-Type: application/json; charset=utf-8');
  echo json_encode(['success' => false, 'message' => 'No autorizado']);
  exit;
}

include "../conexion.php";

$result = $conn->query("SELECT * FROM eventos");

$eventos = [];

while ($row = $result->fetch_assoc()) {
    $eventos[] = $row;
}

echo json_encode($eventos);