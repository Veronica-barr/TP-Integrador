<?php
require_once '../includes/conexion.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = $_POST['producto_id'] ?: 0;
    $codigo = $_POST['codigo'];
    $nombre = $_POST['nombre'];
    $precio = $_POST['precio_unitario'];
    $stock = $_POST['stock'];
    
    if ($id > 0) {
        $query = "UPDATE productos SET codigo = ?, nombre = ?, precio_unitario = ?, stock = ? WHERE producto_id = ?";
        $stmt = $conexion->prepare($query);
        $stmt->bind_param("ssdii", $codigo, $nombre, $precio, $stock, $id);
    } else {
        $query = "INSERT INTO productos (codigo, nombre, precio_unitario, stock) VALUES (?, ?, ?, ?)";
        $stmt = $conexion->prepare($query);
        $stmt->bind_param("ssdi", $codigo, $nombre, $precio, $stock);
    }
    
    $stmt->execute();
}

header("Location: listar.php");
exit;