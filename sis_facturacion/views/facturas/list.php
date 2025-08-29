<?php
require_once __DIR__ . '/../../controller/FacturaController.php';
$facturaController = new FacturaController();

// Filtrar por fechas si se enviaron
$fecha_desde = isset($_GET['fecha_desde']) ? $_GET['fecha_desde'] : null;
$fecha_hasta = isset($_GET['fecha_hasta']) ? $_GET['fecha_hasta'] : null;

$facturas = $facturaController->listarFacturas($fecha_desde, $fecha_hasta);
?>

<?php include __DIR__ . '/.includes/header.php'; ?>
<?php include __DIR__ . '/.includes/navigation.php'; ?>

<div class="container-fluid p-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Listado de Facturas</h2>
        <a href="crear.php" class="btn btn-primary">
            <i class="bi bi-plus-circle"></i> Nueva Factura
        </a>
    </div>

    <?php if (isset($_GET['mensaje'])): ?>
        <div class="alert alert-<?php echo $_GET['tipo'] ?? 'success'; ?> alert-dismissible fade show" role="alert">
            <?php echo $_GET['mensaje']; ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <!-- Filtros -->
    <div class="card mb-4">
        <div class="card-header">
            <h5 class="card-title mb-0">Filtrar Facturas</h5>
        </div>
        <div class="card-body">
            <form method="GET" class="row g-3">
                <div class="col-md-4">
                    <label for="fecha_desde" class="form-label">Fecha Desde</label>
                    <input type="date" class="form-control" id="fecha_desde" name="fecha_desde" value="<?php echo $fecha_desde; ?>">
                </div>
                <div class="col-md-4">
                    <label for="fecha_hasta" class="form-label">Fecha Hasta</label>
                    <input type="date" class="form-control" id="fecha_hasta" name="fecha_hasta" value="<?php echo $fecha_hasta; ?>">
                </div>
                <div class="col-md-4 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary me-2">Filtrar</button>
                    <a href="listar.php" class="btn btn-secondary">Limpiar</a>
                </div>
            </form>
        </div>
    </div>

    <div class="table-responsive">
        <table class="table table-striped table-hover">
            <thead class="table-dark">
                <tr>
                    <th>N° Factura</th>
                    <th>Fecha</th>
                    <th>Cliente</th>
                    <th>CUIL</th>
                    <th>Subtotal</th>
                    <th>Impuesto</th>
                    <th>Total</th>
                    <th>Estado</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php if (count($facturas) > 0): ?>
                    <?php foreach ($facturas as $factura): ?>
                        <tr>
                            <td><?php echo $factura->numero_factura; ?></td>
                            <td><?php echo date('d/m/Y', strtotime($factura->fecha)); ?></td>
                            <td><?php echo htmlspecialchars($factura->cliente_nombre . ' ' . $factura->cliente_apellido); ?></td>
                            <td><?php echo htmlspecialchars($factura->cliente_cuil); ?></td>
                            <td>$<?php echo number_format($factura->subtotal, 2); ?></td>
                            <td>$<?php echo number_format($factura->impuesto, 2); ?></td>
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
                            <td>
                                <a href="detalle.php?id=<?php echo $factura->factura_id; ?>" class="btn btn-sm btn-info">
                                    <i class="bi bi-eye"></i>
                                </a>
                                <?php if ($factura->estado == 'PENDIENTE'): ?>
                                    <a href="editar.php?id=<?php echo $factura->factura_id; ?>" class="btn btn-sm btn-warning">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <button class="btn btn-sm btn-success" onclick="marcarComoPagada(<?php echo $factura->factura_id; ?>)">
                                        <i class="bi bi-check-circle"></i>
                                    </button>
                                    <button class="btn btn-sm btn-danger" onclick="cancelarFactura(<?php echo $factura->factura_id; ?>)">
                                        <i class="bi bi-x-circle"></i>
                                    </button>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="9" class="text-center">No hay facturas registradas</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<script>
function marcarComoPagada(facturaId) {
    if (confirm('¿Está seguro de que desea marcar esta factura como PAGADA?')) {
        window.location.href = 'actualizar_estado.php?id=' + facturaId + '&estado=PAGADA';
    }
}

function cancelarFactura(facturaId) {
    if (confirm('¿Está seguro de que desea CANCELAR esta factura?')) {
        window.location.href = 'actualizar_estado.php?id=' + facturaId + '&estado=CANCELADA';
    }
}
</script>

<?php include __DIR__ . '/.includes/footer.php'; ?>