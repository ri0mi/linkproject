<?php
session_start();
require_once "conecta.php";
$conexion = conecta();

if (!$conexion) {
    echo "Error al conectar a la base de datos.";
    exit;
}

$id_lider = $_SESSION['id_alumno']; // El ID del líder es el mismo que el ID del alumno logueado

if (empty($id_lider)) {
    echo "No hay ID de líder en la sesión.";
    exit;
}

// Consulta para obtener las solicitudes pendientes de los proyectos donde el alumno es líder
$sql = "SELECT s.id, s.alumno_id, s.estado, p.nombre AS nombre_proyecto, a.nombre AS alumno_nombre 
        FROM public.solicitudes s
        JOIN public.proyectos p ON s.proyecto_id = p.id_proyecto
        JOIN public.alumnos a ON s.alumno_id = a.id_alumno
        WHERE p.lider_id = $id_lider AND s.estado = 'pendiente'";

$resultado = pg_query($conexion, $sql);

if (!$resultado) {
    echo "Error en la consulta: " . pg_last_error($conexion);
    exit;
}

$html_solicitudes = "";

if (pg_num_rows($resultado) > 0) {
    while ($solicitud = pg_fetch_assoc($resultado)) {
        $html_solicitudes .= "<li>
            Solicitud de: " . htmlspecialchars($solicitud['alumno_nombre']) . " para unirse al proyecto: " . htmlspecialchars($solicitud['nombre_proyecto']) . "
            <button onclick=\"actualizarSolicitud(" . $solicitud['id'] . ", 'aceptada')\">Aceptar</button>
            <button onclick=\"actualizarSolicitud(" . $solicitud['id'] . ", 'rechazado')\">Rechazar</button>
        </li>";
    }
} else {
    $html_solicitudes = "<li>No tienes solicitudes pendientes.</li>";
}

echo $html_solicitudes;
?>
