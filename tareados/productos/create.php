<?php
include '../data.php';

$productos = getProductos();
$categorias = getCategorias();
$bodegas = getBodegas();

$errors = [];
$fieldsWithErrors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $codigo = trim($_POST['codigo_interno'] ?? '');
    $nombre = trim($_POST['nombre'] ?? '');
    $costo = $_POST['costo'] ?? '';
    $existencias = $_POST['existencias'] ?? '';
    $categoria_id = $_POST['categoria_id'] ?? null;
    $bodega_id = $_POST['bodega_id'] ?? null;

    if ($categoria_id === '') $categoria_id = null;
    if ($bodega_id === '') $bodega_id = null;

    if ($codigo === '') {
        $errors[] = "El código interno es obligatorio.";
        $fieldsWithErrors[] = 'codigo_interno';
    }
    if ($nombre === '') {
        $errors[] = "El nombre es obligatorio.";
        $fieldsWithErrors[] = 'nombre';
    }
    if ($costo !== '') {
        if (!is_numeric($costo) || $costo < 0) {
            $errors[] = "El costo debe ser un número positivo.";
            $fieldsWithErrors[] = 'costo';
        }
    }
    if ($existencias !== '') {
        if (!filter_var($existencias, FILTER_VALIDATE_INT, ["options" => ["min_range" => 0]])) {
            $errors[] = "Existencias debe ser un entero positivo.";
            $fieldsWithErrors[] = 'existencias';
        }
    }

    if (empty($errors)) {
        $conn = sqlsrv_connect($serverName, $connectionOptions);
        if ($conn === false) {
            die(print_r(sqlsrv_errors(), true));
        }

        $sql = "INSERT INTO dbo.Productos (CodigoInterno, Nombre, Costo, Existencias, CategoriaId, BodegaId) 
                VALUES (?, ?, ?, ?, ?, ?)";
        $params = [
            $codigo, 
            $nombre, 
            $costo !== '' ? $costo : null, 
            $existencias !== '' ? $existencias : null, 
            $categoria_id, 
            $bodega_id
        ];
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

function isErrorField($field, $fieldsWithErrors) {
    return in_array($field, $fieldsWithErrors) ? 'is-invalid' : '';
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Crear Producto</title>
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
    <h1 class="mb-4 text-primary">Crear Producto</h1>

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
            <label for="codigo_interno" class="form-label">Código Interno <span class="text-danger">*</span></label>
            <input type="text" 
                   class="form-control <?= isErrorField('codigo_interno', $fieldsWithErrors) ?>" 
                   id="codigo_interno" name="codigo_interno" required
                   aria-describedby="codigoHelp"
                   value="<?= htmlspecialchars($_POST['codigo_interno'] ?? '') ?>">
            <div id="codigoHelp" class="form-text">Código único para identificar el producto.</div>
            <div class="invalid-feedback">Por favor ingresa un código interno válido.</div>
        </div>

        <div class="mb-3">
            <label for="nombre" class="form-label">Nombre <span class="text-danger">*</span></label>
            <input type="text" 
                   class="form-control <?= isErrorField('nombre', $fieldsWithErrors) ?>" 
                   id="nombre" name="nombre" required
                   value="<?= htmlspecialchars($_POST['nombre'] ?? '') ?>">
            <div class="invalid-feedback">Por favor ingresa un nombre.</div>
        </div>

        <div class="mb-3">
            <label for="costo" class="form-label">Costo</label>
            <input type="number" step="0.01" min="0" 
                   class="form-control <?= isErrorField('costo', $fieldsWithErrors) ?>" 
                   id="costo" name="costo"
                   value="<?= htmlspecialchars($_POST['costo'] ?? '') ?>">
            <div class="invalid-feedback">El costo debe ser un número positivo.</div>
        </div>

        <div class="mb-3">
            <label for="existencias" class="form-label">Existencias</label>
            <input type="number" min="0" step="1" 
                   class="form-control <?= isErrorField('existencias', $fieldsWithErrors) ?>" 
                   id="existencias" name="existencias"
                   value="<?= htmlspecialchars($_POST['existencias'] ?? '') ?>">
            <div class="invalid-feedback">Las existencias deben ser un número entero positivo.</div>
        </div>

        <div class="mb-3">
            <label for="categoria_id" class="form-label">Categoría (opcional)</label>
            <select class="form-select" id="categoria_id" name="categoria_id" aria-label="Categoría del producto">
                <option value="">-- Sin categoría asignada --</option>
                <?php foreach ($categorias as $cat): ?>
                    <option value="<?= $cat['IdCategorias'] ?>" <?= (isset($_POST['categoria_id']) && $_POST['categoria_id'] == $cat['IdCategorias']) ? 'selected' : '' ?>>
                        <?= htmlspecialchars($cat['Nombre']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="mb-4">
            <label for="bodega_id" class="form-label">Bodega (opcional)</label>
            <select id="bodega_id" name="bodega_id" class="form-select" aria-label="Bodega del producto">
                <option value="">-- Sin bodega asignada --</option>
                <?php foreach ($bodegas as $bod): ?>
                    <option value="<?= $bod['IdBodegas'] ?>" <?= (isset($_POST['bodega_id']) && $_POST['bodega_id'] == $bod['IdBodegas']) ? 'selected' : '' ?>>
                        <?= htmlspecialchars($bod['Nombre']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <button type="submit" class="btn btn-primary me-2">
            <i class="bi bi-plus-lg me-1"></i> Crear Producto
        </button>
        <a href="index.php" class="btn btn-outline-secondary">
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
