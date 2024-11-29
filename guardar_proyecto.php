<?php
// Incluye la conexión a la base de datos
require_once "conecta.php";
$conexion = conecta();

// Obtener los valores del formulario de manera segura
$nombre = $_POST['nombre'];
$descripcion = $_POST['descripcion'];
$area = $_POST['area'];
$asesor = $_POST['asesor'];
$conocimientos = $_POST['conocimientos'];
$nivel_innovacion = $_POST['nivel_innovacion'];
$lider_id = $_POST['lider_id']; // Este es el id del alumno que crea el proyecto

// Manejo del archivo logo
$directorio = "logos/";
$logo = $_FILES['logo']['name'];
$logo_temp = $_FILES['logo']['tmp_name'];
$ruta_logo = $directorio . basename($logo);

// Mover el archivo a la carpeta de destino
if (move_uploaded_file($logo_temp, $ruta_logo)) {
    // Usar una consulta preparada para insertar en `proyectos`
    $sql = "INSERT INTO proyectos (nombre, descripcion, areas, asesor, conocimientos, nivel_innovacion, logo, lider_id)
            VALUES ($1, $2, $3, $4, $5, $6, $7, $8) RETURNING id_proyecto"; // Obtener id_proyecto
    $stmt = pg_prepare($conexion, "insert_project", $sql);

    if (!$stmt) {
        echo "Error en la preparación de la consulta: " . pg_last_error($conexion);
        exit();
    }

    $resultado = pg_execute($conexion, "insert_project", [
        $nombre, $descripcion, $area, $asesor, $conocimientos, $nivel_innovacion, $ruta_logo, $lider_id
    ]);

    if ($resultado && pg_num_rows($resultado) > 0) {
        // Obtener el ID del proyecto creado
        $row = pg_fetch_assoc($resultado);
        $id_proyecto = $row['id_proyecto'];

        // Insertar al líder en la tabla `equipos` con el rol de "líder"
        $sql_lider = "INSERT INTO equipos (proyecto_id, miembro_id, rol) VALUES ($1, $2, 'líder')";
        $stmt_lider = pg_prepare($conexion, "insert_lider", $sql_lider);

        if (!$stmt_lider) {
            echo "Error en la preparación de la consulta para equipos: " . pg_last_error($conexion);
            exit();
        }

        $resultado_lider = pg_execute($conexion, "insert_lider", [$id_proyecto, $lider_id]);

        if ($resultado_lider) {
            header("Location:Home_alumno.php"); // Redirige a la página deseada
            exit(); // Detiene la ejecución del script
        } else {
            echo "Error al insertar el líder en equipos: " . pg_last_error($conexion);
        }
    } else {
        echo "Error al crear el proyecto: " . pg_last_error($conexion);
    }
} else {
    echo "Error al subir la imagen.";
}
?>
