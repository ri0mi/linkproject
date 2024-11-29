<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/x-icon" href="favicon.png">
    <title>En equipo</title>
    <style>
        /* General */
        body {
            font-family: 'Roboto', sans-serif;
            background: linear-gradient(135deg, #e9ecef, #dee2e6);
            margin: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            color: #495057;
        }

        .contenedor {
            background: #ffffff;
            border-radius: 12px;
            padding: 40px;
            max-width: 400px;
            text-align: center;
            box-shadow: 0px 8px 20px rgba(0, 0, 0, 0.15);
            animation: fadeIn 0.8s ease-in-out;
        }

        h1 {
            color: #212529;
            font-size: 2rem;
            margin-bottom: 20px;
        }

        p {
            color: #6c757d;
            font-size: 1rem;
            line-height: 1.6;
            margin-bottom: 30px;
        }

        a {
            display: inline-block;
            margin: 10px;
            padding: 12px 24px;
            color: #ffffff;
            background-color: #17a2b8;
            text-decoration: none;
            border-radius: 6px;
            font-size: 1rem;
            transition: all 0.3s ease;
        }

        a:hover {
            background-color: #138496;
            transform: scale(1.05);
            box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1);
        }

        /* Animations */
        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(-20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
    </style>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
</head>
<body>
    <div class="contenedor">
        <h1>Ya tienes un proyecto</h1>
        <p>No puedes acceder debido a que eres miembro o lider de un proeycto</p>
        <a href="home_alumno.php">Inicio</a>
    </div>
</body>
</html>
