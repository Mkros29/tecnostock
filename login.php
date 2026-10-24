<?php
session_start();
require_once 'conexion.php';

if (isset($_SESSION['id_usuario'])) {
    header('Location: inicio.php');
    exit;
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $correo = trim($_POST['correo'] ?? '');
    $clave  = $_POST['clave'] ?? '';

    if (!filter_var($correo, FILTER_VALIDATE_EMAIL) || $clave === '') {
        $error = 'Ingrese un correo válido y su clave.';
    } else {
        $st = $pdo->prepare(
            'SELECT id_usuario, nombre, clave
             FROM usuarios WHERE correo = :correo AND estado = 1'
        );
        $st->execute([':correo' => $correo]);
        $u = $st->fetch();

        if ($u && password_verify($clave, $u['clave'])) {
            session_regenerate_id(true);
            $_SESSION['id_usuario'] = (int) $u['id_usuario'];
            $_SESSION['nombre']     = $u['nombre'];
            header('Location: inicio.php');
            exit;
        }
        $error = 'Correo o clave incorrectos.';
    }
}
?>
<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>TecnoStock | Iniciar sesión</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
  <main class="container py-5">
    <div class="card shadow-sm mx-auto" style="max-width: 420px;">
      <div class="card-body p-4">
        <h1 class="h3 mb-3">TecnoStock</h1>
        <p class="text-secondary">Inicie sesión para continuar.</p>

        <?php if ($error !== ''): ?>
          <div class="alert alert-danger">
            <?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?>
          </div>
        <?php endif; ?>

        <form method="post" action="login.php">
          <div class="mb-3">
            <label for="correo" class="form-label">Correo</label>
            <input type="email" class="form-control" id="correo" name="correo" required>
          </div>
          <div class="mb-3">
            <label for="clave" class="form-label">Clave</label>
            <input type="password" class="form-control" id="clave" name="clave" required>
          </div>
          <button type="submit" class="btn btn-primary w-100">Ingresar</button>
        </form>
      </div>
    </div>
  </main>
</body>
</html>