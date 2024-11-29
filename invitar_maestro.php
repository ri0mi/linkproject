<?php
require_once "conecta.php";
$conexion = conecta();

session_start();

// Obtener datos enviados desde el frontend
$id_maestro = $_POST['id_maestro'];  // Cambié a $id_maestro
$id_proyecto = $_POST['id_proyecto'];

// Verificar si los valores son válidos
if (!$id_maestro || !$id_proyecto) {
    echo "Faltan datos"; // Respuesta en texto
    exit;
}

// Verificar existencia de proyecto y maestro
$sql_verificar_proyecto = "SELECT * FROM proyectos WHERE id_proyecto = $1";
$resultado_proyecto = pg_query_params($conexion, $sql_verificar_proyecto, array($id_proyecto));
if (pg_num_rows($resultado_proyecto) == 0) {
    echo "Proyecto no encontrado"; // Respuesta en texto
    exit;
}

$sql_verificar_maestro = "SELECT * FROM maestros WHERE id_maestro = $1";  // Verificar si el maestro es un alumno en este caso
$resultado_maestro = pg_query_params($conexion, $sql_verificar_maestro, array($id_maestro));
if (pg_num_rows($resultado_maestro) == 0) {
    echo "Maestro no encontrado"; // Respuesta en texto
    exit;
}

// Insertar la invitación en la base de datos
$query = "INSERT INTO invitaciones_maestros (proyecto_id, invitado_id, estado_invitacion) 
          VALUES ($1, $2, 'pendiente')";

$resultado = pg_query_params($conexion, $query, array($id_proyecto, $id_maestro));

if ($resultado) {
    echo "Invitación enviada correctamente."; // Respuesta en texto
} else {
    echo "Error al guardar la invitación: " . pg_last_error($conexion); // Respuesta en texto
}
?>


