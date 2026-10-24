<?php
// conexion.php
date_default_timezone_set('America/Santiago');

$dsn = 'mysql:host=localhost;dbname=tecnostock;charset=utf8mb4';
$opciones = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
    $pdo = new PDO($dsn, 'root', '', $opciones);
} catch (PDOException $e) {
    exit('No fue posible conectar con la base de datos.');
}