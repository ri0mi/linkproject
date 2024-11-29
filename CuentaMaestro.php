<?php

require_once "conecta.php";
$conexion=conecta();
$sql = "SELECT * FROM maestros";
$resultado = pg_query($conexion, $sql);
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
    .error-message {
        color: red;
        display: none;
    }
</style>
<body>
    <h5><a href="Seleccion.php" class="return">Regresar</a><br><br></h5>
    <div class="container mt-5">
        <h1>Registro Maestros</h1>
        <form id="registroForm" action="guardar_maestro.php" method="POST">           
            <input type="hidden" name="tipo" value="alumno">
            <div class="mb-3">
                <label class="form-label">Nombre</label>
                <input type="text" id="nombre" class="form-control" name="nombre" required>
                <small id="nombreError" class="error-message">El nombre no puede contener números.</small>
            </div>
            <div class="mb-3">
                <label class="form-label">Código</label>
                <input type="text" id="id_maestro" class="form-control" name="id_maestro" required maxlength="8" pattern=".{8}" title="El código debe tener exactamente 8 caracteres.">
                <small id="codigoError" class="error-message">El código debe ser solo números y tener 8 caracteres.</small>
            </div>
            <div class="mb-3">
                <label class="form-label">Correo</label>
                <input type="email" id="correo" class="form-control" name="correo" required>
                <small id="correoError" class="error-message">Por favor ingresa un correo válido debe terminar con academicos.udg.mx.</small>
            </div>
            <div class="mb-3">
                <label class="form-label">Contraseña</label>
                <input type="password" id="contrasena" class="form-control" name="contrasena" required maxlength="8">
                <div id="password-strength" class="form-text"></div>
            </div>
            <button type="submit" class="btn btn-primary">Continuar</button>
        </form>
        <hr>
    </div>

    <script>
        document.getElementById('registroForm').addEventListener('submit', function(event) {
            let isValid = true;

            // Validar que el nombre no contenga números
            const nombreInput = document.getElementById('nombre');
            const nombreError = document.getElementById('nombreError');
            if (/[\d]/.test(nombreInput.value)) {
                isValid = false;
                nombreError.style.display = 'block';
            } else {
                nombreError.style.display = 'none';
            }

            // Validar que el código solo contenga números y tenga 8 caracteres
            const codigoInput = document.getElementById('id_maestro');
            const codigoError = document.getElementById('codigoError');
            const codigoValue = codigoInput.value;
            if (!/^\d{8}$/.test(codigoValue)) {
                isValid = false;
                codigoError.style.display = 'block';
            } else {
                codigoError.style.display = 'none';
            }

            // Validar que el correo sea válido
        const correoInput = document.getElementById('correo');
        const correoError = document.getElementById('correoError');
        const correoValue = correoInput.value;
        const correoRegex = /^[a-zA-Z0-9._-]+@academicos\.udg.mx$/; // Reemplaza "ejemplo.com" con el dominio que desees
        if (!correoRegex.test(correoValue)) {
            isValid = false;
            correoError.style.display = 'block';
        } else {
            correoError.style.display = 'none';
        }

        // Prevenir el envío del formulario si no es válido
        if (!isValid) {
            event.preventDefault();
        }
    });
        document.getElementById('id_maestro').addEventListener('input', function() {
            if (this.value.length > 8) {
                this.value = this.value.slice(0, 8);  // Limitar a 8 caracteres
            }
        });

        document.getElementById('contrasena').addEventListener('input', function () {
            const passwordInput = document.getElementById('contrasena');
            const passwordStrengthText = document.getElementById('password-strength');
            const password = passwordInput.value;
            const regex = /^(?=.*[A-Z])(?=.*[!@#$%^&*])[A-Za-z\d!@#$%^&*]{8,}$/;

            if (!regex.test(password)) {
                passwordStrengthText.textContent = "La contraseña debe tener al menos 8 caracteres, una letra mayúscula y un carácter especial.";
                passwordStrengthText.style.color = "red";
            } else {
                passwordStrengthText.textContent = "Contraseña segura.";
                passwordStrengthText.style.color = "green";
            }
        });
    </script>
</body>
</html>
