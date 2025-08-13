<?php require_once './includes/header.php'; ?>

<h2>Listado de Facturas</h2>
<a href="index.php?module=facturas&action=create" class="btn btn-primary mb-3">Nueva Factura</a>

<form method="GET" class="mb-3">
    <input type="hidden" name="module" value="facturas">
    <input type="hidden" name="action" value="list">
    <div class="row">
        <div class="col-md-4">
            <label for="fecha_inicio" class="form-label">Fecha Inicio</label>
            <input type="date" class="form-control" id="fecha_inicio" name="fecha_inicio" required>
        </div>
        <div class="col-md-4">
            <label for="fecha_fin" class="form-label">Fecha Fin</label>
            <input type="date" class="form-control" id="fecha_fin" name="fecha_fin" required>
        </div>
        <div class="col-md-4 d-flex align-items-end">
            <button type="submit" class="btn btn-primary">Filtrar</button>
            <a href="index.php?module=facturas&action=list" class="btn btn-secondary ms-2">Limpiar</a>
        </div>
    </div>
</form>

<table class="table table-striped">
    <thead>
        <tr>
            <th>N° Factura</th>
            <th>Cliente</th>
            <th>Fecha</th>
            <th>Total</th>
            <th>Acciones</th>
        </tr>
    </thead>
    <tbody>
        <?php while ($factura = $facturas->fetch(PDO::FETCH_ASSOC)): ?>
        <tr>
            <td><?php echo $factura['numero_factura']; ?></td>
            <td><?php echo $factura['cliente_nombre']; ?></td>
            <td><?php echo $factura['fecha']; ?></td>
            <td>$<?php echo number_format($factura['total'], 2); ?></td>
            <td>
                <a href="index.php?module=facturas&action=show&id=<?php echo $factura['factura_id']; ?>" class="btn btn-info btn-sm">
                    <i class="bi bi-eye"></i> Ver Detalle
                </a>
            </td>
        </tr>
        <?php endwhile; ?>
    </tbody>
</table>

<?php require_once './includes/footer.php'; ?>