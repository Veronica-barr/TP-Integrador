<?php
session_start(); // Inicia o reanuda una sesión para mantener estado entre páginas
//carga los archivos necesarios
require_once 'config/database.php';
require_once 'models/ClienteModel.php';
require_once 'models/ProductoModel.php';
require_once 'models/FacturaModel.php';

//crea la conexión a la base de datos
$database = new Database();
$db = $database->getConnection();

//instancia los modelos principales
$clienteModel = new ClienteModel($db);
$productoModel = new ProductoModel($db);
$facturaModel = new FacturaModel($db);

//obtiene la acción y módulo solicitados de los parametros GET
//por defecto, si no se especifica, se muestra la página de inicio
$action = isset($_GET['action']) ? $_GET['action'] : 'home';
$module = isset($_GET['module']) ? $_GET['module'] : '';

//sistema de enrutamiento basado en el modulo solicitado
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
//FcaturaController necesita mas modelos porqeu trabaja con clientes y productos
        $controller = new FacturaController($facturaModel, $clienteModel, $productoModel);
        break;
    default:
//di no encontrar el modulo, redirige a la página de inicio
        require_once 'includes/header.php';
        echo '<h1>Sistema de Facturación</h1>';
        echo '<p>Bienvenido al sistema de gestión de facturas</p>';
        echo '<ul>';
        echo '<li><a href="index.php?module=clientes">Gestión de Clientes</a></li>';
        echo '<li><a href="index.php?module=productos">Gestión de Productos</a></li>';
        echo '<li><a href="index.php?module=facturas">Gestión de Facturas</a></li>';
        echo '</ul>';
        require_once 'includes/footer.php';
        exit;//termina la ejecución aquí para el caso default
}

//verifica si el metodo action existe en el controlador
if (!method_exists($controller, $action)) {
    $action = 'list';//acion por defecto si no existe
}

//ejecuta la accion solicitada en el controlador correspondiente
$controller->$action();
?>