<?php
require_once "conecta.php";
$conexion = conecta();
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Obtener datos del formulario
    $carrera = pg_escape_string($conexion, $_POST['carrera']);
    $contacto = pg_escape_string($conexion, $_POST['contacto']);
    $laboratorio = pg_escape_string($conexion, $_POST['laboratorio']);
    $horario = pg_escape_string($conexion, $_POST['horario']);
    $habilidades = pg_escape_string($conexion, $_POST['habilidades']);
    $id_alumno = $_SESSION['id_alumno'];

    // Manejo del archivo de foto
$directorio = "foto/";
$foto = $_FILES['foto']['name'];
$foto_temp = $_FILES['foto']['tmp_name'];
$ruta_foto = $directorio . basename($foto);

    // Mover el archivo a la carpeta de destino
    if (move_uploaded_file($foto_temp, $ruta_foto)) {
        // Consulta para actualizar los datos del alumno
        $sql = "UPDATE alumnos SET carrera = $1, contacto = $2, laboratorio = $3, horario = $4, habilidades = $5, foto = $6 WHERE id_alumno = $7";
        
        // Preparar y ejecutar la consulta
        $stmt = pg_prepare($conexion, "update_datos", $sql);
        $resultado = pg_execute($conexion, "update_datos", [
            $carrera, $contacto, $laboratorio, $horario, $habilidades, $ruta_foto, $id_alumno
        ]);

        if ($resultado) {
            header("Location: Home_alumno.php"); // Redirige a la página deseada
            exit(); // Detiene la ejecución del script
        } else {
            echo "Error al actualizar el perfil.";
        }
    } else {
        echo "Error al subir la imagen.";
    }
}
?>
