<?php
// Conectar a la base de datos
require_once "conecta.php";
$conexion = conecta();
session_start();

// Verificar si el usuario ha iniciado sesión y tiene un ID de alumno válido
if (!isset($_SESSION['id_alumno'])) {
    echo "<script>alert('Error: No has iniciado sesión correctamente.');</script>";
    exit;
}

// Obtener el ID del alumno desde la sesión
$id_alumno = $_SESSION['id_alumno'];

// Verificar si se recibe el ID del proyecto desde el formulario
if (!isset($_POST['id_proyecto']) || empty($_POST['id_proyecto'])) {
    echo "<script>alert('Error: ID de proyecto no válido.');</script>";
    exit;
}

// Escapar y convertir el ID del proyecto para evitar inyecciones SQL
$proyecto_id = intval($_POST['id_proyecto']);

// Verificar si ya existe una solicitud para este alumno y proyecto
$sql_verificar = "
    SELECT * 
    FROM solicitudes 
    WHERE alumno_id = $id_alumno AND proyecto_id = $proyecto_id
";
$resultado_verificar = pg_query($conexion, $sql_verificar);

if ($resultado_verificar && pg_num_rows($resultado_verificar) > 0) {
    echo "<script>alert('Ya has enviado una solicitud a este proyecto.');</script>";
} else {
    // Insertar la solicitud si no existe
    $sql_insertar_solicitud = "
        INSERT INTO solicitudes (alumno_id, proyecto_id)
        VALUES ($id_alumno, $proyecto_id)
    ";
    $resultado_insertar = pg_query($conexion, $sql_insertar_solicitud);

    // Verificar si la solicitud se insertó correctamente
    if ($resultado_insertar) {
        echo "<script>alert('¡Solicitud enviada con éxito!');</script>";
    } else {
        echo "<script>alert('Error al enviar la solicitud: " . pg_last_error($conexion) . "');</script>";
    }
}
?>
