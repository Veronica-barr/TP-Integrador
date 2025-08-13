<?php 
$title = "Listado de Clientes";
require_once './includes/header.php'; 
?>

<div class="card">
    <div class="card-header bg-primary text-white">
        <div class="d-flex justify-content-between align-items-center">
            <h4 class="mb-0">Clientes Registrados</h4>
            <a href="index.php?module=clientes&action=create" class="btn btn-light">
                <i class="bi bi-plus-lg"></i> Nuevo Cliente
            </a>
        </div>
    </div>
    
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-striped table-hover table-bordered">
                <thead class="table-dark">
                    <tr>
                        <th width="5%">ID</th>
                        <th>Nombre</th>
                        <th>Apellido</th>
                        <th>CUIL</th>
                        <th width="15%">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($cliente = $clientes->fetch(PDO::FETCH_ASSOC)): ?>
                    <tr>
                        <td><?= $cliente['cliente_id'] ?></td>
                        <td><?= htmlspecialchars($cliente['nombre']) ?></td>
                        <td><?= htmlspecialchars($cliente['apellido']) ?></td>
                        <td><?= $cliente['cuil'] ?></td>
                        <td class="text-center">
                            <div class="btn-group" role="group">
                                <a href="index.php?module=clientes&action=show&id=<?= $cliente['cliente_id'] ?>" 
                                   class="btn btn-sm btn-info" data-bs-toggle="tooltip" title="Ver detalle">
                                    <i class="bi bi-eye"></i>
                                </a>
                                <a href="index.php?module=clientes&action=edit&id=<?= $cliente['cliente_id'] ?>" 
                                   class="btn btn-sm btn-warning" data-bs-toggle="tooltip" title="Editar">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <a href="index.php?module=clientes&action=delete&id=<?= $cliente['cliente_id'] ?>" 
                                   class="btn btn-sm btn-danger" data-bs-toggle="tooltip" title="Eliminar"
                                   onclick="return confirm('¿Está seguro de eliminar este cliente?')">
                                    <i class="bi bi-trash"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
    
    <div class="card-footer text-muted">
        Total de clientes: <?= $clientes->rowCount() ?>
    </div>
</div>

<?php require_once './includes/footer.php'; ?>