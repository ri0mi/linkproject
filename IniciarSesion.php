<?php 
session_start();
require "conecta.php";
$conexion = conecta();

if (isset($_POST['nombre']) && isset($_POST['contrasena'])) {

    function validate($data) {
        $data = trim($data);
        $data = stripslashes($data);
        $data = htmlspecialchars($data);
        return $data;
    }

    $nombre = validate($_POST['nombre']);  
    $contrasena = validate($_POST['contrasena']); 

    // Consulta en la tabla de alumnos
    $SqlAlumno = "SELECT * FROM alumnos WHERE nombre = '$nombre' AND contrasena = '$contrasena'";
    $resultAlumno = pg_query($conexion, $SqlAlumno);

    if (!$resultAlumno) {
        $_SESSION['error'] = "Error en la consulta de alumnos: " . pg_last_error($conexion);
        header("Location: Home.php");
        exit();
    }

    // Verificar si el usuario es un alumno
    if (pg_num_rows($resultAlumno) === 1) {
        $row = pg_fetch_assoc($resultAlumno);
        $_SESSION['nombre'] = $row['nombre'];
        $_SESSION['id_alumno'] = $row['id_alumno'];
        header("Location: Home_alumno.php");
        exit();
    } 

    // Si no es un alumno, consulta en la tabla de maestros
    $SqlMaestro = "SELECT * FROM maestros WHERE nombre = '$nombre' AND contrasena = '$contrasena'";
    $resultMaestro = pg_query($conexion, $SqlMaestro);

    if (!$resultMaestro) {
        $_SESSION['error'] = "Error en la consulta de maestros: " . pg_last_error($conexion);
        header("Location: Home.php");
        exit();
    }

    // Verificar si el usuario es un maestro
    if (pg_num_rows($resultMaestro) === 1) {
        $row = pg_fetch_assoc($resultMaestro);
        $_SESSION['nombre'] = $row['nombre'];
        $_SESSION['id_maestro'] = $row['id_maestro'];
        header("Location: Home_maestro.php");
        exit();
    } else {
        // Si no hay coincidencias, mostrar error
        $_SESSION['error'] = "El usuario o la contraseña son incorrectos";
        header("Location: Home.php");
        exit();
    }
} else {
    // Si faltan campos, mostrar error
    $_SESSION['error'] = "Faltan campos por llenar";
    header("Location: Home.php");
    exit();
}
?>
