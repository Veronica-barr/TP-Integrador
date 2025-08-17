<?php 
$title = "Nuevo Cliente";
require_once './includes/header.php';

// Mostrar mensajes de error
if (isset($_SESSION['form_error'])): ?>
    <div class="alert alert-danger alert-dismissible fade show mt-3">
        <i class="bi bi-exclamation-triangle-fill me-2"></i>
        <?= htmlspecialchars($_SESSION['form_error']) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    <?php unset($_SESSION['form_error']); ?>
<?php endif; ?>

<!-- // Mostrar mensaje de éxito si se creó el cliente correctamente -->
<?php if (isset($_SESSION['form_success'])): ?>
    <div class="alert alert-success alert-dismissible fade show mt-3">
        <i class="bi bi-check-circle-fill me-2"></i>
        <?= $_SESSION['form_success'] ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    <?php unset($_SESSION['form_success']); ?>
<?php endif; ?>

<!-- // Formulario para registrar un nuevo cliente -->
<div class="card">
    <div class="card-header bg-primary text-white">
        <h4 class="mb-0">Registrar Nuevo Cliente</h4>
    </div>
    
    <!-- // Iniciar el formulario -->
    <form action="index.php?module=clientes&action=store" method="POST" class="needs-validation" novalidate>
        <div class="card-body">
            <!-- Sección Datos Básicos -->
            <div class="mb-4 border-bottom pb-3">
                <h5 class="text-primary">
                    <i class="bi bi-person-lines-fill me-2"></i>Datos Básicos
                </h5>
                
                <!-- // Mostrar campos de datos básicos del cliente -->
                <div class="row g-3">
                    <div class="col-md-6">
                        <label for="nombre" class="form-label required-field">Nombre</label>
                        <input type="text" class="form-control" id="nombre" name="nombre" 
                               value="<?= htmlspecialchars($_SESSION['form_data']['nombre'] ?? '') ?>" required>
                        <div class="invalid-feedback">
                            Por favor ingrese el nombre del cliente.
                        </div>
                    </div>
                    
                    <!-- // // Mostrar campo para el apellido del cliente -->
                    <div class="col-md-6">
                        <label for="apellido" class="form-label required-field">Apellido</label>
                        <input type="text" class="form-control" id="apellido" name="apellido" 
                               value="<?= htmlspecialchars($_SESSION['form_data']['apellido'] ?? '') ?>" required>
                        <div class="invalid-feedback">
                            Por favor ingrese el apellido del cliente.
                        </div>
                    </div>
                    
                    <!-- // // Mostrar campo para el cuil del cliente -->
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
                        <i class="bi bi-plus-circle me-1"></i> Agregar Teléfono
                    </button>
                </div>
                
                <div id="telefonos-container">
                    <?php 
                    // Mostrar teléfonos existentes o campo inicial
                    $telefonos_count = !empty($_SESSION['form_data']['telefonos']) ? count($_SESSION['form_data']['telefonos']) : 1;
                    $max_telefonos = 3; // Límite máximo de teléfonos
                    
                    // Mostrar hasta 3 campos de teléfono
                    for ($i = 0; $i < $max_telefonos; $i++):
                        $telefono = $_SESSION['form_data']['telefonos'][$i] ?? [];
                        $hidden = ($i >= $telefonos_count && $i > 0) ? 'style="display:none;"' : '';
                    ?>
                    <!-- // Mostrar cada fila de teléfono -->
                        <div class="row g-3 telefono-row mb-3" <?= $hidden ?> id="telefono-row-<?= $i ?>">
                            <div class="col-md-3">
                                <label class="form-label <?= $i === 0 ? 'required-field' : '' ?>">Tipo</label>
                                <select class="form-select" name="telefonos[<?= $i ?>][tipo]" <?= $i === 0 ? 'required' : '' ?>>
                                    <option value="">Seleccionar...</option>
                                    <option value="Celular" <?= ($telefono['tipo'] ?? '') === 'Celular' ? 'selected' : '' ?>>Celular</option>
                                    <option value="Fijo" <?= ($telefono['tipo'] ?? '') === 'Fijo' ? 'selected' : '' ?>>Fijo</option>
                                    <option value="Trabajo" <?= ($telefono['tipo'] ?? '') === 'Trabajo' ? 'selected' : '' ?>>Trabajo</option>
                                </select>
                            </div>
                            <!-- // Mostrar campo para el código de área y número del teléfono -->
                            <div class="col-md-3">
                                <label class="form-label <?= $i === 0 ? 'required-field' : '' ?>">Código Área</label>
                                <input type="text" class="form-control" name="telefonos[<?= $i ?>][codigo_area]" 
                                       value="<?= htmlspecialchars($telefono['codigo_area'] ?? '') ?>"
                                       placeholder="011" pattern="\d{2,4}" <?= $i === 0 ? 'required' : '' ?>>
                            </div>
                            <!-- // Mostrar campo para el número del teléfono --> 
                            <div class="col-md-4">
                                <label class="form-label <?= $i === 0 ? 'required-field' : '' ?>">Número</label>
                                <input type="tel" class="form-control" name="telefonos[<?= $i ?>][numero]" 
                                       value="<?= htmlspecialchars($telefono['numero'] ?? '') ?>"
                                       placeholder="12345678" pattern="\d{6,15}" <?= $i === 0 ? 'required' : '' ?>>
                            </div>
                            <!-- // Mostrar campo para el número interno del teléfono -->
                            <div class="col-md-2 d-flex align-items-end">
                                <?php if ($i > 0): ?>
                                    <button type="button" class="btn btn-outline-danger remove-telefono w-100" data-index="<?= $i ?>">
                                        <i class="bi bi-trash"></i> Eliminar
                                    </button>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endfor; ?>
                </div>
            </div>
            
            <!-- Sección Direcciones -->
            <div class="mb-4">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="text-primary">
                        <i class="bi bi-house-fill me-2"></i>Direcciones
                    </h5>
                    <button type="button" id="add-direccion" class="btn btn-sm btn-primary">
                        <i class="bi bi-plus-circle me-1"></i> Agregar Dirección
                    </button>
                </div>
                
                <div id="direcciones-container">
                    <?php 
                    // Mostrar direcciones existentes o campo inicial
                    $direcciones_count = !empty($_SESSION['form_data']['direcciones']) ? count($_SESSION['form_data']['direcciones']) : 1;
                    $max_direcciones = 2; // Límite máximo de direcciones
                    
                    // Mostrar hasta 2 campos de dirección
                    for ($i = 0; $i < $max_direcciones; $i++):
                        $direccion = $_SESSION['form_data']['direcciones'][$i] ?? [];
                        $hidden = ($i >= $direcciones_count && $i > 0) ? 'style="display:none;"' : '';
                    ?>
                    <!-- // Mostrar cada fila de dirección -->
                        <div class="row g-3 direccion-row mb-3" <?= $hidden ?> id="direccion-row-<?= $i ?>">
                            <div class="col-md-6">
                                <label class="form-label <?= $i === 0 ? 'required-field' : '' ?>">Calle</label>
                                <input type="text" class="form-control" name="direcciones[<?= $i ?>][calle]" 
                                       value="<?= htmlspecialchars($direccion['calle'] ?? '') ?>" <?= $i === 0 ? 'required' : '' ?>>
                            </div>
                            <!-- // Mostrar campo para el número de la dirección -->
                            <div class="col-md-2">
                                <label class="form-label <?= $i === 0 ? 'required-field' : '' ?>">Número</label>
                                <input type="text" class="form-control" name="direcciones[<?= $i ?>][numero]" 
                                       value="<?= htmlspecialchars($direccion['numero'] ?? '') ?>" <?= $i === 0 ? 'required' : '' ?>>
                            </div>
                            <!-- // Mostrar campo para el piso y departamento de la dirección  -->
                            <div class="col-md-2">
                                <label class="form-label">Piso</label>
                                <input type="text" class="form-control" name="direcciones[<?= $i ?>][piso]"
                                       value="<?= htmlspecialchars($direccion['piso'] ?? '') ?>">
                            </div>
                            <!-- // Mostrar campo para el departamento de la dirección -->
                            <div class="col-md-2">
                                <label class="form-label">Depto</label>
                                <input type="text" class="form-control" name="direcciones[<?= $i ?>][departamento]"
                                       value="<?= htmlspecialchars($direccion['departamento'] ?? '') ?>">
                            </div>
                            <!-- // Mostrar campos para provincia, localidad y código postal de la dirección -->
                            <div class="col-md-4">
                                <label class="form-label <?= $i === 0 ? 'required-field' : '' ?>">Provincia</label>
                                <input type="text" class="form-control" name="direcciones[<?= $i ?>][provincia]"
                                       value="<?= htmlspecialchars($direccion['provincia'] ?? '') ?>" <?= $i === 0 ? 'required' : '' ?>>
                            </div>
                            <!-- // Mostrar campo para la localidad de la dirección -->
                            <div class="col-md-4">
                                <label class="form-label <?= $i === 0 ? 'required-field' : '' ?>">Localidad</label>
                                <input type="text" class="form-control" name="direcciones[<?= $i ?>][localidad]"
                                       value="<?= htmlspecialchars($direccion['localidad'] ?? '') ?>" <?= $i === 0 ? 'required' : '' ?>>
                            </div>
                            <!-- // Mostrar campo para el código postal de la dirección -->
                            <div class="col-md-4">
                                <label class="form-label <?= $i === 0 ? 'required-field' : '' ?>">Código Postal</label>
                                <input type="text" class="form-control" name="direcciones[<?= $i ?>][codigo_postal]"
                                       value="<?= htmlspecialchars($direccion['codigo_postal'] ?? '') ?>" <?= $i === 0 ? 'required' : '' ?>>
                            </div>
                            <!-- // Botón para eliminar la dirección -->
                            <div class="col-md-2 d-flex align-items-end">
                                <?php if ($i > 0): ?>
                                    <button type="button" class="btn btn-outline-danger remove-direccion w-100" data-index="<?= $i ?>">
                                        <i class="bi bi-trash"></i> Eliminar
                                    </button>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endfor; ?>
                </div>
            </div>
        </div>
        <!-- // // Botones de acción del formulario -->
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
    // Controlar la visibilidad de los campos de teléfono y dirección
document.addEventListener('DOMContentLoaded', function() {
    // Variables para controlar los campos visibles
    let telefonosVisibles = <?= $telefonos_count ?>;
    const maxTelefonos = <?= $max_telefonos ?>;
    let direccionesVisibles = <?= $direcciones_count ?>;
    const maxDirecciones = <?= $max_direcciones ?>;
    
    // Botón para agregar teléfono
    document.getElementById('add-telefono').addEventListener('click', function() {
        if (telefonosVisibles < maxTelefonos) {
            document.getElementById('telefono-row-' + telefonosVisibles).style.display = 'flex';
            telefonosVisibles++;
            
            // Ocultar botón si llegamos al máximo
            if (telefonosVisibles >= maxTelefonos) {
                this.style.display = 'none';
            }
        }
    });
    
    // Botón para agregar dirección
    document.getElementById('add-direccion').addEventListener('click', function() {
        if (direccionesVisibles < maxDirecciones) {
            document.getElementById('direccion-row-' + direccionesVisibles).style.display = 'flex';
            direccionesVisibles++;
            
            // Ocultar botón si llegamos al máximo
            if (direccionesVisibles >= maxDirecciones) {
                this.style.display = 'none';
            }
        }
    });
    
    // Eliminar teléfono
    document.querySelectorAll('.remove-telefono').forEach(btn => {
        btn.addEventListener('click', function() {
            const index = this.getAttribute('data-index');
            document.getElementById('telefono-row-' + index).style.display = 'none';
            telefonosVisibles--;
            
            // Resetear los valores del campo eliminado
            const row = document.getElementById('telefono-row-' + index);
            row.querySelectorAll('input, select').forEach(field => {
                field.value = '';
            });
            
            // Mostrar nuevamente el botón de agregar
            document.getElementById('add-telefono').style.display = 'block';
        });
    });
    
    // Eliminar dirección
    document.querySelectorAll('.remove-direccion').forEach(btn => {
        btn.addEventListener('click', function() {
            const index = this.getAttribute('data-index');
            document.getElementById('direccion-row-' + index).style.display = 'none';
            direccionesVisibles--;
            
            // Resetear los valores del campo eliminado
            const row = document.getElementById('direccion-row-' + index);
            row.querySelectorAll('input, select').forEach(field => {
                field.value = '';
            });
            
            // Mostrar nuevamente el botón de agregar
            document.getElementById('add-direccion').style.display = 'block';
        });
    });
    
    // Validación del CUIL
    const cuilInput = document.getElementById('cuil');
    if (cuilInput) {
        cuilInput.addEventListener('input', function() {
            this.value = this.value.replace(/[^0-9]/g, '');
            if (this.value.length > 11) {
                this.value = this.value.slice(0, 11);
            }
        });
    }
    
    // Ocultar botones de agregar si ya están todos visibles
    if (telefonosVisibles >= maxTelefonos) {
        document.getElementById('add-telefono').style.display = 'none';
    }
    if (direccionesVisibles >= maxDirecciones) {
        document.getElementById('add-direccion').style.display = 'none';
    }
});
</script>

<?php 
unset($_SESSION['form_data']);
require_once './includes/footer.php'; 
?>