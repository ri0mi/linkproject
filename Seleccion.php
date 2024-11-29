<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/x-icon" href="favicon.png">
    <title>Registro</title>
    <style>
           .return {
            color: blue;
            text-decoration: none; 
            position: absolute;
            top: 20px; 
            left: 20px; 
            font-size: 1.2em;
        }



        body {
            display: flex;
            justify-content: center; 
            align-items: center; 
            height: 100vh;
            margin: 0; 
            font-family: Arial, sans-serif; 
            background-color: #f0f0f0; 
        }

        .container {
            background-color: transparent; 
            padding: 20px; 
            border-radius: 5px; 
            display: flex; 
            flex-direction: column; 
            align-items: center;
            margin: 0;
        }

        h1 {
            margin-bottom: 20px; 
        }

        label {
            margin: 10px 0; 
        }

        button {
            margin-top: 20px; 
            padding: 10px 15px; 
            background-color: #007bff; 
            color: white; 
            border: none; 
            border-radius: 5px; 
            cursor: pointer; 
        }

        button:hover {
            background-color: #0056b3; 
        }
    </style>
    <script>
        function redirectToForm() {
            const rol = document.querySelector('input[name="rol"]:checked').value;
            if (rol === 'alumno') {
                window.location.href = 'CuentaAlumno.php'; 
            } else if (rol === 'maestro') {
                window.location.href = 'CuentaMaestro.php';
            }
        }
    </script>
</head>
<body>
<h4><a href="index.html" class="return" style="color:blue">Regresar</a><br><br></h4>

    <div class="container">
        <h1>Selecciona tu Rol</h1>
        
        <label>
            <input type="radio" name="rol" value="alumno" required>
            Alumno
        </label>
        <label>
            <input type="radio" name="rol" value="maestro">
            Maestro
        </label>

        <button type="button" onclick="redirectToForm()">Continuar</button>
    </div>
</body>
</html>


