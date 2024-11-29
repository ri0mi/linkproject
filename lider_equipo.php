<?php
session_start();
$nombre = $_SESSION['nombre'];
$id_alumno = $_SESSION['id_alumno']; // Pasa el ID de usuario en la sesión

// Conectar a la base de datos
require_once "conecta.php";
$conexion = conecta();

// Verifica que el alumno no sea miembro de un proyecto
$sql_equipo = "SELECT 1 FROM equipos WHERE miembro_id = $id_alumno AND rol = 'miembro' LIMIT 1";
$resultado_equipo = pg_query($conexion, $sql_equipo);

// Verifica si hay resultados (es miembro de algún equipo)
$es_miembro = pg_num_rows($resultado_equipo) > 0;

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
     <link rel="icon" type="image/x-icon" href="favicon.png">
    <title>Acceso Restringido</title>
    <style>
        /* Estilos generales */
        body {
            font-family: 'Helvetica Neue', Arial, sans-serif;
            background-color: #f5f7fa;
            color: #2980b9; /* Color azul */
            text-align: center;
            padding: 80px 20px;
            margin: 0;
        }

        h1 {
            color: #2980b9; /* Color azul */
            font-size: 48px;
            margin-bottom: 20px;
            font-weight: 700;
        }

        p {
            font-size: 18px;
            margin-bottom: 40px;
            line-height: 1.5;
            color: #7f8c8d;
        }

        /* Estilos para el botón */
        .button {
            text-decoration: none;
            background-color: #3498db;
            color: white;
            padding: 15px 40px;
            border-radius: 30px;
            font-size: 20px;
            font-weight: 600;
            transition: background-color 0.3s, transform 0.3s;
            display: inline-block;
        }

        .button:hover {
            background-color: #2980b9;
            transform: translateY(-2px);
        }

        .button:active {
            transform: translateY(1px);
        }

        /* Estilo para la caja */
        .content {
            background-color: #ffffff;
            border-radius: 10px;
            padding: 50px;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 600px;
            margin: 0 auto;
        }

        /* Estilo para el enlace */
        a {
            text-decoration: none;
        }

        /* Responsividad */
        @media (max-width: 600px) {
            h1 {
                font-size: 36px;
            }
            .button {
                padding: 12px 30px;
                font-size: 18px;
            }
            .content {
                padding: 30px;
            }
        }
    </style>
</head>
<body>

    <div class="content">
        <h1>Acceso Restringido</h1>
        <p>Solo los líderes de equipo pueden buscar equipos. Crea un proyecto para comenzar.</p>
        
        <?php if (!$es_miembro): ?>
            <a href="Crear.php" class="button">Crear Proyecto</a>
        <?php else: ?>
            <button class="button" disabled style="background-color: #ccc; cursor: not-allowed;">
                Crear Proyecto
            </button>
            <p style="color: red;">No puedes crear un proyecto porque ya eres miembro de un equipo.</p>
        <?php endif; ?>

    </div>

</body>
</html>