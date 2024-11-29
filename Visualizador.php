<?php
// Conectar a la base de datos
require_once "conecta.php";
$conexion = conecta();
session_start();

// Obtener el id_alumno desde la sesión
$id_alumno = $_SESSION['id_alumno'];

// Verificar si el alumno ya pertenece a un equipo
$sql_verificar_equipo = "
    SELECT miembro_id 
    FROM equipos 
    WHERE miembro_id = $id_alumno
";
$resultado_equipo = pg_query($conexion, $sql_verificar_equipo);

// Si pertenece a un equipo, redirigir o mostrar un mensaje
if ($resultado_equipo && pg_num_rows($resultado_equipo) > 0) {
    header("Location:en_equipo.php");
    exit;
}

// Consulta para obtener proyectos con menos de 3 miembros
$sql_proyectos_disponibles = "
    SELECT p.id_proyecto, p.nombre, p.descripcion, p.logo, p.areas, p.nivel_innovacion, 
           COUNT(e.miembro_id) AS miembros
    FROM proyectos p
    LEFT JOIN equipos e ON p.id_proyecto = e.proyecto_id
    GROUP BY p.id_proyecto, p.nombre, p.descripcion, p.logo, p.areas, p.nivel_innovacion
    HAVING COUNT(e.miembro_id) <= 2 OR COUNT(e.miembro_id) IS NULL
";
$resultado_proyectos = pg_query($conexion, $sql_proyectos_disponibles);

// Verificar si la consulta se ejecutó correctamente
if (!$resultado_proyectos) {
    echo "Error en la consulta: " . pg_last_error($conexion);
    exit;
}

// Procesar solicitud de unirse a un proyecto
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['proyecto_id'])) {
    $proyecto_id = intval($_POST['proyecto_id']);

    // Verificar si ya existe una solicitud pendiente para este proyecto
    $sql_verificar_solicitud = "
        SELECT id 
        FROM solicitudes 
        WHERE alumno_id = $id_alumno 
        AND proyecto_id = $proyecto_id 
        AND estado = 'pendiente'
    ";
    $resultado_verificar = pg_query($conexion, $sql_verificar_solicitud);

    if ($resultado_verificar && pg_num_rows($resultado_verificar) > 0) {
        echo "Ya tienes una solicitud pendiente para este proyecto.";
    } else {
        // Insertar la nueva solicitud
        $sql_insertar_solicitud = "
            INSERT INTO solicitudes (alumno_id, proyecto_id, estado) 
            VALUES ($id_alumno, $proyecto_id, 'pendiente')
        ";
        $resultado_insertar = pg_query($conexion, $sql_insertar_solicitud);

        if ($resultado_insertar) {
            echo "Solicitud enviada exitosamente.";
        } else {
            echo "Error al enviar la solicitud: " . pg_last_error($conexion);
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Visualizador de Proyectos</title>
    <link rel="icon" type="image/x-icon" href="favicon.png">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="estilos.css"> <!-- Enlace a tu archivo CSS -->
</head>
<style>

body {
    background-color: steelblue; /* Color de fondo de la página */
    font-family: 'Arial', sans-serif; /* Fuente de la página */
}
    /* Estilos para los proyectos */
    .project-card {
    min-width: 300px; /* Ancho mínimo para cada tarjeta */
    margin: 10px; /* Espaciado entre tarjetas */
    background: #fff; /* Fondo blanco para las tarjetas */
    border-radius: 8px; /* Bordes redondeados */
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1); /* Sombra */
    padding: 15px; /* Espaciado interno */
    text-align: center; /* Centrar texto */
}


h1 {
            text-align: center; /* Centra el texto */
            color: white; /* Cambia el color del texto a blanco */
            margin-top: 20px; /* Espaciado superior */
        }


    .project-card img {
        width: 100%;
        height: 250px; /* Aumenta la altura de la imagen */
        object-fit: cover;
    }

    .project-card .project-info {
        padding: 15px; /* Aumenta el espacio dentro de la tarjeta */
    }

    .project-card h3 {
        font-size: 1.5em; /* Aumenta el tamaño del título */
        margin: 10px 0;
    }

    .project-card p {
        font-size: 1.1em; /* Aumenta el tamaño del texto */
        color: #555;
    }

    .carousel {
    display: flex;
    overflow-x: auto; /* Permitir desplazamiento horizontal */
    scroll-behavior: smooth; /* Desplazamiento suave */
}
    .wrapper {
        max-width: 1200px;
        margin: 180px;
        display: flex;
        justify-content: center;
        align-items: center;
        padding: 20px;
    }

    .wrapper i {
        top: 60%;
        height: 46px;
        width: 46px;
        cursor: pointer;
        position: absolute;
        text-align: center;
        font-size: 1.2rem;
        line-height: 46px;
        border-radius: 50%;
        background: #fff;
        transform: translateY(-50%);
        z-index: 1;
    }

    .wrapper i:first-child {
        left: -23px;
        display: none;
    }

    .wrapper i:last-child {
        right: -23px;
    }

    .btn {
    display: inline-block;
    padding: 10px 20px;
    margin-top: 10px;
    text-decoration: none;
    background-color: #4CAF50;
    color: white;
    border-radius: 5px;
    font-weight: bold;
    cursor: pointer;
}
    .btn.details {
        background-color: #008CBA;
    }

   .btn:hover {
    background-color: #45a049; /* Color de fondo al pasar el mouse */
}

    .btn.details:hover {
        background-color: #005f73;
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

</style>
<body>

<div class="navbar">
    <div>
        <a href="Home_alumno.php">Inicio</a>
        <a href="Equipo.php">Equipo</a>
        <a href="Gestor.php">Gestor de Proyectos</a>
        <a href="Visualizador.php">Proyectos</a>
        <a href="Directorio.php">Alumnos</a>
        <a href="Directorio_Asesor.php">Maestros</a>
    </div>
    <div>
        <i class="fas fa-bell notification-icon" onclick="toggleNotificationDropdown()"></i>
        <i class="fas fa-user-circle profile-icon" onclick="toggleDropdown()"></i>
       <div id="notificationDropdown" class="dropdown-content" style="display: none;">
                <ul id="notificationList">
            <!-- Notificaciones se cargarán aquí -->
        </ul>
        <div>
            <h4>Solicitudes</h4>
            <ul id="solicitudList">
                <li>No tienes solicitudes pendientes.</li> <!-- Mensaje predeterminado -->
            </ul>
        </div>
    </div>
        </div>
             <div class="dropdown" id="dropdownMenu">
            <a href="completar_perfil.php">Perfil</a>
            <a href="CerrarSesion.php">Cerrar Sesión</a>
        </div>
    </div>
</div>


<h1>Proyectos Disponibles</h1>
<div class="wrapper">
    <i id="left" class="fa-solid fa-angle-left"></i>
    <div class="carousel">
        <?php
        // Verificar si hay proyectos disponibles
        if ($resultado_proyectos && pg_num_rows($resultado_proyectos) > 0) {
            while ($proyecto = pg_fetch_assoc($resultado_proyectos)) {
                echo '
                <div class="project-card">
                    <div class="project-info">
                        <h3>' . htmlspecialchars($proyecto['nombre']) . '</h3>
                        <img src="' . htmlspecialchars($proyecto['logo']) . '" alt="Logo">
                        <p>' . htmlspecialchars($proyecto['descripcion']) . '</p>
                        <p>Áreas: ' . htmlspecialchars($proyecto['areas']) . '</p>
                        <p>Nivel de Innovación: ' . htmlspecialchars($proyecto['nivel_innovacion']) . '</p>
                        <p>Miembros: ' . htmlspecialchars($proyecto['miembros']) . '/3</p>
                        <button class="btn" onclick="solicitarProyecto(' . $id_alumno . ',' . $proyecto['id_proyecto'] . ')">Solicitar unirse</button>
                    </div>
                </div>';
            }
        } else {
            echo '<p>No hay proyectos disponibles en este momento.</p>';
        }
        ?>
    </div>
    <i id="right" class="fa-solid fa-angle-right"></i>
</div>

<script>
    // Función para mostrar/ocultar el menú del perfil
    function toggleDropdown() {
        var dropdown = document.getElementById("dropdownMenu");
        dropdown.style.display = dropdown.style.display === "block" ? "none" : "block";
    }

      function toggleDetails(id) {
        var detalles = document.getElementById('detalles-' + id);
        if (detalles.style.display === "none") {
            detalles.style.display = "block"; // Mostrar detalles
        } else {
            detalles.style.display = "none"; // Ocultar detalles
        }
    }

    function toggleDropdown() {
    var dropdown = document.getElementById("dropdownMenu");
    dropdown.style.display = dropdown.style.display === "block" ? "none" : "block";
}

// Función para manejar las notificaciones


function toggleNotificationDropdown() {
    var dropdown = document.getElementById("notificationDropdown");

    if (dropdown.style.display === "none" || dropdown.style.display === "") {
        dropdown.style.display = "block";
        obtenerInvitaciones(); // Llamar la función para obtener invitaciones
        obtenerSolicitudes();
    } else {
        dropdown.style.display = "none";
    }
}

function obtenerInvitaciones() {
    var xhr = new XMLHttpRequest();
    xhr.open("GET", "obtener_invitaciones.php", true); // Llamar al script PHP para obtener invitaciones
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
    xhr.open("POST", "actualizar_invitacion.php", true);
    xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
    
    xhr.onload = function() {
        if (xhr.status === 200) {
            if (xhr.responseText === "Invitación actualizada correctamente.") {
                alert("Invitación " + estado);
                obtenerInvitaciones(); // Refrescar la lista de invitaciones
            } else {
                alert("Error al actualizar la invitación: " + xhr.responseText);
            }
        } else {
            alert("Error al actualizar la invitación.");
        }
    };

    // Codificar los parámetros antes de enviarlos
    var data = "id_invitacion=" + encodeURIComponent(id_invitacion) + "&estado=" + encodeURIComponent(estado);
    xhr.send(data);
}



function invitarAlumno(id_alumno, id_proyecto) {
    if (!id_alumno) {
        alert("El ID del alumno está incompleto.");
        return;
    }

    if (!id_proyecto) {
        alert("El ID del proyecto está incompleto.");
        return;
    }

    var xhr = new XMLHttpRequest();
    xhr.open("POST", "invitar.php", true);
    xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");

    xhr.send("id_alumno=" + id_alumno + "&id_proyecto=" + id_proyecto);

    xhr.onload = function() {
        if (xhr.status === 200) {
            // Aquí manejamos la respuesta como texto plano
            if (xhr.responseText.includes("Invitación enviada correctamente")) {
                alert("Invitación enviada correctamente.");
            } else {
                alert("Error al invitar al alumno: " + xhr.responseText);
            }
        } else {
            alert("Error al invitar al alumno.");
        }
    };
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


function actualizarSolicitud(id_solicitud, estado) {
    var xhr = new XMLHttpRequest();
    xhr.open("POST", "actualizar_solicitudes.php", true);
    xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");

    xhr.onload = function() {
        if (xhr.status === 200) {
            // Mostrar el mensaje de éxito o error que se recibió del servidor
            alert(xhr.responseText); 
            
            // Si la respuesta es exitosa, recargar las solicitudes para reflejar el cambio
            if (xhr.responseText.includes('Alumno añadido al equipo') || xhr.responseText.includes('Solicitud rechazada correctamente') || xhr.responseText.includes('Solicitud aceptada')) {
                obtenerSolicitudes(); // Volver a cargar las solicitudes
            }
        } else {
            alert("Error al actualizar la solicitud.");
        }
    };

    // Enviar los datos al servidor
    xhr.send("id=" + encodeURIComponent(id_solicitud) + "&estado=" + encodeURIComponent(estado));
}

// Funciones para manejar el modal
function abrirModal(id) {
    // Obtener el modal
    var modal = document.getElementById("alumnoModal");
    // Mostrar el modal
    modal.style.display = "block";
    // Cargar los detalles del alumno vía AJAX
    cargarDetalles(id);
    // Almacenar el ID del alumno en el botón de invitar del modal
    var inviteButton = document.getElementById("inviteButton");
    inviteButton.setAttribute("data-id", id);
    console.log("ID del alumno asignado al botón:", id); // Depuración
}

function cerrarModal() {
    var modal = document.getElementById("alumnoModal");
    modal.style.display = "none";
}

// Función para cargar detalles del alumno usando AJAX
function cargarDetalles(id) {
    var xhr = new XMLHttpRequest();
    xhr.open("GET", "detalles_alumno.php?id=" + id, true);
    xhr.onreadystatechange = function() {
        if (xhr.readyState === 4 && xhr.status === 200) {
            document.getElementById("detallesAlumno").innerHTML = xhr.responseText;
        }
    };
    xhr.send();
}



// Cerrar el modal al hacer clic fuera de él o en el perfil / notificaciones
window.onclick = function(event) {
    var modal = document.getElementById("alumnoModal");
    var dropdown = document.getElementById("dropdownMenu");

    // Cerrar el menú desplegable si se hace clic fuera de él
    if (!event.target.matches('.profile-icon') && !event.target.matches('.notification-icon')) {
        if (dropdown) {
            dropdown.style.display = "none";
        }
    }

    // Cerrar el modal si se hace clic fuera de él
    if (event.target == modal) {
        modal.style.display = "none";
    }
}

const carousel = document.querySelector(".carousel");
const leftArrow = document.getElementById("left");
const rightArrow = document.getElementById("right");

// Función para manejar el desplazamiento al hacer clic en las flechas
leftArrow.addEventListener("click", () => {
    carousel.scrollLeft -= carousel.clientWidth; // Desplazarse a la izquierda
});

rightArrow.addEventListener("click", () => {
    carousel.scrollLeft += carousel.clientWidth; // Desplazarse a la derecha
});

// Función para mostrar/ocultar flechas
const showHideIcons = () => {
    leftArrow.style.display = carousel.scrollLeft === 0 ? "none" : "block";
    rightArrow.style.display = carousel.scrollLeft >= (carousel.scrollWidth - carousel.clientWidth) ? "none" : "block";
}

// Llamar a la función al cargar
showHideIcons();
carousel.addEventListener("scroll", showHideIcons);
</script>
</body>
</html>

