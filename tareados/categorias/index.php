<?php
include '../data.php';

$categorias = getCategorias();

// Función para obtener productos asignados a una categoría
function getProductosPorCategoria($categoriaId) {
    global $serverName, $connectionOptions;
    $conn = sqlsrv_connect($serverName, $connectionOptions);
    if ($conn === false) die(print_r(sqlsrv_errors(), true));

    $sql = "SELECT Nombre FROM dbo.Productos WHERE CategoriaId = ?";
    $stmt = sqlsrv_query($conn, $sql, [$categoriaId]);
    $productos = [];
    while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
        $productos[] = $row['Nombre'];
    }
    sqlsrv_free_stmt($stmt);
    sqlsrv_close($conn);
    return $productos;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Listado de Categorías</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet" />
    <style>
      body {
        padding-top: 100px; 
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
                    <a class="nav-link active" aria-current="page" href="/tareados/categorias/index.php">
                        <i class="bi bi-tags me-1"></i> Categorías
                    </a>
                </li>                
            </ul>
        </div>
    </div>
</nav>

<div class="container">
    <h1 class="mb-4">Listado de Categorías</h1>

    <div class="mb-3">
        <a href="create.php" class="btn btn-success">
            <i class="bi bi-plus-lg"></i> Crear Categoría
        </a>
    </div>

    <div class="table-responsive">
    <table class="table table-striped table-hover align-middle">
        <thead class="table-primary">
            <tr>
                <th scope="col">Id</th>
                <th scope="col">Nombre</th>
                <th scope="col">Productos asignados</th>
                <th scope="col" style="width: 180px;">Acciones</th>
            </tr>
        </thead>
        <tbody>
        <?php if (!empty($categorias)): ?>
            <?php foreach ($categorias as $cat): 
                $productos = getProductosPorCategoria($cat['IdCategorias']);
            ?>
                <tr>
                    <td><?= htmlspecialchars($cat['IdCategorias']) ?></td>
                    <td><?= htmlspecialchars($cat['Nombre']) ?></td>
                    <td>
                        <?php if (empty($productos)): ?>
                            <em>Sin productos asignados</em>
                        <?php else: ?>
                            <?= htmlspecialchars(implode(', ', $productos)) ?>
                        <?php endif; ?>
                    </td>
                    <td>
                        <a href="edit.php?id=<?= urlencode($cat['IdCategorias']) ?>" class="btn btn-primary btn-sm me-1" aria-label="Editar <?= htmlspecialchars($cat['Nombre']) ?>">
                            <i class="bi bi-pencil"></i> 
                        </a>
                        <a href="delete.php?id=<?= urlencode($cat['IdCategorias']) ?>" 
                           class="btn btn-danger btn-sm" 
                           aria-label="Eliminar <?= htmlspecialchars($cat['Nombre']) ?>">
                            <i class="bi bi-trash"></i> 
                        </a>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr>
                <td colspan="4" class="text-center">No hay categorías registradas.</td>
            </tr>
        <?php endif; ?>
        </tbody>
    </table>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
