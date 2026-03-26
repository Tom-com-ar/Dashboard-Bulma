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
  if ($id <= 0) {
    echo json_encode(['success' => false, 'message' => 'ID inválido']);
    exit;
  }

  $stmt = $conn->prepare("DELETE FROM eventos WHERE id = ?");
  $stmt->bind_param("i", $id);

  if ($stmt->execute()) {
    echo json_encode(['success' => true, 'message' => 'Evento eliminado correctamente']);
  } else {
    echo json_encode(['success' => false, 'message' => 'Error al eliminar el evento']);
  }

  $stmt->close();
}

