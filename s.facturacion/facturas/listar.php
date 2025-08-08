<?php
require_once '../includes/conexion.php';
require_once '../includes/header.php';

// Obtener fechas del filtro si existen
$fecha_desde = $_GET['fecha_desde'] ?? '';
$fecha_hasta = $_GET['fecha_hasta'] ?? '';

// Construir la consulta con filtros
$query = "SELECT f.*, c.nombre, c.apellido 
          FROM facturas f
          JOIN clientes c ON f.cliente_id = c.cliente_id";

// Agregar condiciones de filtro si hay fechas
$conditions = [];
$params = [];
$types = '';

if (!empty($fecha_desde)) {
    $conditions[] = "f.fecha >= ?";
    $params[] = $fecha_desde;
    $types .= 's';
}

if (!empty($fecha_hasta)) {
    $conditions[] = "f.fecha <= ?";
    $params[] = $fecha_hasta;
    $types .= 's';
}

if (!empty($conditions)) {
    $query .= " WHERE " . implode(" AND ", $conditions);
}

$query .= " ORDER BY f.fecha DESC";

// Preparar y ejecutar la consulta
$stmt = $conexion->prepare($query);
if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$facturas = $stmt->get_result();
?>

<h2>Facturas</h2>
<a href="nueva.php" class="btn btn-success mb-3">Nueva Factura</a>

<!-- Formulario de filtrado por fechas -->
<form method="get" class="row g-3 mb-4">
    <div class="col-md-3">
        <label for="fecha_desde" class="form-label">Desde</label>
        <input type="date" class="form-control" id="fecha_desde" name="fecha_desde" value="<?= $fecha_desde ?>">
    </div>
    <div class="col-md-3">
        <label for="fecha_hasta" class="form-label">Hasta</label>
        <input type="date" class="form-control" id="fecha_hasta" name="fecha_hasta" value="<?= $fecha_hasta ?>">
    </div>
    <div class="col-md-2 d-flex align-items-end">
        <button type="submit" class="btn btn-primary">Filtrar</button>
        <?php if (!empty($fecha_desde) || !empty($fecha_hasta)): ?>
            <a href="listar.php" class="btn btn-secondary ms-2">Limpiar</a>
        <?php endif; ?>
    </div>
</form>

<!-- Tabla de facturas (el resto del código permanece igual) -->
<table class="table table-striped">
    <!-- ... contenido existente de la tabla ... -->
</table>

<?php require_once '../includes/footer.php'; ?>