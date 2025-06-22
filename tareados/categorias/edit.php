<?php
include '../data.php';

$id = $_GET['id'] ?? null;
if (!$id) {
    die('ID inválido');
}

// Conexión SQL Server
$conn = sqlsrv_connect($serverName, $connectionOptions);
if ($conn === false) die(print_r(sqlsrv_errors(), true));

// Obtener categoría
$sql = "SELECT * FROM dbo.Categorias WHERE IdCategorias = ?";
$stmt = sqlsrv_query($conn, $sql, [$id]);
$categoria = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC);
if (!$categoria) {
    die('Categoría no encontrada');
}
sqlsrv_free_stmt($stmt);

// Obtener todos los productos
$productos = getProductos();

// Obtener IDs de productos actualmente asignados a esta categoría
$sqlProds = "SELECT IdProducto FROM dbo.Productos WHERE CategoriaId = ?";
$stmtProds = sqlsrv_query($conn, $sqlProds, [$id]);
$productosAsignados = [];
while ($row = sqlsrv_fetch_array($stmtProds, SQLSRV_FETCH_ASSOC)) {
    $productosAsignados[] = $row['IdProducto'];
}
sqlsrv_free_stmt($stmtProds);

$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = trim($_POST['nombre'] ?? '');
    $productos_seleccionados = $_POST['productos'] ?? [];

    if ($nombre === '') {
        $errors[] = "El nombre de la categoría es obligatorio.";
    }

    if (empty($errors)) {
        // Actualizar nombre de la categoría
        $sqlUpdate = "UPDATE dbo.Categorias SET Nombre = ? WHERE IdCategorias = ?";
        $stmtUpdate = sqlsrv_query($conn, $sqlUpdate, [$nombre, $id]);
        if ($stmtUpdate === false) {
            die(print_r(sqlsrv_errors(), true));
        }
        sqlsrv_free_stmt($stmtUpdate);

        // Quitar categoría a todos los productos que la tenían
        $sqlClear = "UPDATE dbo.Productos SET CategoriaId = NULL WHERE CategoriaId = ?";
        $stmtClear = sqlsrv_query($conn, $sqlClear, [$id]);
        if ($stmtClear === false) {
            die(print_r(sqlsrv_errors(), true));
        }
        sqlsrv_free_stmt($stmtClear);

        // Asignar categoría solo a productos seleccionados
        if (!empty($productos_seleccionados)) {
            foreach ($productos_seleccionados as $prodId) {
                $sqlAsignar = "UPDATE dbo.Productos SET CategoriaId = ? WHERE IdProducto = ?";
                $stmtAsignar = sqlsrv_query($conn, $sqlAsignar, [$id, $prodId]);
                if ($stmtAsignar === false) {
                    die(print_r(sqlsrv_errors(), true));
                }
                sqlsrv_free_stmt($stmtAsignar);
            }
        }

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
    <title>Editar Categoría</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
</head>
<body>
<div class="container">
    <h1 class="mt-5">Editar Categoría</h1>

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
        <div class="mb-3">
            <label for="nombre" class="form-label">Nombre *</label>
            <input type="text" class="form-control" id="nombre" name="nombre" 
                value="<?= htmlspecialchars($_POST['nombre'] ?? $categoria['Nombre']) ?>" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Productos (opcional)</label>
            <select name="productos[]" class="form-select" multiple size="8">
                <?php foreach ($productos as $prod): 
                    $selected = false;
                    if (isset($_POST['productos'])) {
                        $selected = in_array($prod['IdProducto'], $_POST['productos']);
                    } else {
                        $selected = in_array($prod['IdProducto'], $productosAsignados);
                    }
                ?>
                <option value="<?= $prod['IdProducto'] ?>" <?= $selected ? 'selected' : '' ?>>
                    <?= htmlspecialchars($prod['Nombre']) ?>
                </option>
                <?php endforeach; ?>
            </select>
            <div class="form-text">Mantén presionada la tecla Ctrl (o Cmd) para seleccionar varios productos.</div>
        </div>

        <button type="submit" class="btn btn-primary">Guardar Cambios</button>
        <a href="index.php" class="btn btn-secondary">Cancelar</a>
    </form>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
