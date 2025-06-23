<?php
include '../data.php';

$id = $_GET['id'] ?? null;
if (!$id) {
    die('ID de bodega no proporcionado');
}

$conn = sqlsrv_connect($serverName, $connectionOptions);
if ($conn === false) {
    die(print_r(sqlsrv_errors(), true));
}

$sql = "SELECT Nombre FROM Bodegas WHERE IdBodegas = ?";
$stmt = sqlsrv_query($conn, $sql, [$id]);
if ($stmt === false) {
    die(print_r(sqlsrv_errors(), true));
}
$bodega = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC);
sqlsrv_free_stmt($stmt);

if (!$bodega) {
    die('Bodega no encontrada');
}

$sql = "SELECT IdProducto, CodigoInterno, Nombre, Existencias
        FROM Productos
        WHERE BodegaId = ?";
$stmt = sqlsrv_query($conn, $sql, [$id]);
if ($stmt === false) {
    die(print_r(sqlsrv_errors(), true));
}

$productos = [];
while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
    $productos[] = $row;
}
sqlsrv_free_stmt($stmt);
sqlsrv_close($conn);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Productos en la Bodega - <?= htmlspecialchars($bodega['Nombre']) ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet" />
    <style>
      body {
        padding-top: 100px;
      }
    </style>
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark bg-primary fixed-top shadow">
    <div class="container">
        <a class="navbar-brand d-flex align-items-center" href="/tareados/">
            <i class="bi bi-hdd-network me-2"></i>
            Tarea 2 
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarMenu"
            aria-controls="navbarMenu" aria-expanded="false" aria-label="Alternar navegación">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarMenu">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                <li class="nav-item">
                    <a class="nav-link" href="/tareados/productos/index.php">
                        <i class="bi bi-box-seam me-1"></i> Productos
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link active" aria-current="page" href="/tareados/bodega/index.php">
                        <i class="bi bi-building me-1"></i> Bodegas
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="/tareados/categorias/index.php">
                        <i class="bi bi-tags me-1"></i> Categorías
                    </a>
                </li>                
            </ul>
        </div>
    </div>
</nav>

<div class="container">
    <h1 class="mb-4">Productos en la Bodega: <strong><?= htmlspecialchars($bodega['Nombre']) ?></strong></h1>

    <?php if (empty($productos)): ?>
        <div class="alert alert-info">Esta bodega no tiene productos asignados.</div>
    <?php else: ?>
        <div class="table-responsive">
            <table class="table table-striped table-hover align-middle">
                <thead class="table-primary">
                    <tr>
                        <th scope="col">ID</th>
                        <th scope="col">Código Interno</th>
                        <th scope="col">Nombre</th>
                        <th scope="col">Existencias</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($productos as $prod): ?>
                    <tr>
                        <td><?= htmlspecialchars($prod['IdProducto']) ?></td>
                        <td><?= htmlspecialchars($prod['CodigoInterno']) ?></td>
                        <td><?= htmlspecialchars($prod['Nombre']) ?></td>
                        <td><?= htmlspecialchars($prod['Existencias']) ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>

    <a href="index.php" class="btn btn-secondary mt-3" aria-label="Volver al listado de bodegas">
        <i class="bi bi-arrow-left-circle me-1"></i> Volver al listado
    </a>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
