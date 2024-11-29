<?php
// Iniciar sesión
session_start();

// Obtener datos de sesión
$nombre = $_SESSION['nombre'] ?? null;
$id_alumno = $_SESSION['id_alumno'] ?? null;
$proyecto_id = $_SESSION['proyecto_id'] ?? null;

// Validar que el id_alumno esté presente en la sesión
if (!$id_alumno) {
    die('Error: No se pudo identificar al alumno.');
}

// Conectar a la base de datos con pg_connect
require_once "conecta.php";
$conexion = conecta();  // Asegúrate de que esta función devuelve un recurso de conexión pg_connect

// Validar si el proyecto_id está presente en la sesión o si se pasa por la URL
if (!$proyecto_id) {
    // Si el proyecto_id no está en la sesión, intentar obtenerlo desde la base de datos
    $sql_proyecto = "SELECT p.id_proyecto, p.lider_id 
                     FROM proyectos p 
                     INNER JOIN equipos e ON e.proyecto_id = p.id_proyecto 
                     WHERE e.miembro_id = $id_alumno LIMIT 1";

    $resultado_proyecto = pg_query($conexion, $sql_proyecto);
    if ($resultado_proyecto && pg_num_rows($resultado_proyecto) > 0) {
        $proyecto = pg_fetch_assoc($resultado_proyecto);
        $_SESSION['proyecto_id'] = $proyecto['id_proyecto'];
        $_SESSION['lider_id'] = $proyecto['lider_id'];
        $proyecto_id = $proyecto['id_proyecto'];
    } else {
           header('Location: invitacion_crea.php'); 
           exit();
    }
}

// Verificar si el alumno es líder del proyecto
$is_lider = (isset($_SESSION['lider_id']) && $_SESSION['lider_id'] == $id_alumno);

// Crear tarea (solo si es el líder)
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['crear'])) {
    if ($is_lider) {
        // Obtener los valores del formulario
        $nombre_tarea = $_POST['nombre'];
        $descripcion_tarea = $_POST['descripcion'];
        $responsable_id = $_POST['responsable_id'];
        $estado = $_POST['estado'] ?? 'pendiente';

        // Validar que el estado esté permitido
        if (!in_array($estado, ['pendiente', 'progreso', 'completo'])) {
            die('Error: El estado proporcionado no es válido.');
        }

        // Insertar la tarea en la base de datos
        $sql_insertar_tarea = "INSERT INTO tareas (nombre, descripcion, responsable_id, proyecto_id, estado, fecha_inicio, fecha_fin) 
                               VALUES ('$nombre_tarea', '$descripcion_tarea', '$responsable_id', '$proyecto_id', '$estado', CURRENT_DATE, CURRENT_DATE + INTERVAL '7 days')";
        pg_query($conexion, $sql_insertar_tarea);
        header("Location: " . $_SERVER['PHP_SELF']);
        exit();  
    } else {
        die('Error: Solo el líder del proyecto puede asignar tareas.');
    }
}

// Obtener las tareas de la base de datos junto con el nombre del responsable
$sql_tareas = "SELECT t.*, a.nombre AS responsable 
               FROM tareas t
               LEFT JOIN alumnos a ON t.responsable_id = a.id_alumno
               WHERE t.proyecto_id = $proyecto_id";
$resultado_tareas = pg_query($conexion, $sql_tareas);
$tareas = pg_fetch_all($resultado_tareas);



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
    <title>Equipo</title>
    <link rel="icon" type="image/x-icon" href="favicon.png">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="estilos.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <style>
        /* Estilos básicos */
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

        .blue-comment {
            color: blue; /* Color azul para el comentario */
            font-weight: bold; /* Negrita para destacar el comentario */
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


    <h1>Mi Tablero</h1>

    <!-- Formulario de creación de tarea (solo para líderes) -->
    <?php if ($is_lider): ?>
        <div class="form-container">
            <h2>Crear Nueva Tarea</h2>
            <form action="" method="POST">
                <input type="text" name="nombre" placeholder="Nombre de la tarea" required>
                <textarea name="descripcion" placeholder="Descripción de la tarea" required></textarea>
                <select name="responsable_id" required>
                    <option value="" disabled selected>Asignar responsable</option>
                    <?php
// Mostrar los miembros del equipo vinculados al proyecto para asignar responsables
$sql_miembros = "SELECT a.id_alumno, a.nombre 
                 FROM alumnos a
                 INNER JOIN equipos e ON e.miembro_id = a.id_alumno
                 WHERE e.proyecto_id = $proyecto_id AND a.id_alumno != $id_alumno";

$result_miembros = pg_query($conexion, $sql_miembros);
while ($miembro = pg_fetch_assoc($result_miembros)):
?>
    <option value="<?= $miembro['id_alumno'] ?>"><?= $miembro['nombre'] ?></option>
<?php endwhile; ?>
                </select>
                <select name="estado" required>
                    <option value="pendiente">Pendiente</option>
                    <option value="progreso">En Progreso</option>
                    <option value="completo">Completada</option>
                </select>
                <button type="submit" name="crear">Crear Tarea</button>
            </form>
        </div>
    <?php endif; ?>
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
    $(document).ready(function() {
    // Dragover (permitir el arrastre sobre la columna)
    $('.kanban-column').on('dragover', function(event) {
        event.preventDefault(); // Permite que el elemento sea soltado
        $(this).css('background-color', '#e3f2fd'); // Cambia el color de fondo
    });

    // Dragleave (restaurar el color cuando el elemento deje de ser arrastrado sobre la columna)
    $('.kanban-column').on('dragleave', function(event) {
        $(this).css('background-color', 'white'); // Vuelve al color original
    });

    // Drop (cuando se suelta el elemento)
    $('.kanban-column').on('drop', function(event) {
        event.preventDefault();

        // Obtener el ID de la tarjeta que fue arrastrada
        var taskId = event.originalEvent.dataTransfer.getData('taskId'); // Asegúrate de pasar el ID de la tarea

        // Obtener el estado de la columna
        var status = $(this).data('status');

        // Actualizar el estado de la tarea en la base de datos
        $.ajax({
            url: 'actualizar_estado.php',
            method: 'POST',
            data: { id_tarea: taskId, estado: status },
            success: function(response) {
                // Recargar la página para reflejar el cambio de estado
                location.reload();
            }
        });
    });

    // Dragstart (cuando empieza el arrastre)
    $('.card').on('dragstart', function(event) {
        // Almacenar el ID de la tarea arrastrada en el evento
        var taskId = $(this).data('id');
        event.originalEvent.dataTransfer.setData('taskId', taskId); // Guardamos el ID de la tarea en el evento
    });
});


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
