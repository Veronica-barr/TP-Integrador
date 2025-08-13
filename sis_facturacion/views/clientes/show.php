<?php 
$title = "Detalle del Cliente";
require_once './includes/header.php'; 
?>

<div class="card">
    <div class="card-header bg-primary text-white">
        <div class="d-flex justify-content-between align-items-center">
            <h4 class="mb-0">Detalle del Cliente</h4>
            <div>
                <a href="index.php?module=clientes&action=edit&id=<?= $cliente['cliente_id'] ?>" class="btn btn-warning">
                    <i class="bi bi-pencil-square me-1"></i> Editar
                </a>
                <a href="index.php?module=clientes&action=list" class="btn btn-light">
                    <i class="bi bi-arrow-left me-1"></i> Volver
                </a>
            </div>
        </div>
    </div>
    
    <div class="card-body">
        <div class="row mb-4">
            <div class="col-md-6">
                <div class="card h-100">
                    <div class="card-header bg-light">
                        <h5 class="mb-0">Información Personal</h5>
                    </div>
                    <div class="card-body">
                        <dl class="row">
                            <dt class="col-sm-4">Nombre:</dt>
                            <dd class="col-sm-8"><?= htmlspecialchars($cliente['nombre']) ?></dd>
                            
                            <dt class="col-sm-4">Apellido:</dt>
                            <dd class="col-sm-8"><?= htmlspecialchars($cliente['apellido']) ?></dd>
                            
                            <dt class="col-sm-4">CUIL/CUIT:</dt>
                            <dd class="col-sm-8"><?= $cliente['cuil'] ?></dd>
                            
                            <dt class="col-sm-4">Fecha Registro:</dt>
                            <dd class="col-sm-8"><?= date('d/m/Y', strtotime($cliente['fecha_registro'])) ?></dd>
                        </dl>
                    </div>
                </div>
            </div>
            
            <div class="col-md-6">
                <div class="card h-100">
                    <div class="card-header bg-light">
                        <h5 class="mb-0">Teléfonos</h5>
                    </div>
                    <div class="card-body">
                        <?php if ($telefonos->rowCount() > 0): ?>
                            <ul class="list-group list-group-flush">
                                <?php while ($telefono = $telefonos->fetch(PDO::FETCH_ASSOC)): ?>
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <span>
                                        <i class="bi bi-telephone me-2"></i>
                                        <?= $telefono['tipo'] ?>: 
                                        <strong><?= $telefono['codigo_area'] ?>-<?= $telefono['numero'] ?></strong>
                                    </span>
                                </li>
                                <?php endwhile; ?>
                            </ul>
                        <?php else: ?>
                            <div class="alert alert-warning mb-0">No hay teléfonos registrados</div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="card">
            <div class="card-header bg-light">
                <h5 class="mb-0">Direcciones</h5>
            </div>
            <div class="card-body">
                <?php if ($direcciones->rowCount() > 0): ?>
                    <div class="row">
                        <?php while ($direccion = $direcciones->fetch(PDO::FETCH_ASSOC)): ?>
                        <div class="col-md-6 mb-3">
                            <div class="card">
                                <div class="card-body">
                                    <h6 class="card-title">
                                        <i class="bi bi-house-door me-2"></i>Dirección
                                    </h6>
                                    <p class="card-text">
                                        <?= htmlspecialchars($direccion['calle']) ?> <?= htmlspecialchars($direccion['numero']) ?>
                                        <?php if (!empty($direccion['piso'])): ?>
                                            - Piso: <?= htmlspecialchars($direccion['piso']) ?>
                                        <?php endif; ?>
                                        <?php if (!empty($direccion['departamento'])): ?>
                                            Depto: <?= htmlspecialchars($direccion['departamento']) ?>
                                        <?php endif; ?>
                                        <br>
                                        <?= htmlspecialchars($direccion['localidad']) ?>, 
                                        <?= htmlspecialchars($direccion['provincia']) ?>
                                        <br>
                                        CP: <?= htmlspecialchars($direccion['codigo_postal']) ?>
                                    </p>
                                </div>
                            </div>
                        </div>
                        <?php endwhile; ?>
                    </div>
                <?php else: ?>
                    <div class="alert alert-warning mb-0">No hay direcciones registradas</div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php require_once './includes/footer.php'; ?>