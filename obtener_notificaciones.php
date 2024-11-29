<?php
require_once "conecta.php";
$conexion = conecta();
session_start();

// Obtener el id_alumno desde la sesión
$id_alumno = $_SESSION['id_alumno'];

// Consulta para obtener las invitaciones pendientes
$sql = "SELECT p.nombre_proyecto, i.id_proyecto 
        FROM invitaciones i
        JOIN proyectos p ON p.id_proyecto = i.proyecto_id
        WHERE i.invitado_id = $id_alumno AND i.estado_invitacion = 'pendiente' 
        ORDER BY i.fecha_invitacion DESC";
$resultado = pg_query($conexion, $sql);

$invitaciones = [];

if ($resultado) {
    while ($row = pg_fetch_assoc($resultado)) {
        $invitaciones[] = [
            'mensaje' => "Tienes una invitación para unirte al proyecto: " . $row['nombre_proyecto'],
            'link' => "unirse_proyecto.php?id_proyecto=" . $row['id_proyecto'] // Este es un ejemplo de cómo podrías generar el link
        ];
    }
}

// Devolver las invitaciones como JSON
echo json_encode($invitaciones);
?>
