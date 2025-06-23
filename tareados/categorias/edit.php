<?php
include '../data.php';

$id = $_GET['id'] ?? null;
if (!$id) {
    die('ID inválido');
}

$conn = sqlsrv_connect($serverName, $connectionOptions);
if ($conn === false) die(print_r(sqlsrv_errors(), true));

$sql = "SELECT * FROM dbo.Categorias WHERE IdCategorias = ?";
$stmt = sqlsrv_query($conn, $sql, [$id]);
$categoria = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC);
if (!$categoria) die('Categoría no encontrada');
sqlsrv_free_stmt($stmt);

$productos = getProductos();

$sqlProds = "SELECT IdProducto FROM dbo.Productos WHERE CategoriaId = ?";
$stmtProds = sqlsrv_query($conn, $sqlProds, [$id]);
$productosAsignados = [];
while ($row = sqlsrv_fetch_array($stmtProds, SQLSRV_FETCH_ASSOC)) {
    $productosAsignados[] = $row['IdProducto'];
}
sqlsrv_free_stmt($stmtProds);

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
        $sqlUpdate = "UPDATE dbo.Categorias SET Nombre = ? WHERE IdCategorias = ?";
        $stmtUpdate = sqlsrv_query($conn, $sqlUpdate, [$nombre, $id]);
        if ($stmtUpdate === false) die(print_r(sqlsrv_errors(), true));
        sqlsrv_free_stmt($stmtUpdate);

        $sqlClear = "UPDATE dbo.Productos SET CategoriaId = NULL WHERE CategoriaId = ?";
        $stmtClear = sqlsrv_query($conn, $sqlClear, [$id]);
        if ($stmtClear === false) die(print_r(sqlsrv_errors(), true));
        sqlsrv_free_stmt($stmtClear);

        if (!empty($productos_seleccionados)) {
            foreach ($productos_seleccionados as $prodId) {
                $sqlAsignar = "UPDATE dbo.Productos SET CategoriaId = ? WHERE IdProducto = ?";
                $stmtAsignar = sqlsrv_query($conn, $sqlAsignar, [$id, $prodId]);
                if ($stmtAsignar === false) die(print_r(sqlsrv_errors(), true));
                sqlsrv_free_stmt($stmtAsignar);
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
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Editar Categoría</title>
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
                    <a class="nav-link active" aria-current="page" href="/tareados/productos/index.php">
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
    <h1 class="mb-4 text-primary">Editar Categoría</h1>

    <?php if (!empty($errors)): ?>
        <div class="alert alert-danger" role="alert" aria-live="assertive">
            <ul class="mb-0">
                <?php foreach ($errors as $error): ?>
                    <li><?= htmlspecialchars($error) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <form method="post" novalidate>
        <div class="mb-3">
            <label for="nombre" class="form-label">Nombre <span class="text-danger">*</span></label>
            <input type="text" id="nombre" name="nombre" required
                   class="form-control <?= isErrorField('nombre', $fieldsWithErrors) ?>"
                   value="<?= htmlspecialchars($_POST['nombre'] ?? $categoria['Nombre']) ?>">
            <div class="invalid-feedback">El nombre es obligatorio.</div>
        </div>

        <div class="mb-3">
            <label for="productos" class="form-label">Productos (opcional)</label>
            <select id="productos" name="productos[]" multiple size="8" class="form-select" aria-describedby="productosHelp">
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
            <div id="productosHelp" class="form-text">Mantén presionada la tecla Ctrl para seleccionar varios productos.</div>
        </div>

        <button type="submit" class="btn btn-primary">
            <i class="bi bi-pencil-square me-1"></i> Guardar Cambios
        </button>
        <a href="index.php" class="btn btn-outline-secondary ms-2">
            <i class="bi bi-x-lg me-1"></i> Cancelar
        </a>
    </form>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', () => {
    const firstInvalid = document.querySelector('.is-invalid');
    if (firstInvalid) firstInvalid.focus();
});
</script>
</body>
</html>
