<?php
include '../data.php';

$id = $_GET['id'] ?? null;
if (!$id) die('ID inválido');

$conn = sqlsrv_connect($serverName, $connectionOptions);
if ($conn === false) die(print_r(sqlsrv_errors(), true));

// Obtener bodega actual
$sql = "SELECT * FROM dbo.Bodegas WHERE IdBodegas = ?";
$stmt = sqlsrv_query($conn, $sql, [$id]);
$bodega = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC);
if (!$bodega) die('Bodega no encontrada');
sqlsrv_free_stmt($stmt);

// Obtener todos los productos con su BodegaId para saber cuáles están asignados
$sql = "SELECT IdProducto, Nombre, BodegaId FROM dbo.Productos";
$stmt = sqlsrv_query($conn, $sql);
$productos = [];
while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
    $productos[] = $row;
}
sqlsrv_free_stmt($stmt);

$productos_asignados = [];
foreach ($productos as $prod) {
    if (($prod['BodegaId'] ?? null) == $id) {
        $productos_asignados[] = $prod['IdProducto'];
    }
}

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = trim($_POST['nombre'] ?? '');
    $productos_seleccionados = $_POST['productos'] ?? [];

    if ($nombre === '') {
        $errors[] = "El nombre de la bodega es obligatorio.";
    }

    if (empty($errors)) {
        // Actualizar nombre de la bodega
        $sql = "UPDATE dbo.Bodegas SET Nombre = ? WHERE IdBodegas = ?";
        $stmt = sqlsrv_query($conn, $sql, [$nombre, $id]);
        if ($stmt === false) die(print_r(sqlsrv_errors(), true));
        sqlsrv_free_stmt($stmt);

        // Desasignar productos que ya no están seleccionados
        if (empty($productos_seleccionados)) {
            // Ningún producto seleccionado: limpiar todos que tengan esta bodega
            $sql = "UPDATE dbo.Productos SET BodegaId = NULL WHERE BodegaId = ?";
            $stmt = sqlsrv_query($conn, $sql, [$id]);
            if ($stmt === false) die(print_r(sqlsrv_errors(), true));
            sqlsrv_free_stmt($stmt);
        } else {
            // Actualizar productos desasignados (que tengan esta bodega pero no están en seleccionados)
            $placeholders = implode(',', array_fill(0, count($productos_seleccionados), '?'));
            $sql = "UPDATE dbo.Productos SET BodegaId = NULL WHERE BodegaId = ? AND IdProducto NOT IN ($placeholders)";
            $params = array_merge([$id], $productos_seleccionados);
            $stmt = sqlsrv_query($conn, $sql, $params);
            if ($stmt === false) die(print_r(sqlsrv_errors(), true));
            sqlsrv_free_stmt($stmt);
        }

        // Asignar BodegaId a los productos seleccionados
        foreach ($productos_seleccionados as $idProducto) {
            $sql = "UPDATE dbo.Productos SET BodegaId = ? WHERE IdProducto = ?";
            $stmt = sqlsrv_query($conn, $sql, [$id, $idProducto]);
            if ($stmt === false) die(print_r(sqlsrv_errors(), true));
            sqlsrv_free_stmt($stmt);
        }

        sqlsrv_close($conn);
        header('Location: index.php');
        exit;
    }
}
sqlsrv_close($conn);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8" />
    <title>Editar Bodega</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
</head>
<body>
<div class="container">
    <h1 class="mt-5">Editar Bodega</h1>

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
            <input type="text" id="nombre" name="nombre" class="form-control" 
                   value="<?= htmlspecialchars($_POST['nombre'] ?? $bodega['Nombre']) ?>" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Productos asociados</label>
            <?php foreach ($productos as $prod): ?>
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" id="prod<?= $prod['IdProducto'] ?>" name="productos[]" value="<?= $prod['IdProducto'] ?>"
                        <?= (in_array($prod['IdProducto'], $_POST['productos'] ?? $productos_asignados)) ? 'checked' : '' ?>>
                    <label class="form-check-label" for="prod<?= $prod['IdProducto'] ?>">
                        <?= htmlspecialchars($prod['Nombre']) ?>
                    </label>
                </div>
            <?php endforeach; ?>
        </div>

        <button type="submit" class="btn btn-primary">Guardar Cambios</button>
        <a href="index.php" class="btn btn-secondary">Cancelar</a>
    </form>
</div>
</body>
</html>
