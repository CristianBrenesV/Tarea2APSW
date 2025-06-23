<?php
include '../data.php';

$productos = getProductos();
$errors = [];
$fieldsWithErrors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = trim($_POST['nombre'] ?? '');
    $productos_seleccionados = $_POST['productos'] ?? [];

    if ($nombre === '') {
        $errors[] = "El nombre de la categoría es obligatorio.";
        $fieldsWithErrors[] = 'nombre';
    }

    if (empty($errors)) {
        $conn = sqlsrv_connect($serverName, $connectionOptions);
        if ($conn === false) die(print_r(sqlsrv_errors(), true));

        $sql = "INSERT INTO dbo.Categorias (Nombre) VALUES (?)";
        $stmt = sqlsrv_query($conn, $sql, [$nombre]);
        if ($stmt === false) die(print_r(sqlsrv_errors(), true));
        sqlsrv_free_stmt($stmt);

        $sqlId = "SELECT IDENT_CURRENT('dbo.Categorias') AS NewId";
        $stmtId = sqlsrv_query($conn, $sqlId);
        $row = sqlsrv_fetch_array($stmtId, SQLSRV_FETCH_ASSOC);
        $newCategoriaId = $row['NewId'];
        sqlsrv_free_stmt($stmtId);

        if (!empty($productos_seleccionados)) {
            foreach ($productos_seleccionados as $prodId) {
                $sqlUpdate = "UPDATE dbo.Productos SET CategoriaId = ? WHERE IdProducto = ?";
                $stmtUpdate = sqlsrv_query($conn, $sqlUpdate, [$newCategoriaId, $prodId]);
                if ($stmtUpdate === false) die(print_r(sqlsrv_errors(), true));
                sqlsrv_free_stmt($stmtUpdate);
            }
        }

        sqlsrv_close($conn);
        header('Location: index.php');
        exit;
    }
}

function isErrorField($field, $fieldsWithErrors) {
    return in_array($field, $fieldsWithErrors) ? 'is-invalid' : '';
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8" />
    <title>Crear Categoría</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet" />
        <style>
        body {
            padding-top: 100px;
            background-color: #f8f9fa;
        }
        .form-label span.text-danger {
            font-weight: 700;
        }
        .container_CPB {
            max-width: 650px;
            margin-left: auto;
            margin-right: auto;
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
                    <a class="nav-link" href="/tareados/bodega/index.php">
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

<div class="container_CPB">
    <h1 class="mb-4 text-primary">Crear Categoría</h1>

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
            <label for="productos" class="form-label">Productos (opcional)</label>
            <select id="productos" name="productos[]" multiple size="8" class="form-select" aria-describedby="productosHelp">
                <?php foreach ($productos as $prod): ?>
                    <option value="<?= $prod['IdProducto'] ?>"
                        <?= (isset($_POST['productos']) && in_array($prod['IdProducto'], $_POST['productos'])) ? 'selected' : '' ?>>
                        <?= htmlspecialchars($prod['Nombre']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <div id="productosHelp" class="form-text">Mantén presionada la tecla Ctrl (o Cmd) para seleccionar varios productos.</div>
        </div>

        <button type="submit" class="btn btn-primary">
            <i class="bi bi-plus-circle me-1"></i> Crear Categoría
        </button>
        <a href="index.php" class="btn btn-outline-secondary ms-2">
            <i class="bi bi-x-lg me-1"></i> Cancelar
        </a>
    </form>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
// Focus al primer input con error para mejor UX
document.addEventListener('DOMContentLoaded', () => {
    const firstInvalid = document.querySelector('.is-invalid');
    if (firstInvalid) {
        firstInvalid.focus();
    }
});
</script>
</body>
</html>
