<?php 
$title = "Nuevo Producto";
require_once './includes/header.php'; 
?>

<div class="card">
    <div class="card-header bg-primary text-white">
        <h4 class="mb-0">
            <i class="bi bi-plus-circle me-2"></i>Registrar Nuevo Producto
        </h4>
    </div>
    
    <form action="index.php?module=productos&action=store" method="POST" class="needs-validation" novalidate>
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-6">
                    <label for="codigo" class="form-label required-field">Código</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-upc"></i></span>
                        <input type="text" class="form-control" id="codigo" name="codigo" 
                               placeholder="Código único del producto" required>
                    </div>
                    <div class="invalid-feedback">
                        Por favor ingrese el código del producto.
                    </div>
                </div>
                
                <div class="col-md-6">
                    <label for="nombre" class="form-label required-field">Nombre</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-box-seam"></i></span>
                        <input type="text" class="form-control" id="nombre" name="nombre" required>
                    </div>
                    <div class="invalid-feedback">
                        Por favor ingrese el nombre del producto.
                    </div>
                </div>
                
                <div class="col-md-12">
                    <label for="descripcion" class="form-label">Descripción</label>
                    <textarea class="form-control" id="descripcion" name="descripcion" rows="3"></textarea>
                </div>
                
                <div class="col-md-4">
                    <label for="precio_unitario" class="form-label required-field">Precio Unitario</label>
                    <div class="input-group">
                        <span class="input-group-text">$</span>
                        <input type="number" class="form-control" id="precio_unitario" name="precio_unitario" 
                               step="0.01" min="0" required>
                    </div>
                    <div class="invalid-feedback">
                        Por favor ingrese un precio válido.
                    </div>
                </div>
                
                <div class="col-md-4">
                    <label for="porcentaje_impuesto" class="form-label required-field">% Impuesto</label>
                    <div class="input-group">
                        <input type="number" class="form-control" id="porcentaje_impuesto" name="porcentaje_impuesto" 
                               value="21.00" step="0.01" min="0" max="100" required>
                        <span class="input-group-text">%</span>
                    </div>
                    <div class="invalid-feedback">
                        Por favor ingrese un porcentaje válido (0-100).
                    </div>
                </div>
                
                <div class="col-md-4">
                    <label for="stock" class="form-label required-field">Stock Inicial</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-boxes"></i></span>
                        <input type="number" class="form-control" id="stock" name="stock" min="0" required>
                    </div>
                    <div class="invalid-feedback">
                        Por favor ingrese la cantidad en stock.
                    </div>
                </div>
            </div>
        </div>
        
        <div class="card-footer bg-light">
            <div class="d-flex justify-content-between">
                <a href="index.php?module=productos&action=list" class="btn btn-secondary">
                    <i class="bi bi-x-circle me-1"></i> Cancelar
                </a>
                <button type="submit" class="btn btn-success">
                    <i class="bi bi-save me-1"></i> Guardar Producto
                </button>
            </div>
        </div>
    </form>
</div>

<script>
// Validación del formulario
document.addEventListener('DOMContentLoaded', function() {
    // Ejemplo de validación personalizada para el código
    const codigoInput = document.getElementById('codigo');
    codigoInput.addEventListener('input', function() {
        if (this.value.length > 20) {
            this.setCustomValidity('El código no puede tener más de 20 caracteres');
        } else {
            this.setCustomValidity('');
        }
    });
    
    // Ejemplo de validación para el precio
    const precioInput = document.getElementById('precio_unitario');
    precioInput.addEventListener('change', function() {
        if (this.value <= 0) {
            this.setCustomValidity('El precio debe ser mayor a cero');
        } else {
            this.setCustomValidity('');
        }
    });
});
</script>

<?php require_once './includes/footer.php'; ?>