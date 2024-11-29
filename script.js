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

// Función para filtrar alumnos
function filtrarAlumnos() {
    const filtro = document.getElementById('filtro').value.toLowerCase();
    const rows = document.querySelectorAll('#tablaAlumnos tbody tr');

    rows.forEach(row => {
        const nombre = row.cells[0].textContent.toLowerCase();
        const correo = row.getAttribute('data-correo').toLowerCase();
        const carrera = row.getAttribute('data-carrera').toLowerCase();

        // Verifica si el filtro coincide en alguno de los campos
        if (
            nombre.includes(filtro) ||
            correo.includes(filtro) ||
            carrera.includes(filtro)
        ) {
            row.style.display = ''; // Mostrar la fila si coincide
        } else {
            row.style.display = 'none'; // Ocultar si no coincide
        }
    });
}

