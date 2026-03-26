<?php
session_start();

if (!empty($_SESSION['user_id'])) {
  header("Location: ./index.php");
  exit;
}

$error = $_GET['error'] ?? '';
$message = $_GET['message'] ?? '';
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bulma@1.0.4/css/bulma.min.css">
  <link rel="stylesheet" href="./css/style.css">
  <title>Iniciar Sesión</title>
</head>

<body>
  <header>
    <nav class="navbar">
      <div class="navbar-brand">
        <div class="navbar-item">
          <h1 class="title is-4">Dashboard</h1>
        </div>
      </div>

      <div class="navbar-end">
        <div class="navbar-item">
          <a class="button" href="./registro.php">Registro</a>
        </div>

        <div class="navbar-item">
          <button class="button" id="themeToggle">
            <span id="themeIcon">Dark</span>
          </button>
        </div>
      </div>
    </nav>
  </header>

  <section class="hero is-fullheight">
    <div class="hero-body">
      <div class="container">
        <div class="columns is-mobile is-centered">
          <div class="column is-4">
            <div class="box">
              <h2 class="title is-3 has-text-centered">Iniciar Sesión</h2>

              <?php if (!empty($error)) : ?>
                <div class="notification is-danger">
                  <?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?>
                </div>
              <?php endif; ?>

              <?php if (!empty($message)) : ?>
                <div class="notification is-success">
                  <?= htmlspecialchars($message, ENT_QUOTES, 'UTF-8'); ?>
                </div>
              <?php endif; ?>

              <form method="POST" action="./php/user/login.php">
                <div class="field">
                  <label class="label" for="usuario">Usuario o Email</label>
                  <div class="control">
                    <input
                      class="input"
                      type="text"
                      id="usuario"
                      name="usuario"
                      placeholder="tuusuario o correo@dominio.com"
                      required
                      autocomplete="username"
                    >
                  </div>
                </div>

                <div class="field">
                  <label class="label" for="password">Contraseña</label>
                  <div class="control">
                    <input
                      class="input"
                      type="password"
                      id="password"
                      name="password"
                      placeholder="Tu contraseña"
                      required
                      autocomplete="current-password"
                    >
                  </div>
                </div>

                <div class="field">
                  <button class="button is-info is-fullwidth" type="submit">Entrar</button>
                </div>

              </form>
            </div>

            <p class="has-text-centered">
              ¿No tienes cuenta? <a href="./registro.php">Regístrate</a>
            </p>
          </div>
        </div>
      </div>
    </div>
  </section>

  <script src="./js/main.js"></script>
</body>
</html>

