<?php
require_once 'auth.php';
require_once 'conexion.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    exit('Método no permitido.');
}

$id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);

if ($id) {
    try {
        $st = $pdo->prepare('UPDATE productos SET estado = 0 WHERE id_producto = :id');
        $st->execute([':id' => $id]);
        $_SESSION['mensaje'] = ['tipo' => 'success', 'texto' => 'Producto desactivado.'];
    } catch (PDOException $ex) {
        $_SESSION['mensaje'] = ['tipo' => 'danger', 'texto' => 'No fue posible desactivar el producto.'];
    }
} else {
    $_SESSION['mensaje'] = ['tipo' => 'danger', 'texto' => 'Producto inválido.'];
}
header('Location: productos.php');
exit;