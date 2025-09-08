<?php
require_once __DIR__ . '/../../controller/ClienteController.php';
include __DIR__ . '/../../includes/header.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $controller = new ClienteController();
    $resultado = $controller->crearCliente($_POST);

    if ($resultado) {
        header('Location: listadoClientes.php?mensaje=Cliente creado correctamente');
        exit();
    } else {
        $error = "Error al crear el cliente";
    }
}
?>

<div class="container mt-5">
    <h2>Registrar Cliente Completo</h2>
    <?php if (isset($error)): ?>
        <div class="alert alert-danger"><?= $error ?></div>
    <?php endif; ?>

    <form method="POST">
        <h4>Datos del Cliente</h4>
        <div class="mb-3">
            <label class="required-label">Nombre</label>
            <input type="text" class="form-control" name="nombre" required>
        </div>
        <div class="mb-3">
            <label class="required-label">Apellido</label>
            <input type="text" class="form-control" name="apellido" required>
        </div>
        <div class="mb-3">
            <label class="required-label">CUIL</label>
            <input type="text" class="form-control" name="cuil" pattern="\d{11}" required>
        </div>
        <div class="mb-3">
            <label class="required-label">Email</label>
            <input type="email" class="form-control" name="email" required>
        </div>

        <h4>Teléfonos</h4>
        <div id="telefonos">
            <div class="mb-3">
                <label class="required-label">Teléfono</label>
                <input type="text" class="form-control" name="telefono[]" required>
                <label>Tipo</label>
                <select class="form-control" name="tipoTelefono[]">
                    <option value="celular">Celular</option>
                    <option value="fijo">Fijo</option>
                    <option value="laboral">Laboral</option>
                    <option value="otro">Otro</option>
                </select>
            </div>
        </div>
        <button type="button" class="btn btn-secondary btn-sm mb-3" onclick="agregarTelefono()">+ Agregar Teléfono</button>

        <h4>Direcciones</h4>
        <div id="direcciones">
            <div class="mb-3">
                <label class="required-label">Calle</label>
                <input type="text" class="form-control" name="calle[]" required>
                <label class="required-label">Número</label>
                <input type="text" class="form-control" name="numero[]" required>
                <label>Piso</label>
                <input type="text" class="form-control" name="piso[]">
                <label>Dpto</label>
                <input type="text" class="form-control" name="dpto[]">
                <label class="required-label">Ciudad</label>
                <input type="text" class="form-control" name="ciudad[]" required>
                <label class="required-label">Provincia</label>
                <input type="text" class="form-control" name="provincia[]" required>
                <label>Código Postal</label>
                <input type="text" class="form-control" name="cp[]">
                <label>Tipo de Dirección</label>
                <select class="form-control" name="tipoDireccion[]">
                    <option value="envio">Envío</option>
                    <option value="fiscal">Fiscal</option>
                    <option value="otro">Otro</option>
                </select>
            </div>
        </div>
        <button type="button" class="btn btn-secondary btn-sm mb-3" onclick="agregarDireccion()">+ Agregar Dirección</button>

        <div class="mt-3">
            <button type="submit" class="btn btn-success">Guardar Cliente</button>
            <a href="listadoClientes.php" class="btn btn-secondary">Cancelar</a>
        </div>
    </form>
</div>

<script>
function agregarTelefono() {
    const div = document.createElement('div');
    div.className = 'mb-3';
    div.innerHTML = `
        <label class="required-label">Teléfono</label>
        <input type="text" class="form-control" name="telefono[]" required>
        <label>Tipo</label>
        <select class="form-control" name="tipoTelefono[]">
            <option value="celular">Celular</option>
            <option value="fijo">Fijo</option>
            <option value="laboral">Laboral</option>
            <option value="otro">Otro</option>
        </select>
    `;
    document.getElementById('telefonos').appendChild(div);
}

function agregarDireccion() {
    const div = document.createElement('div');
    div.className = 'mb-3';
    div.innerHTML = `
        <label class="required-label">Calle</label>
        <input type="text" class="form-control" name="calle[]" required>
        <label class="required-label">Número</label>
        <input type="text" class="form-control" name="numero[]" required>
        <label>Piso</label>
        <input type="text" class="form-control" name="piso[]">
        <label>Dpto</label>
        <input type="text" class="form-control" name="dpto[]">
        <label class="required-label">Ciudad</label>
        <input type="text" class="form-control" name="ciudad[]" required>
        <label class="required-label">Provincia</label>
        <input type="text" class="form-control" name="provincia[]" required>
        <label>Código Postal</label>
        <input type="text" class="form-control" name="cp[]">
        <label>Tipo de Dirección</label>
        <select class="form-control" name="tipoDireccion[]">
            <option value="envio">Envío</option>
            <option value="fiscal">Fiscal</option>
            <option value="otro">Otro</option>
        </select>
    `;
    document.getElementById('direcciones').appendChild(div);
}
</script>

<?php include __DIR__ . '/../../includes/footer.php'; ?>
