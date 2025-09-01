<?php
require_once __DIR__ . '/../../controller/FacturaController.php';
require_once __DIR__ . '/../../controller/ClienteController.php';
require_once __DIR__ . '/../../controller/ProductoController.php';

$facturaController = new FacturaController();
$clienteController = new ClienteController();
$productoController = new ProductoController();

$factura_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($factura_id <= 0) {
    header('Location: list.php?mensaje=Factura no válida&tipo=danger');
    exit;
}

$factura = $facturaController->obtenerFacturaPorId($factura_id);

if (!$factura) {
    header('Location: list.php?mensaje=Factura no encontrada&tipo=danger');
    exit;
}

$headerPath = __DIR__ . '/../includes/header.php';
$navigationPath = __DIR__ . '/../includes/navigation.php';
$footerPath = __DIR__ . '/../includes/footer.php';

if (file_exists($headerPath)) {
    include $headerPath;
} else {
    echo '<!DOCTYPE html><html lang="es"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0"><title>Detalles de Factura</title><link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet"><link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css"></head><body>';
}

if (file_exists($navigationPath)) {
    include $navigationPath;
}
?>

<div class="container-fluid p-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Detalles de Factura #<?php echo $factura->numero_factura; ?></h2>
        <div>
            <a href="list.php" class="btn btn-secondary">
                <i class="bi bi-arrow-left"></i> Volver al Listado
            </a>
            <?php if ($factura->estado == 'PENDIENTE'): ?>
                <a href="editar.php?id=<?php echo $factura->factura_id; ?>" class="btn btn-warning">
                    <i class="bi bi-pencil"></i> Editar
                </a>
            <?php endif; ?>
            <button class="btn btn-primary" onclick="window.print()">
                <i class="bi bi-printer"></i> Imprimir
            </button>
        </div>
    </div>

    <?php if (isset($_GET['mensaje'])): ?>
        <div class="alert alert-<?php echo $_GET['tipo'] ?? 'success'; ?> alert-dismissible fade show" role="alert">
            <?php echo $_GET['mensaje']; ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <div class="row">
        <div class="col-md-6">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">Información de la Factura</h5>
                </div>
                <div class="card-body">
                    <div class="row mb-2">
                        <div class="col-4"><strong>N° Factura:</strong></div>
                        <div class="col-8"><?php echo $factura->numero_factura; ?></div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-4"><strong>Fecha:</strong></div>
                        <div class="col-8"><?php echo date('d/m/Y', strtotime($factura->fecha)); ?></div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-4"><strong>Estado:</strong></div>
                        <div class="col-8">
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
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">Información del Cliente</h5>
                </div>
                <div class="card-body">
                    <div class="row mb-2">
                        <div class="col-4"><strong>Nombre:</strong></div>
                        <div class="col-8"><?php echo htmlspecialchars($factura->cliente_nombre . ' ' . $factura->cliente_apellido); ?></div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-4"><strong>CUIL:</strong></div>
                        <div class="col-8"><?php echo htmlspecialchars($factura->cliente_cuil); ?></div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-4"><strong>Email:</strong></div>
                        <div class="col-8"><?php echo htmlspecialchars($factura->cliente_email ?? 'No disponible'); ?></div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-4"><strong>Teléfono:</strong></div>
                        <div class="col-8"><?php echo htmlspecialchars($factura->cliente_telefono ?? 'No disponible'); ?></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card mb-4">
        <div class="card-header">
            <h5 class="card-title mb-0">Líneas de Factura</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Producto</th>
                            <th>Código</th>
                            <th>Cantidad</th>
                            <th>Precio Unitario</th>
                            <th>% Impuesto</th>
                            <th>Subtotal</th>
                            <th>Impuesto</th>
                            <th>Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($factura->lineas)): ?>
                            <?php foreach ($factura->lineas as $linea): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($linea->producto_nombre); ?></td>
                                    <td><?php echo htmlspecialchars($linea->producto_codigo); ?></td>
                                    <td><?php echo $linea->cantidad; ?></td>
                                    <td>$<?php echo number_format($linea->precio_unitario, 2); ?></td>
                                    <td><?php echo $linea->porcentaje_impuesto; ?>%</td>
                                    <td>$<?php echo number_format($linea->subtotal, 2); ?></td>
                                    <td>$<?php echo number_format($linea->monto_impuesto, 2); ?></td>
                                    <td>$<?php echo number_format($linea->total_linea, 2); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="8" class="text-center">No hay líneas en esta factura</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6 offset-md-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Totales</h5>
                </div>
                <div class="card-body">
                    <div class="row mb-2">
                        <div class="col-6"><strong>Subtotal:</strong></div>
                        <div class="col-6 text-end">$<?php echo number_format($factura->subtotal, 2); ?></div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-6"><strong>Impuestos:</strong></div>
                        <div class="col-6 text-end">$<?php echo number_format($factura->impuesto, 2); ?></div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-6"><strong>Total:</strong></div>
                        <div class="col-6 text-end"><strong>$<?php echo number_format($factura->total, 2); ?></strong></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php if ($factura->estado == 'PENDIENTE'): ?>
    <div class="mt-4">
        <div class="d-flex gap-2 justify-content-end">
            <form action="actualizar_estado.php" method="POST" class="d-inline">
                <input type="hidden" name="id" value="<?php echo $factura->factura_id; ?>">
                <input type="hidden" name="estado" value="PAGADA">
                <button type="submit" class="btn btn-success" onclick="return confirm('¿Marcar como PAGADA?')">
                    <i class="bi bi-check-circle"></i> Marcar como Pagada
                </button>
            </form>
            <form action="actualizar_estado.php" method="POST" class="d-inline">
                <input type="hidden" name="id" value="<?php echo $factura->factura_id; ?>">
                <input type="hidden" name="estado" value="CANCELADA">
                <button type="submit" class="btn btn-danger" onclick="return confirm('¿Cancelar factura?')">
                    <i class="bi bi-x-circle"></i> Cancelar Factura
                </button>
            </form>

        <form action="eliminar.php" method="POST" class="d-inline">
            <input type="hidden" name="id" value="<?php echo $factura->factura_id; ?>">
            <button type="submit" class="btn btn-outline-danger" onclick="return confirm('¿Está seguro de que desea ELIMINAR permanentemente esta factura? Esta acción no se puede deshacer.')">
                <i class="bi bi-trash"></i> Eliminar
            </button>
        </div>
    </div>
    <?php endif; ?>
</div>

<style>
@media print {
    .btn, .d-print-none {
        display: none !important;
    }
    .card {
        border: 1px solid #000 !important;
    }
    body {
        font-size: 12px;
    }
}
</style>

<?php
if (file_exists($footerPath)) {
    include $footerPath;
} else {
    include __DIR__ . '/../../includes/footer.php';
}
?>