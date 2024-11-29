<?php
// Verificar que el archivo que se intenta eliminar existe
if (isset($_GET['file'])) {
    $fileName = basename($_GET['file']);
    $filePath = 'uploads/' . $fileName;

    if (file_exists($filePath)) {
        // Eliminar el archivo
        if (unlink($filePath)) {
            echo "Archivo eliminado exitosamente.";
        } else {
            echo "Error al eliminar el archivo.";
        }
    } else {
        echo "El archivo no existe.";
    }
} else {
    echo "No se especificó ningún archivo para eliminar.";
}
?>
