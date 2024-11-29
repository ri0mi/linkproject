<?php
require_once "conecta.php";
$conexion = conecta();
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Obtener datos del formulario
    $departamento = pg_escape_string($conexion, $_POST['departamento']);
    $id_maestro = $_SESSION['id_maestro'];

    // Manejo del archivo de foto
    $directorio = "foto/";
    $foto = $_FILES['foto']['name'];
    $foto_temp = $_FILES['foto']['tmp_name'];
    $ruta_foto = $directorio . basename($foto);

    // Mover el archivo a la carpeta de destino
    if (move_uploaded_file($foto_temp, $ruta_foto)) {
        // Consulta para actualizar solo los datos necesarios (departamento y foto)
        $sql = "UPDATE maestros SET departamento = $1, foto = $2 WHERE id_maestro = $3";
        
        // Preparar y ejecutar la consulta
        $stmt = pg_prepare($conexion, "update_datos", $sql);
        $resultado = pg_execute($conexion, "update_datos", [
            $departamento, $ruta_foto, $id_maestro
        ]);

        if ($resultado) {
            header("Location: Home_maestro.php"); // Redirige a la página deseada
            exit(); // Detiene la ejecución del script
        } else {
            echo "Error al actualizar el perfil.";
        }
    } else {
        echo "Error al subir la imagen.";
    }
}
?>
