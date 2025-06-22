<?php
include '../data.php';
$bodegas = getBodegas();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8" />
    <title>Listado de Bodegas</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
</head>
<body>
<!-- tareados/header.php -->
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
    <h1 class="mt-5">Listado de Bodegas</h1>

    <div class="mb-3">
        <a href="create.php" class="btn btn-success">Crear Bodega</a>
    </div>

    <table class="table table-striped">
        <thead>
            <tr>
                <th>Id</th>
                <th>Nombre</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
        <?php foreach ($bodegas as $bodega): ?>
            <tr>
                <td><?= htmlspecialchars($bodega['IdBodegas']) ?></td>
                <td><?= htmlspecialchars($bodega['Nombre']) ?></td>
                <td>
                    <a href="ver_productos.php?id=<?= $bodega['IdBodegas'] ?>" class="btn btn-info btn-sm">Ver Productos</a>
                    <a href="edit.php?id=<?= $bodega['IdBodegas'] ?>" class="btn btn-primary btn-sm">Editar</a>
                    <a href="delete.php?id=<?= $bodega['IdBodegas'] ?>" class="btn btn-danger btn-sm">Eliminar</a>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>
</body>
</html>
