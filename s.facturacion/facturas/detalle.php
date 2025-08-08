<?php
require_once '../includes/conexion.php';

if (!isset($_GET['id'])) {
    header("Location: listar.php");
    exit;
}

$id = $_GET['id'];

// Obtener factura
$query = "SELECT f.*, c.nombre, c.apellido, c.cuil 
          FROM facturas f
          JOIN clientes c ON f.cliente_id = c.cliente_id
          WHERE f.factura_id = ?";
$stmt = $conexion->prepare($query);
$stmt->bind_param("i", $id);
$stmt->execute();
$factura = $stmt->get_result()->fetch_assoc();

// Obtener líneas de factura
$query = "SELECT lf.*, p.nombre as producto 
          FROM lineas_factura lf
          JOIN productos p ON lf.producto_id = p.producto_id
          WHERE lf.factura_id = ?";
$stmt = $conexion->prepare($query);
$stmt->bind_param("i", $id);
$stmt->execute();
$lineas = $stmt->get_result();

require_once '../includes/header.php';
?>

<h2>Factura #<?= $factura['numero_factura'] ?></h2>

<div class="row mb-4">
    <div class="col-md-6">
        <h4>Cliente</h4>
        <p><strong>Nombre:</strong> <?= $factura['apellido'] ?>, <?= $factura['nombre'] ?></p>
        <p><strong>CUIL:</strong> <?= $factura['cuil'] ?></p>
    </div>
    <div class="col-md-6">
        <h4>Factura</h4>
        <p><strong>Fecha:</strong> <?= date('d/m/Y', strtotime($factura['fecha'])) ?></p>
        <p><strong>Número:</strong> <?= $factura['numero_factura'] ?></p>
    </div>
</div>

<h4>Detalle</h4>
<table class="table table-bordered">
    <thead>
        <tr>
            <th>Producto</th>
            <th>Cantidad</th>
            <th>Precio Unitario</th>
            <th>Subtotal</th>
        </tr>
    </thead>
    <tbody>
        <?php while($linea = $lineas->fetch_assoc()): ?>
        <tr>
            <td><?= $linea['producto'] ?></td>
            <td><?= $linea['cantidad'] ?></td>
            <td>$<?= number_format($linea['precio_unitario'], 2) ?></td>
            <td>$<?= number_format($linea['total_linea'], 2) ?></td>
        </tr>
        <?php endwhile; ?>
    </tbody>
    <tfoot>
        <tr>
            <th colspan="3" class="text-end">Total:</th>
            <th>$<?= number_format($factura['total'], 2) ?></th>
        </tr>
    </tfoot>
</table>

<a href="listar.php" class="btn btn-secondary">Volver</a>

<?php require_once '../includes/footer.php'; ?>