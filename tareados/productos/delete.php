<?php
include '../data.php';

$id = $_GET['id'] ?? $_POST['id'] ?? null;
if (!$id) {
    die('ID inválido');
}

// Conexión
$conn = sqlsrv_connect($serverName, $connectionOptions);
if ($conn === false) {
    die(print_r(sqlsrv_errors(), true));
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Eliminar el producto
    $sql = "DELETE FROM dbo.Productos WHERE IdProducto = ?";
    $stmt = sqlsrv_query($conn, $sql, [$id]);
    if ($stmt === false) {
        die(print_r(sqlsrv_errors(), true));
    }
    sqlsrv_free_stmt($stmt);
    sqlsrv_close($conn);
    header('Location: index.php');
    exit;
}

// Obtener el producto para mostrar en pantalla
$sql = "SELECT CodigoInterno, Nombre FROM dbo.Productos WHERE IdProducto = ?";
$stmt = sqlsrv_query($conn, $sql, [$id]);
$producto = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC);
if (!$producto) {
    die('Producto no encontrado');
}
sqlsrv_free_stmt($stmt);
sqlsrv_close($conn);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Eliminar Producto</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
</head>
<body>
<div class="container mt-5">
    <h1 class="mb-4 text-danger">¿Estás seguro de eliminar este producto?</h1>

    <div class="alert alert-warning">
        <strong>Código:</strong> <?= htmlspecialchars($producto['CodigoInterno']) ?><br>
        <strong>Nombre:</strong> <?= htmlspecialchars($producto['Nombre']) ?>
    </div>

    <form method="post">
        <input type="hidden" name="id" value="<?= htmlspecialchars($id) ?>">
        <button type="submit" class="btn btn-danger">Sí, eliminar</button>
        <a href="index.php" class="btn btn-secondary">Cancelar</a>
    </form>
</div>
</body>
</html>
