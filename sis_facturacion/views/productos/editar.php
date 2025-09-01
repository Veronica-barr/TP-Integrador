<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$baseDir = __DIR__ . '/../..';
require_once $baseDir . '/controller/ProductoController.php';

$productoController = new ProductoController();
$producto = null;

// Obtener el producto si se proporciona un ID
if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $producto = $productoController->ver($id);
    
    if (!$producto) {
        header('Location: list.php?mensaje=Producto no encontrado&tipo=danger');
        exit();
    }
}

// Procesar el formulario de edición
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $datosProducto = [
        'codigo' => $_POST['codigo'],
        'nombre' => $_POST['nombre'],
        'descripcion' => $_POST['descripcion'],
        'precio_unitario' => floatval($_POST['precio_unitario']),
        'porcentaje_impuesto' => floatval($_POST['porcentaje_impuesto']),
        'stock' => intval($_POST['stock'])
    ];
    
    $id = $_POST['producto_id'];
    
    if ($productoController->actualizar($id, $datosProducto)) {
        header('Location: list.php?mensaje=Producto actualizado correctamente&tipo=success');
        exit();
    } else {
        $error = "Error al actualizar el producto. Intente nuevamente.";
    }
}

// Definir rutas para includes
$headerPath = $baseDir . '/includes/header.php';
$navigationPath = $baseDir . '/includes/navigation.php';

// Verificar si los archivos existen
if (!file_exists($headerPath)) {
    $headerPath = $baseDir . '/.includes/header.php';
}
if (!file_exists($navigationPath)) {
    $navigationPath = $baseDir . '/.includes/navigation.php';
}

// Incluir header y navegación
if (file_exists($headerPath)) {
    include $headerPath;
} else {
    echo "<!DOCTYPE html><html lang='es'><head><meta charset='UTF-8'><title>Editar Producto</title>";
    echo "<link href='https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css' rel='stylesheet'>";
    echo "<link rel='stylesheet' href='https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css'>";
    echo "<style>
        .card { border-radius: 10px; box-shadow: 0 4px 8px rgba(0,0,0,0.1); }
        .form-control:focus { border-color: #4e73df; box-shadow: 0 0 0 0.2rem rgba(78, 115, 223, 0.25); }
        .btn-primary { background-color: #4e73df; border-color: #4e73df; }
        .btn-primary:hover { background-color: #2e59d9; border-color: #2e59d9; }
        .required-label:after { content: '*'; color: red; margin-left: 4px; }
    </style>";
    echo "</head><body>";
}

if (file_exists($navigationPath)) {
    include $navigationPath;
}
?>

<div class="container-fluid p-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header bg-warning text-dark">
                    <h4 class="mb-0"><i class="bi bi-pencil-square me-2"></i>Editar Producto</h4>
                </div>
                <div class="card-body">
                    <?php if (isset($error)): ?>
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <?php echo $error; ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>
                    
                    <form method="POST" action="editar.php">
                        <input type="hidden" name="producto_id" value="<?php echo $producto->producto_id; ?>">
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="codigo" class="form-label required-label">Código</label>
                                <input type="text" class="form-control" id="codigo" name="codigo" 
                                       value="<?php echo htmlspecialchars($producto->codigo ?? ''); ?>" required>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="nombre" class="form-label required-label">Nombre</label>
                                <input type="text" class="form-control" id="nombre" name="nombre" 
                                       value="<?php echo htmlspecialchars($producto->nombre ?? ''); ?>" required>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="descripcion" class="form-label">Descripción</label>
                            <textarea class="form-control" id="descripcion" name="descripcion" rows="3"><?php echo htmlspecialchars($producto->descripcion ?? ''); ?></textarea>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label for="precio_unitario" class="form-label required-label">Precio Unitario</label>
                                <div class="input-group">
                                    <span class="input-group-text">$</span>
                                    <input type="number" class="form-control" id="precio_unitario" name="precio_unitario" 
                                           step="0.01" min="0" value="<?php echo $producto->precio_unitario; ?>" required>
                                </div>
                            </div>
                            
                            <div class="col-md-4 mb-3">
                                <label for="porcentaje_impuesto" class="form-label required-label">Impuesto (%)</label>
                                <div class="input-group">
                                    <input type="number" class="form-control" id="porcentaje_impuesto" name="porcentaje_impuesto" 
                                           step="0.01" min="0" max="100" value="<?php echo $producto->porcentaje_impuesto; ?>" required>
                                    <span class="input-group-text">%</span>
                                </div>
                            </div>
                            
                            <div class="col-md-4 mb-3">
                                <label for="stock" class="form-label required-label">Stock</label>
                                <input type="number" class="form-control" id="stock" name="stock" 
                                       min="0" value="<?php echo $producto->stock; ?>" required>
                            </div>
                        </div>
                        
                        <div class="d-flex justify-content-between mt-4">
                            <a href="list.php" class="btn btn-secondary">
                                <i class="bi bi-arrow-left me-1"></i> Cancelar
                            </a>
                            <button type="submit" class="btn btn-warning">
                                <i class="bi bi-check-circle me-1"></i> Actualizar Producto
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Validación básica del formulario
document.addEventListener('DOMContentLoaded', function() {
    const formulario = document.querySelector('form');
    
    formulario.addEventListener('submit', function(e) {
        let esValido = true;
        const requeridos = formulario.querySelectorAll('[required]');
        
        requeridos.forEach(function(campo) {
            if (!campo.value.trim()) {
                esValido = false;
                campo.classList.add('is-invalid');
            } else {
                campo.classList.remove('is-invalid');
            }
        });
        
        if (!esValido) {
            e.preventDefault();
            alert('Por favor, complete todos los campos requeridos.');
        }
    });
});
</script>

<?php
// Incluir footer
$footerPath = $baseDir . '/includes/footer.php';
if (!file_exists($footerPath)) {
    $footerPath = $baseDir . '/.includes/footer.php';
}

if (file_exists($footerPath)) {
    include $footerPath;
} else {
    echo "</body></html>";
}
?>