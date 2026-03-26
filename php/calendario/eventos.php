<?php
session_start();

if (empty($_SESSION['user_id'])) {
  http_response_code(401);
  header('Content-Type: application/json; charset=utf-8');
  echo json_encode(['success' => false, 'message' => 'No autorizado']);
  exit;
}

include "../conexion.php";

$user_id = (int) $_SESSION['user_id'];


$sql = "
  SELECT 
    e.id,
    e.titulo,
    e.descripcion,
    e.fecha,
    e.hora,
    e.tipo,
    e.color,
    e.user_id,
    u.username
  FROM eventos e
  LEFT JOIN users u ON u.id = e.user_id
  WHERE e.user_id IS NULL
     OR e.user_id = ?
  ORDER BY e.fecha ASC, e.hora ASC
";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();

$result = $stmt->get_result();
$eventos = [];

while ($row = $result->fetch_assoc()) {
  $eventos[] = $row;
}

$stmt->close();

header('Content-Type: application/json; charset=utf-8');
echo json_encode($eventos);