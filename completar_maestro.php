<?php
require_once "conecta.php";
$conexion = conecta();
session_start(); // Iniciar la sesión para obtener el id_alumno
// Suponiendo que el id_alumno está almacenado en la sesión
$id_maestro = $_SESSION['id_maestro'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/x-icon" href="favicon.png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <title>Crear Cuenta</title>
</head>
<style>
      .return {
            color: blue;
            text-decoration: none; 
            position: absolute;
            top: 20px; 
            left: 20px; 
            font-size: 1.2em;
        }
</style>
<body>
    <h5><a href="Home_maestro.php" class="return" style="color:blue">Regresar</a><br><br></h5>
    <div class="container mt-5">
        <h1>Perfil Maestro</h1>
        <form action="perfil_maestro.php" method="POST" enctype="multipart/form-data">           
            <input type="hidden" name="tipo" value="maestro">
              <div class="mb-3">
                <label class="form-label">Departamento</label>
                <input type="text" id="departamento" class="form-control" name="departamento" required>
            </div>

            <div class="mb-3">
                <label for="logo" class="campo">Foto</label>
                <input type="file" name="foto" id="foto" accept="image/*" required>
            </div>
            <button type="submit" class="btn btn-primary">Continuar</button>
        </form>
        <hr>
    </div>
<script>

    function mostrarOtroCampo() {
    const select = document.getElementById("area");
    const otroCampo = document.getElementById("otroCampo");

    // Mostrar el campo de texto si se selecciona "Otra", ocultarlo de lo contrario
    if (select.value === "otra") {
        otroCampo.style.display = "block";
        document.getElementById("otraArea").setAttribute("required", "true");
    } else {
        otroCampo.style.display = "none";
        document.getElementById("otraArea").removeAttribute("required");
    }
}
</script>
</body>
</html>
