<?php
session_start();
require_once "conecta.php";
$conexion = conecta();

if (!$conexion) {
    echo "Error al conectar a la base de datos.";
    exit;
}

$id_alumno = $_SESSION['id_alumno']; // ID del alumno logueado
$id_proyecto = $_POST['id_proyecto']; // ID del proyecto al que quiere unirse

if (empty($id_alumno) || empty($id_proyecto)) {
    echo "Faltan datos para enviar la solicitud.";
    exit;
}

// Consulta para obtener el líder del proyecto
$sql_lider = "SELECT p.lider_id FROM public.proyectos p WHERE p.id_proyecto = $id_proyecto";
$resultado_lider = pg_query($conexion, $sql_lider);

if (!$resultado_lider || pg_num_rows($resultado_lider) == 0) {
    echo "Proyecto no encontrado o sin líder.";
    exit;
}

$lider = pg_fetch_assoc($resultado_lider)['lider_id']; // ID del líder del proyecto

// Registrar la solicitud en la tabla de solicitudes
$sql_solicitud = "INSERT INTO public.solicitudes (alumno_id, proyecto_id, estado) 
                  VALUES ($id_alumno, $id_proyecto, 'pendiente')";
$resultado_solicitud = pg_query($conexion, $sql_solicitud);

if (!$resultado_solicitud) {
    echo "Error al registrar la solicitud: " . pg_last_error($conexion);
    exit;
}

// Aquí podrías implementar un sistema de notificación o un correo para avisar al líder
echo "Solicitud enviada al líder del proyecto con éxito.";
?>
