<?php 
$title = "Nueva Factura";
require_once './includes/header.php'; 
?>

<div class="card">
    <div class="card-header bg-primary text-white">
        <h4 class="mb-0">
            <i class="bi bi-receipt me-2"></i>Generar Nueva Factura
        </h4>
    </div>
    
    <?php if (isset($_SESSION['error'])): ?>
        <div class="alert alert-danger alert-dismissible fade show m-3">
            <i class="bi bi-exclamation-triangle me-2"></i>
            <?= $_SESSION['error'] ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        <?php unset($_SESSION['error']); ?>
    <?php endif; ?>
    
    <form action="index.php?module=facturas&action=store" method="POST" class="needs-validation" novalidate>
        <div class="card-body">
            <!-- Sección Datos de la Factura -->
            <div class="mb-4 border-bottom pb-3">
                <h5 class="text-primary">
                    <i class="bi bi-card-heading me-2"></i>Datos de la Factura
                </h5>
                
                <div class="row g-3">
                    <div class="col-md-4">
                        <label for="numero_factura" class="form-label required-field">Número de Factura</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-hash"></i></span>
                            <input type="text" class="form-control" id="numero_factura" name="numero_factura" required>
                        </div>
                        <div class="invalid-feedback">
                            Por favor ingrese el número de factura.
                        </div>
                    </div>
                    
                    <div class="col-md-4">
                        <label for="fecha" class="form-label required-field">Fecha</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-calendar"></i></span>
                            <input type="date" class="form-control" id="fecha" name="fecha" required>
                        </div>
                        <div class="invalid-feedback">
                            Por favor seleccione la fecha.
                        </div>
                    </div>
                    
                    <div class="col-md-4">
                        <label for="cliente_id" class="form-label required-field">Cliente</label>
                        <select class="form-select" id="cliente_id" name="cliente_id" required>
                            <option value="">Seleccionar cliente...</option>
                            <?php while ($cliente = $clientes->fetch(PDO::FETCH_ASSOC)): ?>
                                <option value="<?= $cliente['cliente_id'] ?>">
                                    <?= htmlspecialchars($cliente['nombre']) ?> <?= htmlspecialchars($cliente['apellido']) ?> - <?= $cliente['cuil'] ?>
                                </option>
                            <?php endwhile; ?>
                        </select>
                        <div class="invalid-feedback">
                            Por favor seleccione un cliente.
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Sección Productos -->
            <div class="mb-4">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="text-primary">
                        <i class="bi bi-cart me-2"></i>Productos
                    </h5>
                    <button type="button" id="add-producto" class="btn btn-sm btn-primary">
                        <i class="bi bi-plus-circle me-1"></i>Agregar Producto
                    </button>
                </div>
                
                <div id="productos-container">
                    <!-- Fila inicial de producto -->
                    <div class="row g-3 producto-row mb-3">
                        <div class="col-md-5">
                            <label class="form-label required-field">Producto</label>
                            <select class="form-select producto-select" name="productos[0][producto_id]" required>
                                <option value="">Seleccionar producto...</option>
                                <?php 
                                // Resetear el puntero del resultado para poder iterar nuevamente
                                $productos->execute();
                                while ($producto = $productos->fetch(PDO::FETCH_ASSOC)): ?>
                                    <option value="<?= $producto['producto_id'] ?>" 
                                            data-precio="<?= $producto['precio_unitario'] ?>"
                                            data-impuesto="<?= $producto['porcentaje_impuesto'] ?>"
                                            data-stock="<?= $producto['stock'] ?>">
                                        <?= htmlspecialchars($producto['nombre']) ?> - $<?= number_format($producto['precio_unitario'], 2) ?> (Stock: <?= $producto['stock'] ?>)
                                    </option>
                                <?php endwhile; ?>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label required-field">Cantidad</label>
                            <input type="number" class="form-control cantidad" name="productos[0][cantidad]" min="1" value="1" required>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Precio Unitario</label>
                            <input type="number" class="form-control precio" name="productos[0][precio_unitario]" step="0.01" min="0" readonly>
                            <input type="hidden" name="productos[0][precio_unitario]">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">% Impuesto</label>
                            <input type="number" class="form-control impuesto" name="productos[0][porcentaje_impuesto]" step="0.01" min="0" readonly>
                            <input type="hidden" name="productos[0][porcentaje_impuesto]">
                        </div>
                        <div class="col-md-1 d-flex align-items-end">
                            <button type="button" class="btn btn-outline-danger remove-producto w-100" style="display: none;">
                                <i class="bi bi-trash"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Resumen de la Factura -->
            <div class="card border-primary mb-3">
                <div class="card-header bg-light">
                    <h5 class="mb-0 text-primary">
                        <i class="bi bi-calculator me-2"></i>Resumen de la Factura
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4 offset-md-8">
                            <table class="table table-bordered">
                                <tr>
                                    <th>Subtotal:</th>
                                    <td class="text-end" id="subtotal">$0.00</td>
                                </tr>
                                <tr>
                                    <th>Impuestos:</th>
                                    <td class="text-end" id="impuesto">$0.00</td>
                                </tr>
                                <tr class="table-active">
                                    <th>Total:</th>
                                    <td class="text-end fw-bold" id="total">$0.00</td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="card-footer bg-light">
            <div class="d-flex justify-content-between">
                <a href="index.php?module=facturas&action=list" class="btn btn-secondary">
                    <i class="bi bi-x-circle me-1"></i> Cancelar
                </a>
                <button type="submit" class="btn btn-success">
                    <i class="bi bi-save me-1"></i> Generar Factura
                </button>
            </div>
        </div>
        
        <!-- Campos ocultos para los totales -->
        <input type="hidden" name="subtotal" id="input-subtotal">
        <input type="hidden" name="impuesto" id="input-impuesto">
        <input type="hidden" name="total" id="input-total">
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Variables para el cálculo
    let productoCounter = 1;
    let subtotal = 0;
    let impuesto = 0;
    let total = 0;
    
    // Agregar nuevo producto
    document.getElementById('add-producto').addEventListener('click', function() {
        const container = document.getElementById('productos-container');
        const newRow = document.querySelector('.producto-row').cloneNode(true);
        
        // Actualizar los nombres de los campos
        Array.from(newRow.querySelectorAll('input, select')).forEach(input => {
            const name = input.name.replace(/\[\d+\]/, `[${productoCounter}]`);
            input.name = name;
            if (input.type !== 'select-one') input.value = '';
            input.classList.remove('is-invalid');
        });
        
        // Mostrar botón de eliminar
        newRow.querySelector('.remove-producto').style.display = 'block';
        
        // Agregar evento al botón de eliminar
        newRow.querySelector('.remove-producto').addEventListener('click', function() {
            container.removeChild(newRow);
            calcularTotales();
        });
        
        // Agregar eventos para el nuevo producto
        newRow.querySelector('.producto-select').addEventListener('change', function() {
            actualizarPrecios(this);
        });
        
        newRow.querySelector('.cantidad').addEventListener('change', function() {
            validarStock(this);
            calcularTotales();
        });
        
        container.appendChild(newRow);
        productoCounter++;
    });
    
    // Actualizar precios cuando se selecciona un producto
    function actualizarPrecios(selectElement) {
        const row = selectElement.closest('.producto-row');
        const selectedOption = selectElement.options[selectElement.selectedIndex];
        const precio = selectedOption.getAttribute('data-precio');
        const impuestoPorc = selectedOption.getAttribute('data-impuesto');
        
        // Actualizar campos visibles
        row.querySelector('.precio').value = precio;
        row.querySelector('.impuesto').value = impuestoPorc;
        
        // Actualizar campos ocultos del formulario
        const index = selectElement.name.match(/\[(\d+)\]/)[1];
        row.querySelector('input[name="productos['+index+'][precio_unitario]"]').value = precio;
        row.querySelector('input[name="productos['+index+'][porcentaje_impuesto]"]').value = impuestoPorc;
        
        calcularTotales();
    }
    
    // Validar stock disponible
    function validarStock(inputElement) {
        const row = inputElement.closest('.producto-row');
        const select = row.querySelector('.producto-select');
        const cantidad = parseInt(inputElement.value);
        
        if (select.selectedIndex > 0) {
            const stock = parseInt(select.options[select.selectedIndex].getAttribute('data-stock'));
            
            if (cantidad > stock) {
                alert('No hay suficiente stock disponible. Stock actual: ' + stock);
                inputElement.value = stock;
            }
        }
    }
    
    // Calcular totales de la factura
    function calcularTotales() {
        subtotal = 0;
        impuesto = 0;
        total = 0;
        
        document.querySelectorAll('.producto-row').forEach(row => {
            const precioInput = row.querySelector('input[name*="[precio_unitario]"]');
            const impuestoInput = row.querySelector('input[name*="[porcentaje_impuesto]"]');
            const cantidadInput = row.querySelector('.cantidad');
            
            if (precioInput.value && impuestoInput.value && cantidadInput.value) {
                const cantidad = parseFloat(cantidadInput.value);
                const precio = parseFloat(precioInput.value);
                const impuestoPorc = parseFloat(impuestoInput.value);
                
                const subtotalLinea = cantidad * precio;
                const impuestoLinea = subtotalLinea * (impuestoPorc / 100);
                const totalLinea = subtotalLinea + impuestoLinea;
                
                subtotal += subtotalLinea;
                impuesto += impuestoLinea;
                total += totalLinea;
            }
        });
        
        // Actualizar la UI
        document.getElementById('subtotal').textContent = '$' + subtotal.toFixed(2);
        document.getElementById('impuesto').textContent = '$' + impuesto.toFixed(2);
        document.getElementById('total').textContent = '$' + total.toFixed(2);
        
        // Actualizar campos ocultos
        document.getElementById('input-subtotal').value = subtotal.toFixed(2);
        document.getElementById('input-impuesto').value = impuesto.toFixed(2);
        document.getElementById('input-total').value = total.toFixed(2);
    }
    
    // Agregar eventos a la fila inicial
    document.querySelector('.producto-select').addEventListener('change', function() {
        actualizarPrecios(this);
    });
    
    document.querySelector('.cantidad').addEventListener('change', function() {
        validarStock(this);
        calcularTotales();
    });
    
    // Calcular al cargar si hay datos
    calcularTotales();
    
    // Validación del formulario
    (function () {
        'use strict'
        
        const forms = document.querySelectorAll('.needs-validation')
        
        Array.prototype.slice.call(forms)
            .forEach(function (form) {
                form.addEventListener('submit', function (event) {
                    if (!form.checkValidity()) {
                        event.preventDefault()
                        event.stopPropagation()
                    }
                    
                    form.classList.add('was-validated')
                }, false)
            })
    })()
});
</script>

<?php require_once './includes/footer.php'; ?>