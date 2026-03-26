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
    
    $user_id     = (int) $_SESSION['user_id'];
    $titulo      = trim($data['titulo'] ?? '');
    $fecha       = $data['fecha'] ?? '';
    $hora        = $data['hora'] ?? '00:00:00';
    $color       = $data['color'] ?? 'event-green';
    $tipo        = 'personal';
    $descripcion = trim($data['descripcion'] ?? '');
    
    if (empty($titulo) || empty($fecha)) {
        echo json_encode(['success' => false, 'message' => 'Faltan datos']);
        exit;
    }

    $sql = "INSERT INTO eventos (user_id, titulo, descripcion, fecha, hora, tipo, color) 
            VALUES (?, ?, ?, ?, ?, ?, ?)";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("issssss", $user_id, $titulo, $descripcion, $fecha, $hora, $tipo, $color);
    
    if ($stmt->execute()) {
        $newId = $conn->insert_id;
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode([
            'success' => true,
            'message' => 'Evento guardado correctamente',
            'id'      => $newId
        ]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Error al guardar el evento']);
    }
    $stmt->close();
}
?>