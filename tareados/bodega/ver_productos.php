<?php
include '../data.php';

$id = $_GET['id'] ?? null;
if (!$id) {
    die('ID de bodega no proporcionado');
}

// Conexión
$conn = sqlsrv_connect($serverName, $connectionOptions);
if ($conn === false) {
    die(print_r(sqlsrv_errors(), true));
}

// Obtener información de la bodega
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

// Obtener productos asignados a esta bodega (por IdBodega en Productos)
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
    <title>Productos en la Bodega</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
</head>
<body>
<div class="container mt-5">
    <h1 class="mb-4">Productos en la Bodega: <strong><?= htmlspecialchars($bodega['Nombre']) ?></strong></h1>

    <?php if (empty($productos)): ?>
        <div class="alert alert-info">Esta bodega no tiene productos asignados.</div>
    <?php else: ?>
        <table class="table table-bordered table-hover">
            <thead class="table-light">
                <tr>
                    <th>ID</th>
                    <th>Código Interno</th>
                    <th>Nombre</th>
                    <th>Existencias</th>
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
    <?php endif; ?>

    <a href="index.php" class="btn btn-secondary mt-3">Volver al listado</a>
</div>
</body>
</html>
