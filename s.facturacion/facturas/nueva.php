<?php
require_once '../includes/conexion.php';
require_once '../includes/header.php';

// Obtener clientes
$query = "SELECT * FROM clientes WHERE activo = 1 ORDER BY apellido, nombre";
$clientes = $conexion->query($query);

// Obtener productos
$query = "SELECT * FROM productos WHERE activo = 1 ORDER BY nombre";
$productos = $conexion->query($query);
?>

<h2>Nueva Factura</h2>

<form action="guardar.php" method="post">
    <div class="row mb-3">
        <div class="col-md-6">
            <label class="form-label">Cliente</label>
            <select class="form-select" name="cliente_id" required>
                <option value="">Seleccione un cliente</option>
                <?php while($cliente = $clientes->fetch_assoc()): ?>
                <option value="<?= $cliente['cliente_id'] ?>">
                    <?= $cliente['apellido'] ?>, <?= $cliente['nombre'] ?>
                </option>
                <?php endwhile; ?>
            </select>
        </div>
        <div class="col-md-6">
            <label class="form-label">Fecha</label>
            <input type="date" class="form-control" name="fecha" value="<?= date('Y-m-d') ?>" required>
        </div>
    </div>
    
    <h4 class="mt-4">Productos</h4>
    <div id="productos-container">
        <div class="row producto-item mb-3">
            <div class="col-md-5">
                <select class="form-select producto-select" name="producto_id[]" required>
                    <option value="">Seleccione un producto</option>
                    <?php while($producto = $productos->fetch_assoc()): ?>
                    <option value="<?= $producto['producto_id'] ?>" data-precio="<?= $producto['precio_unitario'] ?>">
                        <?= $producto['nombre'] ?> ($<?= number_format($producto['precio_unitario'], 2) ?>)
                    </option>
                    <?php endwhile; ?>
                </select>
            </div>
            <div class="col-md-2">
                <input type="number" class="form-control cantidad" name="cantidad[]" min="1" value="1" required>
            </div>
            <div class="col-md-2">
                <input type="number" step="0.01" class="form-control precio" name="precio[]" readonly>
            </div>
            <div class="col-md-2">
                <input type="text" class="form-control subtotal" readonly>
            </div>
            <div class="col-md-1">
                <button type="button" class="btn btn-danger quitar-producto">X</button>
            </div>
        </div>
    </div>
    
    <button type="button" id="agregar-producto" class="btn btn-secondary mb-3">Agregar Producto</button>
    
    <div class="row mt-4">
        <div class="col-md-4 offset-md-8">
            <div class="input-group mb-3">
                <span class="input-group-text">Total</span>
                <input type="text" class="form-control" id="total" readonly>
            </div>
        </div>
    </div>
    
    <button type="submit" class="btn btn-primary">Guardar Factura</button>
    <a href="listar.php" class="btn btn-secondary">Cancelar</a>
</form>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Agregar producto
    document.getElementById('agregar-producto').addEventListener('click', function() {
        const nuevoProducto = document.querySelector('.producto-item').cloneNode(true);
        nuevoProducto.querySelector('.producto-select').selectedIndex = 0;
        nuevoProducto.querySelector('.cantidad').value = 1;
        nuevoProducto.querySelector('.precio').value = '';
        nuevoProducto.querySelector('.subtotal').value = '';
        document.getElementById('productos-container').appendChild(nuevoProducto);
    });
    
    // Cambio de producto
    document.addEventListener('change', function(e) {
        if (e.target.classList.contains('producto-select')) {
            const precio = e.target.options[e.target.selectedIndex].dataset.precio || 0;
            const precioInput = e.target.closest('.producto-item').querySelector('.precio');
            precioInput.value = precio;
            calcularSubtotal(e.target.closest('.producto-item'));
        }
        
        if (e.target.classList.contains('cantidad')) {
            calcularSubtotal(e.target.closest('.producto-item'));
        }
    });
    
    // Quitar producto
    document.addEventListener('click', function(e) {
        if (e.target.classList.contains('quitar-producto')) {
            if (document.querySelectorAll('.producto-item').length > 1) {
                e.target.closest('.producto-item').remove();
                calcularTotal();
            }
        }
    });
    
    // Calcular subtotal
    function calcularSubtotal(item) {
        const precio = parseFloat(item.querySelector('.precio').value) || 0;
        const cantidad = parseInt(item.querySelector('.cantidad').value) || 0;
        const subtotal = precio * cantidad;
        item.querySelector('.subtotal').value = subtotal.toFixed(2);
        calcularTotal();
    }
    
    // Calcular total
    function calcularTotal() {
        let total = 0;
        document.querySelectorAll('.producto-item').forEach(item => {
            const subtotal = parseFloat(item.querySelector('.subtotal').value) || 0;
            total += subtotal;
        });
        document.getElementById('total').value = total.toFixed(2);
    }
});
</script>

<?php require_once '../includes/footer.php'; ?>