<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fuera de servicio - AhorraUnaMulta</title>
    <style>
        body {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
            font-family: 'Roboto', sans-serif;
            background: linear-gradient(135deg, #74ebd5, #acb6e5);
            color: #343a40;
            text-align: center;
        }
        .message {
            padding: 30px;
            border-radius: 15px;
            background-color: #ffffff;
            box-shadow: 0 8px 15px rgba(0, 0, 0, 0.2);
            max-width: 500px;
            animation: fadeIn 1s ease-in-out;
        }
        .message h1 {
            color: #007bff;
            margin-bottom: 20px;
            font-size: 2rem;
        }
        .message p {
            margin: 15px 0;
            font-size: 1.1rem;
            color: #555555;
        }
        .message a {
            display: inline-block;
            margin-top: 20px;
            padding: 10px 20px;
            color: #ffffff;
            background-color: #007bff;
            text-decoration: none;
            border-radius: 5px;
            font-size: 1rem;
            font-weight: bold;
            transition: background-color 0.3s ease;
        }
        .message a:hover {
            background-color: #0056b3;
        }
        @keyframes fadeIn {
            from { opacity: 0; transform: scale(0.9); }
            to { opacity: 1; transform: scale(1); }
        }
    </style>
</head>
<body>
    <div class="message">
        <h1>NOS ESTAMOS ACTUALIZANDO</h1>
        <p><strong>ahorraunamulta.com</strong> está en mantenimiento para traerte una mejor experiencia.</p>
        <p>Por favor, regresa más tarde. Gracias por tu paciencia.</p>
    </div>
</body>
</html>