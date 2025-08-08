<?php
require_once '../includes/conexion.php';
require_once '../includes/header.php';

$query = "SELECT * FROM productos WHERE activo = 1 ORDER BY nombre";
$productos = $conexion->query($query);
?>

<h2>Productos</h2>
<a href="editar.php" class="btn btn-success mb-3">Nuevo Producto</a>

<table class="table table-striped">
    <thead>
        <tr>
            <th>Código</th>
            <th>Nombre</th>
            <th>Precio</th>
            <th>Stock</th>
            <th>Acciones</th>
        </tr>
    </thead>
    <tbody>
        <?php while($producto = $productos->fetch_assoc()): ?>
        <tr>
            <td><?= $producto['codigo'] ?></td>
            <td><?= $producto['nombre'] ?></td>
            <td>$<?= number_format($producto['precio_unitario'], 2) ?></td>
            <td><?= $producto['stock'] ?></td>
            <td>
                <a href="editar.php?id=<?= $producto['producto_id'] ?>" class="btn btn-sm btn-warning">Editar</a>
            </td>
        </tr>
        <?php endwhile; ?>
    </tbody>
</table>

<?php require_once '../includes/footer.php'; ?>