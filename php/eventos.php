<?php
include "conexion.php";

$result = $conn->query("SELECT * FROM eventos");

$eventos = [];

while ($row = $result->fetch_assoc()) {
    $eventos[] = $row;
}

echo json_encode($eventos);