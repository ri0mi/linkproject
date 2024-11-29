<?php
require_once "conecta.php";
$conexion = conecta();
session_start();

// Obtener el id del proyecto que se quiere eliminar
$id_proyecto = $_POST['id_proyecto'];

// Verificar que el id_proyecto es válido
if (isset($id_proyecto) && !empty($id_proyecto)) {
    // Iniciar una transacción para asegurar que ambas eliminaciones sean atómicas
    pg_query($conexion, "BEGIN");

    // Verificar si existen registros en la tabla equipos para el proyecto
    $sql_verificar_equipos = "SELECT COUNT(*) FROM equipos WHERE proyecto_id = $1";
    $stmt_verificar_equipos = pg_prepare($conexion, "verificar_equipos", $sql_verificar_equipos);
    $resultado_verificar = pg_execute($conexion, "verificar_equipos", [$id_proyecto]);
    $registro_equipos = pg_fetch_assoc($resultado_verificar);

    if ($registro_equipos['count'] > 0) {
        // Eliminar los registros de la tabla equipos relacionados con el proyecto
        $sql_eliminar_equipos = "DELETE FROM equipos WHERE proyecto_id = $1";
        $stmt_equipos = pg_prepare($conexion, "eliminar_equipos", $sql_eliminar_equipos);
        $resultado_equipos = pg_execute($conexion, "eliminar_equipos", [$id_proyecto]);

        // Verificar si la eliminación en la tabla equipos fue exitosa
        if (!$resultado_equipos) {
            // Si hay un error al eliminar en la tabla equipos, revertir la transacción y mostrar el error
            pg_query($conexion, "ROLLBACK");
            echo "Error al eliminar miembros en la tabla equipos: " . pg_last_error($conexion);
            exit(); // Detiene la ejecución del script
        }
    } else {
        echo "No hay miembros en la tabla equipos para este proyecto.";
    }

    // Eliminar las solicitudes relacionadas con el proyecto
    $sql_eliminar_solicitudes = "DELETE FROM solicitudes WHERE proyecto_id = $1";
    $stmt_solicitudes = pg_prepare($conexion, "eliminar_solicitudes", $sql_eliminar_solicitudes);
    $resultado_solicitudes = pg_execute($conexion, "eliminar_solicitudes", [$id_proyecto]);

    // Verificar si la eliminación en la tabla solicitudes fue exitosa
    if (!$resultado_solicitudes) {
        // Si hay un error al eliminar en la tabla solicitudes, revertir la transacción y mostrar el error
        pg_query($conexion, "ROLLBACK");
        echo "Error al eliminar solicitudes: " . pg_last_error($conexion);
        exit(); // Detiene la ejecución del script
    }

    // Eliminar los registros de invitaciones pendientes relacionados con el proyecto
    $sql_eliminar_invitaciones = "DELETE FROM invitaciones WHERE proyecto_id = $1";
    $stmt_invitaciones = pg_prepare($conexion, "eliminar_invitaciones", $sql_eliminar_invitaciones);
    $resultado_invitaciones = pg_execute($conexion, "eliminar_invitaciones", [$id_proyecto]);

    // Verificar si la eliminación en la tabla invitaciones fue exitosa
    if (!$resultado_invitaciones) {
        // Si hay un error al eliminar en la tabla invitaciones, revertir la transacción y mostrar el error
        pg_query($conexion, "ROLLBACK");
        echo "Error al eliminar invitaciones: " . pg_last_error($conexion);
        exit(); // Detiene la ejecución del script
    }

    // **Agregar esta sección para eliminar las invitaciones a maestros**
    $sql_eliminar_invitaciones_maestro = "DELETE FROM invitaciones_maestros WHERE proyecto_id = $1";
    $stmt_invitaciones_maestro = pg_prepare($conexion, "eliminar_invitaciones_maestro", $sql_eliminar_invitaciones_maestro);
    $resultado_invitaciones_maestro = pg_execute($conexion, "eliminar_invitaciones_maestro", [$id_proyecto]);

    // Verificar si la eliminación en la tabla invitaciones_maestros fue exitosa
    if (!$resultado_invitaciones_maestro) {
        // Si hay un error al eliminar en la tabla invitaciones_maestros, revertir la transacción y mostrar el error
        pg_query($conexion, "ROLLBACK");
        echo "Error al eliminar invitaciones a maestros: " . pg_last_error($conexion);
        exit(); // Detiene la ejecución del script
    }

    // Eliminar las tareas relacionadas con el proyecto
    $sql_eliminar_tareas = "DELETE FROM tareas WHERE proyecto_id = $1";
    $stmt_tareas = pg_prepare($conexion, "eliminar_tareas", $sql_eliminar_tareas);
    $resultado_tareas = pg_execute($conexion, "eliminar_tareas", [$id_proyecto]);

    // Verificar si la eliminación en la tabla tareas fue exitosa
    if (!$resultado_tareas) {
        // Si hay un error al eliminar en la tabla tareas, revertir la transacción y mostrar el error
        pg_query($conexion, "ROLLBACK");
        echo "Error al eliminar las tareas: " . pg_last_error($conexion);
        exit(); // Detiene la ejecución del script
    }



  // **Eliminar los comentarios relacionados con el proyecto**
    $sql_eliminar_comentarios = "DELETE FROM comentarios WHERE proyecto_id = $1";
    $stmt_comentarios = pg_prepare($conexion, "eliminar_comentarios", $sql_eliminar_comentarios);
    $resultado_comentarios = pg_execute($conexion, "eliminar_comentarios", [$id_proyecto]);

    // Verificar si la eliminación en la tabla comentarios fue exitosa
    if (!$resultado_comentarios) {
        // Si hay un error al eliminar en la tabla comentarios, revertir la transacción
        pg_query($conexion, "ROLLBACK");
        echo "Error al eliminar comentarios: " . pg_last_error($conexion);
        exit(); // Detiene la ejecución del script
    }

    
    // Eliminar el proyecto de la tabla proyectos
    $sql_eliminar_proyecto = "DELETE FROM proyectos WHERE id_proyecto = $1 AND lider_id = $2";
    $stmt_proyecto = pg_prepare($conexion, "eliminar_proyecto", $sql_eliminar_proyecto);
    $resultado_proyecto = pg_execute($conexion, "eliminar_proyecto", [$id_proyecto, $_SESSION['id_alumno']]);

    // Verificar si la eliminación del proyecto fue exitosa
    if ($resultado_proyecto) {
        // Si todo fue exitoso, hacer commit de la transacción
        pg_query($conexion, "COMMIT");

        // Redirigir a otra página después de eliminar el proyecto
        header("Location: Home_alumno.php"); // Cambia esta URL por la página que desees
        exit(); // Detiene la ejecución del script después de la redirección
    } else {
        // Si hubo un error al eliminar el proyecto, revertir la transacción
        pg_query($conexion, "ROLLBACK");
        echo "Error al eliminar el proyecto: " . pg_last_error($conexion);
    }
} else {
    echo "No se ha seleccionado un proyecto para eliminar.";
}
?>

