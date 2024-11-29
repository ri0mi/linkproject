<?php
require_once "conecta.php";

// Establecer conexión a la base de datos
$conexion = conecta();

// Generar un ID único de proyecto y un ID único para el líder
$id_proyecto = rand(100000, 999999); // ID de 6 dígitos para el proyecto
$id_lider = rand(100000, 999999);    // ID de 6 dígitos para el líder del proyecto

// Verificar que el alumno no esté en otro proyecto
$alumno_id = $_SESSION['id_alumno']; // Supongamos que el ID del alumno está en la sesión actual
$sql_verificar = "SELECT * FROM proyectos WHERE id_lider = '$alumno_id' OR id IN (SELECT proyecto_id FROM equipo_proyectos WHERE alumno_id = '$alumno_id')";
$resultado_verificar = pg_query($conexion, $sql_verificar);

if (pg_num_rows($resultado_verificar) > 0) {
    echo '<script>alert("Ya estás registrado en un proyecto. No puedes crear ni unirte a otro proyecto."); window.location.href = "panelUsuario.php";</script>';
    exit();
}

// Inicializar datos del proyecto como NULL (excepto ID, id_lider, y cupos)
$nombre = NULL;
$descripcion = NULL;
$areas = NULL;
$conocimientos = NULL;
$nivel_innovacion = NULL;
$logo = NULL;
$miembros = NULL;
$seleccionar_asesor = NULL; // Puntero para el ID del asesor

// Preparar la consulta de inserción del proyecto con los valores iniciales
$sql_insertar = "INSERT INTO proyectos (id, nombre, descripcion, areas, conocimientos, nivel_innovacion, logo, cupos, id_lider, id_asesor) 
                 VALUES ('$id_proyecto', '$nombre', '$descripcion', '$areas', '$conocimientos', '$nivel_innovacion', '$logo', '$miembros', '$id_lider', '$seleccionar_asesor')";

// Ejecutar la consulta e insertar el proyecto
$resultado_insertar = pg_query($conexion, $sql_insertar);

if ($resultado_insertar) {
    echo '<script>alert("Proyecto creado con éxito. Puedes ahora agregar los detalles."); window.location.href = "editarProyecto.php?id='.$id_proyecto.'";</script>';
} else {
    echo '<script>alert("Error al crear el proyecto. Inténtalo de nuevo."); window.location.href = "crearProyecto.php";</script>';
}
?>
