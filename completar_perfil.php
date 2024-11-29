<?php
require_once "conecta.php";
$conexion = conecta();
session_start(); // Iniciar la sesión para obtener el id_alumno
// Suponiendo que el id_alumno está almacenado en la sesión
$id_alumno = $_SESSION['id_alumno'];
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
    <h5><a href="Home_alumno.php" class="return" style="color:blue">Regresar</a><br><br></h5>
    <div class="container mt-5">
        <h1>Perfil Alumno</h1>
        <form action="perfil_alumno.php" method="POST" enctype="multipart/form-data">           
            <input type="hidden" name="tipo" value="alumno">
            <div class="mb-3">

               
                   <label for="carrera">Seleccionar área de impacto:</label>
    <select name="carrera" id="carrera" class="campo" required onchange="mostrarOtroCampo()">
            <option value="">Seleccionar Carrera</option>
                 <option value="Licenciatura en Fisica">Licenciatura en Fisica</option>
                 <option value="Licenciatura en Ciencia de Materiales">Licenciatura en Ciencia de Materiales</option>
                 <option value="Ingenieria en Biomedica">Ingenieria en Biomedica</option>
                 <option value="Ingenieria Civil">Ingenieria Civil</option>
                 <option value="Ingenieria en Alimentos y Biotecnologia">Ingenieria en Alimentos y Biotecnologia</option>
                <option value="Ingenieria en Computacion">Ingenieria en Computacion</option>
                <option value="Ingenieria en Comunicaciones y Electronica">Ingenieria en Comunicaciones y Electronica</option>
                <option value="Ingenieria en Topografia">Ingenieria en Topografia</option>
                 <option value="Ingenieria en Fotonica">Ingenieria en Fotonica</option>
                <option value="Ingenieria Industrial">Ingenieria Industrial</option>
                <option value="Ingenieria en Informatica">Ingenieria en Informatica</option>
                <option value="Ingenieria en Mecanica Electrica">Ingenieria en Mecanica Electrica</option>
                <option value="Ingenieria Quimica">Ingenieria Quimica</option>
                <option value="Ingenieria Robotica">Ingenieria Robotica</option>
                <option value="Licenciatura en Matematicas">Licenciatura en Matematicas</option>
                <option value="Licenciatura en Quimica">Licenciatura en Quimica</option>
                <option value="Licenciatura en Quimico Farmaceutico Biologo">Licenciatura en Quimico Farmaceutico Biologo</option>
                <option value="otra">Otra</option>

                 </select>

            </div>
            <div class="mb-3">
                <label class="form-label">Contacto</label>
                <input type="text" id="contacto" class="form-control" name="contacto" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Laboratorio</label>
                <input type="text" id="laboratorio" class="form-control" name="laboratorio" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Clave</label>
                <input type="text" id="clave" class="form-control" name="clave" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Horario</label>
                <input type="text" id="horario" class="form-control" name="horario" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Habilidades</label>
                <input type="text" id="habilidades" class="form-control" name="habilidades" required>
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
