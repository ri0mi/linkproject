<?php
// Varios destinatarios
$para = 'carlos@gmail.com'; // Solo un destinatario por ahora

// Título
$titulo = 'Gracias por registrarte';


//codigo
$codigo=rand(10000,99999);

// Mensaje
$mensaje = '
<html>
<head>
    <meta charset="UTF-8" />
    <title>Registro de Usuario</title>
</head>
<body>
    <p>Tu código de verificación es:</p>
    <h2>'.$codigo.'</h2>
</body>
</html>
';

// Para enviar un correo HTML, debe establecerse la cabecera Content-type
$cabeceras  = 'MIME-Version: 1.0' . "\r\n";
$cabeceras .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
$cabeceras .= 'From: No-reply <no-reply@example.com>' . "\r\n"; // Cambia este correo por uno válido

// Enviar el correo
$enviado = mail($para, $titulo, $mensaje, $cabeceras);

if ($enviado) {
    echo "El correo se ha enviado correctamente.";
} else {
    echo "Ha ocurrido un error al enviar el correo.";
}
?>
