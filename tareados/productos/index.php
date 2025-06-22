<?php
include '../data.php';
$productos = getProductos();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8" />
    <title>Listado de Productos</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark bg-primary">
    <div class="container">
        <a class="navbar-brand" href="/tareados/index.php">Tarea 2 Los Binarios</a>
        <div class="collapse navbar-collapse">
            <ul class="navbar-nav me-auto">
                <li class="nav-item">
                    <a class="nav-link" href="/tareados/productos/index.php">Productos</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="/tareados/bodega/index.php">Bodegas</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="/tareados/categorias/index.php">Categorías</a>
                </li>
            </ul>
        </div>
    </div>
</nav>


<div class="container">
    <h1 class="mt-5">Listado de Productos</h1>

    <div class="mb-3">
        <a href="create.php" class="btn btn-success">Crear Producto</a>
    </div>

    <table class="table table-striped">
        <thead>
            <tr>
                <th>Id</th>
                <th>Código Interno</th>
                <th>Nombre</th>
                <th>Costo</th>
                <th>Existencias</th>
                <th>Categoría</th>
                <th>Bodega</th>
                <th>Acciones</th> 
            </tr>
        </thead>
        <tbody>
            <?php foreach ($productos as $prod): ?>
            <tr>
                <td><?= htmlspecialchars($prod['IdProducto']) ?></td>
                <td><?= htmlspecialchars($prod['CodigoInterno']) ?></td>
                <td><?= htmlspecialchars($prod['Nombre']) ?></td>
                <td><?= htmlspecialchars(number_format($prod['Costo'], 2)) ?></td>
                <td><?= htmlspecialchars($prod['Existencias']) ?></td>
                <td><?= htmlspecialchars($prod['Categoria'] ?? 'Sin categoría asignada') ?></td>
                <td><?= htmlspecialchars($prod['Bodega'] ?? 'Sin bodega asignada') ?></td>
                <td>
                    <a href="edit.php?id=<?= $prod['IdProducto'] ?>" class="btn btn-primary btn-sm">Editar</a>
                    <a href="delete.php?id=<?= $prod['IdProducto'] ?>" class="btn btn-danger btn-sm">Eliminar</a>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>