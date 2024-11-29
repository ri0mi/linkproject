<?php
require"conecta.php";

$conexion = conecta();


if ($conexion) {
    echo "Conexión exitosa a la base de datos PostgreSQL.";
} else {
    echo "Error al conectar a la base de datos PostgreSQL.";
}

?>