<?php
require_once "conecta.php";
$conexion = conecta();

session_start();

// Obtener datos enviados desde el frontend
$id_alumno = $_POST['id_alumno'];
$id_proyecto = $_POST['id_proyecto'];

// Verificar si los valores son válidos
if (!$id_alumno || !$id_proyecto) {
    echo "Faltan datos"; // Respuesta en texto
    exit;
}

// Verificar existencia de proyecto y alumno
$sql_verificar_proyecto = "SELECT * FROM proyectos WHERE id_proyecto = $id_proyecto";
$resultado_proyecto = pg_query($conexion, $sql_verificar_proyecto);
if (pg_num_rows($resultado_proyecto) == 0) {
    echo "Proyecto no encontrado"; // Respuesta en texto
    exit;
}

$sql_verificar_alumno = "SELECT * FROM alumnos WHERE id_alumno = $id_alumno";
$resultado_alumno = pg_query($conexion, $sql_verificar_alumno);
if (pg_num_rows($resultado_alumno) == 0) {
    echo "Alumno no encontrado"; // Respuesta en texto
    exit;
}

// Insertar la invitación en la base de datos
$query = "INSERT INTO invitaciones (proyecto_id, invitado_id, estado_invitacion) 
          VALUES ($id_proyecto, $id_alumno, 'pendiente')";

$resultado = pg_query($conexion, $query);

if ($resultado) {
    echo "Invitación enviada correctamente."; // Respuesta en texto
} else {
    echo "Error al guardar la invitación: " . pg_last_error($conexion); // Respuesta en texto
}
?>


