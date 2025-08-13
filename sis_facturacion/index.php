<?php
require_once 'config/database.php';
require_once 'models/ClienteModel.php';
require_once 'models/ProductoModel.php';
require_once 'models/FacturaModel.php';

$database = new Database();
$db = $database->getConnection();

$clienteModel = new ClienteModel($db);
$productoModel = new ProductoModel($db);
$facturaModel = new FacturaModel($db);

$action = isset($_GET['action']) ? $_GET['action'] : 'home';
$module = isset($_GET['module']) ? $_GET['module'] : '';

switch ($module) {
    case 'clientes':
        require_once 'controller/ClienteController.php';
        $controller = new ClienteController($clienteModel);
        break;
    case 'productos':
        require_once 'controller/ProductoController.php';
        $controller = new ProductoController($productoModel);
        break;
    case 'facturas':
        require_once 'controller/FacturaController.php';
        $controller = new FacturaController($facturaModel, $clienteModel, $productoModel);
        break;
    default:
        // Página principal
        require_once 'includes/header.php';
        echo '<h1>Sistema de Facturación</h1>';
        echo '<p>Bienvenido al sistema de gestión de facturas</p>';
        echo '<ul>';
        echo '<li><a href="index.php?module=clientes">Gestión de Clientes</a></li>';
        echo '<li><a href="index.php?module=productos">Gestión de Productos</a></li>';
        echo '<li><a href="index.php?module=facturas">Gestión de Facturas</a></li>';
        echo '</ul>';
        require_once 'includes/footer.php';
        exit;
}

switch ($action) {
    case 'list':
        $controller->list();
        break;
    case 'create':
        $controller->create();
        break;
    case 'store':
        $controller->store();
        break;
    case 'edit':
        $controller->edit();
        break;
    case 'update':
        $controller->update();
        break;
    case 'delete':
        $controller->delete();
        break;
    case 'show':
        $controller->show();
        break;
    default:
        $controller->list();
        break;
}
?>