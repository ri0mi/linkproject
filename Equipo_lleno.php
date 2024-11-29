<?php 
session_start();
require "conecta.php";
$conexion = conecta();

// Verificar si el usuario ha iniciado sesión y tiene un nombre en la sesión
if (!isset($_SESSION['id_alumno']) || !isset($_SESSION['nombre'])) {
    // Redirigir al inicio de sesión si no está autenticado
    header("Location: Home.php");
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
        <h2>Ya tienes equipo no puedes invitar a nadie mas</h2>
        <img src="media/equipo.png" alt="Imagen de Equipo Completo" class="image">
    </div>
</body>
</html>

