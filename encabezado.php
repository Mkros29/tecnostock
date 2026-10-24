<?php require_once __DIR__ . '/funciones.php'; ?>
<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>TecnoStock | <?= e($titulo ?? '') ?></title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<nav class="navbar navbar-expand navbar-dark bg-dark">
  <div class="container">
    <a class="navbar-brand" href="productos.php">TecnoStock</a>
    <a class="nav-link" href="movimiento_form.php">Registrar movimiento</a>
    <a class="nav-link" href="movimientos.php">Historial</a>
    <div class="navbar-nav me-auto">
      <a class="nav-link" href="productos.php">Productos</a>
    </div>
    <span class="navbar-text me-3"><?= e($_SESSION['nombre'] ?? '') ?></span>
    <a class="btn btn-outline-light btn-sm" href="logout.php">Cerrar sesión</a>
  </div>
</nav>
<main class="container py-4">
<?php if (!empty($_SESSION['mensaje'])): ?>
  <div class="alert alert-<?= e($_SESSION['mensaje']['tipo']) ?>">
    <?= e($_SESSION['mensaje']['texto']) ?>
  </div>
  <?php unset($_SESSION['mensaje']); ?>
<?php endif; ?>
