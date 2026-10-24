<?php
require_once 'auth.php';
require_once 'conexion.php';

$sel = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
$old = $_SESSION['old'] ?? [];
unset($_SESSION['old']);
if (!empty($old['id_producto'])) {
    $sel = (int) $old['id_producto'];
}

$productos = $pdo->query(
    'SELECT id_producto, codigo, nombre, stock_actual
     FROM productos WHERE estado = 1 ORDER BY nombre'
)->fetchAll();

$titulo = 'Registrar movimiento';
include 'encabezado.php';
?>
<div class="card shadow-sm mx-auto" style="max-width: 640px;">
  <div class="card-body p-4">
    <h1 class="h3 mb-3">Registrar movimiento</h1>

    <form id="formMov" method="post" action="movimiento_guardar.php" novalidate>
      <div class="mb-3">
        <label for="id_producto" class="form-label">Producto</label>
        <select class="form-select" id="id_producto" name="id_producto" required>
          <option value="">Seleccione...</option>
          <?php foreach ($productos as $p): ?>
            <option value="<?= (int) $p['id_producto'] ?>"
              <?= (int) $sel === (int) $p['id_producto'] ? 'selected' : '' ?>>
              <?= e($p['codigo']) ?> - <?= e($p['nombre']) ?> (stock: <?= (int) $p['stock_actual'] ?>)
            </option>
          <?php endforeach; ?>
        </select>
        <div class="invalid-feedback">Seleccione un producto.</div>
      </div>

      <div class="mb-3">
        <label class="form-label d-block">Tipo</label>
        <div class="form-check form-check-inline">
          <input class="form-check-input" type="radio" name="tipo" id="tipo_entrada"
                 value="entrada" required <?= ($old['tipo'] ?? '') === 'entrada' ? 'checked' : '' ?>>
          <label class="form-check-label" for="tipo_entrada">Entrada</label>
        </div>
        <div class="form-check form-check-inline">
          <input class="form-check-input" type="radio" name="tipo" id="tipo_salida"
                 value="salida" required <?= ($old['tipo'] ?? '') === 'salida' ? 'checked' : '' ?>>
          <label class="form-check-label" for="tipo_salida">Salida</label>
        </div>
      </div>

      <div class="mb-3">
        <label for="cantidad" class="form-label">Cantidad</label>
        <input type="number" class="form-control" id="cantidad" name="cantidad"
               min="1" max="1000000" step="1" value="<?= e($old['cantidad'] ?? '') ?>" required>
        <div class="invalid-feedback">Ingrese un entero mayor a 0.</div>
      </div>

      <div class="mb-3">
        <label for="observacion" class="form-label">Observación (opcional)</label>
        <input type="text" class="form-control" id="observacion" name="observacion"
               maxlength="255" value="<?= e($old['observacion'] ?? '') ?>">
      </div>

      <div class="d-flex gap-2">
        <button type="submit" class="btn btn-primary">Registrar</button>
        <a href="productos.php" class="btn btn-outline-secondary">Cancelar</a>
      </div>
    </form>
  </div>
</div>

<script>
(function () {
  const form = document.getElementById('formMov');
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