<?php
require_once __DIR__ . '/../../controller/ClienteController.php';

if (!isset($_GET['id'])) {
    header('Location: listadoClientes.php');
    exit();
}

$id = intval($_GET['id']);
$controller = new ClienteController();
$controller->eliminarCliente($id);

header('Location: listadoClientes.php?mensaje=Cliente eliminado correctamente');
exit();
?>
