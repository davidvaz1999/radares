<?php
// Función para generar un número de socio único
function generarNumeroSocio() {
    $file = "contador_socios.txt"; // Archivo para almacenar el último número de socio

    if (!file_exists($file)) {
        file_put_contents($file, "0"); // Iniciar con 0 si el archivo no existe
    }

    $fileHandle = fopen($file, "r+");
    if (flock($fileHandle, LOCK_EX)) { // Bloquear el archivo para evitar concurrencia
        $ultimoNumero = intval(fread($fileHandle, filesize($file)));
        $nuevoNumero = $ultimoNumero + 1; // Incrementar el número
        ftruncate($fileHandle, 0); // Vaciar el archivo
        fwrite($fileHandle, $nuevoNumero); // Escribir el nuevo número
        flock($fileHandle, LOCK_UN); // Desbloquear el archivo
    }
    fclose($fileHandle);

    return str_pad($nuevoNumero, 8, "0", STR_PAD_LEFT); // Formato: 00000001
}

// Recoger y validar datos del formulario
$nombre = htmlspecialchars($_POST["nombre"]);
$apellidos = htmlspecialchars($_POST["apellidos"]);
$dni = htmlspecialchars($_POST["dni"]);
$correo = filter_var($_POST["correo"], FILTER_VALIDATE_EMAIL);
$aportacion = floatval($_POST["aportacion"]);

// Validaciones adicionales
if (!$correo) {
    die("El correo electrónico no es válido.");
}

if ($aportacion < 3) {
    die("La aportación mínima es de 3 euros.");
}

// Generar número de socio
$numeroSocio = generarNumeroSocio();

// Guardar datos en Firebase Firestore
$firestoreUrl = "https://firestore.googleapis.com/v1/projects/ahorraunamulta-pagos/databases/(default)/documents/socios";
$data = [
    "fields" => [
        "numeroSocio" => ["stringValue" => $numeroSocio],
        "nombre" => ["stringValue" => $nombre],
        "apellidos" => ["stringValue" => $apellidos],
        "dni" => ["stringValue" => $dni],
        "correo" => ["stringValue" => $correo],
        "aportacion" => ["doubleValue" => $aportacion],
        "fechaRegistro" => ["timestampValue" => date("c")]
    ]
];

$options = [
    "http" => [
        "header" => "Content-Type: application/json\r\n",
        "method" => "POST",
        "content" => json_encode($data)
    ]
];

$context = stream_context_create($options);
$response = file_get_contents($firestoreUrl, false, $context);

if ($response === FALSE) {
    die("Hubo un error al guardar los datos en Firebase.");
}

// Enviar correo de confirmación
$asunto = "Confirmación de Registro y Bienvenida";
$mensaje = "
<!DOCTYPE html>
<html>
<head>
    <title>Confirmación de Registro</title>
</head>
<body>
    <h1>¡Hola $nombre $apellidos!</h1>
    <p>¡Gracias por registrarte en nuestra plataforma! Te damos la más cordial bienvenida a nuestra comunidad.</p>
    
    <h2>Detalles de tu registro:</h2>
    <ul>
        <li><strong>Número de socio:</strong> $numeroSocio</li>
        <li><strong>Aportación mensual:</strong> $aportacion euros</li>
    </ul>

    <h2>Instrucciones para realizar el pago:</h2>
    <p>
        1. <strong>Transferencia Bancaria</strong>:<br>
        - Banco: Banco Ejemplo<br>
        - IBAN: ES12 3456 7890 1234 5678 9012<br>
        - Concepto: Número de socio $numeroSocio<br><br>

        2. <strong>Bizum</strong>:<br>
        - Número de teléfono: 681 102 388<br>
        - Concepto: Número de socio $numeroSocio<br><br>

        3. <strong>Domiciliación Bancaria</strong>:<br>
        - Si prefieres domiciliar el pago, envíanos un correo a soporte@ahorraunamulta.com con tus datos bancarios y te ayudaremos a configurarlo.
    </p>

    <p>Si tienes alguna duda o necesitas ayuda, no dudes en contactarnos en soporte@ahorraunamulta.com.</p>

    <p>¡Gracias por unirte a nuestra comunidad!</p>

    <p>Atentamente,<br>El equipo de ahorraunamulta.com</p>
</body>
</html>
";

$headers = "From: no-reply@ahorraunamulta.com\r\n";
$headers .= "Content-Type: text/html; charset=UTF-8\r\n";

if (mail($correo, $asunto, $mensaje, $headers)) {
    echo "<script>alert('¡Registro exitoso! Tu número de socio es: $numeroSocio. Revisa tu correo electrónico para más detalles.'); window.location.href = 'index';</script>";
} else {
    echo "<script>alert('Hubo un error al enviar el correo de confirmación. Por favor, contacta con soporte.'); window.location.href = 'index';</script>";
}
?>