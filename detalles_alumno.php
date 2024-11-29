<?php
// detalles_alumno.php
require_once "conecta.php";
$conexion = conecta();

// Verificar si se ha recibido un ID de alumno
if (isset($_GET['id'])) {
    $id_alumno = intval($_GET['id']);
    
    // Consulta para obtener los detalles del alumno
    $sql = "SELECT * FROM alumnos WHERE id_alumno = $id_alumno";
    $resultado = pg_query($conexion, $sql);
    
    // Verificar si la consulta tuvo éxito
    if ($resultado && pg_num_rows($resultado) > 0) {
        // Asignar el resultado a la variable $row
        $row = pg_fetch_assoc($resultado);
        
        // Ruta relativa de la foto almacenada en la base de datos
        $foto = $row["foto"];  // 'foto/auto-car-logo-template-vector-icon.jpg'
        
        // Verificar si hay una foto de perfil o si no existe
        if (empty($foto) || !file_exists($foto)) {
            // Si no hay foto o el archivo no existe, mostrar mensaje "Foto no disponible"
            echo "<div style='display: flex; justify-content: center; align-items: center;'>
                    <p>Foto no disponible</p>
                  </div>";
        } else {
            // Si existe la foto, mostrarla centrada
            echo "<div style='display: flex; justify-content: center; align-items: center;'>
                    <img src='" . htmlspecialchars($foto) . "' alt='Foto del alumno' style='width:150px; height:auto;'>
                  </div>";
        }

        // Generar el HTML con los detalles del alumno
        echo "<p><strong>Nombre:</strong> " . htmlspecialchars($row['nombre']) . "</p>";
        echo "<p><strong>Correo:</strong> " . htmlspecialchars($row['correo']) . "</p>";
        echo "<p><strong>Carrera:</strong> " . htmlspecialchars($row['carrera']) . "</p>";
        echo "<p><strong>Habilidades:</strong> " . htmlspecialchars($row['habilidades']) . "</p>";
        echo "<p><strong>Estado:</strong> " . htmlspecialchars($row['estado']) . "</p>";
    } else {
        echo "<p>No se encontraron datos para este usuario.</p>";
    }
} else {
    echo "<p>Error: No se proporcionó un ID de alumno.</p>";
}
?>
