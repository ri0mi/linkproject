<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inicio - Linkproject</title>
    <link rel="icon" type="image/x-icon" href="favicon.png">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="estilos.css"> <!-- Enlace a tu archivo CSS -->

    <style>
        /* Estilos generales */
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            background-color: #f0f2f5;
        }
        .profile-container {
            width: 100%;
            max-width: 600px;
            margin: 50px auto;
            background-color: #fff;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        /* Foto de portada */
        .profile-cover {
            width: 100%;
            height: 200px;
            background-color: #ddd;
            background-size: cover;
            background-position: center;
            background-color: steelblue;
        }
        /* Foto de perfil circular */
       .profile-photo {
    width: 120px;
    height: 120px;
    border-radius: 50%; /* Aseguramos que el círculo sea perfecto */
    border: 3px solid #fff;
    margin-top: -60px;
    margin-left: 20px;
    overflow: hidden;
    display: flex; /* Usamos flexbox para centrar la imagen si es necesario */
    justify-content: center;
    align-items: center;
}
        .profile-photo img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        /* Información del perfil */
        .profile-info {
            padding: 20px;
        }
        .profile-info h2 {
            margin: 0;
            color: #333;
        }
        .profile-info ul {
            list-style-type: none;
            padding: 0;
        }
        .profile-info li {
            margin: 8px 0;
            color: #666;
        }
        .profile-info strong {
            color: #333;
        }
    </style>
</head>
<body>
<div class="navbar">
    <div>
         <a href="Home_maestro.php">Inicio</a>
        <a href="Equipo_maestro.php">Proyectos</a>
        <a href="Tableros.php">Tableros</a>
    </div>
    <div>
        <i class="fas fa-bell notification-icon" onclick="toggleNotificationDropdown()"></i>
        <i class="fas fa-user-circle profile-icon" onclick="toggleDropdown()"></i>
        <div class="dropdown" id="dropdownMenu">
            <a href="verAlumno.php">Perfil</a>
            <a href="CerrarSesion.php">Cerrar Sesion</a>
        </div>
    </div>
</div>
    <div class="profile-container">
        <!-- Foto de portada -->
        <div class="profile-cover"></div>

        <!-- Foto de perfil -->
    <div class="profile-photo">
    <?php 
    require_once "conecta.php";
    session_start();

    // Obtener el ID de alumno desde la sesión
    $id_maestro = $_SESSION['id_maestro'];

    // Conectar a la base de datos
    $conexion = conecta();

    // Consultar los datos del alumno
    $sql = "SELECT * FROM maestros WHERE id_maestro='$id_maestro'";
    $result = pg_query($conexion, $sql);

    // Verificar si hay un resultado
    if ($result && pg_num_rows($result) > 0) {
        // Asignar el resultado a la variable $row
        $row = pg_fetch_assoc($result);
        
        // Verificar si hay una foto de perfil
        $foto = !empty($row['foto']) ? 'foto/' . htmlspecialchars($row['foto']) : 'foto/default-avatar.jpg';
        
        // Mostrar la imagen de perfil
      echo "<li> <img src='" . htmlspecialchars($row["foto"]) . "' alt='Logo'> </li>";
    } else {
        echo "<p>No se encontraron datos para este usuario.</p>";
    }
    ?>
</div>


        <!-- Información del perfil -->
        <div class="profile-info">
            <?php
            // Verificar si la consulta devolvió datos
            if ($result && pg_num_rows($result) > 0) {
                // Mostrar la información del alumno
                echo "<h2>" . htmlspecialchars($row['nombre']) . "</h2>";
                echo "<ul>";
                echo "<li><strong>Correo:</strong> " . htmlspecialchars($row['correo']) . "</li>";
                echo "<li><strong>Departamento:</strong> " . htmlspecialchars($row['departamento']) . "</li>";
                
                echo "</ul>";
            }
            ?>
        </div>
    </div>
</body>}

<script>
    function toggleDropdown() {
        var dropdown = document.getElementById("dropdownMenu");
        dropdown.style.display = dropdown.style.display === "block" ? "none" : "block";
    }

    function toggleNotificationDropdown() {
     
        alert('Aquí se mostrarían las notificaciones.'); // Ejemplo de alerta
    }

    // Cerrar el menú desplegable si se hace clic fuera de él
    window.onclick = function(event) {
        if (!event.target.matches('.profile-icon') && !event.target.matches('.notification-icon')) {
            var dropdown = document.getElementById("dropdownMenu");
            dropdown.style.display = "none"; // Oculta el menú desplegable
        }
    }
</script>

</html>
