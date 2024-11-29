<?php
require_once "conecta.php";
$conexion = conecta();
session_start();

$id_alumno = $_SESSION['id_alumno'];

// Obtener el ID del proyecto y el maestro asignado
$sql_proyecto = "SELECT id_proyecto, maestro_id FROM proyectos WHERE lider_id = $id_alumno LIMIT 1";
$resultado_proyecto = pg_query($conexion, $sql_proyecto);

if ($resultado_proyecto && pg_num_rows($resultado_proyecto) > 0) {
    $proyecto = pg_fetch_assoc($resultado_proyecto);
    $id_proyecto = $proyecto['id_proyecto'];
    $maestro_id_asignado = $proyecto['maestro_id']; // Obtener el ID del maestro asignado
    $_SESSION['id_proyecto'] = $id_proyecto;
} else {
    header("Location: lider_equipo.php");
    exit;
}
// Definir la consulta de asesores
$query_asesores = "SELECT id_maestro, nombre, correo FROM maestros";

// Ejecutar la consulta de asesores
$resultado_asesores = pg_query($conexion, $query_asesores);
if (!$resultado_asesores) {
    die("Error en la consulta de asesores: " . pg_last_error($conexion));
}

$filtro = isset($_GET['filtro']) ? $_GET['filtro'] : '';
?>

<!DOCTYPE html>
<html lang="es">
<head>
   
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Directorio</title>
    <link rel="icon" type="image/x-icon" href="favicon.png">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="estilos.css">
    <style>
        /* Estilos adicionales */
        .content {
            padding: 20px;
        }

        h2 {
            text-align: center;
            margin-bottom: 20px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin: 0 auto;
        }

        th, td {
            padding: 12px;
            border: 1px solid #ddd;
            text-align: center;
        }

        th {
            background-color: #f2f2f2;
        }

        /* Estilos para el modal */
        .modal {
            display: none; /* Oculto por defecto */
            position: fixed; /* Posición fija */
            z-index: 1; /* Por encima de otros elementos */
            left: 0;
            top: 0;
            width: 100%; /* Ancho completo */
            height: 100%; /* Alto completo */
            overflow: auto; /* Habilitar scroll si es necesario */
            background-color: rgba(0,0,0,0.4); /* Fondo con opacidad */
        }

        .modal-content {
            background-color: #fff;
            margin: 10% auto; /* Centrado vertical y horizontal */
            padding: 20px;
            border: 1px solid #888;
            width: 80%; /* Ancho del modal */
            max-width: 500px;
            border-radius: 8px;
            position: relative;
        }

         input[type="text"] {
            width: 100%;
            padding: 8px;
            margin-bottom: 20px;
            border-radius: 4px;
            border: 1px solid #ccc;
        }

        .close {
            color: #aaa;
            position: absolute;
            top: 10px;
            right: 20px;
            font-size: 28px;
            font-weight: bold;
            cursor: pointer;
        }

        .close:hover,
        .close:focus {
            color: black;
            text-decoration: none;
        }

        .invite-button {
            background-color: #28a745;
            color: white;
            padding: 10px 15px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }

        .invite-button:hover {
            background-color: #218838;
        }

        .ver-button {
            background-color: #007bff;
            color: white;
            padding: 8px 12px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }

        .ver-button:hover {
            background-color: #0069d9;
        }

        .invite-row-button {
            background-color: #ffc107;
            color: white;
            padding: 8px 12px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }

        .invite-row-button:hover {
            background-color: #e0a800;
        }


        /* Estilo para el dropdown de notificaciones */
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

  .mensaje-bloqueo {
            color: red;
            font-weight: bold;
            margin-top: 10px;
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

        <div id="notificationDropdown" class="dropdown-content" style="display: none;">
    <!-- Aquí aparecerán las notificaciones -->
         <ul id="notificationList"></ul>
        </div>


        <i class="fas fa-user-circle profile-icon" onclick="toggleDropdown()"></i>
        <div class="dropdown" id="dropdownMenu">
            <a href="verAlumno.php">Perfil</a>
            <a href="CerrarSesion.php">Cerrar Sesion</a>
        </div>
    </div>
</div>

<div class="content">
    <h2>Maestros</h2>


    <input type="text" id="filtro" placeholder="Buscar por nombre, apellido, carrera..." onkeyup="filtrarMaestros()">
   <table id="tablaMaestros">
    <thead>
        <tr>
            <th>Nombre</th>
            <th>Ver</th>
            <th>Invitar</th>
        </tr>
    </thead>
    <tbody>
        <?php
        if (pg_num_rows($resultado_asesores) > 0) {
            while ($maestro = pg_fetch_assoc($resultado_asesores)) {
                $id_maestro= htmlspecialchars($maestro['id_maestro']); 
                $nombre = htmlspecialchars($maestro['nombre']);
                $correo = htmlspecialchars($maestro['correo']);
                
                echo "<tr data-correo='$correo' data-proyecto='$id_proyecto'>";
                echo "<td>$nombre</td>";
                echo "<td><button class='ver-button' onclick='abrirModal($id_maestro)'>Ver</button></td>";
                
                // Verificar si ya hay un maestro asignado
                if (!is_null($maestro_id_asignado)) {
                    echo "<td><button class='invite-row-button' disabled>Invitar</button></td>";
                    echo "<td class='mensaje-bloqueo'>Ya tienes un maestro asignado.</td>";
                } else {
                    echo "<td><button class='invite-row-button' id='invite_$id_maestro' data-proyecto='$id_proyecto' onclick='invitarMaestro($id_maestro, $id_proyecto)'>Invitar</button></td>";
                }
                echo "</tr>";
            }
        } else {
            echo "<tr><td colspan='3'>No hay usuarios disponibles en este momento.</td></tr>";
        }
        ?>
        </tbody>
    </table>
</div>
    <script>
// Función para alternar el menú desplegable de perfil
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



function invitarMaestro(id_maestro, id_proyecto) {
    if (!id_maestro) {
        alert("El ID del maestro está incompleto.");  // Cambié el mensaje
        return;
    }

    if (!id_proyecto) {
        alert("El ID del proyecto está incompleto.");
        return;
    }

    var xhr = new XMLHttpRequest();
    xhr.open("POST", "invitar_maestro.php", true);
    xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");

    xhr.send("id_maestro=" + id_maestro + "&id_proyecto=" + id_proyecto);

    xhr.onload = function() {
        if (xhr.status === 200) {
            // Aquí manejamos la respuesta como texto plano
            if (xhr.responseText.includes("Invitación enviada correctamente")) {
                alert("Invitación enviada correctamente.");
            } else {
                alert("Error al invitar al maestro: " + xhr.responseText);
            }
        } else {
            alert("Error al invitar al maestro.");
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
    xhr.open("GET", "detalles_maestros.php?id=" + id, true);
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

function filtrarMaestros() {
    const filtro = document.getElementById('filtro').value.toLowerCase();
    const rows = document.querySelectorAll('#tablaMaestros tbody tr');

    rows.forEach(row => {
        const nombre = row.cells[0].textContent.toLowerCase();
        const correo = row.getAttribute('data-correo').toLowerCase();

        // Verifica si el filtro coincide en alguno de los campos
        if (nombre.includes(filtro) || correo.includes(filtro)) {
            row.style.display = ''; // Mostrar la fila si coincide
        } else {
            row.style.display = 'none'; // Ocultar la fila si no coincide
        }
    });
}




</script>

<!-- Modal para ver detalles del alumno -->
<div id="alumnoModal" class="modal">
    <div class="modal-content">
        <span class="close" onclick="cerrarModal()">&times;</span>
        <h2>Detalle del Maestro</h2>
        <div id="detallesAlumno">
            <!-- Los detalles se cargarán aquí dinámicamente -->
            <p>Cargando...</p>
        </div>
    </div>
</div>

</body>
</html>