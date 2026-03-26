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

  $id          = (int)($data['id'] ?? 0);
  $user_id     = (int) $_SESSION['user_id'];
  $titulo      = trim($data['titulo'] ?? '');
  $fecha       = $data['fecha'] ?? '';
  $hora        = $data['hora'] ?? '00:00:00';
  $descripcion = trim($data['descripcion'] ?? '');
  $color       = $data['color'] ?? 'event-green';

  if ($id <= 0 || $titulo === '' || $fecha === '') {
    echo json_encode(['success' => false, 'message' => 'Faltan datos']);
    exit;
  }

  // ✅ Solo puede actualizar sus propios eventos (user_id = sesión)
  $sql = "UPDATE eventos
          SET titulo = ?, descripcion = ?, fecha = ?, hora = ?, color = ?
          WHERE id = ? AND user_id = ?";

  $stmt = $conn->prepare($sql);
  $stmt->bind_param("sssssii", $titulo, $descripcion, $fecha, $hora, $color, $id, $user_id);

  if ($stmt->execute()) {
    if ($stmt->affected_rows > 0) {
      echo json_encode(['success' => true, 'message' => 'Evento actualizado correctamente']);
    } else {
      // Sin filas afectadas: el evento no existe o no le pertenece
      echo json_encode(['success' => false, 'message' => 'No tienes permiso para editar este evento']);
    }
  } else {
    echo json_encode(['success' => false, 'message' => 'Error al actualizar el evento']);
  }

  $stmt->close();
}