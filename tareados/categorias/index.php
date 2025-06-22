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
    <title>Listado de Categorías</title>
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
    <h1 class="mt-5">Listado de Categorías</h1>

    <div class="mb-3">
        <a href="create.php" class="btn btn-success">Crear Categoría</a>
    </div>

    <table class="table table-striped">
        <thead>
            <tr>
                <th>Id</th>
                <th>Nombre</th>
                <th>Productos asignados</th> <!-- Nueva columna -->
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
        <?php foreach ($categorias as $cat): 
            $productos = getProductosPorCategoria($cat['IdCategorias']);
        ?>
            <tr>
                <td><?= htmlspecialchars($cat['IdCategorias']) ?></td>
                <td><?= htmlspecialchars($cat['Nombre']) ?></td>
                <td>
                    <?php
                    if (count($productos) === 0) {
                        echo '<em>Sin productos asignados</em>';
                    } else {
                        echo htmlspecialchars(implode(', ', $productos));
                    }
                    ?>
                </td>
                <td>
                    <a href="edit.php?id=<?= $cat['IdCategorias'] ?>" class="btn btn-primary btn-sm">Editar</a>
                    <a href="delete.php?id=<?= $cat['IdCategorias'] ?>" class="btn btn-danger btn-sm">Eliminar</a>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>
</body>
</html>
