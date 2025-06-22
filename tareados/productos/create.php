<?php
include '../data.php';

$productos = getProductos();
$categorias = getCategorias();
$bodegas = getBodegas();  // <-- Cargar bodegas

$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $codigo = trim($_POST['codigo_interno'] ?? '');
    $nombre = trim($_POST['nombre'] ?? '');
    $costo = $_POST['costo'] ?? '';
    $existencias = $_POST['existencias'] ?? '';
    $categoria_id = $_POST['categoria_id'] ?? null;
    $bodega_id = $_POST['bodega_id'] ?? null;
    if ($categoria_id === '') $categoria_id = null;
    if ($bodega_id === '') $bodega_id = null;

    if ($codigo === '') $errors[] = "El código interno es obligatorio.";
    if ($nombre === '') $errors[] = "El nombre es obligatorio.";
    if (!is_numeric($costo) || $costo < 0) $errors[] = "El costo debe ser un número positivo.";
    if (!filter_var($existencias, FILTER_VALIDATE_INT, ["options" => ["min_range" => 0]])) $errors[] = "Existencias debe ser un entero positivo.";

    if (empty($errors)) {
        $conn = sqlsrv_connect($serverName, $connectionOptions);
        if ($conn === false) {
            die(print_r(sqlsrv_errors(), true));
        }

        $sql = "INSERT INTO dbo.Productos (CodigoInterno, Nombre, Costo, Existencias, CategoriaId, BodegaId) 
                VALUES (?, ?, ?, ?, ?, ?)";
        $params = [$codigo, $nombre, $costo, $existencias, $categoria_id, $bodega_id];
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
    <meta charset="UTF-8" />
    <title>Crear Producto</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
</head>
<body>
<div class="container">
    <h1 class="mt-5">Crear Producto</h1>

    <?php if (!empty($errors)): ?>
        <div class="alert alert-danger">
            <ul>
                <?php foreach ($errors as $error): ?>
                    <li><?= htmlspecialchars($error) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <form method="post" novalidate>
        <!-- Otros campos... -->

        <div class="mb-3">
            <label for="categoria_id" class="form-label">Categoría (opcional)</label>
            <select class="form-select" id="categoria_id" name="categoria_id">
                <option value="">-- Sin categoría asignada --</option>
                <?php foreach ($categorias as $cat): ?>
                    <option value="<?= $cat['IdCategorias'] ?>" <?= (isset($_POST['categoria_id']) && $_POST['categoria_id'] == $cat['IdCategorias']) ? 'selected' : '' ?>>
                        <?= htmlspecialchars($cat['Nombre']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="mb-3">
            <label for="bodega_id" class="form-label">Bodega (opcional)</label>
            <select class="form-select" id="bodega_id" name="bodega_id">
                <option value="">-- Sin bodega asignada --</option>
                <?php foreach ($bodegas as $b): ?>
                    <option value="<?= $b['IdBodega'] ?>" <?= (isset($_POST['bodega_id']) && $_POST['bodega_id'] == $b['IdBodega']) ? 'selected' : '' ?>>
                        <?= htmlspecialchars($b['Nombre']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <button type="submit" class="btn btn-primary">Crear Producto</button>
        <a href="index.php" class="btn btn-secondary">Cancelar</a>
    </form>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
