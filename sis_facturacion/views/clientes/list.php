<?php

require_once __DIR__ . '/../../controller/ClienteController.php';

$clienteController = new ClienteController();
$clientes = $clienteController->listarClientes();
?>

<?php include __DIR__ . '/../includes/header.php'; ?>
<?php include __DIR__ . '/../includes/navigation.php'; ?>

<div class="container-fluid p-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Listado de Clientes</h2>
        <a href="./create.php" class="btn btn-primary">
            <i class="bi bi-plus-circle"></i> Nuevo Cliente
        </a>
    </div>

    <?php if (isset($_GET['mensaje'])): ?>
        <div class="alert alert-<?php echo $_GET['tipo'] ?? 'success'; ?> alert-dismissible fade show" role="alert">
            <?php echo $_GET['mensaje']; ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <div class="table-responsive">
        <table class="table table-striped table-hover">
            <thead class="table-dark">
                <tr>
                    <th>ID</th>
                    <th>Nombre</th>
                    <th>Apellido</th>
                    <th>CUIL</th>
                    <th>Teléfonos</th>
                    <th>Direcciones</th>
                    <th>Fecha Registro</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php if (count($clientes) > 0): ?>
                    <?php foreach ($clientes as $cliente): ?>
                        <tr>
                            <td><?php echo $cliente->cliente_id; ?></td>
                            <td><?php echo htmlspecialchars($cliente->nombre); ?></td>
                            <td><?php echo htmlspecialchars($cliente->apellido); ?></td>
                            <td><?php echo htmlspecialchars($cliente->cuil); ?></td>
                            <td>
                                <?php foreach ($cliente->telefonos as $telefono): ?>
                                    <span class="badge bg-secondary"><?php echo $telefono->tipo . ': ' . $telefono->codigo_area . '-' . $telefono->numero; ?></span><br>
                                <?php endforeach; ?>
                            </td>
                            <td>
                                <?php foreach ($cliente->direcciones as $direccion): ?>
                                    <span class="badge bg-light text-dark"><?php echo $direccion->calle . ' ' . $direccion->numero . ', ' . $direccion->localidad_nombre; ?></span><br>
                                <?php endforeach; ?>
                            </td>
                            <td><?php echo date('d/m/Y', strtotime($cliente->fecha_registro)); ?></td>
                            <td>
                                <a href="editar.php?id=<?php echo $cliente->cliente_id; ?>" class="btn btn-sm btn-warning">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <button class="btn btn-sm btn-danger" onclick="confirmarEliminacion(<?php echo $cliente->cliente_id; ?>)">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="8" class="text-center">No hay clientes registrados</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<script>
function confirmarEliminacion(clienteId) {
    if (confirm('¿Está seguro de que desea eliminar este cliente?')) {
        window.location.href = 'eliminar.php?id=' + clienteId;
    }
}
</script>

<?php include __DIR__ . '/../includes/footer.php'; ?>