<?php
require_once 'auth.php';
require_once 'conexion.php';

$movs = $pdo->query(
    'SELECT m.fecha_hora, m.tipo, m.cantidad, m.observacion,
            p.codigo, p.nombre AS producto, u.nombre AS usuario
     FROM movimientos m
     INNER JOIN productos p ON p.id_producto = m.id_producto
     INNER JOIN usuarios  u ON u.id_usuario  = m.id_usuario
     ORDER BY m.fecha_hora DESC, m.id_movimiento DESC
     LIMIT 100'
)->fetchAll();

$titulo = 'Historial';
include 'encabezado.php';
?>
<div class="d-flex justify-content-between align-items-center mb-3">
  <h1 class="h3 mb-0">Historial de movimientos</h1>
  <a href="movimiento_form.php" class="btn btn-primary">Registrar movimiento</a>
</div>

<div class="table-responsive">
<table class="table table-bordered table-hover align-middle bg-white">
  <thead class="table-light">
    <tr>
      <th>Fecha y hora</th><th>Producto</th><th>Tipo</th>
      <th class="text-end">Cantidad</th><th>Usuario</th><th>Observación</th>
    </tr>
  </thead>
  <tbody>
  <?php if (!$movs): ?>
    <tr><td colspan="6" class="text-center text-secondary">Aún no hay movimientos.</td></tr>
  <?php endif; ?>
  <?php foreach ($movs as $m): ?>
    <tr>
      <td><?= e(date('d-m-Y H:i', strtotime($m['fecha_hora']))) ?></td>
      <td><?= e($m['codigo']) ?> - <?= e($m['producto']) ?></td>
      <td>
        <span class="badge text-bg-<?= $m['tipo'] === 'entrada' ? 'success' : 'warning' ?>">
          <?= e(ucfirst($m['tipo'])) ?>
        </span>
      </td>
      <td class="text-end"><?= (int) $m['cantidad'] ?></td>
      <td><?= e($m['usuario']) ?></td>
      <td><?= e($m['observacion'] ?? '') ?></td>
    </tr>
  <?php endforeach; ?>
  </tbody>
</table>
</div>
<?php include 'pie.php'; ?>