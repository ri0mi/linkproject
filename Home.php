<?php
session_start();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/x-icon" href="favicon.png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
    <link rel="stylesheet" href="Style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css" integrity="sha512-xh6O/CkQoPOWDdYTDqeRdPCVd1SpvCA9XXcUnZS2FmJNp1coAFzvtCN9BmamE+4aHK8yyUHUSCcJHgXloTyT2A==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <title>Inicio de sesión</title>
</head>
<body>
    <form action="IniciarSesion.php" method="POST">
        <h1>INICIAR SESIÓN</h1>
        <hr>

        <!-- Mensaje de error -->
        <?php
        if (isset($_SESSION['error'])) {
            echo '<p id="error-message" style="color:red;">' . $_SESSION['error'] . '</p>';
            unset($_SESSION['error']); // Limpiar el mensaje de error después de mostrarlo
        }
        ?>

        <hr>

        <!-- Formulario de inicio de sesión -->
        <i class="fa-solid fa-user"></i>
        <label>Usuario</label>
        <input type="text" name="nombre" placeholder="Nombre" required>

        <i class="fa-solid fa-unlock"></i>
        <label>Contraseña</label>
        <input type="password" name="contrasena" placeholder="Contraseña" required>
        
        <hr>
        
        <button type="submit">Iniciar Sesión</button>
        <a href="Seleccion.php">Crear Cuenta</a>
    </form>

    <script>
        window.onload = function() {
            // Verificar si el mensaje de error está presente
            var errorMessage = document.getElementById('error-message');
            if (errorMessage) {
                // Ocultar el mensaje después de 5 segundos
                setTimeout(function() {
                    errorMessage.style.display = 'none';
                }, 5000); // 5000 ms = 5 segundos
            }
        };
    </script>
</body>
</html>
