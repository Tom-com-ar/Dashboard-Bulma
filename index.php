<?php
include "./php/conexion.php";

?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bulma@1.0.4/css/bulma.min.css">
    <link rel="stylesheet" href="./css/style.css">
    <title>Dashboard - Bulma</title>
</head>

<body>

    <header>
        <!-- Navbar -->
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
            </div>
        </nav>
    </header>

    <main>
        <!-- Main Container -->
        <div class="container is-fluid" style="padding: 2rem 1.5rem;">
            <div class="columns">
                <!-- Sidebar -->
                <div class="column is-3">
                    <aside class="box menu">
                        <p class="menu-label">Menú Principal</p>
                        <ul class="menu-list">
                            <li><a href="#" class="is-active">Dashboard</a></li>
                            <li><a href="./pages/validador.html">Validador de Contraseñas</a></li>
                            <li><a href="#">Pdf</a></li>
                        </ul>
                    </aside>
                </div>

                <div id="content" class="column is-9">
                    <div class="level mb-4">
                        <div class="level-left">
                            <button class="button" onclick="prevMonth()">←</button>
                        </div>

                        <div class="level-item">
                            <h2 class="title" id="monthYear" style="margin-bottom: 0;"></h2>
                        </div>

                        <div class="level-right">
                            <button class="button" onclick="nextMonth()">→</button>
                        </div>
                    </div>

                    <!-- Días -->
                    <div class="columns is-mobile has-text-centered mb-2">
                        <div class="column">Lunes</div>
                        <div class="column">Martes</div>
                        <div class="column">Miércoles</div>
                        <div class="column">Jueves</div>
                        <div class="column">Viernes</div>
                        <div class="column">Sábado</div>
                        <div class="column">Domingo</div>
                    </div>

                    <!-- Calendario -->
                    <div id="calendar" class="calendar-grid has-7-columns">
                        <div class="calendar-cell"></div>
                    </div>
                </div>
            </div>
    </main>

<!-- Modal para crear evento -->
    <div id="modalEvento" class="modal">
        <div class="modal-background" onclick="cerrarModal()"></div>
        <div class="modal-card">
            <header class="modal-card-head">
                <p class="modal-card-title">Crear Evento</p>
                <button class="delete" onclick="cerrarModal()"></button>
            </header>
            <section class="modal-card-body">
                <div class="field">
                    <label class="label">Nombre del Evento</label>
                    <div class="control">
                        <input class="input" type="text" id="eventoTitulo" placeholder="Ej: Reunión importante">
                    </div>
                </div>
                <div class="field">
                    <label class="label">Hora</label>
                    <div class="control">
                        <input class="input" type="time" id="eventoHora">
                    </div>
                </div>
                <div class="field">
                    <label class="label">Fecha</label>
                    <div class="control">
                        <input class="input" type="date" id="eventoFecha" disabled>
                    </div>
                </div>
                <div class="field">
                    <label class="label">Descripción</label>
                    <div class="control">
                        <input class="input" type="text" id="eventoDescripcion" placeholder="Ej: Reunión importante">
                    </div>
                </div>
            </section>
            <footer class="modal-card-foot">
                <button class="button" onclick="cerrarModal()">Cancelar</button>
                <button class="button is-info" onclick="guardarEvento()">Guardar</button>
            </footer>
        </div>
    </div>

    <script src="./js/main.js"></script>
    <script src="./js/calendario.js"></script>

</body>

</html>