<?php
require_once "conecta.php";  // Asegúrate de que "conecta.php" tenga la función conecta() definida
$conexion = conecta();  // Establece la conexión con la base de datos

session_start();  // Inicia la sesión

// Obtenemos el nombre del maestro y su ID desde la sesión
$nombre = $_SESSION['nombre'];
$id_maestro = $_SESSION['id_maestro']; 

// Obtener el ID del proyecto desde la URL
$proyecto_id = isset($_GET['id']) ? $_GET['id'] : null;

// Asegúrate de que $proyecto_id no sea nulo antes de ejecutar la consulta
if ($proyecto_id) {
    // Consulta segura utilizando pg_query_params
    $query = "SELECT * FROM proyectos WHERE id_proyecto = $1";
    $result = pg_query_params($conexion, $query, array($proyecto_id));

    // Verificar si la consulta fue exitosa
    if (!$result) {
        die("Error en la consulta: " . pg_last_error());
    }

    // Verificar si el proyecto fue encontrado
    if (pg_num_rows($result) > 0) {
        // El proyecto se encuentra, puedes realizar otras acciones aquí
        // sin necesidad de mostrar el nombre y la descripción.
    } else {
        echo "Proyecto no encontrado.";
    }
} else {
    echo "No se proporcionó un ID de proyecto.";
}

// Obtener las tareas de la base de datos junto con el nombre del responsable
$sql_tareas = "SELECT t.*, a.nombre AS responsable 
               FROM tareas t
               LEFT JOIN alumnos a ON t.responsable_id = a.id_alumno
               WHERE t.proyecto_id = $proyecto_id";
$resultado_tareas = pg_query($conexion, $sql_tareas);
$tareas = pg_fetch_all($resultado_tareas);


if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['comentario'])) {
    $comentario = pg_escape_string($conexion, $_POST['comentario']);
    
    // Inserta el nuevo comentario en la base de datos
    $query_comentario = "INSERT INTO comentarios (proyecto_id, autor_id, contenido) 
                         VALUES ($1, $2, $3)";
    $resultado_comentario = pg_query_params($conexion, $query_comentario, array($proyecto_id, $id_maestro, $comentario));
    
    // Verificar si la inserción fue exitosa
    if (!$resultado_comentario) {
        die("Error al insertar comentario: " . pg_last_error());
    }
}

// Obtener los comentarios del proyecto
$query_comentarios = "SELECT c.*, m.nombre AS autor_nombre 
                      FROM comentarios c
                      LEFT JOIN maestros m ON c.autor_id = m.id_maestro
                      WHERE c.proyecto_id = $1 
                      ORDER BY c.fecha DESC"; // Ordenar por fecha
$resultado_comentarios = pg_query_params($conexion, $query_comentarios, array($proyecto_id));
$comentarios = pg_fetch_all($resultado_comentarios);

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
  body {
            font-family: 'Arial', sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
        }

        h1 {
            text-align: center;
        }

        .kanban-board {
            display: flex;
            justify-content: space-around;
            margin-top: 20px;
        }

        .kanban-column {
            width: 30%;
            background-color: #fff;
            border: 1px solid #ccc;
            border-radius: 5px;
            padding: 10px;
            min-height: 300px;
        }

        .kanban-column h2 {
            text-align: center;
        }

        .kanban-cards {
            padding: 10px;
            border-top: 1px solid #ccc;
        }

        .card {
            background-color: #e3f2fd;
            border: 1px solid #90caf9;
            border-radius: 5px;
            padding: 10px;
            margin: 5px 0;
            cursor: grab;
        }

        /* Estilo del formulario de creación de tarea */
        .form-container {
            margin-top: 20px;
        }

        .form-container form {
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        .form-container input, .form-container textarea, .form-container select {
            margin: 10px;
            padding: 10px;
            width: 80%;
        }

        .form-container button {
            padding: 10px 20px;
            background-color: #4CAF50;
            color: white;
            border: none;
            cursor: pointer;
            margin-top: 10px;
        }

        .form-container button:hover {
            background-color: #45a049;
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

  h1 {
            text-align: center;
            color: #333;
        }

        .comentarios-container {
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            padding: 20px;
            margin: 20px auto;
            max-width: 600px;
        }

        .comentario-form {
            margin-bottom: 20px;
        }

        .comentario-form textarea {
            width: 100%;
            height: 100px;
            border: 1px solid #ccc;
            border-radius: 4px;
            padding: 10px;
            font-size: 14px;
            resize: none;
            box-shadow: inset 0 1px 3px rgba(0, 0, 0, 0.1);
        }

        .comentario-form button {
            background-color: #4CAF50;
            color: white;
            border: none;
            padding: 10px 15px;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
            transition: background-color 0.3s;
        }

        .comentario-form button:hover {
            background-color: #45a049;
        }

        .comentarios-list {
            list-style-type: none;
            padding: 0;
        }

        .comentarios-list li {
            background-color: #f9f9f9;
            border: 1px solid #ddd;
            border-radius: 4px;
            padding: 10px;
            margin-bottom: 10px;
            position: relative;
        }

        .comentarios-list li strong {
            color: #333;
        }

        .comentarios-list li em {
            font-size: 12px;
            color: #888;
            position: absolute;
            right: 10px;
            bottom: 10px;
        }


        .contact-button {
            background-color: #4CAF50; /* Color de fondo del botón */
            color: white; /* Color del texto del botón */
            border: none; /* Sin borde */
            padding: 10px 15px; /* Espaciado interno */
            border-radius: 4px; /* Bordes redondeados */
            cursor: pointer; /* Cambia el cursor al pasar sobre el botón */
            margin-top: 10px; /* Espaciado superior */
        }

        
         .contact-button:hover {
            background-color: #45a049; /* Color de fondo al pasar el mouse */
        }

         .blue-comment {
            color: blue; /* Color azul para el comentario */
            font-weight: bold; /* Negrita para destacar el comentario */
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
<h2 class="centered-title">Gestor</h2>
<div class="kanban-board">
    <!-- Columna de pendientes -->
    <div class="kanban-column" data-status="pendiente" id="pendiente">
        <h2>Pendientes</h2>
        <div class="kanban-cards">
            <?php foreach ($tareas as $tarea): ?>
                <?php if ($tarea['estado'] == 'pendiente'): ?>
                    <div class="card" data-id="<?= $tarea['id'] ?>" draggable="true">
                        <strong><?= $tarea['nombre'] ?></strong><br>
                        <span><?= $tarea['descripcion'] ?></span><br>
                        <small>Responsable: <?= $tarea['responsable'] ?></small>
                    </div>
                <?php endif; ?>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- Columna de progreso -->
    <div class="kanban-column" data-status="progreso" id="progreso">
        <h2>En Progreso</h2>
        <div class="kanban-cards">
            <?php foreach ($tareas as $tarea): ?>
                <?php if ($tarea['estado'] == 'progreso'): ?>
                    <div class="card" data-id="<?= $tarea['id'] ?>" draggable="true">
                        <strong><?= $tarea['nombre'] ?></strong><br>
                        <span><?= $tarea['descripcion'] ?></span><br>
                        <small>Responsable: <?= $tarea['responsable'] ?></small>
                    </div>
                <?php endif; ?>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- Columna de completadas -->
    <div class="kanban-column" data-status="completo" id="completo">
        <h2>Completadas</h2>
        <div class="kanban-cards">
            <?php foreach ($tareas as $tarea): ?>
                <?php if ($tarea['estado'] == 'completo'): ?>
                    <div class="card" data-id="<?= $tarea['id'] ?>" draggable="true">
                        <strong><?= $tarea['nombre'] ?></strong><br>
                        <span><?= $tarea['descripcion'] ?></span><br>
                        <small>Responsable: <?= $tarea['responsable'] ?></small>
                    </div>
                <?php endif; ?>
            <?php endforeach; ?>
        </div>
    </div>
</div>

    

<div class="comentarios-container">
        <h1>Comentarios del Proyecto</h1>
        
        <!-- Formulario para agregar un nuevo comentario -->
        <form class="comentario-form" action="" method="POST">
            <textarea name="comentario" placeholder="Escribe tu comentario aquí..." required></textarea>
            <button type="submit">Agregar Comentario</button>
        </form>


           <button class="contact-button" onclick="agregarComentarioConferencia()">Conferencia</button>


        <h2>Comentarios:</h2>
        <ul class="comentarios-list">
            <?php if ($comentarios): ?>
                <?php foreach ($comentarios as $comentario): ?>
                        <li class="<?= $comentario['contenido'] == 'Contactarme para conferencia' ? 'blue-comment' : '' ?>">
                        <strong><?= htmlspecialchars($comentario['autor_nombre']) ?></strong>: <?= htmlspecialchars($comentario['contenido']) ?>
                        <em>(<?= date('d/m/Y H:i', strtotime($comentario['fecha'])) ?>)</em>
                    </li>
                <?php endforeach; ?>
            <?php else: ?>
                <li>No hay comentarios aún.</li>
            <?php endif; ?>
        </ul>
    </div>
    
<script>
     function agregarComentarioConferencia() {
        const form = document.querySelector('.comentario-form');
        const textarea = form.querySelector('textarea');
        textarea.value = 'Contactarme para conferencia';
        form.submit();
    }



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