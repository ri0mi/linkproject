<?php
require_once "conecta.php";
$conexion = conecta();
session_start();

// Verificar que la sesión está iniciada
if (!isset($_SESSION['id_maestro'])) {
    header("Location: Home.php");  // Redirigir al login si no está autenticado
    exit;
}

// Obtener el id del maestro desde la sesión
$id_maestro = $_SESSION['id_maestro'];

// Obtener los datos enviados desde la petición AJAX
$id_invitacion = $_POST['id_invitacion'];
$estado = $_POST['estado']; // 'aceptada' o 'rechazada'

if ($estado === 'aceptada') {
    // Paso 1: Verificar la invitación
    $sql = "SELECT invitado_id, proyecto_id 
            FROM invitaciones_maestros 
            WHERE id_invitacion = $1 AND estado_invitacion = 'pendiente'";
    $resultado = pg_query_params($conexion, $sql, array($id_invitacion));
    $invitacion = pg_fetch_assoc($resultado);

    if ($invitacion) {
        $miembro_id = $invitacion['invitado_id'];
        $proyecto_id = $invitacion['proyecto_id'];

        // Paso 2: Contar los miembros actuales del equipo (permitidos 4 miembros)
        $sql_count = "SELECT COUNT(*) AS total_miembros 
                      FROM equipos 
                      WHERE proyecto_id = $1";
        $resultado_count = pg_query_params($conexion, $sql_count, array($proyecto_id));
        $conteo = pg_fetch_assoc($resultado_count);

        if ($conteo['total_miembros'] < 3) { // 3 miembros + 1 asesor
            // Paso 3: Actualizar el estado de la invitación a 'aceptada'
            $sql_update = "UPDATE invitaciones_maestros 
                           SET estado_invitacion = 'aceptada' 
                           WHERE id_invitacion = $1";
            $resultado_update = pg_query_params($conexion, $sql_update, array($id_invitacion));

            if ($resultado_update) {
                // Paso 4: Actualizar la tabla proyectos para asociar al maestro al proyecto
                $sql_insert = "UPDATE public.proyectos 
                               SET maestro_id = $1 
                               WHERE id_proyecto = $2";
                $resultado_insert = pg_query_params($conexion, $sql_insert, array($id_maestro, $proyecto_id));

                if ($resultado_insert) {
                    echo "Invitación aceptada y maestro asignado al proyecto.";
                } else {
                    echo "Error al asignar al maestro al proyecto.";
                }
            } else {
                echo "Error al actualizar el estado de la invitación a 'aceptada'.";
            }
        } else {
            echo "El equipo ya tiene el máximo permitido de miembros.";
        }
    } else {
        echo "La invitación no es válida o ya fue procesada.";
    }
} elseif ($estado === 'rechazada') {
    // Actualizar el estado de la invitación a rechazada
    $sql = "UPDATE invitaciones_maestros 
            SET estado_invitacion = 'rechazada' 
            WHERE id_invitacion = $1";
    $resultado = pg_query_params($conexion, $sql, array($id_invitacion));

    if ($resultado) {
        echo "Invitación rechazada correctamente.";
    } else {
        echo "Error al rechazar la invitación.";
    }
} else {
    echo "Estado no válido.";
}
?>

