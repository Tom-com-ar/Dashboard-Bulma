<?php
require('./libreria/fpdf.php');
include "../conexion.php";

session_start();

if (empty($_SESSION['user_id'])) {
    die("No autorizado");
}

$user_id = (int) $_SESSION['user_id'];

// Recibir mes y año
$year = $_GET['year'] ?? date('Y');
$month = $_GET['month'] ?? date('m');

// Rango de fechas
$desde = "$year-" . str_pad($month, 2, "0", STR_PAD_LEFT) . "-01";
$hasta = date("Y-m-t", strtotime($desde));

// =====================
// Traer eventos del usuario o sin usuario (feriados/efemérides)
// =====================
$sql = "SELECT titulo, fecha, hora, descripcion, user_id, tipo 
        FROM eventos 
        WHERE (user_id = $user_id OR user_id IS NULL)
        AND fecha BETWEEN '$desde' AND '$hasta'
        ORDER BY fecha, hora";
$result = mysqli_query($conn, $sql);
if (!$result) die("Error al consultar eventos: " . mysqli_error($conn));

// =====================
// Crear PDF
// =====================
$pdf = new FPDF();
$pdf->AddPage();

// Colores corporativos
$headerColor = [50, 50, 50];   // Gris oscuro
$usuarioColor = [0, 102, 204]; // Azul
$feriadoColor = [204, 0, 0];   // Rojo
$fechaBgColor = [230, 230, 230]; // Gris claro

// Fuentes
$pdf->SetFont('Arial','',12);
date_default_timezone_set('America/Argentina/Buenos_Aires');
$fechaHora = date('d/m/Y H:i:s');

// Nombre del mes
$meses = [
    1=>"Enero","Febrero","Marzo","Abril","Mayo","Junio",
    "Julio","Agosto","Septiembre","Octubre","Noviembre","Diciembre"
];
$nombreMes = $meses[(int)$month];

// =====================
// HEADER
// =====================
$pdf->SetFillColor(...$headerColor);
$pdf->SetTextColor(255,255,255);
$pdf->SetFont('Arial','B',16);
$pdf->Cell(0, 12, utf8_decode("Eventos de $nombreMes $year"), 0, 1, 'C', true);

$pdf->SetFont('Arial','',12);
$pdf->Cell(0, 10, utf8_decode("Generado: $fechaHora"), 0, 1, 'C');
$pdf->Ln(5);

// =====================
// LISTA DE EVENTOS
// =====================
$current_fecha = '';
while ($row = mysqli_fetch_assoc($result)) {
    $fecha = date("d/m/Y", strtotime($row['fecha']));
    $hora = $row['hora'] ? substr($row['hora'], 0, 5) : '';
    $titulo = $row['titulo'];
    $descripcion = $row['descripcion'];
    $tipo = $row['tipo'];
    $esUsuario = $row['user_id'] == $user_id;

    // Nueva fecha: fondo gris
    if ($fecha !== $current_fecha) {
        $pdf->Ln(3);
        $pdf->SetFillColor(...$fechaBgColor);
        $pdf->SetTextColor(0,0,0);
        $pdf->SetFont('Arial','B',12);
        $pdf->Cell(0, 8, utf8_decode("Fecha: $fecha"), 0, 1, 'L', true);
        $current_fecha = $fecha;
    }

    // Colores según tipo
    if ($esUsuario) {
        $pdf->SetTextColor(...$usuarioColor);
    } elseif ($tipo !== 'personal') {
        $pdf->SetTextColor(...$feriadoColor);
    } else {
        $pdf->SetTextColor(0,0,0);
    }

    $pdf->SetFont('Arial','',12);

    // Título y hora
    $line = ($hora ? "$hora " : "") . $titulo;
    if ($esUsuario) $line .= " (Tuyo)";
    elseif ($tipo !== 'personal') $line .= " (" . $tipo . ")";

    $pdf->Cell(0, 8, utf8_decode($line), 0, 1);

    // Descripción
    if (!empty($descripcion)) {
        $pdf->SetTextColor(0,0,0);
        $pdf->MultiCell(0, 6, utf8_decode("    " . $descripcion));
    }
}

// =====================
// Salida
// =====================
$pdf->Output('I', 'eventos.pdf');