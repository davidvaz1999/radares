<?php
// Mostrar errores para depuración (esto es opcional, puedes desactivarlo para no mostrar errores)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Obtener la IP del visitante
$ip = $_SERVER['REMOTE_ADDR'] ?? 'UNKNOWN';

// Obtener el User-Agent del visitante
$user_agent = $_SERVER['HTTP_USER_AGENT'] ?? 'UNKNOWN';

// Fecha y hora
$date = date("Y-m-d H:i:s");

// Archivo de logs
$log_file = __DIR__ . '/ips.log';

// Detectar tipo de dispositivo (opcional)
$device_type = 'Desktop'; // Por defecto
if (preg_match('/mobile/i', $user_agent)) {
    $device_type = 'Mobile';
} elseif (preg_match('/tablet/i', $user_agent)) {
    $device_type = 'Tablet';
}

// Mensaje para guardar en el archivo
$log_entry = "$date - IP: $ip - Dispositivo: $device_type - Navegador: $user_agent";

// Verificar si la IP ya está registrada
if (file_exists($log_file)) {
    $logs = file($log_file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    $ip_exists = false;

    foreach ($logs as $log) {
        if (strpos($log, "IP: $ip") !== false) {
            $ip_exists = true;
            break;
        }
    }

    if ($ip_exists) {
        exit; // Si la IP ya está registrada, simplemente salimos sin hacer nada
    }
}

// Si la IP no existe, registrar los datos
try {
    // Guardar en el archivo
    file_put_contents($log_file, $log_entry . "\n", FILE_APPEND | LOCK_EX);
} catch (Exception $e) {
    // Si hay algún error al guardar, no hacemos nada
    // Puedes logearlo si lo deseas, pero no mostrar mensajes al usuario
}
?>
