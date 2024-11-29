<?php
require_once "conecta.php";  // Asegúrate de que "conecta.php" tenga la función conecta() definida
$conexion = conecta();  // Establece la conexión con la base de datos

session_start();  // Inicia la sesión

// Obtenemos el nombre del maestro y su ID desde la sesión
$nombre = $_SESSION['nombre'];
$id_maestro = $_SESSION['id_maestro']; // ID del maestro actual

// Consulta SQL para obtener proyectos y equipos donde el maestro está invitado
$query = "
    SELECT 
        p.id_proyecto,
        p.nombre AS nombre_proyecto,
        p.descripcion,
        p.logo,
        e.id AS id_equipo,
        e.miembro_id,
        a.nombre AS nombre_miembro, 
        e.rol
    FROM 
        proyectos p
    INNER JOIN 
        equipos e ON p.id_proyecto = e.proyecto_id
    INNER JOIN 
        alumnos a ON e.miembro_id = a.id_alumno
    WHERE 
        p.maestro_id = '$id_maestro'
    GROUP BY 
        p.id_proyecto, e.id, e.miembro_id, a.nombre, e.rol
    ORDER BY 
        p.id_proyecto;
";

// Ejecutar la consulta
$result = pg_query($conexion, $query);

// Verificar si la consulta fue exitosa
if (!$result) {
    die("Error en la consulta: " . pg_last_error());
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tableros</title>
    <link rel="icon" type="image/x-icon" href="favicon.png">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="estilos.css"> 
</head>
<style>
    /* Contenedor general de la sección de proyectos */



#notificationDropdown {
    display: none;
    position: absolute;
    top: 50px;
    right: 10px;
    background-color: #fff;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
    min-width: 250px;
    border-radius: 5px;
    padding: 10px;
    z-index: 1000;
}

#notificationDropdown ul {
    list-style-type: none;
    margin: 0;
    padding: 0;
}

#notificationDropdown ul li {
    padding: 10px;
    border-bottom: 1px solid #ddd;
}

#notificationDropdown ul li:last-child {
    border-bottom: none;
}

#notificationDropdown ul li a {
    color: #333;
    text-decoration: none;
}

#notificationDropdown ul li:hover {
    background-color: #f1f1f1;
}


.error-message {
    margin-top: 10px;
    font-size: 14px;
    color: red;
    font-weight: bold;
}

.project-list {
    display: flex;
    flex-direction: column;
    gap: 15px; /* Espaciado entre proyectos */
}

.project-item {
    display: flex;
    align-items: center;
    padding: 40px;
    border: 1px solid #ddd;
    border-radius: 5px;
    background-color: #f9f9f9; /* Fondo claro para los items */
    transition: box-shadow 0.3s;
}

.project-item:hover {
    box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1); /* Efecto de sombra al pasar el mouse */
}

.project-logo {
    width: 100px; /* Tamaño del logo */
    height: 100px;
    margin-right: 15px; /* Espaciado a la derecha */
    border-radius: 5px; /* Bordes redondeados para el logo */
}

.project-info {
    flex-grow: 1; /* Para que ocupe el espacio restante */
}

.project-info h4 {
    margin: 0;
    font-size: 1.2em; /* Tamaño de la fuente del nombre */
}

.project-info p {
    margin: 5px 0; /* Margen entre el título y la descripción */
    color: #555; /* Color de la descripción */
}

.view-button {
    margin-top: 10px; /* Espaciado superior */
    padding: 8px 12px; /* Padding del botón */
    background-color: blue; /* Color de fondo del botón */
    color: white;
    border: none;
    border-radius: 5px; /* Bordes redondeados */
    text-decoration: none; /* Sin subrayado */
    display: inline-block; /* Para que el padding funcione correctamente */
    transition: background-color 0.3s; /* Transición suave para el hover */
}

.view-button:hover {
    background-color: blue; /* Color del botón al pasar el mouse */
}

.centered-title {
    text-align: center; /* Centra el texto horizontalmente */
    margin-bottom: 20px; /* Espaciado inferior para separar del contenido */
}
</style>
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
          <div id="notificationDropdown" class="dropdown-content" style="display: none;">
        <!-- Aquí aparecerán las notificaciones -->
        <ul id="notificationList">
            <!-- Notificaciones se cargarán aquí -->
        </ul>
    </div>
        <div class="dropdown" id="dropdownMenu">
            <a href="Intermedio.php">Perfil</a>
            <a href="CerrarSesion.php">Cerrar Sesion</a>
        </div>
    </div>
</div>
<h2 class="centered-title">Tus Proyectos</h2>
<div class="project-list">
    <?php
    // Definir un array para almacenar los proyectos procesados
    $proyectos = [];

    // Ejecutar la consulta
    $result = pg_query($conexion, $query);

    // Verificar si la consulta fue exitosa
    if (!$result) {
        die("Error en la consulta: " . pg_last_error());
    }

    // Procesar los resultados
    while ($proyecto = pg_fetch_assoc($result)) {
        // Verificar si el proyecto ya fue agregado al array
        if (!isset($proyectos[$proyecto['id_proyecto']])) {
            // Si el proyecto no está en el array, lo agregamos junto con los miembros
            $proyectos[$proyecto['id_proyecto']] = [
                'id_proyecto' => $proyecto['id_proyecto'],
                'nombre' => $proyecto['nombre_proyecto'],
                'logo' => $proyecto['logo'],
                'miembros' => []  // Creamos un array para los miembros
            ];
        }

        // Agregar el miembro al proyecto correspondiente
        $proyectos[$proyecto['id_proyecto']]['miembros'][] = [
            'nombre' => $proyecto['nombre_miembro'],
            'rol' => $proyecto['rol']
        ];
    }

    // Liberar resultados
    pg_free_result($result);

    // Mostrar los proyectos y los miembros
    foreach ($proyectos as $proyecto) {
        echo '<div class="project-item">';
        echo '<img src="' . $proyecto['logo'] . '" alt="Logo del Proyecto" class="project-logo">';
        echo '<div class="project-info">';
        echo '<h4>' . $proyecto['nombre'] . '</h4>';
        echo '<h5>Miembros:</h5>';
        echo '<ul class="team-members">';
        
        // Mostrar los miembros del equipo
        foreach ($proyecto['miembros'] as $miembro) {
            echo '<li>' . $miembro['nombre'] . ' (' . $miembro['rol'] . ')</li>';
        }
 echo '<a href="VerGestor.php?id=' . $proyecto['id_proyecto'] . '" class="view-button">Ir al Gestor</a>';
        echo '</ul>';
        echo '</div>';
        echo '</div>';
    }
    ?>
</div>


<script>

    function toggleDropdown() {
        var dropdown = document.getElementById("dropdownMenu");
        dropdown.style.display = dropdown.style.display === "block" ? "none" : "block";
    }

   

function toggleNotificationDropdown() {
    var dropdown = document.getElementById("notificationDropdown");

    if (dropdown.style.display === "none" || dropdown.style.display === "") {
        dropdown.style.display = "block";
        obtenerInvitaciones(); // Llamar la función para obtener invitaciones
    } else {
        dropdown.style.display = "none";
    }
}


async function loadFiles() {
        const fileList = document.getElementById('fileList');
        const response = await fetch('list_files.php');
        const html = await response.text();
        fileList.innerHTML = html; // Inserta el HTML directamente
    }

    document.getElementById('uploadForm').addEventListener('submit', async function(e) {
        e.preventDefault();
        const formData = new FormData(this);
        const response = await fetch('upload.php', {
            method: 'POST',
            body: formData
        });
        const message = await response.text();
        document.getElementById('uploadMessage').innerText = message;
        loadFiles();
    });


    async function deleteFile(fileName) {
    const response = await fetch(`delete.php?file=${fileName}`);
    const message = await response.text();
    alert(message);
    loadFiles(); // Recargar los archivos después de eliminar uno
}

    window.onload = loadFiles;




function obtenerInvitaciones() {
    var xhr = new XMLHttpRequest();
    xhr.open("GET", "invitaciones_maestro.php", true); // Llamar al script PHP para obtener invitaciones
    xhr.onload = function() {
        if (xhr.status === 200) {
            var invitacionesHTML = xhr.responseText;  // Obtenemos directamente el HTML generado
            var notificationList = document.getElementById("notificationList");
            notificationList.innerHTML = ""; // Limpiar lista antes de agregar las nuevas invitaciones

            if (invitacionesHTML.trim() !== "") {
                // Si la respuesta no está vacía, añadirla al contenedor
                notificationList.innerHTML = invitacionesHTML;
            } else {
                notificationList.innerHTML = "<li>No tienes invitaciones pendientes.</li>";
            }
        } else {
            alert("Error al obtener invitaciones.");
        }
    };
    xhr.send();
}


function actualizarEstadoInvitacion(id_invitacion, estado) {
    var xhr = new XMLHttpRequest();
    xhr.open("POST", "actualizar_invitacion_maestro.php", true);
    xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
    xhr.onload = function() {
        if (xhr.status === 200) {
            alert("Invitación " + estado);
            obtenerInvitaciones(); // Refrescar la lista de invitaciones
            location.reload();
        } else {
            alert("Error al actualizar la invitación.");
        }
    };
    xhr.send("id_invitacion=" + id_invitacion + "&estado=" + estado);
}




function solicitarProyecto(id_alumno, id_proyecto) {
    if (!id_alumno || !id_proyecto) {
        alert("Los IDs son incompletos.");
        return;
    }

    var xhr = new XMLHttpRequest();
    xhr.open("POST", "solicitar_proyecto.php", true);
    xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");

    xhr.onload = function() {
        if (xhr.status === 200) {
            alert(xhr.responseText); // Mensaje del servidor
        } else {
            alert("Error al enviar la solicitud.");
        }
    };

    // Enviar los datos
    xhr.send("id_proyecto=" + encodeURIComponent(id_proyecto));
}


function obtenerSolicitudes() {
    console.log("Obteniendo solicitudes..."); // Mensaje de depuración
    var xhr = new XMLHttpRequest();
    xhr.open("GET", "obtener_solicitudes.php", true);
    xhr.onload = function() {
        console.log("Estado de la solicitud: " + xhr.status); // Mensaje de depuración
        if (xhr.status === 200) {
            console.log("Respuesta recibida: " + xhr.responseText); // Mensaje de depuración
            var html_solicitudes = xhr.responseText; 
            var solicitudList = document.getElementById("solicitudList");
            solicitudList.innerHTML = ""; // Limpia la lista antes de agregar nuevas solicitudes
            
            if (html_solicitudes.trim() !== "") {
                solicitudList.innerHTML = html_solicitudes; // Inserta las solicitudes recibidas
            } else {
                solicitudList.innerHTML = "<li>No tienes solicitudes pendientes.</li>"; // Mensaje predeterminado
            }
        } else {
            console.error("Error al obtener solicitudes: " + xhr.status); // Mensaje de error
            alert("Error al obtener solicitudes.");
        }
    };
    xhr.onerror = function() {
        console.error("Error de red al intentar obtener solicitudes."); // Mensaje de error de red
    };
    xhr.send();
}



    // Cerrar el menú desplegable si se hace clic fuera de él
    window.onclick = function(event) {
        if (!event.target.matches('.profile-icon') && !event.target.matches('.notification-icon')) {
            var dropdown = document.getElementById("dropdownMenu");
            dropdown.style.display = "none"; // Oculta el menú desplegable
        }
    }
</script>

</body>
</html>