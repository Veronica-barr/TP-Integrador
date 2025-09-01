<?php
require_once __DIR__ . '/../../controller/FacturaController.php';

$facturaController = new FacturaController();
$factura_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($factura_id <= 0) {
    header('Location: list.php?mensaje=Factura no válida&tipo=danger');
    exit;
}

// Obtener la factura para verificar su estado
$factura = $facturaController->obtenerFacturaPorId($factura_id);

if (!$factura) {
    header('Location: list.php?mensaje=Factura no encontrada&tipo=danger');
    exit;
}

// Verificar que la factura esté pendiente para poder eliminarla
if ($factura->estado != 'PENDIENTE') {
    header('Location: list.php?mensaje=Solo se pueden eliminar facturas con estado PENDIENTE&tipo=warning');
    exit;
}

try {
    // Eliminar la factura
    $resultado = $facturaController->eliminarFactura($factura_id);
    
    if ($resultado) {
        header('Location: list.php?mensaje=Factura eliminada exitosamente&tipo=success');
    } else {
        header('Location: list.php?mensaje=Error al eliminar la factura&tipo=danger');
    }
} catch (Exception $e) {
    header('Location: list.php?mensaje=Error: ' . $e->getMessage() . '&tipo=danger');
}
exit;