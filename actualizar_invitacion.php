<?php
require_once "conecta.php";
$conexion = conecta();

// Obtener los datos enviados desde la petición AJAX
$id_invitacion = $_POST['id_invitacion'];
$estado = $_POST['estado']; // 'aceptada' o 'rechazada'

if ($estado === 'aceptada') {
    // Paso 1: Verificar la invitación
    $sql = "SELECT invitado_id, proyecto_id 
            FROM invitaciones 
            WHERE id_invitacion = $1 AND estado_invitacion = 'pendiente'";
    $resultado = pg_query_params($conexion, $sql, array($id_invitacion));
    $invitacion = pg_fetch_assoc($resultado);

    if ($invitacion) {
        $miembro_id = $invitacion['invitado_id'];
        $proyecto_id = $invitacion['proyecto_id'];

        // Paso 2: Contar los miembros actuales del equipo
        $sql_count = "SELECT COUNT(*) AS total_miembros 
                      FROM equipos 
                      WHERE proyecto_id = $1";
        $resultado_count = pg_query_params($conexion, $sql_count, array($proyecto_id));
        $conteo = pg_fetch_assoc($resultado_count);

        if ($conteo['total_miembros'] < 3) { // 2 miembros + 1 asesor
            // Paso 3: Agregar al alumno como miembro
            $sql_insert = "INSERT INTO equipos (proyecto_id, miembro_id, rol) VALUES ($1, $2, $3)";
            $resultado_insert = pg_query_params($conexion, $sql_insert, array($proyecto_id, $miembro_id, 'miembro'));

            if ($resultado_insert) {
                // Paso 4: Actualizar el estado de la invitación
                $sql_update = "UPDATE invitaciones SET estado_invitacion = 'aceptada' WHERE id_invitacion = $1";
                $resultado_update = pg_query_params($conexion, $sql_update, array($id_invitacion));

                if ($resultado_update) {
                    echo "Alumno añadido al equipo y invitación aceptada.";
                } else {
                    echo "Error al actualizar el estado de la invitación.";
                }
            } else {
                echo "Error al agregar al alumno al equipo.";
            }
        } else {
            echo "El equipo ya tiene el máximo permitido de miembros.";
        }
    } else {
        echo "La invitación no es válida o ya fue procesada.";
    }
} elseif ($estado === 'rechazada') {
    // Actualizar el estado de la invitación a rechazada
    $sql = "UPDATE invitaciones SET estado_invitacion = 'rechazada' WHERE id_invitacion = $1";
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