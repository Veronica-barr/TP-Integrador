<?php
// Incluir configuración y controladores
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/controller/FacturaController.php';
require_once __DIR__ . '/controller/ClienteController.php';
require_once __DIR__ . '/controller/ProductoController.php';

// Inicializar controladores
$facturaController = new FacturaController();
$clienteController = new ClienteController();
$productoController = new ProductoController();

// Obtener estadísticas para el dashboard
$totalClientes = count($clienteController->listarClientes());
$totalProductos = count($productoController->listar());
$facturas = $facturaController->listarFacturas();
$totalFacturas = count($facturas);
$totalVentas = 0;

foreach ($facturas as $factura) {
    $totalVentas += $factura->total;
}

// Obtener últimas facturas
$ultimasFacturas = array_slice($facturas, 0, 5);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistema de Facturación - Inicio</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <style>
        .dashboard-card {
            transition: transform 0.3s, box-shadow 0.3s;
            border: none;
            border-radius: 10px;
        }
        .dashboard-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0,0,0,0.1);
        }
        .stat-number {
            font-size: 2.5rem;
            font-weight: bold;
        }
        .bg-primary { background: linear-gradient(45deg, #4e73df, #224abe); }
        .bg-success { background: linear-gradient(45deg, #1cc88a, #13855c); }
        .bg-info { background: linear-gradient(45deg, #36b9cc, #258391); }
        .bg-warning { background: linear-gradient(45deg, #f6c23e, #dda20a); }
        .sidebar {
            min-height: 100vh;
            background-color: #f8f9fa;
            border-right: 1px solid #dee2e6;
        }
        .sidebar .nav-link {
            color: #333;
            padding: 0.8rem 1rem;
            border-radius: 5px;
            margin-bottom: 0.2rem;
        }
        .sidebar .nav-link:hover {
            background-color: #e9ecef;
        }
        .sidebar .nav-link.active {
            font-weight: bold;
            background-color: #e9ecef;
        }
        .main-content {
            background-color: #f8f9fa;
            min-height: 100vh;
            padding: 20px;
        }
        .navbar-brand {
            font-weight: bold;
            font-size: 1.5rem;
        }
    </style>
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container-fluid">
            <a class="navbar-brand" href="index.php">
                <i class="bi bi-receipt"></i> Sistema de Facturación
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="#"><i class="bi bi-person-circle"></i> Admin</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#"><i class="bi bi-box-arrow-right"></i> Cerrar Sesión</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-md-2 sidebar p-0">
                <nav class="nav flex-column p-3">
                    <a class="nav-link active" href="index.php">
                        <i class="bi bi-house-door"></i> Inicio
                    </a>
                    <a class="nav-link" href="views/clientes/list.php">
                        <i class="bi bi-people"></i> Clientes
                    </a>
                    <a class="nav-link" href="views/productos/list.php">
                        <i class="bi bi-box"></i> Productos
                    </a>
                    <a class="nav-link" href="views/facturas/list.php">
                        <i class="bi bi-receipt"></i> Facturas
                    </a>
                    <hr>
                    <div class="px-3">
                        <small class="text-muted">MÓDULOS</small>
                    </div>
                    <a class="nav-link" href="views/facturas/create.php">
                        <i class="bi bi-plus-circle"></i> Nueva Factura
                    </a>
                    <a class="nav-link" href="views/clientes/create.php">
                        <i class="bi bi-person-plus"></i> Nuevo Cliente
                    </a>
                    <a class="nav-link" href="views/productos/create.php">
                        <i class="bi bi-plus-square"></i> Nuevo Producto
                    </a>
                </nav>
            </div>

            <!-- Main Content -->
            <div class="col-md-10 main-content">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h2>Panel Principal</h2>
                    <span class="text-muted"><?php echo date('d/m/Y'); ?></span>
                </div>

                <!-- Estadísticas -->
                <div class="row mb-4">
                    <div class="col-xl-3 col-md-6 mb-4">
                        <div class="card dashboard-card bg-primary text-white shadow h-100 py-2">
                            <div class="card-body">
                                <div class="row no-gutters align-items-center">
                                    <div class="col mr-2">
                                        <div class="text-xs font-weight-bold text-uppercase mb-1">Clientes</div>
                                        <div class="stat-number"><?php echo $totalClientes; ?></div>
                                    </div>
                                    <div class="col-auto">
                                        <i class="bi bi-people fa-2x text-white-300"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-xl-3 col-md-6 mb-4">
                        <div class="card dashboard-card bg-success text-white shadow h-100 py-2">
                            <div class="card-body">
                                <div class="row no-gutters align-items-center">
                                    <div class="col mr-2">
                                        <div class="text-xs font-weight-bold text-uppercase mb-1">Productos</div>
                                        <div class="stat-number"><?php echo $totalProductos; ?></div>
                                    </div>
                                    <div class="col-auto">
                                        <i class="bi bi-box fa-2x text-white-300"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-xl-3 col-md-6 mb-4">
                        <div class="card dashboard-card bg-info text-white shadow h-100 py-2">
                            <div class="card-body">
                                <div class="row no-gutters align-items-center">
                                    <div class="col mr-2">
                                        <div class="text-xs font-weight-bold text-uppercase mb-1">Facturas</div>
                                        <div class="stat-number"><?php echo $totalFacturas; ?></div>
                                    </div>
                                    <div class="col-auto">
                                        <i class="bi bi-receipt fa-2x text-white-300"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-xl-3 col-md-6 mb-4">
                        <div class="card dashboard-card bg-warning text-white shadow h-100 py-2">
                            <div class="card-body">
                                <div class="row no-gutters align-items-center">
                                    <div class="col mr-2">
                                        <div class="text-xs font-weight-bold text-uppercase mb-1">Ventas Totales</div>
                                        <div class="stat-number">$<?php echo number_format($totalVentas, 2); ?></div>
                                    </div>
                                    <div class="col-auto">
                                        <i class="bi bi-currency-dollar fa-2x text-white-300"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Últimas facturas y Acciones rápidas -->
                <div class="row">
                    <!-- Últimas facturas -->
                    <div class="col-lg-8 mb-4">
                        <div class="card shadow h-100">
                            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                                <h6 class="m-0 font-weight-bold text-primary">Últimas Facturas</h6>
                                <a href="views/facturas/listar.php" class="btn btn-sm btn-primary">Ver todas</a>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-hover">
                                        <thead>
                                            <tr>
                                                <th>N° Factura</th>
                                                <th>Cliente</th>
                                                <th>Fecha</th>
                                                <th>Total</th>
                                                <th>Estado</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php if (count($ultimasFacturas) > 0): ?>
                                                <?php foreach ($ultimasFacturas as $factura): ?>
                                                    <tr>
                                                        <td><?php echo $factura->numero_factura; ?></td>
                                                        <td><?php echo $factura->cliente_nombre . ' ' . $factura->cliente_apellido; ?></td>
                                                        <td><?php echo date('d/m/Y', strtotime($factura->fecha)); ?></td>
                                                        <td>$<?php echo number_format($factura->total, 2); ?></td>
                                                        <td>
                                                            <span class="badge bg-<?php 
                                                                switch($factura->estado) {
                                                                    case 'PENDIENTE': echo 'warning'; break;
                                                                    case 'PAGADA': echo 'success'; break;
                                                                    case 'CANCELADA': echo 'danger'; break;
                                                                    default: echo 'secondary';
                                                                }
                                                            ?>">
                                                                <?php echo $factura->estado; ?>
                                                            </span>
                                                        </td>
                                                    </tr>
                                                <?php endforeach; ?>
                                            <?php else: ?>
                                                <tr>
                                                    <td colspan="5" class="text-center">No hay facturas registradas</td>
                                                </tr>
                                            <?php endif; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Acciones rápidas -->
                    <div class="col-lg-4 mb-4">
                        <div class="card shadow h-100">
                            <div class="card-header py-3">
                                <h6 class="m-0 font-weight-bold text-primary">Acciones Rápidas</h6>
                            </div>
                            <div class="card-body">
                                <div class="d-grid gap-2">
                                    <a href="views/facturas/create.php" class="btn btn-primary btn-block mb-3">
                                        <i class="bi bi-plus-circle"></i> Crear Factura
                                    </a>
                                    <a href="views/clientes/create.php" class="btn btn-success btn-block mb-3">
                                        <i class="bi bi-person-plus"></i> Agregar Cliente
                                    </a>
                                    <a href="views/productos/create.php" class="btn btn-info btn-block mb-3">
                                        <i class="bi bi-plus-square"></i> Agregar Producto
                                    </a>
                                    <a href="views/facturas/list.php" class="btn btn-warning btn-block mb-3">
                                        <i class="bi bi-search"></i> Buscar Facturas
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Información del sistema -->
                <div class="row mt-4">
                    <div class="col-12">
                        <div class="card shadow">
                            <div class="card-header py-3">
                                <h6 class="m-0 font-weight-bold text-primary">Información del Sistema</h6>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <p><strong>Versión PHP:</strong> <?php echo PHP_VERSION; ?></p>
                                        <p><strong>Servidor:</strong> <?php echo $_SERVER['SERVER_SOFTWARE']; ?></p>
                                    </div>
                                    <div class="col-md-6">
                                        <p><strong>Base de datos:</strong> MySQL</p>
                                        <p><strong>Usuario:</strong> <?php echo ini_get('mysql.default_user') ?: 'No configurado'; ?></p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>