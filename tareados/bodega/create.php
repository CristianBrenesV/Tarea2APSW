<?php
include '../data.php';

$productos = getProductos();
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = trim($_POST['nombre'] ?? '');
    $productos_seleccionados = $_POST['productos'] ?? [];

    if ($nombre === '') {
        $errors[] = "El nombre de la bodega es obligatorio.";
    }

    if (empty($errors)) {
        $conn = sqlsrv_connect($serverName, $connectionOptions);
        if ($conn === false) {
            die(print_r(sqlsrv_errors(), true));
        }

        // Insertar bodega
        $sql = "INSERT INTO dbo.Bodegas (Nombre) VALUES (?)";
        $stmt = sqlsrv_query($conn, $sql, [$nombre]);
        if ($stmt === false) {
            die(print_r(sqlsrv_errors(), true));
        }
        sqlsrv_free_stmt($stmt);

        // Obtener Id insertado
        $sql = "SELECT SCOPE_IDENTITY() AS NewId";
        $stmt = sqlsrv_query($conn, $sql);
        $row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC);
        $newId = $row['NewId'];
        sqlsrv_free_stmt($stmt);

        // Actualizar productos para asignar la bodega
        if (!empty($productos_seleccionados)) {
            foreach ($productos_seleccionados as $idProducto) {
                $sql = "UPDATE dbo.Productos SET BodegaId = ? WHERE IdProducto = ?";
                $stmt = sqlsrv_query($conn, $sql, [$newId, $idProducto]);
                if ($stmt === false) {
                    die(print_r(sqlsrv_errors(), true));
                }
                sqlsrv_free_stmt($stmt);
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
    <title>Crear Bodega</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
</head>
<body>
<div class="container">
    <h1 class="mt-5">Crear Bodega</h1>

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
            <label for="nombre" class="form-label">Nombre de la Bodega *</label>
            <input type="text" id="nombre" name="nombre" class="form-control" value="<?= htmlspecialchars($_POST['nombre'] ?? '') ?>" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Productos a asociar (opcional)</label>
            <?php foreach ($productos as $prod): ?>
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" id="prod<?= $prod['IdProducto'] ?>" name="productos[]" value="<?= $prod['IdProducto'] ?>"
                        <?= (isset($_POST['productos']) && in_array($prod['IdProducto'], $_POST['productos'])) ? 'checked' : '' ?>>
                    <label class="form-check-label" for="prod<?= $prod['IdProducto'] ?>">
                        <?= htmlspecialchars($prod['Nombre']) ?>
                    </label>
                </div>
            <?php endforeach; ?>
        </div>

        <button type="submit" class="btn btn-primary">Crear Bodega</button>
        <a href="index.php" class="btn btn-secondary">Cancelar</a>
    </form>
</div>
</body>
</html>
