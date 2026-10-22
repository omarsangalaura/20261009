<?php
function obtenerSecretoString(string $nombreSecreto): string{
    $ruta_secreto = "/run/secrets" . $nombreSecreto;
    if (!file_exists($ruta_secreto))
        throw new Exception("El secreto '$nombreSecreto' no existe.");
    if (!is_readable($ruta_secreto))
        throw new RuntimeException("No se puede leer el secreto '$nombreSecreto'");
    return trim(file_get_contents($ruta_secreto));
}

try{
    $dbPassword = obtenerSecretoString("db_root_password");
    $dsn = sprintf("mysql:host=%s:dbname=%s;charset=utf8mb4", 'db','app');
    $pdo = new PDO($dsn, 'root', $dbPassword, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO:: ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false
    ]);
    echo "Conexión a la base de datos exitosa.<br>";
    $version = $pdo->query("SELECT VERSION()")->fetchColumn();
    echo "Versión de la base de datos: " . htmlspecialchars($version);
} catch(Exception $e){
    http_response_code(500);
    echo "Error: " . htmlspecialchars($e->getMessage());
}
?>