<?php
require_once '../includes/conexion.php';

$producto = ['producto_id' => 0, 'codigo' => '', 'nombre' => '', 'precio_unitario' => 0, 'stock' => 0];

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $query = "SELECT * FROM productos WHERE producto_id = ?";
    $stmt = $conexion->prepare($query);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $producto = $result->fetch_assoc();
}

require_once '../includes/header.php';
?>

<h2><?= $producto['producto_id'] ? 'Editar' : 'Nuevo' ?> Producto</h2>

<form action="guardar.php" method="post">
    <input type="hidden" name="producto_id" value="<?= $producto['producto_id'] ?>">
    
    <div class="mb-3">
        <label class="form-label">Código</label>
        <input type="text" class="form-control" name="codigo" value="<?= $producto['codigo'] ?>" required>
    </div>
    
    <div class="mb-3">
        <label class="form-label">Nombre</label>
        <input type="text" class="form-control" name="nombre" value="<?= $producto['nombre'] ?>" required>
    </div>
    
    <div class="mb-3">
        <label class="form-label">Precio Unitario</label>
        <input type="number" step="0.01" class="form-control" name="precio_unitario" value="<?= $producto['precio_unitario'] ?>" required>
    </div>
    
    <div class="mb-3">
        <label class="form-label">Stock</label>
        <input type="number" class="form-control" name="stock" value="<?= $producto['stock'] ?>" required>
    </div>
    
    <button type="submit" class="btn btn-primary">Guardar</button>
    <a href="listar.php" class="btn btn-secondary">Cancelar</a>
</form>

<?php require_once '../includes/footer.php'; ?>