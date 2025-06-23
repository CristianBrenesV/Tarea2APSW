<?php
include '../data.php';

$id = $_GET['id'] ?? $_POST['id'] ?? null;
if (!$id) {
    die('ID inválido');
}

// Conexión
$conn = sqlsrv_connect($serverName, $connectionOptions);
if ($conn === false) {
    die(print_r(sqlsrv_errors(), true));
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Eliminar el producto
    $sql = "DELETE FROM dbo.Productos WHERE IdProducto = ?";
    $stmt = sqlsrv_query($conn, $sql, [$id]);
    if ($stmt === false) {
        die(print_r(sqlsrv_errors(), true));
    }
    sqlsrv_free_stmt($stmt);
    sqlsrv_close($conn);
    header('Location: index.php');
    exit;
}

// Obtener el producto para mostrar en pantalla
$sql = "SELECT CodigoInterno, Nombre FROM dbo.Productos WHERE IdProducto = ?";
$stmt = sqlsrv_query($conn, $sql, [$id]);
$producto = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC);
if (!$producto) {
    die('Producto no encontrado');
}
sqlsrv_free_stmt($stmt);
sqlsrv_close($conn);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Eliminar Producto</title>
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
        max-width: 600px;
        margin: 60px auto 40px;
        background: #fff;
        padding: 35px 40px;
        border-radius: 12px;
        box-shadow: 0 12px 25px rgba(0,0,0,0.12);
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        text-align: center;
        }

        .container_CPB h1 {
        font-size: 1.9rem;
        font-weight: 700;
        color: #d6336c; 
        margin-bottom: 30px;
        text-shadow: 0 1px 2px rgba(0,0,0,0.1);
        display: flex;
        align-items: center;
        justify-content: center;
        }

        .container_CPB .alert-warning {
        background-color: #fff3cd;
        color: #856404;
        border-radius: 10px;
        padding: 20px 25px;
        font-size: 1.15rem;
        line-height: 1.5;
        box-shadow: inset 0 0 10px rgba(0,0,0,0.03);
        margin-bottom: 35px;
        text-align: left;
        display: flex;
        align-items: center;
        }

        .container_CPB .alert-warning i {
        flex-shrink: 0;
        font-size: 2.2rem;
        margin-right: 20px;
        color: #856404;
        }

        .container_CPB .alert-warning p {
        margin: 0.15rem 0;
        font-weight: 600;
        }

        .container_CPB .alert-warning strong {
        width: 90px;
        display: inline-block;
        color: #6c757d;
        }

        .container_CPB form {
        display: flex;
        justify-content: center;
        gap: 20px;
        }

        .container_CPB form button,
        .container_CPB form a.btn {
        min-width: 140px;
        padding: 12px 0;
        font-size: 1.1rem;
        font-weight: 600;
        border-radius: 8px;
        transition: background-color 0.3s ease;
        display: flex;
        align-items: center;
        justify-content: center;
        }

        .container_CPB form button.btn-danger:hover {
        background-color: #b02a37;
        }

        .container_CPB form a.btn-secondary:hover {
        background-color: #6c757d;
        color: #fff;
        }

        .container_CPB form i {
        margin-right: 8px;
        font-size: 1.2rem;
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
  <h1 class="mb-4 text-danger">
    <i class="bi bi-exclamation-triangle-fill me-2"></i>
    ¿Estás seguro de eliminar este producto?
  </h1>

  <div class="alert alert-warning d-flex align-items-center" role="alert">
    <i class="bi bi-info-circle-fill fs-3 me-3"></i>
    <div>
      <p><strong>Código:</strong> <?= htmlspecialchars($producto['CodigoInterno']) ?></p>
      <p><strong>Nombre:</strong> <?= htmlspecialchars($producto['Nombre']) ?></p>
    </div>
  </div>

  <form method="post" class="d-flex justify-content-center gap-3">
    <input type="hidden" name="id" value="<?= htmlspecialchars($id) ?>">
    <button type="submit" class="btn btn-danger btn-lg px-4">
      <i class="bi bi-trash3-fill me-2"></i> Sí, eliminar
    </button>
    <a href="index.php" class="btn btn-secondary btn-lg px-4">
      <i class="bi bi-x-lg me-2"></i> Cancelar
    </a>
  </form>
</div>

</body>
</html>
