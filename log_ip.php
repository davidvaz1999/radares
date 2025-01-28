<?php
// Obtén la dirección IP del visitante
$ip = $_SERVER['REMOTE_ADDR'];

// Obtén la fecha y hora actual
$date = date("Y-m-d H:i:s");

// Ruta del archivo donde se guardarán las IPs
$log_file = 'ips.log';

// Formato de la línea que se agregará al archivo
$log_entry = "$date - $ip\n";

// Escribe la entrada en el archivo
file_put_contents($log_file, $log_entry, FILE_APPEND | LOCK_EX);
?>
