<?php
// detalles_alumno.php
require_once "conecta.php";
$conexion = conecta();

// Verificar si se ha recibido un ID de alumno
if (isset($_GET['id'])) {
    $id_maestro = intval($_GET['id']);
    
    // Consulta para obtener los detalles del alumno
    $sql = "SELECT * FROM maestros WHERE id_maestro = $id_maestro";
    $resultado = pg_query($conexion, $sql);
    
    // Verificar si la consulta tuvo éxito
    if ($resultado && pg_num_rows($resultado) > 0) {
        // Asignar el resultado a la variable $row
        $row = pg_fetch_assoc($resultado);

        // Generar el HTML con los detalles del alumno
        echo "<p><strong>Nombre:</strong> " . htmlspecialchars($row['nombre']) . "</p>";
        echo "<p><strong>Correo:</strong> " . htmlspecialchars($row['correo']) . "</p>";
        echo "<p><strong>Rol:</strong> " . htmlspecialchars($row['rol']) . "</p>";
    } else {
        echo "<p>No se encontraron datos para este usuario.</p>";
    }
} else {
    echo "<p>Error: No se proporcionó un ID de alumno.</p>";
}
?>
