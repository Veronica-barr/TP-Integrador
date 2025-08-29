<?php
require_once __DIR__ . '/controller/ClienteController.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $clienteController = new ClienteController();
    $resultado = $clienteController->crearCliente($_POST);
    
    if ($resultado) {
        header('Location: list.php?mensaje=Cliente creado correctamente&tipo=success');
        exit();
    } else {
        $error = "Error al crear el cliente";
    }
}
?>

<?php include __DIR__ . '/includes/header.php'; ?>
<?php include __DIR__ . '/includes/navigation.php'; ?>

<div class="container-fluid p-4">
    <h2>Crear Nuevo Cliente</h2>
    
    <?php if (isset($error)): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <?php echo $error; ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>
    
    <form method="POST" id="clienteForm">
        <div class="row mb-3">
            <div class="col-md-4">
                <label for="nombre" class="form-label">Nombre</label>
                <input type="text" class="form-control" id="nombre" name="nombre" required>
            </div>
            <div class="col-md-4">
                <label for="apellido" class="form-label">Apellido</label>
                <input type="text" class="form-control" id="apellido" name="apellido" required>
            </div>
            <div class="col-md-4">
                <label for="cuil" class="form-label">CUIL</label>
                <input type="text" class="form-control" id="cuil" name="cuil" required>
            </div>
        </div>
        
        <h4 class="mt-4">Teléfonos</h4>
        <div id="telefonos-container">
            <div class="row mb-3 telefono-row">
                <div class="col-md-3">
                    <label class="form-label">Tipo</label>
                    <select class="form-control" name="telefonos[0][tipo]">
                        <option value="MOVIL">Móvil</option>
                        <option value="FIJO">Fijo</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Código Área</label>
                    <input type="text" class="form-control" name="telefonos[0][codigo_area]">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Número</label>
                    <input type="text" class="form-control" name="telefonos[0][numero]">
                </div>
                <div class="col-md-2 d-flex align-items-end">
                    <button type="button" class="btn btn-danger btn-sm remove-telefono">
                        <i class="bi bi-trash"></i>
                    </button>
                </div>
            </div>
        </div>
        <button type="button" class="btn btn-secondary btn-sm" id="add-telefono">
            <i class="bi bi-plus"></i> Agregar Teléfono
        </button>
        
        <h4 class="mt-4">Direcciones</h4>
        <div id="direcciones-container">
            <div class="row mb-3 direccion-row">
                <div class="col-md-4">
                    <label class="form-label">Calle</label>
                    <input type="text" class="form-control" name="direcciones[0][calle]">
                </div>
                <div class="col-md-2">
                    <label class="form-label">Número</label>
                    <input type="text" class="form-control" name="direcciones[0][numero]">
                </div>
                <div class="col-md-2">
                    <label class="form-label">Piso</label>
                    <input type="text" class="form-control" name="direcciones[0][piso]">
                </div>
                <div class="col-md-2">
                    <label class="form-label">Depto</label>
                    <input type="text" class="form-control" name="direcciones[0][departamento]">
                </div>
                <div class="col-md-2 d-flex align-items-end">
                    <button type="button" class="btn btn-danger btn-sm remove-direccion">
                        <i class="bi bi-trash"></i>
                    </button>
                </div>
                <div class="col-md-4 mt-2">
                    <label class="form-label">Localidad</label>
                    <select class="form-control" name="direcciones[0][localidad_id]">
                        <option value="1">Buenos Aires</option>
                        <option value="2">Córdoba</option>
                        <option value="3">Rosario</option>
                    </select>
                </div>
            </div>
        </div>
        <button type="button" class="btn btn-secondary btn-sm" id="add-direccion">
            <i class="bi bi-plus"></i> Agregar Dirección
        </button>
        
        <div class="mt-4">
            <button type="submit" class="btn btn-success">Guardar Cliente</button>
            <a href="listar.php" class="btn btn-secondary">Cancelar</a>
        </div>
    </form>
</div>

<script>
let telefonoCount = 1;
let direccionCount = 1;

document.getElementById('add-telefono').addEventListener('click', function() {
    const container = document.getElementById('telefonos-container');
    const newRow = document.createElement('div');
    newRow.className = 'row mb-3 telefono-row';
    newRow.innerHTML = `
        <div class="col-md-3">
            <select class="form-control" name="telefonos[${telefonoCount}][tipo]">
                <option value="MOVIL">Móvil</option>
                <option value="FIJO">Fijo</option>
            </select>
        </div>
        <div class="col-md-3">
            <input type="text" class="form-control" name="telefonos[${telefonoCount}][codigo_area]">
        </div>
        <div class="col-md-4">
            <input type="text" class="form-control" name="telefonos[${telefonoCount}][numero]">
        </div>
        <div class="col-md-2 d-flex align-items-end">
            <button type="button" class="btn btn-danger btn-sm remove-telefono">
                <i class="bi bi-trash"></i>
            </button>
        </div>
    `;
    container.appendChild(newRow);
    telefonoCount++;
});

document.getElementById('add-direccion').addEventListener('click', function() {
    const container = document.getElementById('direcciones-container');
    const newRow = document.createElement('div');
    newRow.className = 'row mb-3 direccion-row';
    newRow.innerHTML = `
        <div class="col-md-4">
            <input type="text" class="form-control" name="direcciones[${direccionCount}][calle]">
        </div>
        <div class="col-md-2">
            <input type="text" class="form-control" name="direcciones[${direccionCount}][numero]">
        </div>
        <div class="col-md-2">
            <input type="text" class="form-control" name="direcciones[${direccionCount}][piso]">
        </div>
        <div class="col-md-2">
            <input type="text" class="form-control" name="direcciones[${direccionCount}][departamento]">
        </div>
        <div class="col-md-2 d-flex align-items-end">
            <button type="button" class="btn btn-danger btn-sm remove-direccion">
                <i class="bi bi-trash"></i>
            </button>
        </div>
        <div class="col-md-4 mt-2">
            <select class="form-control" name="direcciones[${direccionCount}][localidad_id]">
                <option value="1">Buenos Aires</option>
                <option value="2">Córdoba</option>
                <option value="3">Rosario</option>
            </select>
        </div>
    `;
    container.appendChild(newRow);
    direccionCount++;
});

document.addEventListener('click', function(e) {
    if (e.target.classList.contains('remove-telefono')) {
        e.target.closest('.telefono-row').remove();
    }
    if (e.target.classList.contains('remove-direccion')) {
        e.target.closest('.direccion-row').remove();
    }
});
</script>

<?php include __DIR__ . '/includes/footer.php'; ?>