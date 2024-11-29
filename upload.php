<?php
$uploadDir = "uploads/";

if (!file_exists($uploadDir)) {
    mkdir($uploadDir, 0777, true); // Crea el directorio si no existe
}

if (isset($_FILES['file']) && $_FILES['file']['error'] == UPLOAD_ERR_OK) {
    $fileName = basename($_FILES['file']['name']);
    $targetFile = $uploadDir . $fileName;

    if (move_uploaded_file($_FILES['file']['tmp_name'], $targetFile)) {
        echo "Archivo subido exitosamente.";
    } else {
        echo "Error al subir el archivo.";
    }
} else {
    echo "No se recibió ningún archivo.";
}
?>