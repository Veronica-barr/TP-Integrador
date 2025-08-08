<?php
require_once '../includes/conexion.php';

$cliente = ['cliente_id' => 0, 'nombre' => '', 'apellido' => '', 'cuil' => ''];

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $query = "SELECT * FROM clientes WHERE cliente_id = ?";
    $stmt = $conexion->prepare($query);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $cliente = $result->fetch_assoc();
    
    // Obtener teléfonos del cliente
    $query = "SELECT * FROM telefonos WHERE cliente_id = ?";
    $stmt = $conexion->prepare($query);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $telefonos = $stmt->get_result();
    
    // Obtener direcciones del cliente (optimizada)
    $query = "SELECT d.*, l.localidad_id, p.provincia_id 
              FROM direcciones d
              JOIN localidades l ON d.localidad_id = l.localidad_id
              JOIN provincias p ON l.provincia_id = p.provincia_id
              WHERE d.cliente_id = ?";
    $stmt = $conexion->prepare($query);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $direcciones = $stmt->get_result();
}

// Cargar todas las provincias
$provincias = $conexion->query("SELECT * FROM provincias ORDER BY nombre");

require_once '../includes/header.php';
?>

<h2><?= htmlspecialchars($cliente['cliente_id'] ? 'Editar' : 'Nuevo') ?> Cliente</h2>

<form action="guardar.php" method="post" id="form-cliente">
    <input type="hidden" name="cliente_id" value="<?= htmlspecialchars($cliente['cliente_id']) ?>">
    
    <div class="row mb-3">
        <div class="col-md-4">
            <label class="form-label">Nombre</label>
            <input type="text" class="form-control" name="nombre" value="<?= htmlspecialchars($cliente['nombre']) ?>" required>
        </div>
        <div class="col-md-4">
            <label class="form-label">Apellido</label>
            <input type="text" class="form-control" name="apellido" value="<?= htmlspecialchars($cliente['apellido']) ?>" required>
        </div>
        <div class="col-md-4">
            <label class="form-label">CUIL</label>
            <input type="text" class="form-control" name="cuil" value="<?= htmlspecialchars($cliente['cuil']) ?>" required>
        </div>
    </div>
    
    <h4 class="mt-4">Teléfonos</h4>
    <div id="telefonos-container">
        <?php if (isset($telefonos) && $telefonos->num_rows > 0): ?>
            <?php while($telefono = $telefonos->fetch_assoc()): ?>
                <div class="row telefono-item mb-3">
                    <div class="col-md-3">
                        <select class="form-select" name="telefono_tipo[]" required>
                            <option value="Celular" <?= $telefono['tipo'] == 'Celular' ? 'selected' : '' ?>>Celular</option>
                            <option value="Fijo" <?= $telefono['tipo'] == 'Fijo' ? 'selected' : '' ?>>Fijo</option>
                            <option value="Trabajo" <?= $telefono['tipo'] == 'Trabajo' ? 'selected' : '' ?>>Trabajo</option>
                            <option value="Otro" <?= $telefono['tipo'] == 'Otro' ? 'selected' : '' ?>>Otro</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <input type="text" class="form-control" name="telefono_codigo_area[]" 
                               value="<?= htmlspecialchars($telefono['codigo_area']) ?>" placeholder="Cód. área" required>
                    </div>
                    <div class="col-md-4">
                        <input type="text" class="form-control" name="telefono_numero[]" 
                               value="<?= htmlspecialchars($telefono['numero']) ?>" placeholder="Número" required>
                    </div>
                    <div class="col-md-2">
                        <input type="hidden" name="telefono_id[]" value="<?= htmlspecialchars($telefono['telefono_id']) ?>">
                        <button type="button" class="btn btn-danger quitar-telefono">Eliminar</button>
                    </div>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <div class="row telefono-item mb-3">
                <div class="col-md-3">
                    <select class="form-select" name="telefono_tipo[]" required>
                        <option value="Celular">Celular</option>
                        <option value="Fijo">Fijo</option>
                        <option value="Trabajo">Trabajo</option>
                        <option value="Otro">Otro</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <input type="text" class="form-control" name="telefono_codigo_area[]" placeholder="Cód. área" required>
                </div>
                <div class="col-md-4">
                    <input type="text" class="form-control" name="telefono_numero[]" placeholder="Número" required>
                </div>
                <div class="col-md-2">
                    <input type="hidden" name="telefono_id[]" value="0">
                    <button type="button" class="btn btn-danger quitar-telefono">Eliminar</button>
                </div>
            </div>
        <?php endif; ?>
    </div>
    <button type="button" id="agregar-telefono" class="btn btn-secondary mb-4">Agregar Teléfono</button>
    
    <h4>Direcciones</h4>
    <div id="direcciones-container">
        <?php if (isset($direcciones) && $direcciones->num_rows > 0): ?>
            <?php while($direccion = $direcciones->fetch_assoc()): ?>
                <div class="row direccion-item mb-3 border p-3">
                    <div class="col-md-6">
                        <label class="form-label">Calle</label>
                        <input type="text" class="form-control" name="direccion_calle[]" 
                               value="<?= htmlspecialchars($direccion['calle']) ?>" required>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Número</label>
                        <input type="text" class="form-control" name="direccion_numero[]" 
                               value="<?= htmlspecialchars($direccion['numero']) ?>" required>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Piso</label>
                        <input type="text" class="form-control" name="direccion_piso[]" 
                               value="<?= htmlspecialchars($direccion['piso']) ?>">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Depto</label>
                        <input type="text" class="form-control" name="direccion_departamento[]" 
                               value="<?= htmlspecialchars($direccion['departamento']) ?>">
                    </div>
                    
                    <div class="col-md-4">
                        <label class="form-label">Provincia</label>
                        <select class="form-select provincia-select" name="direccion_provincia_id[]" required
                                data-selected-localidad="<?= htmlspecialchars($direccion['localidad_id']) ?>">
                            <option value="">Seleccione provincia</option>
                            <?php 
                            $provincias->data_seek(0);
                            while($provincia = $provincias->fetch_assoc()): ?>
                                <option value="<?= htmlspecialchars($provincia['provincia_id']) ?>" 
                                    <?= $provincia['provincia_id'] == $direccion['provincia_id'] ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($provincia['nombre']) ?>
                                </option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Localidad</label>
                        <select class="form-select localidad-select" name="direccion_localidad_id[]" required>
                            <option value="">Cargando localidades...</option>
                        </select>
                    </div>
                    
                    <div class="col-md-2">
                        <label class="form-label">Código Postal</label>
                        <input type="text" class="form-control" name="direccion_codigo_postal[]" 
                               value="<?= htmlspecialchars($direccion['codigo_postal']) ?>">
                    </div>
                    <div class="col-md-2">
                        <input type="hidden" name="direccion_id[]" value="<?= htmlspecialchars($direccion['direccion_id']) ?>">
                        <button type="button" class="btn btn-danger quitar-direccion mt-4">Eliminar</button>
                    </div>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <div class="row direccion-item mb-3 border p-3">
                <div class="col-md-6">
                    <label class="form-label">Calle</label>
                    <input type="text" class="form-control" name="direccion_calle[]" required>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Número</label>
                    <input type="text" class="form-control" name="direccion_numero[]" required>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Piso</label>
                    <input type="text" class="form-control" name="direccion_piso[]">
                </div>
                <div class="col-md-2">
                    <label class="form-label">Depto</label>
                    <input type="text" class="form-control" name="direccion_departamento[]">
                </div>
                
                <div class="col-md-4">
                    <label class="form-label">Provincia</label>
                    <select class="form-select provincia-select" name="direccion_provincia_id[]" required>
                        <option value="">Seleccione provincia</option>
                        <?php 
                        $provincias->data_seek(0);
                        while($provincia = $provincias->fetch_assoc()): ?>
                            <option value="<?= htmlspecialchars($provincia['provincia_id']) ?>">
                                <?= htmlspecialchars($provincia['nombre']) ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Localidad</label>
                    <select class="form-select localidad-select" name="direccion_localidad_id[]" required>
                        <option value="">Seleccione provincia primero</option>
                    </select>
                </div>
                
                <div class="col-md-2">
                    <label class="form-label">Código Postal</label>
                    <input type="text" class="form-control" name="direccion_codigo_postal[]">
                </div>
                <div class="col-md-2">
                    <input type="hidden" name="direccion_id[]" value="0">
                    <button type="button" class="btn btn-danger quitar-direccion mt-4">Eliminar</button>
                </div>
            </div>
        <?php endif; ?>
    </div>
    <button type="button" id="agregar-direccion" class="btn btn-secondary mb-4">Agregar Dirección</button>
    
    <div class="d-grid gap-2 d-md-flex justify-content-md-end">
        <button type="submit" class="btn btn-primary me-md-2">Guardar</button>
        <a href="listar.php" class="btn btn-secondary">Cancelar</a>
    </div>
</form>

<script>
// Cache para localidades
const localidadesCache = {};

document.addEventListener('DOMContentLoaded', function() {
    // Agregar teléfono
    document.getElementById('agregar-telefono').addEventListener('click', function() {
        const nuevoTelefono = document.querySelector('.telefono-item').cloneNode(true);
        nuevoTelefono.querySelectorAll('input').forEach(input => {
            if (input.type !== 'hidden') input.value = '';
            else input.value = '0';
        });
        nuevoTelefono.querySelector('select').selectedIndex = 0;
        document.getElementById('telefonos-container').appendChild(nuevoTelefono);
    });
    
    // Quitar teléfono
    document.addEventListener('click', function(e) {
        if (e.target.classList.contains('quitar-telefono')) {
            if (document.querySelectorAll('.telefono-item').length > 1) {
                e.target.closest('.telefono-item').remove();
            }
        }
    });
    
    // Agregar dirección
    document.getElementById('agregar-direccion').addEventListener('click', function() {
        const template = document.querySelector('.direccion-item:first-child');
        const nuevaDireccion = template.cloneNode(true);
        
        // Limpiar campos
        nuevaDireccion.querySelectorAll('input[type="text"]').forEach(input => input.value = '');
        nuevaDireccion.querySelectorAll('input[type="hidden"]').forEach(input => input.value = '0');
        nuevaDireccion.querySelector('.provincia-select').selectedIndex = 0;
        
        // Configurar select de localidades
        const localidadSelect = nuevaDireccion.querySelector('.localidad-select');
        localidadSelect.innerHTML = '<option value="">Seleccione provincia primero</option>';
        localidadSelect.disabled = false;
        
        document.getElementById('direcciones-container').appendChild(nuevaDireccion);
    });
    
    // Quitar dirección
    document.addEventListener('click', function(e) {
        if (e.target.classList.contains('quitar-direccion')) {
            if (document.querySelectorAll('.direccion-item').length > 1) {
                e.target.closest('.direccion-item').remove();
            }
        }
    });
    
    // Cargar localidades al cambiar provincia
    document.addEventListener('change', function(e) {
        if (e.target.classList.contains('provincia-select')) {
            const provinciaSelect = e.target;
            const direccionItem = provinciaSelect.closest('.direccion-item');
            const localidadSelect = direccionItem.querySelector('.localidad-select');
            const provinciaId = provinciaSelect.value;
            
            // Estado de carga
            localidadSelect.innerHTML = '<option value="">Cargando localidades...</option>';
            localidadSelect.disabled = false;
            
            if (provinciaId) {
                // Verificar cache primero
                if (localidadesCache[provinciaId]) {
                    cargarLocalidades(localidadSelect, localidadesCache[provinciaId]);
                    seleccionarLocalidadGuardada(direccionItem);
                } else {
                    fetchLocalidades(provinciaId)
                        .then(data => {
                            localidadesCache[provinciaId] = data;
                            cargarLocalidades(localidadSelect, data);
                            seleccionarLocalidadGuardada(direccionItem);
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            localidadSelect.innerHTML = '<option value="">Error al cargar localidades</option>';
                        });
                }
            } else {
                localidadSelect.innerHTML = '<option value="">Seleccione provincia primero</option>';
            }
        }
    });
    
    // Función para cargar localidades via fetch
    function fetchLocalidades(provinciaId) {
        return fetch(`../ajax/get_localidades.php?provincia_id=${provinciaId}`)
            .then(response => {
                if (!response.ok) throw new Error('Error en la respuesta del servidor');
                return response.json();
            })
            .then(data => {
                if (!data.success) throw new Error(data.error || 'Error desconocido');
                return data.data;
            });
    }
    
    // Función para cargar localidades en el select
    function cargarLocalidades(selectElement, localidades) {
        // Limpiar select
        selectElement.innerHTML = '';
        
        // Agregar opción por defecto
        const defaultOption = new Option('Seleccione localidad', '');
        selectElement.add(defaultOption);
        
        // Usar objeto para evitar duplicados
        const localidadesUnicas = {};
        
        // Agregar localidades únicas
        localidades.forEach(localidad => {
            if (!localidadesUnicas[localidad.localidad_id]) {
                const option = new Option(localidad.nombre, localidad.localidad_id);
                selectElement.add(option);
                localidadesUnicas[localidad.localidad_id] = true;
            }
        });
        
        // Si no hay localidades
        if (Object.keys(localidadesUnicas).length === 0) {
            selectElement.innerHTML = '<option value="">No hay localidades disponibles</option>';
        }
    }
    
    // Función para seleccionar localidad guardada
    function seleccionarLocalidadGuardada(direccionItem) {
        const provinciaSelect = direccionItem.querySelector('.provincia-select');
        const localidadSelect = direccionItem.querySelector('.localidad-select');
        const localidadId = provinciaSelect.dataset.selectedLocalidad;
        
        if (localidadId) {
            setTimeout(() => {
                const option = localidadSelect.querySelector(`option[value="${localidadId}"]`);
                if (option) {
                    localidadSelect.value = localidadId;
                }
                provinciaSelect.removeAttribute('data-selected-localidad');
            }, 100);
        }
    }
    
    // Inicializar direcciones existentes
    document.querySelectorAll('.direccion-item').forEach(item => {
        const provinciaSelect = item.querySelector('.provincia-select');
        if (provinciaSelect.value) {
            provinciaSelect.dispatchEvent(new Event('change'));
        }
    });
    
    // Validación del formulario
    document.getElementById('form-cliente').addEventListener('submit', function(e) {
        let formularioValido = true;
        
        // Validar selects de localidad
        document.querySelectorAll('.localidad-select').forEach(select => {
            if (select.value === '' && select.required) {
                select.classList.add('is-invalid');
                formularioValido = false;
            } else {
                select.classList.remove('is-invalid');
            }
        });
        
        if (!formularioValido) {
            e.preventDefault();
            alert('Por favor complete todas las localidades requeridas');
        }
    });
});
</script>

<style>
.is-invalid {
    border-color: #dc3545;
}
</style>

<?php require_once '../includes/footer.php'; ?>