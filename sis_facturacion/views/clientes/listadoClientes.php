<?php
require_once __DIR__ . '/../../controller/ClienteController.php';
include __DIR__ . '/../../includes/header.php';

$controller = new ClienteController();
$clientes = $controller->obtenerTodosLosClientes();
?>

<div class="container mt-5">
    <h1 class="text-center mb-4">Listado de Clientes</h1>

    <div class="text-center mb-3">
        <a href="crearCliente.php" class="btn btn-success"><i class="bi bi-plus-lg"></i> Nuevo Cliente</a>
    </div>

    <table class="table table-bordered table-striped table-hover text-center">
        <thead class="table-light">
            <tr>
                <th>ID</th>
                <th>Nombre</th>
                <th>Apellido</th>
                <th>CUIL</th>
                <th>Email</th>
                <th>Teléfonos</th>
                <th>Direcciones</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
        <?php if (!empty($clientes)): ?>
            <?php foreach ($clientes as $c): ?>
                <tr>
                    <td><?= $c->getIdCliente() ?></td>
                    <td><?= htmlspecialchars($c->getNombre()) ?></td>
                    <td><?= htmlspecialchars($c->getApellido()) ?></td>
                    <td><?= htmlspecialchars($c->getCuil()) ?></td>
                    <td><?= htmlspecialchars($c->getEmail()) ?></td>
                    <td><?= implode('<br>', $c->getTelefonos()) ?></td>
                    <td><?= implode('<br>', $c->getDirecciones()) ?></td>
                    <td>
                        <a href="editarCliente.php?id=<?= $c->getIdCliente() ?>" class="btn btn-primary btn-sm">
                            <i class="bi bi-pencil"></i>
                        </a>
                        <a href="eliminarCliente.php?id=<?= $c->getIdCliente() ?>" class="btn btn-danger btn-sm" onclick="return confirm('¿Eliminar cliente?')">
                            <i class="bi bi-trash"></i>
                        </a>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr>
                <td colspan="8">No hay clientes registrados.</td>
            </tr>
        <?php endif; ?>
        </tbody>
    </table>
</div>

<?php include __DIR__ . '/../../includes/footer.php'; ?>
