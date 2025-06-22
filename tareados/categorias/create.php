<?php
include '../data.php';

$productos = getProductos(); // para listar productos en el formulario
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = trim($_POST['nombre'] ?? '');
    $productos_seleccionados = $_POST['productos'] ?? []; // array de ids de productos

    if ($nombre === '') {
        $errors[] = "El nombre de la categoría es obligatorio.";
    }

    if (empty($errors)) {
        $conn = sqlsrv_connect($serverName, $connectionOptions);
        if ($conn === false) {
            die(print_r(sqlsrv_errors(), true));
        }

        // Insertar categoría y obtener el nuevo ID
        $sql = "INSERT INTO dbo.Categorias (Nombre) VALUES (?)";
        $stmt = sqlsrv_query($conn, $sql, [$nombre]);
        if ($stmt === false) {
            die(print_r(sqlsrv_errors(), true));
        }
        sqlsrv_free_stmt($stmt);

        // Obtener Id insertado (IDENT_CURRENT puede usarse para SQL Server)
        $sqlId = "SELECT IDENT_CURRENT('dbo.Categorias') AS NewId";
        $stmtId = sqlsrv_query($conn, $sqlId);
        $row = sqlsrv_fetch_array($stmtId, SQLSRV_FETCH_ASSOC);
        $newCategoriaId = $row['NewId'];
        sqlsrv_free_stmt($stmtId);

        // Asignar la categoría a los productos seleccionados (si hay)
        if (!empty($productos_seleccionados)) {
            foreach ($productos_seleccionados as $prodId) {
                $sqlUpdate = "UPDATE dbo.Productos SET CategoriaId = ? WHERE IdProducto = ?";
                $stmtUpdate = sqlsrv_query($conn, $sqlUpdate, [$newCategoriaId, $prodId]);
                if ($stmtUpdate === false) {
                    die(print_r(sqlsrv_errors(), true));
                }
                sqlsrv_free_stmt($stmtUpdate);
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
    <title>Crear Categoría</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
</head>
<body>
<div class="container">
    <h1 class="mt-5">Crear Categoría</h1>

    <?php if (!empty($errors)): ?>
        <div class="alert alert-danger">
            <ul>
                <?php foreach($errors as $error): ?>
                <li><?= htmlspecialchars($error) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <form method="post" novalidate>
        <div class="mb-3">
            <label for="nombre" class="form-label">Nombre *</label>
            <input type="text" class="form-control" id="nombre" name="nombre" value="<?= htmlspecialchars($_POST['nombre'] ?? '') ?>" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Productos (opcional)</label>
            <select name="productos[]" class="form-select" multiple size="8">
                <?php foreach ($productos as $prod): ?>
                <option value="<?= $prod['IdProducto'] ?>" <?= (isset($_POST['productos']) && in_array($prod['IdProducto'], $_POST['productos'])) ? 'selected' : '' ?>>
                    <?= htmlspecialchars($prod['Nombre']) ?>
                </option>
                <?php endforeach; ?>
            </select>
            <div class="form-text">Mantén presionada la tecla Ctrl (o Cmd) para seleccionar varios productos.</div>
        </div>

        <button type="submit" class="btn btn-primary">Crear Categoría</button>
        <a href="index.php" class="btn btn-secondary">Cancelar</a>
    </form>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
