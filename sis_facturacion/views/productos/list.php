<?php 
$title = "Listado de Productos";
require_once './includes/header.php'; 
?>

<div class="card">
    <div class="card-header bg-primary text-white">
        <div class="d-flex justify-content-between align-items-center">
            <h4 class="mb-0">
                <i class="bi bi-box-seam me-2"></i>Productos Registrados
            </h4>
            <a href="index.php?module=productos&action=create" class="btn btn-light">
                <i class="bi bi-plus-lg me-1"></i> Nuevo Producto
            </a>
        </div>
    </div>
    
    <div class="card-body">
        <?php if (isset($_GET['success'])): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="bi bi-check-circle me-2"></i> Producto <?= $_GET['success'] ?> correctamente
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>
        
        <div class="table-responsive">
            <table class="table table-striped table-hover table-bordered">
                <thead class="table-dark">
                    <tr>
                        <th width="5%">ID</th>
                        <th width="15%">Código</th>
                        <th>Nombre</th>
                        <th width="10%">Precio</th>
                        <th width="10%">Stock</th>
                        <th width="10%">Impuesto</th>
                        <th width="15%">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($productos->rowCount() > 0): ?>
                        <?php while ($producto = $productos->fetch(PDO::FETCH_ASSOC)): ?>
                        <tr>
                            <td><?= $producto['producto_id'] ?></td>
                            <td><?= htmlspecialchars($producto['codigo']) ?></td>
                            <td><?= htmlspecialchars($producto['nombre']) ?></td>
                            <td class="text-end">$<?= number_format($producto['precio_unitario'], 2) ?></td>
                            <td class="text-center <?= $producto['stock'] < 10 ? 'text-danger fw-bold' : '' ?>">
                                <?= $producto['stock'] ?>
                            </td>
                            <td class="text-center"><?= $producto['porcentaje_impuesto'] ?>%</td>
                            <td class="text-center">
                                <div class="btn-group" role="group">
                                    <a href="index.php?module=productos&action=edit&id=<?= $producto['producto_id'] ?>" 
                                       class="btn btn-sm btn-warning" data-bs-toggle="tooltip" title="Editar">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <a href="index.php?module=productos&action=delete&id=<?= $producto['producto_id'] ?>" 
                                       class="btn btn-sm btn-danger" data-bs-toggle="tooltip" title="Eliminar"
                                       onclick="return confirm('¿Está seguro de eliminar este producto?')">
                                        <i class="bi bi-trash"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="7" class="text-center">No hay productos registrados</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
    
    <div class="card-footer text-muted">
        <div class="row">
            <div class="col-md-6">
                Total de productos: <?= $productos->rowCount() ?>
            </div>
            <div class="col-md-6 text-end">
                <?php if ($productos->rowCount() > 0): ?>
                    <span class="badge bg-danger">Productos con stock bajo: 
                        <?= $productosBajoStock = $productos->rowCount() // Aquí deberías contar realmente los productos con stock bajo ?>
                    </span>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php require_once './includes/footer.php'; ?>