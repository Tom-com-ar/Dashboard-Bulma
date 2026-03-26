-----------------------
HACER TODO RESPONSIVE
-----------------------

PDP

-Crearemos un script `generar_reporte.php` que capture los datos de la sesión (por ejemplo, la contraseña evaluada y el estado del calendario)
-Crear el pdf
-Fecha actual.
-Estado del calendario (mes visualizado).
-Última contraseña analizada y su nivel de seguridad.
-Listado de los últimos 5 registros guardados en la tabla `password_logs`.

Validador de Contraseñas

-Mirar bien el pdf porque no lo entiendo muy bien
-Crear un endpoint (`save_password.php`) que reciba la contraseña (validada previamente en frontend, pero re-validada en backend), la hashee (usando `password_hash`) y la guarde en la DB
-Crearemos una función JS que evalúe:
 Error 1: Menos de 8 caracteres.
 Error 2: No contiene números.
 Error 3: No contiene mayúsculas.
 Error 4: No contiene caracteres especiales.

-Mostraremos una lista de "checkmarks" rojos/verdes en tiempo real. Esto cumple con la premisa de
"identificación de errores".
