<?php
session_start();
include "../conexion.php";

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
  header("Location: ../../registro.php");
  exit;
}

if (!empty($_SESSION['user_id'])) {
  header("Location: ../../../index.php");
  exit;
}

$username = trim($_POST['username'] ?? '');
$email = trim($_POST['email'] ?? '');
$password = $_POST['password'] ?? '';
$confirm = $_POST['confirm_password'] ?? '';

function redirectWithError(string $location, string $error): void {
  header("Location: {$location}?error=" . urlencode($error));
  exit;
}

$errors = [];

if ($username === '' || strlen($username) < 3 || strlen($username) > 50) {
  $errors[] = "El usuario debe tener entre 3 y 50 caracteres.";
}

if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
  $errors[] = "El email no es válido.";
}

if ($password === '' || strlen($password) < 8) {
  $errors[] = "La contraseña debe tener al menos 8 caracteres.";
}

// Reglas simples para la contraseña (se pueden ajustar luego)
if (!preg_match('/\d/', $password)) {
  $errors[] = "La contraseña debe contener al menos un número.";
}
if (!preg_match('/[A-Z]/', $password)) {
  $errors[] = "La contraseña debe contener al menos una mayúscula.";
}
if (!preg_match('/[^a-zA-Z0-9]/', $password)) {
  $errors[] = "La contraseña debe contener al menos un carácter especial.";
}

if ($password !== $confirm) {
  $errors[] = "Las contraseñas no coinciden.";
}

if (!empty($errors)) {
  redirectWithError("../../registro.php", $errors[0]);
}

try {
  // Verificar que no exista usuario/email
  $stmt = $conn->prepare("SELECT id FROM users WHERE username = ? OR email = ? LIMIT 1");
  $stmt->bind_param("ss", $username, $email);
  $stmt->execute();
  $result = $stmt->get_result();
  $existing = $result ? $result->fetch_assoc() : null;
  $stmt->close();

  if ($existing) {
    redirectWithError("../../registro.php", "Ese usuario o email ya existe.");
  }

  $hash = password_hash($password, PASSWORD_DEFAULT);

  $insert = $conn->prepare(
    "INSERT INTO users (username, email, password_hash) VALUES (?, ?, ?)"
  );
  $insert->bind_param("sss", $username, $email, $hash);

  if (!$insert->execute()) {
    redirectWithError("../../registro.php", "Error al crear la cuenta.");
  }

  $insert->close();

  header("Location: ../../login.php?message=" . urlencode("Cuenta creada. Inicia sesión."));
  exit;
} catch (Throwable $e) {
  redirectWithError("../../registro.php", "Error al registrar.");
}

