<?php
require_once "conecta.php";
$conexion = conecta();

// Obtener los datos enviados desde la petición AJAX
$id = $_POST['id'];  // Cambié 'id_solicitud' por 'id'
$estado = $_POST['estado']; // 'aceptada' o 'rechazada'

if ($estado === 'aceptada') {
    // Paso 1: Verificar la solicitud
    $sql = "SELECT alumno_id, proyecto_id 
            FROM solicitudes 
            WHERE id = $1 AND estado = 'pendiente'";  // Se utiliza 'id' aquí
    $resultado = pg_query_params($conexion, $sql, array($id));
    $solicitud = pg_fetch_assoc($resultado);

    if ($solicitud) {
        $alumno_id = $solicitud['alumno_id'];
        $proyecto_id = $solicitud['proyecto_id'];

        // Paso 2: Contar los miembros actuales del equipo
        $sql_count = "SELECT COUNT(*) AS total_miembros 
                      FROM equipos 
                      WHERE proyecto_id = $1";
        $resultado_count = pg_query_params($conexion, $sql_count, array($proyecto_id));
        $conteo = pg_fetch_assoc($resultado_count);

        if ($conteo['total_miembros'] < 3) { // 2 miembros + 1 asesor
            // Paso 3: Agregar al alumno como miembro
            $sql_insert = "INSERT INTO equipos (proyecto_id, miembro_id, rol) VALUES ($1, $2, $3)";
            $resultado_insert = pg_query_params($conexion, $sql_insert, array($proyecto_id, $alumno_id, 'miembro'));

            if ($resultado_insert) {
                // Paso 4: Actualizar el estado de la solicitud
                $sql_update = "UPDATE solicitudes SET estado = 'aceptada' WHERE id = $1"; // Aquí se usa 'id'
                $resultado_update = pg_query_params($conexion, $sql_update, array($id));

                if ($resultado_update) {
                    echo "Alumno añadido al equipo y solicitud aceptada.";
                } else {
                    echo "Error al actualizar el estado de la solicitud.";
                }
            } else {
                echo "Error al agregar al alumno al equipo.";
            }
        } else {
            echo "El equipo ya tiene el máximo permitido de miembros.";
        }
    } else {
        echo "La solicitud no es válida o ya fue procesada.";
    }
} elseif ($estado === 'rechazada') {
    // Actualizar el estado de la solicitud a rechazada
    $sql = "UPDATE solicitudes SET estado = 'rechazada' WHERE id = $1";  // Se usa 'id' aquí también
    $resultado = pg_query_params($conexion, $sql, array($id));

    if ($resultado) {
        echo "Solicitud rechazada correctamente.";
    } else {
        echo "Error al rechazar la solicitud.";
    }
} else {
    echo "Estado no válido.";
}
?>
