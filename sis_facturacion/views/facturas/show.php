<?php require_once './includes/header.php'; ?>

<h2>Detalle de Factura</h2>

<div class="card mb-4">
    <div class="card-header">
        <h4>Factura #<?php echo $factura['numero_factura']; ?></h4>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-6">
                <h5>Datos del Cliente</h5>
                <p><strong>Nombre:</strong> <?php echo $factura['cliente_nombre']; ?></p>
                <p><strong>CUIL:</strong> <?php echo $factura['cuil']; ?></p>
            </div>
            <div class="col-md-6">
                <h5>Datos de la Factura</h5>
                <p><strong>Fecha:</strong> <?php echo $factura['fecha']; ?></p>
                <p><strong>Estado:</strong> <?php echo $factura['estado']; ?></p>
            </div>
        </div>
    </div>
</div>

<h4>Productos</h4>
<table class="table table-striped">
    <thead>
        <tr>
            <th>Código</th>
            <th>Producto</th>
            <th>Cantidad</th>
            <th>Precio Unitario</th>
            <th>Impuesto (%)</th>
            <th>Subtotal</th>
            <th>Total</th>
        </tr>
    </thead>
    <tbody>
        <?php while ($linea = $lineas->fetch(PDO::FETCH_ASSOC)): ?>
        <tr>
            <td><?php echo $linea['producto_codigo']; ?></td>
            <td><?php echo $linea['producto_nombre']; ?></td>
            <td><?php echo $linea['cantidad']; ?></td>
            <td>$<?php echo number_format($linea['precio_unitario'], 2); ?></td>
            <td><?php echo $linea['porcentaje_impuesto']; ?>%</td>
            <td>$<?php echo number_format($linea['subtotal'], 2); ?></td>
            <td>$<?php echo number_format($linea['total_linea'], 2); ?></td>
        </tr>
        <?php endwhile; ?>
    </tbody>
    <tfoot>
        <tr>
            <td colspan="5" class="text-end"><strong>Subtotal:</strong></td>
            <td>$<?php echo number_format($factura['subtotal'], 2); ?></td>
            <td></td>
        </tr>
        <tr>
            <td colspan="5" class="text-end"><strong>Impuesto:</strong></td>
            <td>$<?php echo number_format($factura['impuesto'], 2); ?></td>
            <td></td>
        </tr>
        <tr>
            <td colspan="5" class="text-end"><strong>Total:</strong></td>
            <td></td>
            <td>$<?php echo number_format($factura['total'], 2); ?></td>
        </tr>
    </tfoot>
</table>

<a href="index.php?module=facturas&action=list" class="btn btn-secondary">Volver</a>

<?php require_once './includes/footer.php'; ?>