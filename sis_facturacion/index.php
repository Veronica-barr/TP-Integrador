<?php
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/models/ClienteModel.php';
require_once __DIR__ . '/models/ProductoModel.php';
require_once __DIR__ . '/models/FacturaModel.php';
require_once __DIR__ . '/DAL/ClienteDAL.php';
require_once __DIR__ . '/DAL/ProductoDAL.php';
require_once __DIR__ . '/DAL/FacturaDAL.php';
require_once __DIR__ . '/controller/ClienteController.php';
require_once __DIR__ . '/controller/ProductoController.php';
require_once __DIR__ . '/controller/FacturaController.php';

$clienteCtrl = new ClienteController();
$productoCtrl = new ProductoController();
$facturaCtrl  = new FacturaController();

// Obtiene la acción y módulo solicitados de los parámetros GET
$action = isset($_GET['action']) ? $_GET['action'] : 'listar';
$module = isset($_GET['module']) ? $_GET['module'] : '';

// Sistema de enrutamiento basado en el módulo solicitado
switch ($module) {
    case 'clientes':
        $controller = $clienteCtrl;
        break;
    case 'productos':
        $controller = $productoCtrl;
        break;
    case 'facturas':
        $controller = $facturaCtrl;
        break;
    case '':
    default:
        // Página de inicio
        require_once 'includes/header.php';
        echo '<h1>Bienvenido al Sistema de Facturación</h1>';
        echo '<div class="row">';
        echo '<div class="col-md-4">';
        echo '<div class="card">';
        echo '<div class="card-body text-center">';
        echo '<h5 class="card-title"><i class="bi bi-people fs-1"></i></h5>';
        echo '<a href="index.php?module=clientes&action=listar" class="btn btn-primary">Gestión de Clientes</a>';
        echo '</div></div></div>';
        
        echo '<div class="col-md-4">';
        echo '<div class="card">';
        echo '<div class="card-body text-center">';
        echo '<h5 class="card-title"><i class="bi bi-box-seam fs-1"></i></h5>';
        echo '<a href="index.php?module=productos&action=listar" class="btn btn-primary">Gestión de Productos</a>';
        echo '</div></div></div>';
        
        echo '<div class="col-md-4">';
        echo '<div class="card">';
        echo '<div class="card-body text-center">';
        echo '<h5 class="card-title"><i class="bi bi-receipt fs-1"></i></h5>';
        echo '<a href="index.php?module=facturas&action=listar" class="btn btn-primary">Gestión de Facturas</a>';
        echo '</div></div></div>';
        echo '</div>';
        require_once 'includes/footer.php';
        exit;
}

// Verifica si el método action existe en el controlador
if (!method_exists($controller, $action)) {
    // Acción por defecto si no existe
    $action = 'listar';
}

// Ejecuta la acción solicitada en el controlador correspondiente
$controller->$action();
?>