<?php
require_once '../includes/conexion.php';
require_once '../includes/header.php';

$query = "SELECT * FROM clientes WHERE activo = 1 ORDER BY apellido, nombre";
$clientes = $conexion->query($query);
?>

<h2>Clientes</h2>
<a href="editar.php" class="btn btn-success mb-3">Nuevo Cliente</a>

<table class="table table-striped">
    <thead>
        <tr>
            <th>ID</th>
            <th>Apellido y Nombre</th>
            <th>CUIL</th>
            <th>Teléfonos</th>
            <th>Direcciones</th>
            <th>Acciones</th>
        </tr>
    </thead>
    <tbody>
        <?php while($cliente = $clientes->fetch_assoc()): 
            // Obtener teléfonos del cliente
            $query = "SELECT * FROM telefonos WHERE cliente_id = ?";
            $stmt = $conexion->prepare($query);
            $stmt->bind_param("i", $cliente['cliente_id']);
            $stmt->execute();
            $telefonos = $stmt->get_result();
            
            // Obtener direcciones del cliente
            $query = "SELECT d.*, l.nombre as localidad, p.nombre as provincia 
                      FROM direcciones d
                      JOIN localidades l ON d.localidad_id = l.localidad_id
                      JOIN provincias p ON l.provincia_id = p.provincia_id
                      WHERE d.cliente_id = ?";
            $stmt = $conexion->prepare($query);
            $stmt->bind_param("i", $cliente['cliente_id']);
            $stmt->execute();
            $direcciones = $stmt->get_result();
        ?>
        <tr>
            <td><?= $cliente['cliente_id'] ?></td>
            <td><?= $cliente['apellido'] ?>, <?= $cliente['nombre'] ?></td>
            <td><?= $cliente['cuil'] ?></td>
            <td>
                <?php while($telefono = $telefonos->fetch_assoc()): ?>
                    <div><?= $telefono['tipo'] ?>: <?= $telefono['codigo_area'] ?>-<?= $telefono['numero'] ?></div>
                <?php endwhile; ?>
            </td>
            <td>
                <?php while($direccion = $direcciones->fetch_assoc()): ?>
                    <div>
                        <?= $direccion['calle'] ?> <?= $direccion['numero'] ?>
                        <?= $direccion['piso'] ? 'Piso '.$direccion['piso'] : '' ?>
                        <?= $direccion['departamento'] ? 'Depto '.$direccion['departamento'] : '' ?>
                        - <?= $direccion['localidad'] ?>, <?= $direccion['provincia'] ?>
                    </div>
                <?php endwhile; ?>
            </td>
            <td>
                <a href="editar.php?id=<?= $cliente['cliente_id'] ?>" class="btn btn-sm btn-warning">Editar</a>
            </td>
        </tr>
        <?php endwhile; ?>
    </tbody>
</table>

<?php require_once '../includes/footer.php'; ?>