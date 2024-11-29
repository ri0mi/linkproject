<?php
session_start();
include 'conexion.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $alumnoId = $_POST['alumno_id'];
    $proyectoId = $_POST['proyecto_id'];

    if (!$alumnoId || !$proyectoId) {
        echo "Error: Datos incompletos.";
        exit;
    }

    // Verificar si ya existe una solicitud pendiente
    $queryVerificar = "SELECT * FROM solicitudes WHERE alumno_id = $1 AND proyecto_id = $2 AND estado = 'pendiente'";
    $resultVerificar = pg_query_params($conexion, $queryVerificar, [$alumnoId, $proyectoId]);

    if (pg_num_rows($resultVerificar) > 0) {
        echo "Error: Ya existe una solicitud pendiente.";
        exit;
    }

    // Insertar nueva solicitud
    $queryInsertar = "INSERT INTO solicitudes (alumno_id, proyecto_id, estado) VALUES ($1, $2, 'pendiente')";
    $resultInsertar = pg_query_params($conexion, $queryInsertar, [$alumnoId, $proyectoId]);

    if ($resultInsertar) {
        echo "Solicitud enviada con éxito.";
    } else {
        echo "Error al enviar la solicitud.";
    }
} else {
    echo "Error: Método no permitido.";
}
?>
