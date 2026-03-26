<?php
session_start();

if (empty($_SESSION['user_id'])) {
  http_response_code(401);
  header('Content-Type: application/json; charset=utf-8');
  echo json_encode(['success' => false, 'message' => 'No autorizado']);
  exit;
}

include "../conexion.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $data = json_decode(file_get_contents("php://input"), true);

  $id = (int)($data['id'] ?? 0);
  $titulo = trim($data['titulo'] ?? '');
  $fecha = $data['fecha'] ?? '';
  $hora = $data['hora'] ?? '00:00:00';
  $descripcion = $data['descripcion'] ?? '';
  $color = $data['color'] ?? 'event-green';

  if ($id <= 0 || $titulo === '' || $fecha === '') {
    echo json_encode(['success' => false, 'message' => 'Faltan datos']);
    exit;
  }

  $sql = "UPDATE eventos
          SET titulo = ?, descripcion = ?, fecha = ?, hora = ?, color = ?
          WHERE id = ?";

  $stmt = $conn->prepare($sql);
  $stmt->bind_param("sssssi", $titulo, $descripcion, $fecha, $hora, $color, $id);

  if ($stmt->execute()) {
    echo json_encode(['success' => true, 'message' => 'Evento actualizado correctamente']);
  } else {
    echo json_encode(['success' => false, 'message' => 'Error al actualizar el evento']);
  }

  $stmt->close();
}

