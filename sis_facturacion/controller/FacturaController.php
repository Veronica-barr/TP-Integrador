<?php
class FacturaController {
    private $facturaModel;
    private $clienteModel;
    private $productoModel;

    public function __construct($facturaModel, $clienteModel, $productoModel) {
        $this->facturaModel = $facturaModel;
        $this->clienteModel = $clienteModel;
        $this->productoModel = $productoModel;
    }

    public function list() {
        if (isset($_GET['fecha_inicio']) && isset($_GET['fecha_fin'])) {
            $fecha_inicio = $_GET['fecha_inicio'];
            $fecha_fin = $_GET['fecha_fin'];
            $facturas = $this->facturaModel->getFacturasByDateRange($fecha_inicio, $fecha_fin);
        } else {
            $facturas = $this->facturaModel->getAllFacturas();
        }
        require_once 'views/facturas/list.php';
    }

    public function create() {
        $clientes = $this->clienteModel->getAllClientes();
        $productos = $this->productoModel->getAllProductos();
        require_once 'views/facturas/create.php';
    }

    public function store() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Validar datos básicos
            if (empty($_POST['cliente_id']) || empty($_POST['numero_factura']) || empty($_POST['fecha'])) {
                $_SESSION['error'] = "Todos los campos básicos son requeridos";
                header('Location: index.php?module=facturas&action=create');
                exit;
            }

            $cliente_id = (int)$_POST['cliente_id'];
            $numero_factura = $_POST['numero_factura'];
            $fecha = $_POST['fecha'];
            
            // Validar productos
            if (empty($_POST['productos']) || !is_array($_POST['productos'])) {
                $_SESSION['error'] = "Debe agregar al menos un producto a la factura";
                header('Location: index.php?module=facturas&action=create');
                exit;
            }

            // Calcular totales
            $subtotal = 0;
            $impuesto = 0;
            $total = 0;
            
            $this->facturaModel->getConnection()->beginTransaction();
            
            try {
                // Crear factura primero con totales en 0 (se actualizarán después)
                $factura_id = $this->facturaModel->createFactura($cliente_id, $numero_factura, $fecha, 0, 0, 0);
                
                if (!$factura_id) {
                    throw new Exception("No se pudo crear la factura");
                }
                
                // Procesar cada producto
                foreach ($_POST['productos'] as $productoData) {
                    if (empty($productoData['producto_id']) || empty($productoData['cantidad'])) {
                        throw new Exception("Datos de producto incompletos");
                    }
                    
                    $producto_id = (int)$productoData['producto_id'];
                    $cantidad = (int)$productoData['cantidad'];
                    
                    // Obtener datos del producto
                    $producto = $this->productoModel->getProductoById($producto_id);
                    if (!$producto) {
                        throw new Exception("Producto no encontrado");
                    }
                    
                    // Validar stock
                    if ($producto['stock'] < $cantidad) {
                        throw new Exception("Stock insuficiente para el producto: " . $producto['nombre']);
                    }
                    
                    $precio_unitario = (float)$producto['precio_unitario'];
                    $porcentaje_impuesto = (float)$producto['porcentaje_impuesto'];
                    
                    // Calcular valores de la línea
                    $subtotal_linea = $precio_unitario * $cantidad;
                    $monto_impuesto = $subtotal_linea * ($porcentaje_impuesto / 100);
                    $total_linea = $subtotal_linea + $monto_impuesto;
                    
                    // Agregar línea de factura
                    $result = $this->facturaModel->addLineaFactura(
                        $factura_id,
                        $producto_id,
                        $cantidad,
                        $precio_unitario,
                        $porcentaje_impuesto,
                        $subtotal_linea,
                        $monto_impuesto,
                        $total_linea
                    );
                    
                    if (!$result) {
                        throw new Exception("Error al agregar línea de factura");
                    }
                    
                    // Actualizar stock
                    $result = $this->facturaModel->updateStockProducto($producto_id, $cantidad);
                    if (!$result) {
                        throw new Exception("Error al actualizar el stock del producto");
                    }
                    
                    // Acumular totales
                    $subtotal += $subtotal_linea;
                    $impuesto += $monto_impuesto;
                    $total += $total_linea;
                }
                
                // Actualizar factura con los totales reales
                $result = $this->facturaModel->updateTotalesFactura($factura_id, $subtotal, $impuesto, $total);
                if (!$result) {
                    throw new Exception("Error al actualizar los totales de la factura");
                }
                
                $this->facturaModel->getConnection()->commit();
                
                $_SESSION['success'] = "Factura #$numero_factura creada correctamente";
                header('Location: index.php?module=facturas&action=list');
                exit;
                
            } catch (Exception $e) {
                $this->facturaModel->getConnection()->rollBack();
                $_SESSION['error'] = "Error al crear la factura: " . $e->getMessage();
                error_log("Error en FacturaController::store(): " . $e->getMessage());
                header('Location: index.php?module=facturas&action=create');
                exit;
            }
        }
    }

    public function show() {
        if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
            $_SESSION['error'] = "ID de factura inválido";
            header('Location: index.php?module=facturas&action=list');
            exit;
        }

        $id = (int)$_GET['id'];
        $factura = $this->facturaModel->getFacturaById($id);
        
        if (!$factura) {
            $_SESSION['error'] = "Factura no encontrada";
            header('Location: index.php?module=facturas&action=list');
            exit;
        }

        $lineas = $this->facturaModel->getLineasFactura($id);
        require_once 'views/facturas/show.php';
    }

    public function delete() {
        if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
            $_SESSION['error'] = "ID de factura inválido";
            header('Location: index.php?module=facturas&action=list');
            exit;
        }

        $id = (int)$_GET['id'];
        $result = $this->facturaModel->deleteFactura($id);
        
        if ($result) {
            $_SESSION['success'] = "Factura eliminada correctamente";
        } else {
            $_SESSION['error'] = "Error al eliminar la factura";
        }
        
        header('Location: index.php?module=facturas&action=list');
        exit;
    }
}  