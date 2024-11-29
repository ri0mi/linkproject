<?php
$dir = 'uploads/'; // Directorio donde se guardan los archivos
$files = scandir($dir);

$html = '';

foreach ($files as $file) {
    if ($file !== '.' && $file !== '..') {
        $filePath = $dir . $file;
        $fileType = mime_content_type($filePath); // Obtiene el tipo MIME del archivo

        $html .= '<li class="file-card">';

        // Verificar si es una imagen
        if (strpos($fileType, 'image') === 0) {
            // Mostrar miniatura de imagen
            $html .= '<img src="' . $filePath . '" alt="' . $file . '" class="thumbnail" />';
        } elseif ($fileType == 'application/pdf') {
            // Mostrar miniatura para PDF (puedes cambiar el ícono)
            $html .= '<img src="pdf-icon.png" alt="PDF" class="thumbnail" />';
        } else {
            // Para otros tipos de archivos, mostrar un ícono genérico
            $html .= '<img src="file-icon.png" alt="Archivo" class="thumbnail" />';
        }

        // Enlace para ver archivo y botón para eliminar
        $html .= '<a href="' . $filePath . '" target="_blank">Ver archivo</a>';
        $html .= '<button onclick="deleteFile(\'' . $file . '\')">Eliminar</button>';
        $html .= '</li>';
    }
}

echo $html; // Devuelve la lista de archivos en HTML
?>
