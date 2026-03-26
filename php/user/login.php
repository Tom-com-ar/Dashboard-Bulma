<?php
session_start();
include "../conexion.php";

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
  header("Location: ../../login.php");
  exit;
}

$usuario = trim($_POST['usuario'] ?? '');
$password = $_POST['password'] ?? '';

if ($usuario === '' || $password === '') {
  header("Location: ../../login.php?error=" . urlencode("Completa todos los campos"));
  exit;
}

try {
  $user = null;

  if (strpos($usuario, '@') !== false) {
    $stmt = $conn->prepare("SELECT id, username, password_hash FROM users WHERE email = ?");
    $stmt->bind_param("s", $usuario);
  } else {
    $stmt = $conn->prepare("SELECT id, username, password_hash FROM users WHERE username = ?");
    $stmt->bind_param("s", $usuario);
  }

  $stmt->execute();
  $result = $stmt->get_result();
  $user = $result ? $result->fetch_assoc() : null;
  $stmt->close();

  if (!$user || !password_verify($password, $user['password_hash'])) {
    header("Location: ../../login.php?error=" . urlencode("Usuario o contraseña inválidos"));
    exit;
  }

  $_SESSION['user_id'] = (int) $user['id'];
  $_SESSION['username'] = $user['username'];

  header("Location: ../../index.php?login=1");
  exit;
} catch (Throwable $e) {
  header("Location: ../../login.php?error=" . urlencode("Error al iniciar sesión"));
  exit;
}

