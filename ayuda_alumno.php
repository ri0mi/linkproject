<?php
require_once "conecta.php";
$conexion = conecta();

session_start();
$nombre = $_SESSION['nombre'];
$id_alumno = $_SESSION['id_alumno']; // ID del alumno actual
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ayuda</title>
    <link rel="icon" type="image/x-icon" href="favicon.png">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="estilos.css"> 
    <style>
        /* Estilos para el body y la estructura básica */
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f4f7fc;
            margin: 0;
            padding: 0;
            color: #333;
        }

        .welcome h1 {
            font-size: 36px;
            color: #333;
            margin: 0;
        }

        /* Estilo para el título h2 */
        h2 {
            font-size: 28px;
            color: #007BFF;
            text-align: center;
            font-weight: bold;
            margin-top: 30px;
            margin-bottom: 20px;
            position: relative;
            font-family: 'Arial', sans-serif;
        }

        /* Efecto de subrayado en el título */
        h2::after {
            content: "";
            position: absolute;
            width: 60%;
            height: 4px;
            background-color: #007BFF;
            bottom: -5px;
            left: 20%;
        }

        /* Estilo para el video */
        .video-container {
            display: flex;
            justify-content: center;
            margin-top: 30px;
        }
        video {
            max-width: 100%;
            height: auto;
            border-radius: 8px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }
    </style>
</head>
<body>

<!-- Barra de navegación -->
<div class="navbar">
    <a href="Home_alumno.php">Regresar</a>
</div>

<!-- Contenedor de bienvenida -->
<div class="welcome">
    <h1>Ayuda</h1>
</div>

<!-- Título de la sección de video -->
<h2>Video Tutorial de la Aplicación</h2>

<!-- Contenedor de video -->
<div class="video-container">
    <video controls>
<source src="alumno.mp4" type="video/mp4">

        Tu navegador no soporta el elemento de video.
    </video>
</div>

</body>
</html>
