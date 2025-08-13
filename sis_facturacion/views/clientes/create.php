<?php 
$title = "Nuevo Cliente";
require_once './includes/header.php'; 
?>

<!-- Mostrar mensajes de error -->
<?php if (isset($_SESSION['form_error'])): ?>
    <div class="alert alert-danger alert-dismissible fade show mt-3">
        <i class="bi bi-exclamation-triangle-fill me-2"></i>
        <?= $_SESSION['form_error'] ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    <?php unset($_SESSION['form_error']); ?>
<?php endif; ?>

<!-- Mostrar mensajes de éxito -->
<?php if (isset($_SESSION['form_success'])): ?>
    <div class="alert alert-success alert-dismissible fade show mt-3">
        <i class="bi bi-check-circle-fill me-2"></i>
        <?= $_SESSION['form_success'] ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    <?php unset($_SESSION['form_success']); ?>
<?php endif; ?>

<div class="card">
    <div class="card-header bg-primary text-white">
        <h4 class="mb-0">Registrar Nuevo Cliente</h4>
    </div>
    
    <form action="index.php?module=clientes&action=store" method="POST" class="needs-validation" novalidate>
        <div class="card-body">
            <!-- Sección Datos Básicos -->
            <div class="mb-4 border-bottom pb-3">
                <h5 class="text-primary">
                    <i class="bi bi-person-lines-fill me-2"></i>Datos Básicos
                </h5>
                
                <div class="row g-3">
                    <div class="col-md-6">
                        <label for="nombre" class="form-label required-field">Nombre</label>
                        <input type="text" class="form-control" id="nombre" name="nombre" 
                               value="<?= htmlspecialchars($_SESSION['form_data']['nombre'] ?? '') ?>" required>
                        <div class="invalid-feedback">
                            Por favor ingrese el nombre del cliente.
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <label for="apellido" class="form-label required-field">Apellido</label>
                        <input type="text" class="form-control" id="apellido" name="apellido" 
                               value="<?= htmlspecialchars($_SESSION['form_data']['apellido'] ?? '') ?>" required>
                        <div class="invalid-feedback">
                            Por favor ingrese el apellido del cliente.
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <label for="cuil" class="form-label required-field">CUIL/CUIT</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-card-heading"></i></span>
                            <input type="text" class="form-control" id="cuil" name="cuil" 
                                   value="<?= htmlspecialchars($_SESSION['form_data']['cuil'] ?? '') ?>"
                                   placeholder="20123456789" pattern="\d{11}" required>
                        </div>
                        <div class="invalid-feedback">
                            Ingrese un CUIL válido (11 dígitos sin guiones).
                        </div>
                        <small class="form-text text-muted">11 dígitos sin guiones (Ej: 20123456789)</small>
                    </div>
                </div>
            </div>
            
            <!-- Sección Teléfonos -->
            <div class="mb-4 border-bottom pb-3">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="text-primary">
                        <i class="bi bi-telephone-fill me-2"></i>Teléfonos
                    </h5>
                    <button type="button" id="add-telefono" class="btn btn-sm btn-primary">
                        <i class="bi bi-plus-circle me-1"></i>Agregar Teléfono
                    </button>
                </div>
                
                <div id="telefonos-container">
                    <?php if (!empty($_SESSION['form_data']['telefonos'])): ?>
                        <?php foreach ($_SESSION['form_data']['telefonos'] as $index => $telefono): ?>
                            <div class="row g-3 telefono-row mb-3">
                                <div class="col-md-3">
                                    <label class="form-label required-field">Tipo</label>
                                    <select class="form-select" name="telefonos[<?= $index ?>][tipo]" required>
                                        <option value="">Seleccionar...</option>
                                        <option value="Celular" <?= $telefono['tipo'] === 'Celular' ? 'selected' : '' ?>>Celular</option>
                                        <option value="Fijo" <?= $telefono['tipo'] === 'Fijo' ? 'selected' : '' ?>>Fijo</option>
                                        <option value="Trabajo" <?= $telefono['tipo'] === 'Trabajo' ? 'selected' : '' ?>>Trabajo</option>
                                        <option value="Otro" <?= $telefono['tipo'] === 'Otro' ? 'selected' : '' ?>>Otro</option>
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label required-field">Código Área</label>
                                    <input type="text" class="form-control" name="telefonos[<?= $index ?>][codigo_area]" 
                                           value="<?= htmlspecialchars($telefono['codigo_area'] ?? '') ?>"
                                           placeholder="011" pattern="\d{2,4}" required>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label required-field">Número</label>
                                    <input type="tel" class="form-control" name="telefonos[<?= $index ?>][numero]" 
                                           value="<?= htmlspecialchars($telefono['numero'] ?? '') ?>"
                                           placeholder="12345678" pattern="\d{6,15}" required>
                                </div>
                                <div class="col-md-2 d-flex align-items-end">
                                    <button type="button" class="btn btn-outline-danger remove-telefono w-100">
                                        <i class="bi bi-trash"></i> Eliminar
                                    </button>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="row g-3 telefono-row mb-3">
                            <div class="col-md-3">
                                <label class="form-label required-field">Tipo</label>
                                <select class="form-select" name="telefonos[0][tipo]" required>
                                    <option value="">Seleccionar...</option>
                                    <option value="Celular">Celular</option>
                                    <option value="Fijo">Fijo</option>
                                    <option value="Trabajo">Trabajo</option>
                                    <option value="Otro">Otro</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label required-field">Código Área</label>
                                <input type="text" class="form-control" name="telefonos[0][codigo_area]" 
                                       placeholder="011" pattern="\d{2,4}" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label required-field">Número</label>
                                <input type="tel" class="form-control" name="telefonos[0][numero]" 
                                       placeholder="12345678" pattern="\d{6,15}" required>
                            </div>
                            <div class="col-md-2 d-flex align-items-end">
                                <button type="button" class="btn btn-outline-danger remove-telefono w-100" style="display: none;">
                                    <i class="bi bi-trash"></i> Eliminar
                                </button>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
            
            <!-- Sección Direcciones -->
            <div class="mb-4">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="text-primary">
                        <i class="bi bi-house-fill me-2"></i>Direcciones
                    </h5>
                    <button type="button" id="add-direccion" class="btn btn-sm btn-primary">
                        <i class="bi bi-plus-circle me-1"></i>Agregar Dirección
                    </button>
                </div>
                
                <div id="direcciones-container">
                    <?php if (!empty($_SESSION['form_data']['direcciones'])): ?>
                        <?php foreach ($_SESSION['form_data']['direcciones'] as $index => $direccion): ?>
                            <div class="row g-3 direccion-row mb-3">
                                <div class="col-md-6">
                                    <label class="form-label required-field">Calle</label>
                                    <input type="text" class="form-control" name="direcciones[<?= $index ?>][calle]" 
                                           value="<?= htmlspecialchars($direccion['calle'] ?? '') ?>" required>
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label required-field">Número</label>
                                    <input type="text" class="form-control" name="direcciones[<?= $index ?>][numero]" 
                                           value="<?= htmlspecialchars($direccion['numero'] ?? '') ?>" required>
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label">Piso</label>
                                    <input type="text" class="form-control" name="direcciones[<?= $index ?>][piso]"
                                           value="<?= htmlspecialchars($direccion['piso'] ?? '') ?>">
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label">Depto</label>
                                    <input type="text" class="form-control" name="direcciones[<?= $index ?>][departamento]"
                                           value="<?= htmlspecialchars($direccion['departamento'] ?? '') ?>">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label required-field">Provincia</label>
                                    <input type="text" class="form-control" name="direcciones[<?= $index ?>][provincia]"
                                           value="<?= htmlspecialchars($direccion['provincia'] ?? '') ?>" required>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label required-field">Localidad</label>
                                    <input type="text" class="form-control" name="direcciones[<?= $index ?>][localidad]"
                                           value="<?= htmlspecialchars($direccion['localidad'] ?? '') ?>" required>
                                </div>
                                <div class="col-md-2 d-flex align-items-end">
                                    <button type="button" class="btn btn-outline-danger remove-direccion w-100">
                                        <i class="bi bi-trash"></i> Eliminar
                                    </button>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="row g-3 direccion-row mb-3">
                            <div class="col-md-6">
                                <label class="form-label required-field">Calle</label>
                                <input type="text" class="form-control" name="direcciones[0][calle]" required>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label required-field">Número</label>
                                <input type="text" class="form-control" name="direcciones[0][numero]" required>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">Piso</label>
                                <input type="text" class="form-control" name="direcciones[0][piso]">
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">Depto</label>
                                <input type="text" class="form-control" name="direcciones[0][departamento]">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label required-field">Provincia</label>
                                <input type="text" class="form-control" name="direcciones[0][provincia]" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label required-field">Localidad</label>
                                <input type="text" class="form-control" name="direcciones[0][localidad]" required>
                            </div>
                            <div class="col-md-4">
                            <label class="form-label required-field">Código Postal</label>
                            <input type="text" class="form-control" name="direcciones[0][codigo_postal]" required>
                            </div>
                            <div class="col-md-2 d-flex align-items-end">
                                <button type="button" class="btn btn-outline-danger remove-direccion w-100" style="display: none;">
                                    <i class="bi bi-trash"></i> Eliminar
                                </button>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        
        <div class="card-footer bg-light">
            <div class="d-flex justify-content-between">
                <button type="reset" class="btn btn-secondary">
                    <i class="bi bi-eraser me-1"></i> Limpiar
                </button>
                <button type="submit" class="btn btn-success">
                    <i class="bi bi-save me-1"></i> Guardar Cliente
                </button>
            </div>
        </div>
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Manejar teléfonos
    let telefonoCounter = <?= !empty($_SESSION['form_data']['telefonos']) ? count($_SESSION['form_data']['telefonos']) : 1 ?>;
    document.getElementById('add-telefono').addEventListener('click', function() {
        const container = document.getElementById('telefonos-container');
        const newRow = document.querySelector('.telefono-row').cloneNode(true);
        
        // Actualizar los nombres de los campos
        Array.from(newRow.querySelectorAll('input, select')).forEach(input => {
            const name = input.name.replace(/\[\d+\]/, `[${telefonoCounter}]`);
            input.name = name;
            input.value = '';
            input.classList.remove('is-invalid');
        });
        
        // Mostrar botón de eliminar
        newRow.querySelector('.remove-telefono').style.display = 'block';
        
        // Agregar evento al botón de eliminar
        newRow.querySelector('.remove-telefono').addEventListener('click', function() {
            container.removeChild(newRow);
        });
        
        container.appendChild(newRow);
        telefonoCounter++;
    });

    // Manejar direcciones
    let direccionCounter = <?= !empty($_SESSION['form_data']['direcciones']) ? count($_SESSION['form_data']['direcciones']) : 1 ?>;
    document.getElementById('add-direccion').addEventListener('click', function() {
        const container = document.getElementById('direcciones-container');
        const newRow = document.querySelector('.direccion-row').cloneNode(true);
        
        // Actualizar los nombres de los campos
        Array.from(newRow.querySelectorAll('input, select')).forEach(input => {
            const name = input.name.replace(/\[\d+\]/, `[${direccionCounter}]`);
            input.name = name;
            if (input.type !== 'select-one') input.value = '';
            input.classList.remove('is-invalid');
        });
        
        // Mostrar botón de eliminar
        newRow.querySelector('.remove-direccion').style.display = 'block';
        
        // Agregar evento al botón de eliminar
        newRow.querySelector('.remove-direccion').addEventListener('click', function() {
            container.removeChild(newRow);
        });
        
        container.appendChild(newRow);
        direccionCounter++;
    });

    // Validación del CUIL
    const cuilInput = document.getElementById('cuil');
    if (cuilInput) {
        cuilInput.addEventListener('input', function() {
            // Eliminar todo lo que no sean números
            this.value = this.value.replace(/[^0-9]/g, '');
            
            // Limitar a 11 caracteres
            if (this.value.length > 11) {
                this.value = this.value.slice(0, 11);
            }
        });
    }
});

// Eliminar elementos existentes al cargar la página
document.querySelectorAll('.remove-telefono').forEach(btn => {
    if (btn.style.display !== 'none') {
        btn.addEventListener('click', function() {
            this.closest('.telefono-row').remove();
        });
    }
});

document.querySelectorAll('.remove-direccion').forEach(btn => {
    if (btn.style.display !== 'none') {
        btn.addEventListener('click', function() {
            this.closest('.direccion-row').remove();
        });
    }
});
</script>

<?php 
unset($_SESSION['form_data']);
require_once './includes/footer.php'; 
?>