<?php
session_start();

// Verificar que el usuario esté autenticado
if (empty($_SESSION['user_id'])) {
    header("HTTP/1.1 401 Unauthorized");
    echo json_encode(['éxito' => false, 'mensaje' => 'No autorizado']);
    exit;
}

// Solo aceptar POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("HTTP/1.1 405 Method Not Allowed");
    echo json_encode(['éxito' => false, 'mensaje' => 'Método no permitido']);
    exit;
}

include "./conexion.php";

$user_id = $_SESSION['user_id'];
$password = isset($_POST['password']) ? $_POST['password'] : '';
$confirm_password = isset($_POST['confirm_password']) ? $_POST['confirm_password'] : '';

// Validaciones en el backend
$errores = [];

// Error 1: Menos de 8 caracteres
if (strlen($password) < 8) {
    $errores[] = 'La contraseña debe tener al menos 8 caracteres';
}

// Error 2: No contiene números
if (!preg_match('/\d/', $password)) {
    $errores[] = 'La contraseña debe contener números';
}

// Error 3: No contiene mayúsculas
if (!preg_match('/[A-Z]/', $password)) {
    $errores[] = 'La contraseña debe contener mayúsculas';
}

// Error 4: No contiene caracteres especiales
if (!preg_match('/[!@#$%^&*()_+\-=\[\]{};\':"\\|,.<>\/?]/', $password)) {
    $errores[] = 'La contraseña debe contener caracteres especiales';
}

// Verificar que las contraseñas coincidan
if ($password !== $confirm_password) {
    $errores[] = 'Las contraseñas no coinciden';
}

// Si hay errores, devolverlos
if (!empty($errores)) {
    header("HTTP/1.1 400 Bad Request");
    echo json_encode([
        'éxito' => false,
        'mensaje' => 'Errores en la validación',
        'errores' => $errores
    ]);
    exit;
}

// Hashear la contraseña usando password_hash
$password_hash = password_hash($password, PASSWORD_BCRYPT, ['cost' => 10]);

// Calcular nivel de seguridad
$security_level = 0;
if (strlen($password) >= 8) $security_level++;
if (preg_match('/\d/', $password)) $security_level++;
if (preg_match('/[A-Z]/', $password)) $security_level++;
if (preg_match('/[!@#$%^&*()_+\-=\[\]{};\':"\\|,.<>\/?]/', $password)) $security_level++;

$strength_label = 'débil';
if ($security_level == 2) $strength_label = 'media';
if ($security_level >= 3) $strength_label = 'fuerte';

// Guardar la contraseña en la base de datos users
$sql = "UPDATE users SET password_hash = ? WHERE id = ?";
$stmt = $conn->prepare($sql);

if (!$stmt) {
    header("HTTP/1.1 500 Internal Server Error");
    echo json_encode([
        'éxito' => false,
        'mensaje' => 'Error en la preparación de la consulta: ' . $conn->error
    ]);
    exit;
}

$stmt->bind_param('si', $password_hash, $user_id);

if (!$stmt->execute()) {
    header("HTTP/1.1 500 Internal Server Error");
    echo json_encode([
        'éxito' => false,
        'mensaje' => 'Error al guardar la contraseña: ' . $stmt->error
    ]);
    exit;
}

$stmt->close();

// Guardar registro en password_logs
$sql_log = "INSERT INTO password_logs (user_id, password_hash, security_level, strength_label) VALUES (?, ?, ?, ?)";
$stmt_log = $conn->prepare($sql_log);

if (!$stmt_log) {
    // No interrumpir si falla el log
    error_log("Error preparando log: " . $conn->error);
} else {
    $stmt_log->bind_param('isss', $user_id, $password_hash, $security_level, $strength_label);
    if (!$stmt_log->execute()) {
        // No interrumpir si falla el log
        error_log("Error ejecutando log: " . $stmt_log->error);
    }
    $stmt_log->close();
}

$conn->close();

// Si todo fue exitoso, devolver mensaje de éxito
header("HTTP/1.1 200 OK");
header("Content-Type: application/json");

// Redirigir después de 2 segundos
echo json_encode([
    'éxito' => true,
    'mensaje' => 'Contraseña guardada exitosamente',
    'redirigir' => '../pages/cambiar_contrasena.php'
]);

// Registrar en el archivo de log (opcional)
$log_file = __DIR__ . '/logs/password_changes.log';
if (!is_dir(__DIR__ . '/logs')) {
    mkdir(__DIR__ . '/logs', 0755, true);
}
file_put_contents(
    $log_file,
    date('Y-m-d H:i:s') . " - Usuario ID: $user_id - Contraseña actualizada\n",
    FILE_APPEND
);

exit;
?>
