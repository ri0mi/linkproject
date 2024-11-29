<?php
require_once "conecta.php";

// Establecer conexión a la base de datos
$conexion = conecta();

// Obtener el ID del alumno del formulario
$id_maestro = pg_escape_string($conexion, $_POST['id_maestro']);

// Verificar si el ID ya existe en la base de datos
$sql_verificar = "SELECT id_maestro FROM maestros WHERE id_maestro= '$id_maestro'";
$resultado_verificar = pg_query($conexion, $sql_verificar);

if (pg_num_rows($resultado_verificar) > 0) {
    echo '<script>alert("El ID de curso ya existe. Introduce un ID único."); window.location.href = "CuentaMaestro.php";</script>';
} else {
    $nombre = pg_escape_string($conexion, $_POST['nombre']);
    $correo = pg_escape_string($conexion, $_POST['correo']);
    $contrasena = pg_escape_string($conexion, $_POST['contrasena']);

    $sql_insertar = "INSERT INTO maestros (id_maestro, nombre, correo, contrasena) VALUES ('$id_maestro', '$nombre', '$correo', '$contrasena')";
    $resultado_insertar = pg_query($conexion, $sql_insertar);

    if ($resultado_insertar) {
        // Iniciar sesión para almacenar el código de verificación
        session_start();

        // Generar un código aleatorio
        $codigo = rand(10000, 99999);
        
        // Almacenar el código y el nombre en la sesión
        $_SESSION['codigo_verificacion'] = $codigo;
        $_SESSION['nombre'] = $nombre;
        $_SESSION['correo'] = $correo;

        // Configurar el correo
        $titulo = 'Gracias por registrarte';
        $mensaje = "
        <html>
        <head>
            <meta charset='UTF-8' />
            <title>Registro de Usuario</title>
        </head>
        <body>
            <p>Hola $nombre,</p>
             <p>Gracias por incribirte en Linkproject!:</p>
            <p>Tu codigo de verificacion es:</p>
            <h2>$codigo</h2>
        </body>
        </html>
        ";

        // Cabeceras del correo
        $cabeceras  = "MIME-Version: 1.0" . "\r\n";
        $cabeceras .= "Content-type: text/html; charset=iso-8859-1" . "\r\n";
        $cabeceras .= "From: soporte@linkproject.com>" . "\r\n"; // Cambia esto a un correo válido

        // Enviar el correo
        $enviado = mail($correo, $titulo, $mensaje, $cabeceras);

        if ($enviado) {
            // Redirigir a la página de confirmación si el correo se envía correctamente
            header("Location: Confirmacion.php");
            exit();
        } else {
            echo "Registro exitoso, pero ocurrió un error al enviar el correo de confirmación.";
        }
    } else {
        // Mostrar mensaje de error si ocurrió un problema durante la inserción
        error_log(pg_last_error($conexion)); // Log del error para depuración
        echo "Ocurrió un error al registrar el maestro.";
    }
}
?>
