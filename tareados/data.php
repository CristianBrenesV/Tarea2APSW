<?php
include 'config.php';


function getCategorias() {
    
    global $serverName, $connectionOptions;

    // Conectar a la base de datos
    $conn = sqlsrv_connect($serverName, $connectionOptions);
    if ($conn === false) {
        die(print_r(sqlsrv_errors(), true));
    }

    $sql = "SELECT IdCategorias, Nombre FROM dbo.Categorias";
    $stmt = sqlsrv_query($conn, $sql);
    if ($stmt === false) die(print_r(sqlsrv_errors(), true));
    
    $categorias = [];
    while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
        $categorias[] = $row;
    }
    sqlsrv_free_stmt($stmt);
    sqlsrv_close($conn);
    return $categorias;
}

function getBodegas() {

    global $serverName, $connectionOptions;

    // Conectar a la base de datos
    $conn = sqlsrv_connect($serverName, $connectionOptions);
    if ($conn === false) {
        die(print_r(sqlsrv_errors(), true));
    }

    $sql = "SELECT IdBodegas, Nombre FROM dbo.Bodegas";
    $stmt = sqlsrv_query($conn, $sql);
    if ($stmt === false) die(print_r(sqlsrv_errors(), true));
    
    $bodegas = [];
    while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
        $bodegas[] = $row;
    }
    sqlsrv_free_stmt($stmt);
    sqlsrv_close($conn);
    return $bodegas;
}

function getProductos() {

    global $serverName, $connectionOptions;

    // Conectar a la base de datos
    $conn = sqlsrv_connect($serverName, $connectionOptions);
    if ($conn === false) {
        die(print_r(sqlsrv_errors(), true));
    }
    
    $sql = "SELECT p.IdProducto, p.CodigoInterno, p.Nombre, p.Costo, p.Existencias,
                   c.Nombre AS Categoria, b.Nombre AS Bodega
            FROM dbo.Productos p
            LEFT JOIN dbo.Categorias c ON p.CategoriaId = c.IdCategorias
            LEFT JOIN dbo.Bodegas b ON p.BodegaId = b.IdBodegas";
    $stmt = sqlsrv_query($conn, $sql);
    if ($stmt === false) die(print_r(sqlsrv_errors(), true));
    
    $productos = [];
    while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
        $productos[] = $row;
    }
    sqlsrv_free_stmt($stmt);
    sqlsrv_close($conn);
    return $productos;
}
?>
