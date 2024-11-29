<?php
require_once "conecta.php";
$conexion = conecta();
session_start();

// Verificar que la sesión está iniciada
if (!isset($_SESSION['id_maestro'])) {
    header("Location: Home.php");  // Redirigir al login si no está autenticado
    exit;
}

// Obtener el id_alumno desde la sesión
$id_maestro = $_SESSION['id_maestro'];

// Verificar si el usuario ya pertenece a un proyecto
$sql_verificar_proyecto = "
    SELECT miembro_id 
    FROM equipos 
    WHERE miembro_id = $1
";
$resultado_verificacion = pg_query_params($conexion, $sql_verificar_proyecto, array($id_maestro));

// Si el usuario ya está en un proyecto, no mostrar invitaciones
if ($resultado_verificacion && pg_num_rows($resultado_verificacion) > 0) {
    echo "Ya eres miembro de un proyecto.";
    exit;
}

// Consulta para obtener las invitaciones pendientes
$sql = "
    SELECT p.nombre, p.id_proyecto, i.id_invitacion
    FROM invitaciones_maestros i
    JOIN proyectos p ON p.id_proyecto = i.proyecto_id
    WHERE i.invitado_id = $1 AND i.estado_invitacion = 'pendiente'
    ORDER BY i.fecha_invitacion DESC
";

$resultado = pg_query_params($conexion, $sql, array($id_maestro));

$invitacionesHTML = "";  // Variable para almacenar el HTML generado

if ($resultado) {
    while ($row = pg_fetch_assoc($resultado)) {
        // Construir el HTML para cada invitación
        $invitacionesHTML .= '<li>';
        $invitacionesHTML .= '<a href="unirse_proyecto.php?id_proyecto=' . htmlspecialchars($row['id_proyecto']) . '">Tienes una invitación para unirte al proyecto: ' . htmlspecialchars($row['nombre']) . '</a>';
        $invitacionesHTML .= '<button onclick="actualizarEstadoInvitacion(' . htmlspecialchars($row['id_invitacion']) . ', \'aceptada\')">Aceptar</button>';
        $invitacionesHTML .= '<button onclick="actualizarEstadoInvitacion(' . htmlspecialchars($row['id_invitacion']) . ', \'rechazada\')">Rechazar</button>';
        $invitacionesHTML .= '</li>';
    }

    // Si no hay invitaciones, mostrar mensaje alternativo
    if ($invitacionesHTML === "") {
        $invitacionesHTML = "<li>No tienes invitaciones pendientes.</li>";
    }
} else {
    echo "Error en la consulta: " . pg_last_error($conexion);
}

// Devolver el HTML generado como respuesta
echo $invitacionesHTML;
?>
