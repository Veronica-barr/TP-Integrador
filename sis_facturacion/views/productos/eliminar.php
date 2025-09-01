<?php
// eliminar.php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$baseDir = __DIR__ . '/../..';
require_once $baseDir . '/controller/ProductoController.php';

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $productoController = new ProductoController();
    
    if ($productoController->eliminar($id)) {
        header('Location: list.php?mensaje=Producto eliminado correctamente&tipo=success');
    } else {
        header('Location: list.php?mensaje=Error al eliminar el producto&tipo=danger');
    }
} else {
    header('Location: list.php?mensaje=ID de producto no especificado&tipo=danger');
}
exit();
?>