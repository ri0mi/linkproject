<?php
session_start();
$nombre = $_SESSION['nombre'];
$id_alumno = $_SESSION['id_alumno']; // ID del alumno actual

// Conectar a la base de datos
require_once "conecta.php";
$conexion = conecta();

// Obtener los proyectos como líder
$sql_lider = "SELECT * FROM proyectos WHERE lider_id = $id_alumno";
$resultado_lider = pg_query($conexion, $sql_lider);

// Obtener los proyectos como miembro
$sql_miembro = "SELECT p.*, e.rol, e.miembro_id 
                FROM proyectos p
                INNER JOIN equipos e ON e.proyecto_id = p.id_proyecto
                WHERE e.miembro_id = $id_alumno";
$resultado_miembro = pg_query($conexion, $sql_miembro);

// Combinamos los resultados
$proyectos = [];

// Agregar proyectos como líder
while ($fila = pg_fetch_assoc($resultado_lider)) {
    $proyectos[$fila['id_proyecto']] = $fila;
}

// Agregar proyectos como miembro
while ($fila = pg_fetch_assoc($resultado_miembro)) {
    if (!isset($proyectos[$fila['id_proyecto']])) {
        $proyectos[$fila['id_proyecto']] = $fila;
    }
}


if (empty($proyectos)) {
    header("Location: invitacion_crea.php");
    exit(); // Asegurarse de detener la ejecución después de la redirección
}



?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Equipo</title>
    <link rel="icon" type="image/x-icon" href="favicon.png">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="estilos.css">
    <style>
        body {
            font-family: 'Arial', sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
        }

        .container {
            width: 80%;
            margin: 30px auto;
        }

        h2 {
            text-align: center;
            color: #34495e;
            font-size: 28px;
            margin-bottom: 20px;
        }



        .proyecto {
            background-color: white;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            padding: 20px;
            margin-bottom: 30px;
            border-radius: 8px;
        }

        .proyecto h3 {
            color: #2c3e50;
            font-size: 22px;
            margin-top: 0;
            
        }

        .proyecto img {
            max-width: 200px;
            border-radius: 5px;
            margin: 15px 0;
        }

        .proyecto table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        .proyecto table th, .proyecto table td {
            text-align: left;
            padding: 10px;
            border-bottom: 1px solid #ddd;
        }

        .proyecto table th {
            background-color: #2c3e50;
            color: white;
        }

        .proyecto table tr:hover {
            background-color: #f1f1f1;
        }

        .miembro {
            display: flex;
            align-items: center;
            margin: 15px 0;
        }

        .miembro img {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            margin-right: 10px;
        }

        .miembro .nombre {
            font-weight: bold;
            color: #2c3e50;
            cursor: pointer;
            text-decoration: underline;
        }

        .miembro .nombre:hover {
            color: #3498db;
        }

        .detalles {
            margin-top: 10px;
            padding-left: 20px;
            background-color: #f9f9f9;
            border-left: 3px solid #3498db;
            display: none; /* Inicialmente oculto */
        }

        .miembro .rol {
            color: #7f8c8d;
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
</head>
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
            <ul id="notificationList"></ul>
        </div>
        <div class="dropdown" id="dropdownMenu">
            <a href="Intermedio.php">Perfil</a>
            <a href="CerrarSesion.php">Cerrar Sesión</a>
        </div>
    </div>
</div>

<div class="container">
    <h2>Equipo</h2>

    <?php foreach ($proyectos as $proyecto) : ?>
        <div class="proyecto">
            <h3>Proyecto: <?= htmlspecialchars($proyecto['nombre']); ?></h3>
            <img src="<?= htmlspecialchars($proyecto['logo']); ?>" alt="Logo">
            <p><strong>Asesor:</strong> <?= htmlspecialchars($proyecto['asesor']); ?></p>
            <p><strong>Descripción:</strong> <?= htmlspecialchars($proyecto['descripcion']); ?></p>
            <p><strong>Área:</strong> <?= htmlspecialchars($proyecto['areas']); ?></p>
            <p><strong>Nivel de Innovación:</strong> <?= htmlspecialchars($proyecto['nivel_innovacion']); ?></p>
            <p><strong>Conocimientos:</strong> <?= htmlspecialchars($proyecto['conocimientos']); ?></p>

            <h3>Miembros del Proyecto</h3>
            <table>
                <thead>
                    <tr>
                        <th>Miembro</th>
                        <th>Rol</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    // Obtener los miembros y roles del equipo
                    $sql_equipo = "SELECT a.nombre, e.rol ,a.foto, a.carrera, a.habilidades, a.contacto, a.id_alumno
                                   FROM equipos e
                                   INNER JOIN alumnos a ON e.miembro_id = a.id_alumno
                                   WHERE e.proyecto_id = " . $proyecto['id_proyecto'];
                    $resultado_equipo = pg_query($conexion, $sql_equipo);

                    while ($miembro = pg_fetch_assoc($resultado_equipo)) : ?>
                        <tr>
                            <td>
                                <div class="miembro">
                                    <img src="<?= htmlspecialchars($miembro['foto']); ?>" alt="Foto del miembro">
                                    <span class="nombre" onclick="toggleDetails(<?= $miembro['id_alumno']; ?>)"><?= htmlspecialchars($miembro['nombre']); ?></span>
                                </div>
                                <!-- Contenedor para los detalles ocultos del miembro -->
                                <div class="detalles" id="detalles-<?= $miembro['id_alumno']; ?>" style="display: none;">
                                    <p><strong>Carrera:</strong> <?= htmlspecialchars($miembro['carrera']); ?></p>
                                    <p><strong>Habilidades:</strong> <?= htmlspecialchars($miembro['habilidades']); ?></p>
                                    <p><strong>Contacto:</strong> <?= htmlspecialchars($miembro['contacto']); ?></p>
                                </div>
                            </td>
                            <td class="rol"><?= htmlspecialchars($miembro['rol']); ?></td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    <?php endforeach; ?>
</div>

<script>
    // Función para alternar la visibilidad de los detalles de un miembro
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
</script>

</body>
</html>
