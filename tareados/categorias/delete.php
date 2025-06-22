<?php
include '../data.php';

$id = $_GET['id'] ?? $_POST['id'] ?? null;
if (!$id) {
    die('ID inválido');
}

$conn = sqlsrv_connect($serverName, $connectionOptions);
if ($conn === false) {
    die(print_r(sqlsrv_errors(), true));
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Antes de eliminar categoría, quitar categoría a productos relacionados
    $sqlQuitarCat = "UPDATE dbo.Productos SET CategoriaId = NULL WHERE CategoriaId = ?";
    $stmtQuitarCat = sqlsrv_query($conn, $sqlQuitarCat, [$id]);
    if ($stmtQuitarCat === false) {
        die(print_r(sqlsrv_errors(), true));
    }
    sqlsrv_free_stmt($stmtQuitarCat);

    // Eliminar categoría
    $sqlEliminar = "DELETE FROM dbo.Categorias WHERE IdCategorias = ?";
    $stmtEliminar = sqlsrv_query($conn, $sqlEliminar, [$id]);
    if ($stmtEliminar === false) {
        die(print_r(sqlsrv_errors(), true));
    }
    sqlsrv_free_stmt($stmtEliminar);
    sqlsrv_close($conn);

    header('Location: index.php');
    exit;
}

// Obtener categoría para mostrar datos
$sql = "SELECT Nombre FROM dbo.Categorias WHERE IdCategorias = ?";
$stmt = sqlsrv_query($conn, $sql, [$id]);
$categoria = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC);
if (!$categoria) {
    die('Categoría no encontrada');
}
sqlsrv_free_stmt($stmt);
sqlsrv_close($conn);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8" />
    <title>Eliminar Categoría</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
</head>
<body>
<div class="container mt-5">
    <h1 class="mb-4 text-danger">¿Estás seguro de eliminar esta categoría?</h1>

    <div class="alert alert-warning">
        <strong>Nombre:</strong> <?= htmlspecialchars($categoria['Nombre']) ?>
    </div>

    <form method="post">
        <input type="hidden" name="id" value="<?= htmlspecialchars($id) ?>">
        <button type="submit" class="btn btn-danger">Sí, eliminar</button>
        <a href="index.php" class="btn btn-secondary">Cancelar</a>
    </form>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
