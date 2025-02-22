<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Formulario de Multas</title>
    <style>
        /* Estilos generales */
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 20px;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
        }

        .container {
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
            max-width: 600px;
            width: 100%;
        }

        h2, h3 {
            text-align: center;
            color: #333;
        }

        p {
            color: #666;
            line-height: 1.6;
        }

        label {
            display: block;
            margin-top: 15px;
            color: #333;
            font-weight: bold;
        }

        input, button {
            width: 100%;
            padding: 10px;
            margin-top: 5px;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 16px;
            box-sizing: border-box;
        }

        input[type="file"] {
            padding: 5px;
        }

        button {
            background: #28a745;
            color: white;
            border: none;
            cursor: pointer;
            font-size: 16px;
            margin-top: 20px;
        }

        button:hover {
            background: #218838;
        }

        .error {
            color: red;
            font-size: 14px;
        }

        /* Estilos del acordeón */
        .acordeon {
            background-color: #28a745;
            color: white;
            padding: 15px;
            border-radius: 5px;
            cursor: pointer;
            margin-top: 20px;
            font-weight: bold;
            text-align: center;
            transition: background-color 0.3s ease;
        }

        .acordeon:hover {
            background-color: #218838;
        }

        .contenido {
            display: none;
            padding: 15px;
            background-color: #f9f9f9;
            border: 1px solid #ddd;
            border-radius: 5px;
            margin-top: 10px;
            color: #333;
        }

        .contenido ul {
            list-style-type: disc;
            padding-left: 20px;
        }

        .contenido ul li {
            margin-bottom: 10px;
        }

        /* Estilos responsive */
        @media (max-width: 600px) {
            body {
                padding: 10px;
            }

            .container {
                padding: 15px;
            }

            h2 {
                font-size: 24px;
            }

            p {
                font-size: 14px;
            }

            input, button {
                font-size: 14px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>CAJA DE RESISTENCIA - ahorraunamulta.com</h2>
        <p>
            La <strong>Caja de Resistencia</strong> de ahorraunamulta.com nace con el firme propósito de apoyar a los conductores profesionales que, en el desempeño de su labor, se enfrentan a sanciones económicas en la carretera. Sabemos que estas multas pueden suponer un golpe significativo a tu economía, especialmente cuando se acumulan o cuando las circunstancias hacen difícil afrontarlas. Por eso, hemos creado este fondo solidario para cubrir, <strong>total o parcialmente</strong>, el importe de las sanciones, brindándote un respaldo en momentos difíciles.
        </p>

        <!-- Acordeón: ¿Cómo funciona la Caja de Resistencia? -->
        <div class="acordeon" onclick="toggleAcordeon('funcionamiento')">
            ▼ ¿CÓMO FUNCIONA LA CAJA DE RESISTENCIA?
        </div>
        <div class="contenido" id="funcionamiento">
            <p>
                1. <strong>Solicita tu evaluación</strong>:<br>
                Completa el formulario que encontrarás a continuación. Es rápido, sencillo y seguro. Necesitamos algunos datos personales, información sobre la multa y una foto legible de la sanción para poder evaluar tu caso.
            </p>
            <p>
                2. <strong>Revisión del equipo</strong>:<br>
                Una vez que envíes tu solicitud, nuestro equipo especializado revisará la documentación proporcionada. Verificaremos que cumples con los requisitos necesarios para recibir apoyo.
            </p>
            <p>
                3. <strong>Respuesta en 72 horas</strong>:<br>
                En un plazo máximo de <strong>72 horas hábiles</strong>, te contactaremos para informarte sobre el resultado de la evaluación. Si tu solicitud es aprobada, te indicaremos los siguientes pasos y el monto de apoyo que podrás recibir.
            </p>
            <p>
                4. <strong>Resolución y apoyo</strong>:<br>
                Si cumples con los requisitos, procederemos a cubrir total o parcialmente el importe de la multa. Nuestro objetivo es aliviar tu carga económica y permitirte continuar con tu trabajo sin preocupaciones.
            </p>
        </div>

        <!-- Acordeón: ¿Por qué confiar en ahorraunamulta.com? -->
        <div class="acordeon" onclick="toggleAcordeon('confiar')">
            ▼ ¿POR QUÉ CONFIAR EN AHORRAUNAMULTA.COM?
        </div>
        <div class="contenido" id="confiar">
            <p>
                - <strong>Experiencia</strong>: Contamos con un equipo especializado en gestión de sanciones y apoyo a conductores profesionales.<br>
                - <strong>Transparencia</strong>: Te mantendremos informado en todo momento sobre el estado de tu solicitud.<br>
                - <strong>Solidaridad</strong>: La Caja de Resistencia es un proyecto creado por y para los conductores, con el fin de fomentar la ayuda mutua y el apoyo en momentos difíciles.<br>
                - <strong>Rapidez</strong>: Sabemos que el tiempo es crucial, por lo que nos comprometemos a darte una respuesta en menos de 48 horas.
            </p>
        </div>

        <!-- Acordeón: Requisitos para acceder a la ayuda -->
        <div class="acordeon" onclick="toggleAcordeon('requisitos')">
            ▼ REQUISITOS PARA ACCEDER A LA AYUDA
        </div>
        <div class="contenido" id="requisitos">
            <p>
                Para poder beneficiarte de la ayuda de la <strong>Caja de Resistencia</strong>, es necesario cumplir con los siguientes requisitos:
            </p>
            <ul>
                <li><strong>Ser conductor profesional</strong>: Debes ejercer como conductor profesional, ya sea como taxista, conductor de furgoneta, camión, trailer, autobús o cualquier otro vehículo destinado al transporte de personas o mercancías.</li>
                <li><strong>Multa por exceso de velocidad</strong>: Solo se cubrirán multas por exceso de velocidad.</li>
                <li><strong>Multa en periodo de gracia</strong>: La multa debe estar en el periodo de gracia, es decir, dentro de los primeros 20 días desde su notificación, cuando la administración aplica un <strong>50% de descuento</strong>.</li>
                <li><strong>Importe máximo abonado</strong>: Cubriremos el <strong>75% del importe de la multa después de aplicar el descuento del 50%</strong>.</li>
                <li><strong>Documentación completa</strong>: Debes proporcionar toda la información solicitada en el formulario, incluyendo una foto clara y legible de la multa.</li>
                <li><strong>No haber recibido ayuda recientemente</strong>: Para garantizar que el apoyo llegue al mayor número de conductores posible, no podrás solicitar ayuda si ya has recibido apoyo de la Caja de Resistencia en los últimos <strong>6 meses</strong>.</li>
                <li><strong>Compromiso con la seguridad vial</strong>: Aunque entendemos que los errores pueden ocurrir, valoramos positivamente a aquellos conductores que demuestran un compromiso con la seguridad vial y el cumplimiento de las normas de tráfico.</li>
            </ul>
            <p>
                <strong>Ejemplos de cobertura:</strong><br>
                - Multa de <strong>100€</strong>: Con el 50% de descuento, el importe a pagar sería de 50€. La Caja de Resistencia cubriría el 75% de 50€, es decir, <strong>37.50€</strong>.<br>
                - Multa de <strong>200€</strong>: Con el 50% de descuento, el importe a pagar sería de 100€. La Caja de Resistencia cubriría el 75% de 100€, es decir, <strong>75€</strong>.<br>
                - Multa de <strong>300€</strong>: Con el 50% de descuento, el importe a pagar sería de 150€. La Caja de Resistencia cubriría el 75% de 150€, es decir, <strong>112.50€</strong>.
            </p>
            <p>
                Si cumples con estos requisitos, ¡estás en el lugar correcto! Completa el formulario a continuación y nuestro equipo evaluará tu solicitud en menos de 48 horas.
            </p>
        </div>

        <!-- Formulario de solicitud -->
        <h3>Bienvenido al formulario de solicitud</h3>
        <p>
            Este formulario es el primer paso para recibir el apoyo que necesitas. Por favor, completa todos los campos con la información solicitada y adjunta una foto clara de la multa. Asegúrate de que los datos sean correctos y estén actualizados, ya que esto agilizará el proceso de evaluación.
        </p>
        <form action="" method="post" enctype="multipart/form-data">
            <label for="nombre">NOMBRE:</label>
            <input type="text" id="nombre" name="nombre" required>
            
            <label for="apellidos">APELLIDOS:</label>
            <input type="text" id="apellidos" name="apellidos" required>
            
            <label for="socio">NÚMERO DE SOCIO:</label>
            <input type="text" id="socio" name="socio" required>
            
            <label for="correo">CORREO ELECTRÓNICO:</label>
            <input type="email" id="correo" name="correo" required>
            
            <label for="telefono">TELÉFONO:</label>
            <input type="tel" id="telefono" name="telefono" required>
            
            <label for="matricula">MATRÍCULA DEL VEHÍCULO:</label>
            <input type="text" id="matricula" name="matricula" required>
            
            <label for="importe">IMPORTE DE LA MULTA (€):</label>
            <input type="number" id="importe" name="importe" step="0.01" required>
            
            <label for="foto">FOTO DE LA MULTA:</label>
            <input type="file" id="foto" name="foto" accept="image/*" required>
            
            <button type="submit" name="enviar">ENVIAR SOLICITUD</button>
        </form>

        <h3>¿Tienes dudas?</h3>
        <p>
            Si tienes alguna pregunta o necesitas asistencia durante el proceso, no dudes en contactarnos a través de nuestro correo electrónico: <strong>soporte@ahorraunamulta.com</strong>. Estaremos encantados de ayudarte.
        </p>
    </div>

    <script>
        // Función para mostrar/ocultar el acordeón
        function toggleAcordeon(id) {
            const contenido = document.getElementById(id);
            const acordeon = document.querySelector(`[onclick="toggleAcordeon('${id}')"]`);

            if (contenido.style.display === "block") {
                contenido.style.display = "none";
                acordeon.innerHTML = `▼ ${acordeon.textContent.replace("▲ ", "")}`;
            } else {
                contenido.style.display = "block";
                acordeon.innerHTML = `▲ ${acordeon.textContent.replace("▼ ", "")}`;
            }
        }
    </script>

    <?php
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        // Recoger datos del formulario
        $nombre = htmlspecialchars($_POST["nombre"]);
        $apellidos = htmlspecialchars($_POST["apellidos"]);
        $socio = htmlspecialchars($_POST["socio"]);
        $correo = htmlspecialchars($_POST["correo"]);
        $telefono = htmlspecialchars($_POST["telefono"]);
        $matricula = htmlspecialchars($_POST["matricula"]);
        $importe = htmlspecialchars($_POST["importe"]);

        // Generar un ID simple de 6 dígitos
        $id_file = "recursos/id_counter.txt"; // Archivo para almacenar el último ID usado
        if (!file_exists($id_file)) {
            file_put_contents($id_file, "000000"); // Iniciar con 000000 si el archivo no existe
        }
        $last_id = intval(file_get_contents($id_file)); // Leer el último ID
        $new_id = str_pad($last_id + 1, 6, "0", STR_PAD_LEFT); // Incrementar y formatear a 6 dígitos
        file_put_contents($id_file, $new_id); // Guardar el nuevo ID

        // Procesar la imagen
        if (isset($_FILES["foto"]) && $_FILES["foto"]["error"] == 0) {
            $foto_tmp = $_FILES["foto"]["tmp_name"];
            $foto_extension = pathinfo($_FILES["foto"]["name"], PATHINFO_EXTENSION);
            $foto_nombre = $new_id . "." . $foto_extension; // Usar el nuevo ID como nombre
            $foto_destino = "recursos/" . $foto_nombre;

            // Crear la carpeta "recursos" si no existe
            if (!is_dir("recursos")) {
                mkdir("recursos", 0777, true);
            }

            // Mover la imagen a la carpeta "recursos"
            if (move_uploaded_file($foto_tmp, $foto_destino)) {
                // Enviar el correo electrónico con la imagen adjunta
                $destino = "soporte@ahorraunamulta.com";
                $asunto = "Nueva solicitud de evaluacion de multa";
                $mensaje = "Nombre: $nombre\n";
                $mensaje .= "Apellidos: $apellidos\n";
                $mensaje .= "Socio: $socio\n";
                $mensaje .= "Correo electrónico: $correo\n";
                $mensaje .= "Teléfono: $telefono\n";
                $mensaje .= "Matrícula del vehículo: $matricula\n";
                $mensaje .= "Importe de la multa: €$importe\n";
                $mensaje .= "ID de la imagen: $new_id\n";

                // Cabeceras del correo
                $headers = "From: $correo\r\n";
                $headers .= "MIME-Version: 1.0\r\n";
                $headers .= "Content-Type: multipart/mixed; boundary=\"boundary\"\r\n";

                // Cuerpo del correo
                $body = "--boundary\r\n";
                $body .= "Content-Type: text/plain; charset=\"UTF-8\"\r\n";
                $body .= "Content-Transfer-Encoding: 7bit\r\n\r\n";
                $body .= $mensaje . "\r\n\r\n";

                // Adjuntar la imagen
                $file_content = file_get_contents($foto_destino);
                $file_content = chunk_split(base64_encode($file_content));
                $body .= "--boundary\r\n";
                $body .= "Content-Type: image/$foto_extension; name=\"$foto_nombre\"\r\n";
                $body .= "Content-Disposition: attachment; filename=\"$foto_nombre\"\r\n";
                $body .= "Content-Transfer-Encoding: base64\r\n\r\n";
                $body .= $file_content . "\r\n";
                $body .= "--boundary--";

                // Enviar el correo
                if (mail($destino, $asunto, $body, $headers)) {
                    echo "<script>alert('Solicitud enviada correctamente.');</script>";
                } else {
                    echo "<script>alert('Hubo un error al enviar la solicitud.');</script>";
                }
            } else {
                echo "<script>alert('Error al subir la imagen.');</script>";
            }
        } else {
            echo "<script>alert('No se ha subido ninguna imagen.');</script>";
        }
    }
    ?>
</body>
</html>