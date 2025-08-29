<?php
require_once __DIR__ . '/../../controller/ProductoController.php';
$productoController = new ProductoController();
$productos = $productoController->listar();
?>

<?php include __DIR__ . '/.includes/header.php'; ?>
<?php include __DIR__ . '/.includes/navigation.php'; ?>

<div class="container-fluid p-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Listado de Productos</h2>
        <a href="crear.php" class="btn btn-primary">
            <i class="bi bi-plus-circle"></i> Nuevo Producto
        </a>
    </div>

    <?php if (isset($_GET['mensaje'])): ?>
        <div class="alert alert-<?php echo $_GET['tipo'] ?? 'success'; ?> alert-dismissible fade show" role="alert">
            <?php echo $_GET['mensaje']; ?>
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
                            <td><?php echo htmlspecialchars($producto->codigo); ?></td>
                            <td><?php echo htmlspecialchars($producto->nombre); ?></td>
                            <td><?php echo htmlspecialchars($producto->descripcion); ?></td>
                            <td>$<?php echo number_format($producto->precio_unitario, 2); ?></td>
                            <td><?php echo $producto->porcentaje_impuesto; ?>%</td>
                            <td><?php echo $producto->stock; ?></td>
                            <td>
                                <a href="editar.php?id=<?php echo $producto->producto_id; ?>" class="btn btn-sm btn-warning">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <button class="btn btn-sm btn-danger" onclick="confirmarEliminacion(<?php echo $producto->producto_id; ?>)">
                                    <i class="bi bi-trash"></i>
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

<?php include __DIR__ . '/.includes/footer.php'; ?>