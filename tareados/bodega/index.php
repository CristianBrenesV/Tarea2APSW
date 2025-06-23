<?php
include '../data.php';
$bodegas = getBodegas();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Listado de Bodegas</title>
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
                    <a class="nav-link active" aria-current="page" href="/tareados/bodega/index.php">
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

<div class="container">
    <h1 class="mb-4">Listado de Bodegas</h1>

    <div class="mb-3">
        <a href="create.php" class="btn btn-success">
            <i class="bi bi-plus-lg"></i> Crear Bodega
        </a>
    </div>

    <div class="table-responsive">
      <table class="table table-striped table-hover align-middle">
          <thead class="table-primary">
              <tr>
                  <th scope="col">Id</th>
                  <th scope="col">Nombre</th>
                  <th scope="col" style="width: 240px;">Acciones</th>
              </tr>
          </thead>
          <tbody>
          <?php if (!empty($bodegas)): ?>
            <?php foreach ($bodegas as $bodega): ?>
                <tr>
                    <td><?= htmlspecialchars($bodega['IdBodegas']) ?></td>
                    <td><?= htmlspecialchars($bodega['Nombre']) ?></td>
                    <td>
                        <a href="ver_productos.php?id=<?= urlencode($bodega['IdBodegas']) ?>" class="btn btn-info btn-sm me-1" aria-label="Ver productos de <?= htmlspecialchars($bodega['Nombre']) ?>">
                            <i class="bi bi-box-seam"></i> Ver Productos
                        </a>
                        <a href="edit.php?id=<?= urlencode($bodega['IdBodegas']) ?>" class="btn btn-primary btn-sm me-1" aria-label="Editar <?= htmlspecialchars($bodega['Nombre']) ?>">
                            <i class="bi bi-pencil"></i> 
                        </a>
                        <a href="delete.php?id=<?= urlencode($bodega['IdBodegas']) ?>" 
                           class="btn btn-danger btn-sm"
                           aria-label="Eliminar <?= htmlspecialchars($bodega['Nombre']) ?>">
                            <i class="bi bi-trash"></i> 
                        </a>
                    </td>
                </tr>
            <?php endforeach; ?>
          <?php else: ?>
            <tr>
                <td colspan="3" class="text-center">No hay bodegas registradas.</td>
            </tr>
          <?php endif; ?>
          </tbody>
      </table>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
