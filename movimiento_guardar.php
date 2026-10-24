<?php
require_once 'auth.php';
require_once 'conexion.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    exit('Método no permitido.');
}

$idProducto  = filter_input(INPUT_POST, 'id_producto', FILTER_VALIDATE_INT);
$tipo        = $_POST['tipo'] ?? '';
$cantidad    = filter_input(INPUT_POST, 'cantidad', FILTER_VALIDATE_INT);
$observacion = trim($_POST['observacion'] ?? '');

$errores = [];
if ($idProducto === false || $idProducto === null || $idProducto < 1) {
    $errores[] = 'Producto inválido.';
}
if (!in_array($tipo, ['entrada', 'salida'], true)) {
    $errores[] = 'Tipo de movimiento inválido.';
}
if ($cantidad === false || $cantidad === null || $cantidad < 1 || $cantidad > 1000000) {
    $errores[] = 'La cantidad debe ser un entero entre 1 y 1.000.000.';
}
if (mb_strlen($observacion) > 255) {
    $errores[] = 'La observación no puede superar 255 caracteres.';
}

function volver_con_error(string $texto): void
{
    $_SESSION['old'] = [
        'id_producto' => $_POST['id_producto'] ?? '',
        'tipo'        => $_POST['tipo'] ?? '',
        'cantidad'    => $_POST['cantidad'] ?? '',
        'observacion' => $_POST['observacion'] ?? '',
    ];
    $_SESSION['mensaje'] = ['tipo' => 'danger', 'texto' => $texto];
    header('Location: movimiento_form.php');
    exit;
}

if ($errores) {
    volver_con_error(implode(' ', $errores));
}

try {
    $pdo->beginTransaction();

    // Bloquea la fila del producto mientras dura la transacción
    $st = $pdo->prepare(
        'SELECT stock_actual FROM productos
         WHERE id_producto = :id AND estado = 1 FOR UPDATE'
    );
    $st->execute([':id' => $idProducto]);
    $stock = $st->fetchColumn();

    if ($stock === false) {
        throw new RuntimeException('El producto no existe o está desactivado.');
    }
    if ($tipo === 'salida' && $cantidad > (int) $stock) {
        throw new RuntimeException(
            'Stock insuficiente: hay ' . (int) $stock . ' unidades y la salida pide ' . $cantidad . '.'
        );
    }

    $pdo->prepare(
        'INSERT INTO movimientos
           (fecha_hora, tipo, cantidad, observacion, id_producto, id_usuario)
         VALUES
           (:fecha, :tipo, :cantidad, :obs, :producto, :usuario)'
    )->execute([
        ':fecha'    => date('Y-m-d H:i:s'),
        ':tipo'     => $tipo,
        ':cantidad' => $cantidad,
        ':obs'      => $observacion !== '' ? $observacion : null,
        ':producto' => $idProducto,
        ':usuario'  => $_SESSION['id_usuario'],
    ]);

    $signo = $tipo === 'entrada' ? '+' : '-';
    $pdo->prepare(
        "UPDATE productos SET stock_actual = stock_actual $signo :cantidad
         WHERE id_producto = :id"
    )->execute([':cantidad' => $cantidad, ':id' => $idProducto]);

    $pdo->commit();
    $_SESSION['mensaje'] = ['tipo' => 'success', 'texto' => 'Movimiento registrado y stock actualizado.'];
    header('Location: movimientos.php');
    exit;
} catch (RuntimeException $ex) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    volver_con_error($ex->getMessage());
} catch (PDOException $ex) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    volver_con_error('No fue posible registrar el movimiento.');
}