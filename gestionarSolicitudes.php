<?php
session_start();
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    include 'conexion.php';

    $solicitudId = $_POST['solicitud_id'];
    $accion = $_POST['accion']; // 'aceptar' o 'rechazar'

    if (!$solicitudId || !in_array($accion, ['aceptar', 'rechazar'])) {
        echo "Error: Datos inválidos.";
        exit;
    }

    // Actualizar estado de la solicitud
    $nuevoEstado = $accion === 'aceptar' ? 'aceptada' : 'rechazada';
    $queryActualizar = "UPDATE solicitudes SET estado = $1 WHERE id = $2";
    $resultActualizar = pg_query_params($conexion, $queryActualizar, [$nuevoEstado, $solicitudId]);

    if ($resultActualizar) {
        // Si se acepta, agregar al alumno al proyecto
        if ($accion === 'aceptar') {
            $queryProyecto = "SELECT proyecto_id, alumno_id FROM solicitudes WHERE id = $1";
            $resultProyecto = pg_query_params($conexion, $queryProyecto, [$solicitudId]);
            $data = pg_fetch_assoc($resultProyecto);

            $queryAgregar = "INSERT INTO miembros_proyecto (proyecto_id, alumno_id) VALUES ($1, $2)";
            pg_query_params($conexion, $queryAgregar, [$data['proyecto_id'], $data['alumno_id']]);
        }

        echo "Solicitud actualizada con éxito.";
    } else {
        echo "Error al actualizar la solicitud.";
    }
}
?>

