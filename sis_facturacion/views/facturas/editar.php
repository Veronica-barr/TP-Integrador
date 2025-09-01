<?php
require_once __DIR__ . '/../../controller/FacturaController.php';
require_once __DIR__ . '/../../controller/ClienteController.php';
require_once __DIR__ . '/../../controller/ProductoController.php';

$facturaController = new FacturaController();
$clienteController = new ClienteController();
$productoController = new ProductoController();

$factura_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($factura_id <= 0) {
    header('Location: list.php?mensaje=Factura no válida&tipo=danger');
    exit;
}

// Obtener la factura existente
$factura = $facturaController->obtenerFacturaPorId($factura_id);

if (!$factura) {
    header('Location: list.php?mensaje=Factura no encontrada&tipo=danger');
    exit;
}

// Verificar que la factura esté pendiente para poder editarla
if ($factura->estado != 'PENDIENTE') {
    header('Location: list.php?mensaje=Solo se pueden editar facturas con estado PENDIENTE&tipo=warning');
    exit;
}

$clientes = $clienteController->listarClientes();
$productos = $productoController->listar();

$mensaje = '';
$tipoMensaje = '';

// Procesar el formulario cuando se envía
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $data = [
            'cliente_id' => $_POST['cliente_id'],
            'fecha' => $_POST['fecha'],
            'lineas' => []
        ];
        
        // Procesar líneas de factura
        if (isset($_POST['producto_id']) && is_array($_POST['producto_id'])) {
            foreach ($_POST['producto_id'] as $index => $producto_id) {
                if (!empty($producto_id) && !empty($_POST['cantidad'][$index])) {
                    $data['lineas'][] = [
                        'producto_id' => $producto_id,
                        'cantidad' => $_POST['cantidad'][$index]
                    ];
                }
            }
        }
        
        // Actualizar la factura
        $resultado = $facturaController->actualizarFactura($factura_id, $data);
        
        if ($resultado) {
            $mensaje = "Factura actualizada exitosamente";
            $tipoMensaje = 'success';
            
            // Redirigir después de 2 segundos
            header("Refresh: 2; URL=show.php?id=" . $factura_id);
        }
    } catch (Exception $e) {
        $mensaje = "Error al actualizar la factura: " . $e->getMessage();
        $tipoMensaje = 'danger';
    }
}

// Verificar y cargar los archivos de inclusión
$headerPath = __DIR__ . '/.includes/header.php';
$navigationPath = __DIR__ . '/.includes/navigation.php';
$footerPath = __DIR__ . '/.includes/footer.php';

// Incluir header si existe
if (file_exists($headerPath)) {
    include $headerPath;
} else {
    // Header mínimo si no existe el archivo
    echo '<!DOCTYPE html><html lang="es"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0"><title>Editar Factura</title><link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet"><link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css"><style>.product-row { margin-bottom: 10px; } .totals-container { background-color: #f8f9fa; padding: 15px; border-radius: 5px; }</style></head><body>';
}

// Incluir navegación si existe
if (file_exists($navigationPath)) {
    include $navigationPath;
}
?>

<div class="container-fluid p-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Editar Factura #<?php echo $factura->numero_factura; ?></h2>
        <a href="list.php" class="btn btn-secondary">
            <i class="bi bi-arrow-left"></i> Volver al Listado
        </a>
    </div>

    <?php if ($mensaje): ?>
        <div class="alert alert-<?php echo $tipoMensaje; ?> alert-dismissible fade show" role="alert">
            <?php echo $mensaje; ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <form method="POST" id="facturaForm">
        <div class="row">
            <div class="col-md-6">
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Información de la Factura</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label for="cliente_id" class="form-label">Cliente <span class="text-danger">*</span></label>
                            <select class="form-select" id="cliente_id" name="cliente_id" required>
                                <option value="">Seleccionar cliente...</option>
                                <?php foreach ($clientes as $cliente): ?>
                                    <option value="<?php echo $cliente->cliente_id; ?>" <?php echo ($cliente->cliente_id == $factura->cliente_id) ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($cliente->nombre . ' ' . $cliente->apellido . ' (' . $cliente->cuil . ')'); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <div class="mb-3">
                            <label for="fecha" class="form-label">Fecha <span class="text-danger">*</span></label>
                            <input type="date" class="form-control" id="fecha" name="fecha" 
                                   value="<?php echo $factura->fecha; ?>" required>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-md-6">
                <div class="card mb-4">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0">Totales</h5>
                    </div>
                    <div class="card-body totals-container">
                        <div class="row mb-2">
                            <div class="col-6">
                                <strong>Subtotal:</strong>
                            </div>
                            <div class="col-6 text-end">
                                $<span id="subtotal"><?php echo number_format($factura->subtotal, 2); ?></span>
                            </div>
                        </div>
                        <div class="row mb-2">
                            <div class="col-6">
                                <strong>Impuestos:</strong>
                            </div>
                            <div class="col-6 text-end">
                                $<span id="impuestos"><?php echo number_format($factura->impuesto, 2); ?></span>
                            </div>
                        </div>
                        <div class="row mb-2">
                            <div class="col-6">
                                <strong>Total:</strong>
                            </div>
                            <div class="col-6 text-end">
                                $<span id="total"><?php echo number_format($factura->total, 2); ?></span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">Líneas de Factura</h5>
                <button type="button" class="btn btn-sm btn-primary" id="agregarLinea">
                    <i class="bi bi-plus-circle"></i> Agregar Producto
                </button>
            </div>
            <div class="card-body">
                <div id="lineasFactura">
                    <!-- Las líneas de factura existentes se cargarán aquí -->
                    <?php foreach ($factura->lineas as $index => $linea): ?>
                    <div class="row product-row align-items-end" id="linea-<?php echo $index; ?>">
                        <div class="col-md-5">
                            <label class="form-label">Producto</label>
                            <select class="form-select producto-select" name="producto_id[]" required onchange="actualizarPrecio(<?php echo $index; ?>)">
                                <option value="">Seleccionar producto...</option>
                                <?php foreach ($productos as $producto): ?>
                                    <option value="<?php echo $producto->producto_id; ?>" 
                                            data-precio="<?php echo $producto->precio_unitario; ?>"
                                            data-impuesto="<?php echo $producto->porcentaje_impuesto; ?>"
                                            data-stock="<?php echo $producto->stock; ?>"
                                            <?php echo ($producto->producto_id == $linea->producto_id) ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($producto->nombre . ' (' . $producto->codigo . ') - Stock: ' . $producto->stock); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Cantidad</label>
                            <input type="number" class="form-control cantidad-input" name="cantidad[]" min="1" 
                                   value="<?php echo $linea->cantidad; ?>" 
                                   onchange="calcularTotalesLinea(<?php echo $index; ?>)" 
                                   oninput="validarStock(<?php echo $index; ?>)" required>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Precio Unitario</label>
                            <input type="text" class="form-control precio-input" id="precio-<?php echo $index; ?>" 
                                   value="<?php echo number_format($linea->precio_unitario, 2); ?>" readonly>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Total Línea</label>
                            <input type="text" class="form-control total-linea-input" id="total-linea-<?php echo $index; ?>" 
                                   value="<?php echo number_format($linea->total_linea, 2); ?>" readonly>
                        </div>
                        <div class="col-md-1">
                            <button type="button" class="btn btn-danger" onclick="eliminarLinea(<?php echo $index; ?>)">
                                <i class="bi bi-trash"></i>
                            </button>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>

        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
            <button type="submit" class="btn btn-primary btn-lg">
                <i class="bi bi-check-circle"></i> Actualizar Factura
            </button>
        </div>
    </form>
</div>

<script>
// Almacenar información de productos para cálculos
const productos = <?php echo json_encode($productos); ?>;

// Contador para IDs únicos de líneas (iniciar después de las líneas existentes)
let contadorLineas = <?php echo count($factura->lineas); ?>;

// Función para agregar una nueva línea de producto
function agregarLineaProducto() {
    const lineasContainer = document.getElementById('lineasFactura');
    const lineaId = contadorLineas++;
    
    const nuevaLinea = document.createElement('div');
    nuevaLinea.className = 'row product-row align-items-end';
    nuevaLinea.id = `linea-${lineaId}`;
    nuevaLinea.innerHTML = `
        <div class="col-md-5">
            <label class="form-label">Producto</label>
            <select class="form-select producto-select" name="producto_id[]" required onchange="actualizarPrecio(${lineaId})">
                <option value="">Seleccionar producto...</option>
                <?php foreach ($productos as $producto): ?>
                    <option value="<?php echo $producto->producto_id; ?>" 
                            data-precio="<?php echo $producto->precio_unitario; ?>"
                            data-impuesto="<?php echo $producto->porcentaje_impuesto; ?>"
                            data-stock="<?php echo $producto->stock; ?>">
                        <?php echo htmlspecialchars($producto->nombre . ' (' . $producto->codigo . ') - Stock: ' . $producto->stock); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-md-2">
            <label class="form-label">Cantidad</label>
            <input type="number" class="form-control cantidad-input" name="cantidad[]" min="1" value="1" 
                   onchange="calcularTotalesLinea(${lineaId})" oninput="validarStock(${lineaId})" required>
        </div>
        <div class="col-md-2">
            <label class="form-label">Precio Unitario</label>
            <input type="text" class="form-control precio-input" id="precio-${lineaId}" readonly>
        </div>
        <div class="col-md-2">
            <label class="form-label">Total Línea</label>
            <input type="text" class="form-control total-linea-input" id="total-linea-${lineaId}" readonly>
        </div>
        <div class="col-md-1">
            <button type="button" class="btn btn-danger" onclick="eliminarLinea(${lineaId})">
                <i class="bi bi-trash"></i>
            </button>
        </div>
    `;
    
    lineasContainer.appendChild(nuevaLinea);
    calcularTotales();
}

// Función para eliminar una línea
function eliminarLinea(lineaId) {
    const linea = document.getElementById(`linea-${lineaId}`);
    if (linea) {
        linea.remove();
        calcularTotales();
    }
}

// Función para actualizar el precio cuando se selecciona un producto
function actualizarPrecio(lineaId) {
    const select = document.querySelector(`#linea-${lineaId} .producto-select`);
    const precioInput = document.getElementById(`precio-${lineaId}`);
    const selectedOption = select.options[select.selectedIndex];
    
    if (selectedOption && selectedOption.value) {
        const precio = parseFloat(selectedOption.getAttribute('data-precio'));
        precioInput.value = precio.toFixed(2);
        
        // Calcular total de la línea
        calcularTotalesLinea(lineaId);
    } else {
        precioInput.value = '';
    }
}

// Función para validar stock disponible
function validarStock(lineaId) {
    const select = document.querySelector(`#linea-${lineaId} .producto-select`);
    const cantidadInput = document.querySelector(`#linea-${lineaId} .cantidad-input`);
    const selectedOption = select.options[select.selectedIndex];
    
    if (selectedOption && selectedOption.value) {
        const stock = parseInt(selectedOption.getAttribute('data-stock'));
        const cantidad = parseInt(cantidadInput.value);
        
        if (cantidad > stock) {
            alert('No hay suficiente stock disponible. Stock actual: ' + stock);
            cantidadInput.value = stock;
        }
        
        calcularTotalesLinea(lineaId);
    }
}

// Función para calcular el total de una línea individual
function calcularTotalesLinea(lineaId) {
    const select = document.querySelector(`#linea-${lineaId} .producto-select`);
    const cantidadInput = document.querySelector(`#linea-${lineaId} .cantidad-input`);
    const totalLineaInput = document.getElementById(`total-linea-${lineaId}`);
    
    const selectedOption = select.options[select.selectedIndex];
    
    if (selectedOption && selectedOption.value && cantidadInput.value) {
        const precio = parseFloat(selectedOption.getAttribute('data-precio'));
        const cantidad = parseInt(cantidadInput.value);
        const impuestoPorcentaje = parseFloat(selectedOption.getAttribute('data-impuesto'));
        
        const subtotal = precio * cantidad;
        const impuesto = subtotal * (impuestoPorcentaje / 100);
        const total = subtotal + impuesto;
        
        totalLineaInput.value = total.toFixed(2);
        calcularTotales();
    } else {
        totalLineaInput.value = '0.00';
    }
}

// Función para calcular los totales generales
function calcularTotales() {
    let subtotal = 0;
    let impuestos = 0;
    let total = 0;
    
    // Recorrer todas las líneas
    const lineas = document.querySelectorAll('.product-row');
    lineas.forEach((linea, index) => {
        const totalLineaInput = document.getElementById(`total-linea-${index}`);
        const select = document.querySelector(`#linea-${index} .producto-select`);
        const cantidadInput = document.querySelector(`#linea-${index} .cantidad-input`);
        
        if (select && select.value && cantidadInput && cantidadInput.value && totalLineaInput) {
            const selectedOption = select.options[select.selectedIndex];
            const precio = parseFloat(selectedOption.getAttribute('data-precio'));
            const cantidad = parseInt(cantidadInput.value);
            const impuestoPorcentaje = parseFloat(selectedOption.getAttribute('data-impuesto'));
            
            const lineaSubtotal = precio * cantidad;
            const lineaImpuesto = lineaSubtotal * (impuestoPorcentaje / 100);
            
            subtotal += lineaSubtotal;
            impuestos += lineaImpuesto;
        }
    });
    
    total = subtotal + impuestos;
    
    // Actualizar la UI
    document.getElementById('subtotal').textContent = subtotal.toFixed(2);
    document.getElementById('impuestos').textContent = impuestos.toFixed(2);
    document.getElementById('total').textContent = total.toFixed(2);
}

// Event listeners
document.getElementById('agregarLinea').addEventListener('click', agregarLineaProducto);

// Inicializar precios y totales para las líneas existentes al cargar la página
document.addEventListener('DOMContentLoaded', function() {
    // Inicializar cada línea existente
    <?php foreach ($factura->lineas as $index => $linea): ?>
        actualizarPrecio(<?php echo $index; ?>);
    <?php endforeach; ?>
    
    calcularTotales();
});
</script>

<?php
// Incluir footer si existe
if (file_exists($footerPath)) {
    include $footerPath;
} else {
    include __DIR__ . '/../../includes/footer.php';
}
?>