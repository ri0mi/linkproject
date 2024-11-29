<?php 
session_start();
require "conecta.php";
$conexion = conecta();

// Verificar si el usuario ha iniciado sesión y tiene un nombre en la sesión
if (!isset($_SESSION['id_maestro']) || !isset($_SESSION['nombre'])) {
    // Redirigir al inicio de sesión si no está autenticado
    header("Location: Home.php");
    exit();
}

// Consulta para verificar si el campo 'carrera' está completo
$id_maestro = $_SESSION['id_maestro'];
$query = "SELECT departamento FROM maestros WHERE id_maestro = '$id_maestro'";
$result = pg_query($conexion, $query);

if ($result && $row = pg_fetch_assoc($result)) {
    if (empty($row['departamento'])) {
        // Mensaje si el campo 'carrera' está vacío
        $mensaje = "Por favor, completa tu perfil para acceder a todas las funciones.";
    } else {
        // Si el campo 'carrera' está lleno, redirige a otra página
        header("Location: verMaestro.php");
        exit();
    }
} else {
    // En caso de error en la consulta o usuario no encontrado
    echo "Error al verificar el campo 'carrera'.";
    exit();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/x-icon" href="favicon.png">
    <title>Inicio-Linkproject</title>
    <link rel="stylesheet" href="estilos.css"> 
    <style>
        /* Estilo para centrar el contenido */
        body {
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            margin: 0;
            font-family: Arial, sans-serif;
            background-color: #f4f4f9;
        }
        .welcome-container {
            text-align: center;
        }
        h1 {
            font-size: 3em;
            margin-bottom: 20px;
        }
        .options {
            margin-top: 20px;
        }
        .options button {
            padding: 10px 20px;
            font-size: 1em;
            margin: 5px;
            cursor: pointer;
        }
        .image {
            max-width: 900px; /* Ajusta el tamaño máximo de la imagen */
            height: auto; /* Mantiene la relación de aspecto */
            margin-bottom: 20px; /* Espacio entre la imagen y los botones */
        }
    </style>
</head>
<body>
    <div class="welcome-container">
        <h1>Bienvenido, <?php echo htmlspecialchars($_SESSION['nombre']); ?></h1>
        <p>Completar tu perfil te permitirá aprovechar al máximo todas las funciones de Linkproject.</p>
        <!-- Imagen antes de los botones -->
        <img src="media/Perfil.png" alt="Imagen de bienvenida para completar el perfil" class="image">
        <div class="options">
            <button onclick="window.location.href='completar_maestro.php'">Completar perfil</button>
            <button onclick="window.location.href='verMaestro.php?omitir=true'">Omitir</button>
        </div>
    </div>
</body>
</html>

