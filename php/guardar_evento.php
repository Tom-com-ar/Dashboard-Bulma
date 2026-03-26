<?php
include "conexion.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents("php://input"), true);
    
    $titulo = $data['titulo'] ?? '';
    $fecha = $data['fecha'] ?? '';
    $hora = $data['hora'] ?? '00:00:00';
    $color = $data['color'] ?? 'event-green';
    $tipo = '';
    $descripcion = $data['descripcion'] ?? '';
    
    if (!empty($titulo) && !empty($fecha)) {
        $sql = "INSERT INTO eventos (titulo, descripcion, fecha, hora, tipo, color) 
                VALUES (?, ?, ?, ?, ?, ?)";
        
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssssss", $titulo, $descripcion, $fecha, $hora, $tipo, $color);
        
        if ($stmt->execute()) {
            echo json_encode(['success' => true, 'message' => 'Evento guardado correctamente']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Error al guardar el evento']);
        }
        $stmt->close();
    } else {
        echo json_encode(['success' => false, 'message' => 'Faltan datos']);
    }
}
?>
