<?php
session_start();
require_once "conecta.php";
$conexion = conecta();

$proyecto_id = $_SESSION['proyecto_id'] ?? null;

if (!$proyecto_id) {
    die('Error: No se identificó el proyecto.');
}

$sql_tareas = "SELECT * FROM tareas WHERE proyecto_id = $proyecto_id ORDER BY estado";
$resultado = pg_query($conexion, $sql_tareas);

$tareas = [];
while ($fila = pg_fetch_assoc($resultado)) {
    $tareas[] = $fila;
}

echo json_encode($tareas);
?>
