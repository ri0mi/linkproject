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

    // Eliminar primero los registros de la tabla equipos relacionados con el proyecto
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

    // Eliminar el proyecto de la tabla proyectos
    $sql_eliminar_proyecto = "DELETE FROM proyectos WHERE id_proyecto = $1 AND lider_id = $2";
    $stmt_proyecto = pg_prepare($conexion, "eliminar_proyecto", $sql_eliminar_proyecto);
    $resultado_proyecto = pg_execute($conexion, "eliminar_proyecto", [$id_proyecto, $_SESSION['id_alumno']]);

    // Verificar si la eliminación del proyecto fue exitosa
    if ($resultado_proyecto) {
        // Si todo fue exitoso, hacer commit de la transacción
        pg_query($conexion, "COMMIT");
        echo "Proyecto eliminado correctamente.";
    } else {
        // Si hubo un error al eliminar el proyecto, revertir la transacción
        pg_query($conexion, "ROLLBACK");
        echo "Error al eliminar el proyecto: " . pg_last_error($conexion);
    }
} else {
    echo "No se ha seleccionado un proyecto para eliminar.";
}
?>
