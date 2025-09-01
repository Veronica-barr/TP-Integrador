<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Definir rutas de inclusión antes de cualquier salida
$baseDir = __DIR__ . '/../..';
require_once $baseDir . '/controller/ProductoController.php';

// Inicializar controlador
$productoController = new ProductoController();
$productos = $productoController->listar();

// Definir rutas para includes
$headerPath = $baseDir . '/includes/header.php';
$navigationPath = $baseDir . '/includes/navigation.php';
$footerPath = $baseDir . '/includes/footer.php';

// Verificar si los archivos existen, si no, usar rutas alternativas
if (!file_exists($headerPath)) {
    $headerPath = $baseDir . '/.includes/header.php';
}
if (!file_exists($navigationPath)) {
    $navigationPath = $baseDir . '/.includes/navigation.php';
}
if (!file_exists($footerPath)) {
    $footerPath = $baseDir . '/.includes/footer.php';
}

// Incluir header y navegación
if (file_exists($headerPath)) {
    include $headerPath;
} else {
    echo "<!DOCTYPE html><html lang='es'><head><meta charset='UTF-8'><title>Listado de Productos</title>";
    echo "<link href='https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css' rel='stylesheet'>";
    echo "<link rel='stylesheet' href='https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css'>";
    echo "</head><body>";
}

if (file_exists($navigationPath)) {
    include $navigationPath;
}
?>

<div class="container-fluid p-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Listado de Productos</h2>
        <a href="create.php" class="btn btn-primary">
            <i class="bi bi-plus-circle"></i> Nuevo Producto
        </a>
    </div>

    <?php if (isset($_GET['mensaje'])): ?>
        <div class="alert alert-<?php echo $_GET['tipo'] ?? 'success'; ?> alert-dismissible fade show" role="alert">
            <?php echo htmlspecialchars($_GET['mensaje']); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <div class="table-responsive">
        <table class="table table-striped table-hover">
            <thead class="table-dark">
                <tr>
                    <th>ID</th>
                    <th>Código</th>
                    <th>Nombre</th>
                    <th>Descripción</th>
                    <th>Precio Unitario</th>
                    <th>Impuesto %</th>
                    <th>Stock</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php if (count($productos) > 0): ?>
                    <?php foreach ($productos as $producto): ?>
                        <tr>
                            <td><?php echo $producto->producto_id; ?></td>
                            <td><?php echo htmlspecialchars($producto->codigo ?? ''); ?></td>
                            <td><?php echo htmlspecialchars($producto->nombre ?? ''); ?></td>
                            <td><?php echo htmlspecialchars($producto->descripcion ?? ''); ?></td>
                            <td>$<?php echo number_format($producto->precio_unitario, 2); ?></td>
                            <td><?php echo $producto->porcentaje_impuesto; ?>%</td>
                            <td><?php echo $producto->stock; ?></td>
                            <td>
                                <a href="editar.php?id=<?php echo $producto->producto_id; ?>" class="btn btn-sm btn-warning">
                                    <i class="bi bi-pencil"></i> Editar
                                </a>
                                <button class="btn btn-sm btn-danger" onclick="confirmarEliminacion(<?php echo $producto->producto_id; ?>)">
                                    <i class="bi bi-trash"></i> Eliminar
                                </button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="8" class="text-center">No hay productos registrados</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<script>
function confirmarEliminacion(productoId) {
    if (confirm('¿Está seguro de que desea eliminar este producto?')) {
        window.location.href = 'eliminar.php?id=' + productoId;
    }
}
</script>

<?php
// Incluir footer
if (file_exists($footerPath)) {
    include $footerPath;
} else {
    echo "</body></html>";
}
?>