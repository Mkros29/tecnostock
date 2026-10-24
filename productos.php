<?php
require_once 'auth.php';
require_once 'conexion.php';

$q = trim($_GET['q'] ?? '');

$sql = 'SELECT p.id_producto, p.codigo, p.nombre, c.nombre AS categoria,
               p.precio, p.stock_actual, p.stock_minimo, p.estado
        FROM productos p
        INNER JOIN categorias c ON c.id_categoria = p.id_categoria';
$params = [];
if ($q !== '') {
    $sql .= ' WHERE p.nombre LIKE :q1 OR p.codigo LIKE :q2 OR c.nombre LIKE :q3';
    $like = '%' . $q . '%';
    $params = [':q1' => $like, ':q2' => $like, ':q3' => $like];
}
$sql .= ' ORDER BY p.nombre';

$st = $pdo->prepare($sql);
$st->execute($params);
$productos = $st->fetchAll();

$bajos = 0;
foreach ($productos as $p) {
    if ($p['estado'] == 1 && $p['stock_actual'] < $p['stock_minimo']) {
        $bajos++;
    }
}

$titulo = 'Productos';
include 'encabezado.php';
?>
<div class="d-flex justify-content-between align-items-center mb-3">
  <h1 class="h3 mb-0">Productos</h1>
  <a href="producto_form.php" class="btn btn-primary">Nuevo producto</a>
</div>

<?php if ($bajos > 0): ?>
  <div class="alert alert-warning">
    Hay <strong><?= $bajos ?></strong> producto(s) con stock bajo.
  </div>
<?php endif; ?>

<form method="get" action="productos.php" class="row g-2 mb-3">
  <div class="col-md-8">
    <label for="q" class="visually-hidden">Buscar</label>
    <input type="text" class="form-control" id="q" name="q"
           placeholder="Buscar por nombre, código o categoría" value="<?= e($q) ?>">
  </div>
  <div class="col-md-4 d-flex gap-2">
    <button type="submit" class="btn btn-secondary">Buscar</button>
    <a href="productos.php" class="btn btn-outline-secondary">Limpiar</a>
  </div>
</form>

<div class="table-responsive">
<table class="table table-bordered table-hover align-middle bg-white">
  <thead class="table-light">
    <tr>
      <th>Código</th><th>Nombre</th><th>Categoría</th>
      <th class="text-end">Precio</th><th class="text-end">Stock</th>
      <th>Estado</th><th>Acciones</th>
    </tr>
  </thead>
  <tbody>
  <?php if (!$productos): ?>
    <tr><td colspan="7" class="text-center text-secondary">No hay productos para mostrar.</td></tr>
  <?php endif; ?>
  <?php foreach ($productos as $p):
      $activo = $p['estado'] == 1;
      $bajo   = $activo && $p['stock_actual'] < $p['stock_minimo'];
  ?>
    <tr class="<?= $bajo ? 'table-danger' : '' ?> <?= $activo ? '' : 'text-secondary' ?>">
      <td><?= e($p['codigo']) ?></td>
      <td><?= e($p['nombre']) ?></td>
      <td><?= e($p['categoria']) ?></td>
      <td class="text-end">$<?= number_format((float) $p['precio'], 0, ',', '.') ?></td>
      <td class="text-end">
        <?= (int) $p['stock_actual'] ?> / mín. <?= (int) $p['stock_minimo'] ?>
        <?php if ($bajo): ?><span class="badge text-bg-danger">Stock bajo</span><?php endif; ?>
      </td>
      <td>
        <span class="badge text-bg-<?= $activo ? 'success' : 'secondary' ?>">
          <?= $activo ? 'Activo' : 'Desactivado' ?>
        </span>
      </td>
      <td>
        <?php if ($activo): ?>
          <a class="btn btn-sm btn-outline-success"
             href="movimiento_form.php?id=<?= (int) $p['id_producto'] ?>">Movimiento</a>
          <a class="btn btn-sm btn-outline-primary"
             href="producto_form.php?id=<?= (int) $p['id_producto'] ?>">Editar</a>
          <form method="post" action="producto_estado.php" class="d-inline"
                onsubmit="return confirm('¿Desactivar este producto?');">
            <input type="hidden" name="id" value="<?= (int) $p['id_producto'] ?>">
            <button type="submit" class="btn btn-sm btn-outline-danger">Desactivar</button>
          </form>
        <?php endif; ?>
      </td>
    </tr>
  <?php endforeach; ?>
  </tbody>
</table>
</div>
<?php include 'pie.php'; ?>