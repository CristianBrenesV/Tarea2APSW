<?php
include '../data.php';

$id = $_GET['id'] ?? null;
if (!$id) {
    die('ID inválido');
}

// Cargar producto
$conn = sqlsrv_connect($serverName, $connectionOptions);
if ($conn === false) die(print_r(sqlsrv_errors(), true));

$sql = "SELECT * FROM dbo.Productos WHERE IdProducto = ?";
$stmt = sqlsrv_query($conn, $sql, [$id]);
$producto = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC);
if (!$producto) {
    die('Producto no encontrado');
}
sqlsrv_free_stmt($stmt);

// Cargar categorías
$productos = getProductos();
$categorias = getCategorias();
$bodegas = getBodegas();

$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $codigo = trim($_POST['codigo_interno'] ?? '');
    $nombre = trim($_POST['nombre'] ?? '');
    $costo = $_POST['costo'] ?? '';
    $existencias = $_POST['existencias'] ?? '';
    $categoria_id = $_POST['categoria_id'] ?? null;
    if ($categoria_id === '') $categoria_id = null;

    if ($codigo === '') $errors[] = "El código interno es obligatorio.";
    if ($nombre === '') $errors[] = "El nombre es obligatorio.";
    if (!is_numeric($costo) || $costo < 0) $errors[] = "El costo debe ser un número positivo.";
    if (!filter_var($existencias, FILTER_VALIDATE_INT, ["options" => ["min_range" => 0]])) $errors[] = "Existencias debe ser un entero positivo.";

    if (empty($errors)) {
        $sql = "UPDATE dbo.Productos
                SET CodigoInterno = ?, Nombre = ?, Costo = ?, Existencias = ?, CategoriaId = ?
                WHERE IdProducto = ?";
        $params = [$codigo, $nombre, $costo, $existencias, $categoria_id, $id];
        $stmt = sqlsrv_query($conn, $sql, $params);
        if ($stmt === false) {
            die(print_r(sqlsrv_errors(), true));
        }
        sqlsrv_free_stmt($stmt);
        sqlsrv_close($conn);
        header('Location: index.php');
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Editar Producto</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
</head>
<body>
<div class="container">
    <h1 class="mt-5">Editar Producto</h1>

    <?php if (!empty($errors)): ?>
        <div class="alert alert-danger">
            <ul>
                <?php foreach ($errors as $error): ?>
                    <li><?= htmlspecialchars($error) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <form method="post">
        <div class="mb-3">
            <label class="form-label">Código Interno *</label>
            <input type="text" name="codigo_interno" class="form-control" value="<?= htmlspecialchars($_POST['codigo_interno'] ?? $producto['CodigoInterno']) ?>" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Nombre *</label>
            <input type="text" name="nombre" class="form-control" value="<?= htmlspecialchars($_POST['nombre'] ?? $producto['Nombre']) ?>" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Costo *</label>
            <input type="number" name="costo" step="0.01" class="form-control" value="<?= htmlspecialchars($_POST['costo'] ?? $producto['Costo']) ?>" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Existencias *</label>
            <input type="number" name="existencias" class="form-control" value="<?= htmlspecialchars($_POST['existencias'] ?? $producto['Existencias']) ?>" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Categoría</label>
            <select name="categoria_id" class="form-select">
                <option value="">-- Sin categoría asignada --</option>
                <?php foreach ($categorias as $cat): ?>
                    <option value="<?= $cat['IdCategorias'] ?>" <?= (($producto['CategoriaId'] ?? '') == $cat['IdCategorias']) ? 'selected' : '' ?>>
                        <?= htmlspecialchars($cat['Nombre']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="mb-3">
            <label class="form-label">Bodega (opcional)</label>
            <select name="bodega_id" class="form-select">
                <option value="">-- Sin bodega asignada --</option>
                <?php foreach ($bodegas as $b): ?>
                    <option value="<?= $b['IdBodegas'] ?>"
                        <?= (($producto['BodegaId'] ?? '') == $b['IdBodegas']) ? 'selected' : '' ?>>
                        <?= htmlspecialchars($b['Nombre']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <button type="submit" class="btn btn-primary">Guardar Cambios</button>
        <a href="index.php" class="btn btn-secondary">Cancelar</a>
    </form>
</div>
</body>
</html>
