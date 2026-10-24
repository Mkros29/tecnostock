<?php
require_once 'auth.php';
require_once 'conexion.php';

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
$p = [
    'codigo' => '', 'nombre' => '', 'descripcion' => '', 'precio' => '',
    'stock_actual' => '', 'stock_minimo' => '', 'id_categoria' => '',
];

if ($id) {
    $st = $pdo->prepare('SELECT * FROM productos WHERE id_producto = :id AND estado = 1');
    $st->execute([':id' => $id]);
    $fila = $st->fetch();
    if (!$fila) {
        $_SESSION['mensaje'] = ['tipo' => 'danger', 'texto' => 'Producto no encontrado o desactivado.'];
        header('Location: productos.php');
        exit;
    }
    $p = $fila;
}

// Si hubo errores al guardar, se recuperan los datos escritos
if (!empty($_SESSION['old'])) {
    $p = array_merge($p, $_SESSION['old']);
    unset($_SESSION['old']);
}

$cats = $pdo->query(
    'SELECT id_categoria, nombre FROM categorias WHERE estado = 1 ORDER BY nombre'
)->fetchAll();

$editando = (bool) $id;
$titulo = $editando ? 'Editar producto' : 'Nuevo producto';
include 'encabezado.php';
?>
<div class="card shadow-sm mx-auto" style="max-width: 760px;">
  <div class="card-body p-4">
    <h1 class="h3 mb-3"><?= e($titulo) ?></h1>

    <form id="formProducto" method="post" action="producto_guardar.php" novalidate>
      <?php if ($editando): ?>
        <input type="hidden" name="id" value="<?= (int) $id ?>">
      <?php endif; ?>

      <div class="row g-3">
        <div class="col-md-4">
          <label for="codigo" class="form-label">Código</label>
          <input type="text" class="form-control" id="codigo" name="codigo"
                 maxlength="20" pattern="[A-Za-z0-9\-]+" placeholder="TEC-001"
                 value="<?= e($p['codigo']) ?>"
                 <?= $editando ? 'readonly' : 'required' ?>>
          <div class="invalid-feedback">Solo letras, números y guiones.</div>
        </div>
        <div class="col-md-8">
          <label for="nombre" class="form-label">Nombre</label>
          <input type="text" class="form-control" id="nombre" name="nombre"
                 minlength="3" maxlength="100" value="<?= e($p['nombre']) ?>"
                 <?= $editando ? 'readonly' : 'required' ?>>
          <div class="invalid-feedback">Entre 3 y 100 caracteres.</div>
        </div>
        <div class="col-md-6">
          <label for="id_categoria" class="form-label">Categoría</label>
          <select class="form-select" id="id_categoria" name="id_categoria" required>
            <option value="">Seleccione...</option>
            <?php foreach ($cats as $c): ?>
              <option value="<?= (int) $c['id_categoria'] ?>"
                <?= (int) $p['id_categoria'] === (int) $c['id_categoria'] ? 'selected' : '' ?>>
                <?= e($c['nombre']) ?>
              </option>
            <?php endforeach; ?>
          </select>
          <div class="invalid-feedback">Seleccione una categoría.</div>
        </div>
        <div class="col-md-6">
          <label for="precio" class="form-label">Precio</label>
          <input type="number" class="form-control" id="precio" name="precio"
                 min="0" max="99999999" step="0.01" value="<?= e($p['precio']) ?>" required>
          <div class="invalid-feedback">Ingrese un precio válido (0 o mayor).</div>
        </div>
        <div class="col-md-6">
          <label for="stock_actual" class="form-label">Stock actual</label>
          <input type="number" class="form-control" id="stock_actual" name="stock_actual"
                 min="0" max="1000000" step="1" value="<?= e($p['stock_actual']) ?>" required>
          <div class="invalid-feedback">Ingrese un entero entre 0 y 1.000.000.</div>
        </div>
        <div class="col-md-6">
          <label for="stock_minimo" class="form-label">Stock mínimo</label>
          <input type="number" class="form-control" id="stock_minimo" name="stock_minimo"
                 min="0" max="1000000" step="1" value="<?= e($p['stock_minimo']) ?>" required>
          <div class="invalid-feedback">Ingrese un entero entre 0 y 1.000.000.</div>
        </div>
        <div class="col-12">
          <label for="descripcion" class="form-label">Descripción</label>
          <textarea class="form-control" id="descripcion" name="descripcion"
                    rows="3" maxlength="255"><?= e($p['descripcion']) ?></textarea>
        </div>
        <div class="col-12 d-flex gap-2">
          <button type="submit" class="btn btn-primary">Guardar</button>
          <a href="productos.php" class="btn btn-outline-secondary">Cancelar</a>
        </div>
      </div>
    </form>
  </div>
</div>

<script>
(function () {
  const form = document.getElementById('formProducto');
  form.addEventListener('submit', function (ev) {
    if (!form.checkValidity()) {
      ev.preventDefault();
      ev.stopPropagation();
    }
    form.classList.add('was-validated');
  });
})();
</script>
<?php include 'pie.php'; ?>