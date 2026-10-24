<?php
require_once 'auth.php';
require_once 'conexion.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    exit('Método no permitido.');
}

$id          = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
$editando    = (bool) $id;
$codigo      = strtoupper(trim($_POST['codigo'] ?? ''));
$nombre      = trim($_POST['nombre'] ?? '');
$descripcion = trim($_POST['descripcion'] ?? '');
$categoriaId = filter_input(INPUT_POST, 'id_categoria', FILTER_VALIDATE_INT);
$precio      = filter_input(INPUT_POST, 'precio', FILTER_VALIDATE_FLOAT);
$stockActual = filter_input(INPUT_POST, 'stock_actual', FILTER_VALIDATE_INT);
$stockMinimo = filter_input(INPUT_POST, 'stock_minimo', FILTER_VALIDATE_INT);

$errores = [];

if (!$editando) {
    if ($codigo === '' || mb_strlen($codigo) > 20 || !preg_match('/^[A-Z0-9-]+$/', $codigo)) {
        $errores[] = 'Código obligatorio o inválido.';
    }
    if (mb_strlen($nombre) < 3 || mb_strlen($nombre) > 100) {
        $errores[] = 'El nombre debe tener entre 3 y 100 caracteres.';
    }
}
if (mb_strlen($descripcion) > 255) {
    $errores[] = 'La descripción no puede superar 255 caracteres.';
}
if ($precio === false || $precio === null || $precio < 0 || $precio > 99999999) {
    $errores[] = 'Precio inválido.';
}
if ($stockActual === false || $stockActual === null || $stockActual < 0 || $stockActual > 1000000) {
    $errores[] = 'Stock actual inválido.';
}
if ($stockMinimo === false || $stockMinimo === null || $stockMinimo < 0 || $stockMinimo > 1000000) {
    $errores[] = 'Stock mínimo inválido.';
}

// La categoría debe existir y estar activa
if ($categoriaId === false || $categoriaId === null || $categoriaId < 1) {
    $errores[] = 'Categoría inválida.';
} else {
    $st = $pdo->prepare('SELECT COUNT(*) FROM categorias WHERE id_categoria = :c AND estado = 1');
    $st->execute([':c' => $categoriaId]);
    if ((int) $st->fetchColumn() === 0) {
        $errores[] = 'Categoría inválida.';
    }
}

$volver = $editando ? 'producto_form.php?id=' . (int) $id : 'producto_form.php';

if ($errores) {
    $_SESSION['old'] = [
        'codigo' => $codigo, 'nombre' => $nombre, 'descripcion' => $descripcion,
        'precio' => $_POST['precio'] ?? '', 'stock_actual' => $_POST['stock_actual'] ?? '',
        'stock_minimo' => $_POST['stock_minimo'] ?? '', 'id_categoria' => $_POST['id_categoria'] ?? '',
    ];
    $_SESSION['mensaje'] = ['tipo' => 'danger', 'texto' => implode(' ', $errores)];
    header('Location: ' . $volver);
    exit;
}

try {
    if ($editando) {
        $sql = 'UPDATE productos
                SET descripcion = :descripcion, precio = :precio,
                    stock_actual = :stock_actual, stock_minimo = :stock_minimo,
                    id_categoria = :id_categoria
                WHERE id_producto = :id AND estado = 1';
        $pdo->prepare($sql)->execute([
            ':descripcion'  => $descripcion !== '' ? $descripcion : null,
            ':precio'       => $precio,
            ':stock_actual' => $stockActual,
            ':stock_minimo' => $stockMinimo,
            ':id_categoria' => $categoriaId,
            ':id'           => $id,
        ]);
        $_SESSION['mensaje'] = ['tipo' => 'success', 'texto' => 'Producto actualizado correctamente.'];
    } else {
        $sql = 'INSERT INTO productos
                  (codigo, nombre, descripcion, precio, stock_actual, stock_minimo, id_categoria)
                VALUES
                  (:codigo, :nombre, :descripcion, :precio, :stock_actual, :stock_minimo, :id_categoria)';
        $pdo->prepare($sql)->execute([
            ':codigo'       => $codigo,
            ':nombre'       => $nombre,
            ':descripcion'  => $descripcion !== '' ? $descripcion : null,
            ':precio'       => $precio,
            ':stock_actual' => $stockActual,
            ':stock_minimo' => $stockMinimo,
            ':id_categoria' => $categoriaId,
        ]);
        $_SESSION['mensaje'] = ['tipo' => 'success', 'texto' => 'Producto registrado correctamente.'];
    }
    header('Location: productos.php');
    exit;
} catch (PDOException $ex) {
    $texto = $ex->getCode() === '23000'
        ? 'El código ya existe.'
        : 'No fue posible guardar el producto.';
    $_SESSION['old'] = [
        'codigo' => $codigo, 'nombre' => $nombre, 'descripcion' => $descripcion,
        'precio' => $_POST['precio'] ?? '', 'stock_actual' => $_POST['stock_actual'] ?? '',
        'stock_minimo' => $_POST['stock_minimo'] ?? '', 'id_categoria' => $_POST['id_categoria'] ?? '',
    ];
    $_SESSION['mensaje'] = ['tipo' => 'danger', 'texto' => $texto];
    header('Location: ' . $volver);
    exit;
}