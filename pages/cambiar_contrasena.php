<?php
session_start();

if (empty($_SESSION['user_id'])) {
    header("Location: ./login.php");
    exit;
}

include "../php/conexion.php";

$mensaje = "";
$tipo_mensaje = "";
$user_id = $_SESSION['user_id'];

// Obtener última contraseña registrada
$sql_ultima = "SELECT id, security_level, strength_label, fecha_creacion FROM password_logs 
               WHERE user_id = ? 
               ORDER BY fecha_creacion DESC 
               LIMIT 1";
$stmt_ultima = $conn->prepare($sql_ultima);
$stmt_ultima->bind_param('i', $user_id);
$stmt_ultima->execute();
$resultado_ultima = $stmt_ultima->get_result();
$ultima_contrasena = $resultado_ultima->fetch_assoc();
$stmt_ultima->close();

// Obtener últimos 5 registros
$sql_registros = "SELECT id, security_level, strength_label, fecha_creacion FROM password_logs 
                  WHERE user_id = ? 
                  ORDER BY fecha_creacion DESC 
                  LIMIT 5";
$stmt_registros = $conn->prepare($sql_registros);
$stmt_registros->bind_param('i', $user_id);
$stmt_registros->execute();
$resultado_registros = $stmt_registros->get_result();
$registros = $resultado_registros->fetch_all(MYSQLI_ASSOC);
$stmt_registros->close();

?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bulma@1.0.4/css/bulma.min.css">
    <link rel="stylesheet" href="../css/style.css">
    <title>Cambiar Contraseña - Dashboard</title>
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
                    <button class="button" id="themeToggle">
                        <span id="themeIcon">Dark</span>
                    </button>
                </div>
                <div class="navbar-item">
                    <a class="button is-light" href="../index.php">Volver</a>
                </div>
                <div class="navbar-item">
                    <a class="button" href="../php/user/logout.php">Salir</a>
                </div>
            </div>
        </nav>
    </header>

    <main>
        <div class="container" style="max-width: 600px; padding: 2rem 1.5rem;">
            <div class="box">
                <h2 class="title is-3">Cambiar Contraseña</h2>

                <?php if ($mensaje): ?>
                    <div class="notification is-<?php echo $tipo_mensaje === 'error' ? 'danger' : 'success'; ?>">
                        <button class="delete"></button>
                        <?php echo $mensaje; ?>
                    </div>
                <?php endif; ?>

                <form id="passwordForm">
                    <div class="form-group">
                        <label class="label">Nueva Contraseña</label>
                        <div class="password-input-group">
                            <input 
                                type="password" 
                                id="newPassword" 
                                name="password" 
                                class="input" 
                                placeholder="Ingresa tu nueva contraseña"
                                required
                            >
                            <button type="button" class="toggle-password" onclick="togglePasswordVisibility('newPassword')">
                                👁️
                            </button>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="label">Confirmar Contraseña</label>
                        <div class="password-input-group">
                            <input 
                                type="password" 
                                id="confirmPassword" 
                                name="confirm_password" 
                                class="input" 
                                placeholder="Confirma tu nueva contraseña"
                                required
                            >
                            <button type="button" class="toggle-password" onclick="togglePasswordVisibility('confirmPassword')">
                                👁️
                            </button>
                        </div>
                    </div>

                    <!-- Password Validator -->
                    <div class="password-validator">
                        <h4 class="subtitle is-6">Requisitos de Contraseña</h4>

                        <div class="check-item">
                            <div class="icon">
                                <span class="check-icon error" id="check-length">✗</span>
                            </div>
                            <span>Mínimo 8 caracteres</span>
                        </div>

                        <div class="check-item">
                            <div class="icon">
                                <span class="check-icon error" id="check-number">✗</span>
                            </div>
                            <span>Contiene números (0-9)</span>
                        </div>

                        <div class="check-item">
                            <div class="icon">
                                <span class="check-icon error" id="check-uppercase">✗</span>
                            </div>
                            <span>Contiene mayúsculas (A-Z)</span>
                        </div>

                        <div class="check-item">
                            <div class="icon">
                                <span class="check-icon error" id="check-special">✗</span>
                            </div>
                            <span>Contiene caracteres especiales (!@#$%^&*)</span>
                        </div>

                        <div class="password-strength" id="strengthIndicator" style="display: none;">
                            <span id="strengthText"></span>
                        </div>
                    </div>

                    <div class="field is-grouped" style="margin-top: 2rem;">
                        <div class="control">
                            <button type="submit" class="button is-success" id="submitBtn" disabled>
                                Guardar Contraseña
                            </button>
                        </div>
                        <div class="control">
                            <a href="../index.php" class="button is-light">Cancelar</a>
                        </div>
                    </div>
                </form>

                <!-- Última Contraseña Analizada -->
                <hr style="margin: 2rem 0;">
                
                <div style="margin-top: 2rem;">
                    <h3 class="subtitle is-5"> Última Contraseña Analizada</h3>
                    
                    <?php if ($ultima_contrasena): ?>
                        <div class="box" style="background-color: #813939;">
                            <div class="level mb-3">
                                <div class="level-left">
                                    <div class="level-item">
                                        <div>
                                            <p class="heading">Nivel de Seguridad</p>
                                            <?php
                                            $strength = $ultima_contrasena['strength_label'];
                                            $color = match($strength) {
                                                'débil' => 'is-danger',
                                                'media' => 'is-warning',
                                                'fuerte' => 'is-success',
                                                default => 'is-info'
                                            };
                                            $emoji = match($strength) {
                                                'débil' => '🔴',
                                                'media' => '🟡',
                                                'fuerte' => '🟢',
                                                default => '⚪'
                                            };
                                            ?>
                                            <p class="title is-5">
                                                <span class="tag <?php echo $color; ?>">
                                                    <?php echo $emoji . ' ' . ucfirst($strength); ?>
                                                </span>
                                            </p>
                                        </div>
                                    </div>
                                </div>
                                <div class="level-right">
                                    <div class="level-item">
                                        <div>
                                            <p class="heading">Requisitos Cumplidos</p>
                                            <p class="title is-5"><?php echo $ultima_contrasena['security_level']; ?>/4</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <p class="is-size-7" style="color: #999;">
                                 <?php echo date('d/m/Y H:i:s', strtotime($ultima_contrasena['fecha_creacion'])); ?>
                            </p>
                        </div>
                    <?php else: ?>
                        <div class="notification is-info">
                            <p>No hay registros de contraseñas anteriores</p>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Historial de Contraseñas -->
                <div style="margin-top: 2rem;">
                    <h3 class="subtitle is-5"> Últimos 5 Cambios de Contraseña</h3>
                    
                    <?php if (!empty($registros)): ?>
                        <div class="table-container">
                            <table class="table is-fullwidth is-striped is-hoverable">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Fortaleza</th>
                                        <th>Requisitos</th>
                                        <th>Fecha</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($registros as $index => $registro): ?>
                                        <?php
                                        $strength = $registro['strength_label'];
                                        $color = match($strength) {
                                            'débil' => '#f14668',
                                            'media' => '#ffdd57',
                                            'fuerte' => '#48c774',
                                            default => '#3273dc'
                                        };
                                        $emoji = match($strength) {
                                            'débil' => '🔴',
                                            'media' => '🟡',
                                            'fuerte' => '🟢',
                                            default => '⚪'
                                        };
                                        ?>
                                        <tr>
                                            <td><?php echo $index + 1; ?></td>
                                            <td>
                                                <span style="color: <?php echo $color; ?>; font-weight: bold;">
                                                    <?php echo $emoji . ' ' . ucfirst($strength); ?>
                                                </span>
                                            </td>
                                            <td>
                                                <progress class="progress" value="<?php echo $registro['security_level']; ?>" max="4"></progress>
                                                <p class="is-size-7"><?php echo $registro['security_level']; ?>/4</p>
                                            </td>
                                            <td><?php echo date('d/m/Y H:i:s', strtotime($registro['fecha_creacion'])); ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <div class="notification is-info">
                            <p>No hay registros historiales de contraseñas</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </main>

    <script src="../js/password-validator.js"></script>

</body>

</html>
