<?php
// Conectar a la base de datos
require_once "conecta.php";
$conexion = conecta();  // Asegúrate de que esta función devuelve un recurso de conexión pg_connect

// Obtener los parámetros enviados por la solicitud AJAX
$id_tarea = $_POST['id_tarea'];
$estado = $_POST['estado'];

// Validar el estado
if (!in_array($estado, ['pendiente', 'progreso', 'completo'])) {
    die('Error: Estado inválido.');
}

// Actualizar el estado de la tarea en la base de datos
$sql_actualizar_estado = "UPDATE tareas SET estado = '$estado' WHERE id = $id_tarea";
$resultado = pg_query($conexion, $sql_actualizar_estado);

// Responder con éxito
if ($resultado) {
    echo "Estado actualizado correctamente.";
} else {
    echo "Error al actualizar el estado.";
}
?>
