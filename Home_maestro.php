<?php
require_once "conecta.php";
$conexion = conecta();


session_start();
$nombre = $_SESSION['nombre'];
$id_maestro = $_SESSION['id_maestro']; // ID del alumno actual
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inicio - Linkproject</title>
    <link rel="icon" type="image/x-icon" href="favicon.png">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="estilos.css"> 
</head>
<style>
    /* Contenedor general de la sección de proyectos */
.project-section {
    display: flex;
    flex-direction: column;
    align-items: center;
    margin-top: 2rem;
    padding: 20px;
    background-color: #f9f9f9;
    border-radius: 10px;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    width: 100%;
}

/* Botones de acciones (crear, eliminar) */
.project-actions {
    display: flex;
    gap: 15px;
    margin-bottom: 1.5rem;
}

.project-btn {
    padding: 10px 20px;
    font-size: 1rem;
    font-weight: bold;
    color: #fff;
    background-color: #4caf50;
    border-radius: 8px;
    text-decoration: none;
    transition: background-color 0.3s;
}

.project-btn:hover {
    background-color: #45a049;
}

/* Vista de proyectos */
.project-view {
    text-align: center;
    width: 100%;
    min-height: 400px; /* Establece un alto mínimo para dar más presencia */
    padding: 20px;
    background-color: #ffffff;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    margin-top: 1rem; /* Añade un poco de margen superior */
    box-sizing: border-box;
}

.project-view h2 {
    font-size: 1.5rem;
    color: #333;
    margin-bottom: flex;
}

.project-view p {
    font-size: 1rem;
    color: #666;
}


.file-section {
        margin-top: 2rem;
        text-align: center;
    }

    .file-list {
        margin-top: 1rem;
    }

    .file-list ul {
        list-style-type: none;
        padding: 0;
    }

    .file-list ul li {
        margin-bottom: 10px;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .file-list button {
        background-color: #f44336;
        color: #fff;
        border: none;
        padding: 5px 10px;
        border-radius: 5px;
        cursor: pointer;
    }

    .file-list button:hover {
        background-color: #d32f2f;
    }


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


.image-container {
    display: flex;
    justify-content: center;
    align-items: center;
    margin-top: 20px; /* Espaciado entre el texto y la imagen */
}

.image-container img {
    max-width: 100%; /* Ajusta automáticamente al tamaño del contenedor */
    width: 300px; /* Tamaño predeterminado de la imagen */
    height: auto;
    border-radius: 10px; /* Opcional: bordes redondeados */
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2); /* Opcional: sombra */
}


.help-btn {
    width: 50px; /* Ancho del botón */
    height: 50px; /* Altura del botón */
    border-radius: 50%; /* Hace el botón circular */
    background-color: #007bff; /* Color de fondo azul */
    color: #fff; /* Color del ícono */
    border: none; /* Sin bordes */
    display: flex; /* Centra el ícono */
    justify-content: center; /* Centra horizontalmente */
    align-items: center; /* Centra verticalmente */
    cursor: pointer; /* Manito al pasar el mouse */
    transition: background-color 0.3s; /* Animación al pasar el mouse */
}

.help-btn:hover {
    background-color: #0056b3; /* Azul más oscuro al pasar el mouse */
}

.help-btn i {
    font-size: 24px; /* Tamaño del ícono */
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
            <a href="Intermedio_Maestro.php">Perfil</a>
            <a href="CerrarSesion.php">Cerrar Sesion</a>
        </div>
    </div>
</div>

<div class="welcome">
    <h1>Bienvenido <?php echo htmlspecialchars($nombre); ?></h1>
    <div class="image-container">
<a href="https://www.cucei.udg.mx/" target="_blank">
        <img src="cucei.jpeg" alt="Página de la Escuela" style="width: 1450px; height: auto;">
    </div>
</div>

<button class="help-btn" onclick="window.location.href='ayuda_maestro.php'" title="Ayuda">
    <i class="fas fa-question-circle"></i>
</button>


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