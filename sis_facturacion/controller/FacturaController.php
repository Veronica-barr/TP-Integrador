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
        $fecha_inicio = $_GET['fecha_inicio'] ?? date('Y-m-01');
        $fecha_fin = $_GET['fecha_fin'] ?? date('Y-m-t');
        
        $facturas = $this->facturaModel->getFacturasByDateRange($fecha_inicio, $fecha_fin);
        require_once 'views/facturas/list.php';
    }

    public function create() {
        $clientes = $this->clienteModel->getAllClientes();
        $productos = $this->productoModel->getAllProductos();
        require_once 'views/facturas/create.php';
    }

public function store() {
    // Verifica si se envió el formulario
    // POST es el método de envío del formulario
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        try {
            // Validaciones básicas (se mantienen igual)
            if (empty($_POST['cliente_id'])) {
                throw new Exception("Seleccione un cliente");
            }
            
            if (empty($_POST['numero_factura'])) {
                throw new Exception("Ingrese un número de factura");
            }
            
            if (empty($_POST['fecha'])) {
                throw new Exception("Seleccione una fecha");
            }
            
            if (empty($_POST['productos']) || count($_POST['productos']) == 0) {
                throw new Exception("Agregue al menos un producto");
            }

            // crea la factura en la base de datos
            // comienza una transacción para asegurar la integridad de los datos
            $factura_id = $this->facturaModel->createFactura(
                $_POST['cliente_id'],
                $_POST['numero_factura'],
                $_POST['fecha'],
                0, 0, 0  //valores temporales para subtotal, impuesto y total
            );
            
            if (!$factura_id) {
                throw new Exception("Error al crear la factura");
            }
//variables para acumular totales
            $subtotal = 0;
            $impuesto = 0;
            $total = 0;
            
            // Procesa cada producto
            // $_POST['productos'] es un array de productos seleccionados
            foreach ($_POST['productos'] as $producto) {
                if (empty($producto['producto_id']) || empty($producto['cantidad'])) {
                    continue;
                }
                
                $producto_id = $producto['producto_id'];
                $cantidad = (int)$producto['cantidad'];
                
                // Verifica si el producto existe y tiene stock suficiente

                $producto_info = $this->productoModel->getProductoById($producto_id);

                // Si el producto no existe o no tiene stock, lanza una excepción
                if (!$producto_info) {
                    // Eliminar la factura creada si hay error
                    $this->facturaModel->deleteFactura($factura_id);//Rollback, es para deshacer cambios
                    throw new Exception("Producto no encontrado");
                }
                
                // Verifica si hay suficiente stock
                if ($producto_info['stock'] < $cantidad) {
                    // Eliminar la factura creada si hay error
                    // throw es para lanzar una excepción, excepción es un error que se lanza
                    $this->facturaModel->deleteFactura($factura_id);
                    throw new Exception("Stock insuficiente para: " . $producto_info['nombre']);
                }
                
                // Calcula los totales de la línea
                // Asumiendo que el producto tiene los campos 'precio_unitario' y 'porcentaje_impuesto'
                $precio = (float)$producto_info['precio_unitario'];
                $impuesto_porc = (float)$producto_info['porcentaje_impuesto'];
                
                // Calcula subtotal, impuesto y total de la línea
                $subtotal_linea = $precio * $cantidad;
                $impuesto_linea = $subtotal_linea * ($impuesto_porc / 100);
                $total_linea = $subtotal_linea + $impuesto_linea;
                
                // Agregar línea de factura
                $this->facturaModel->addLineaFactura(
                    $factura_id,
                    $producto_id,
                    $cantidad,
                    $precio,
                    $impuesto_porc,
                    $subtotal_linea,
                    $impuesto_linea,
                    $total_linea
                );
                
                // Actualizar stock
                $this->productoModel->decrementarStock($producto_id, $cantidad);
                
                // Acumular totales
                $subtotal += $subtotal_linea;
                $impuesto += $impuesto_linea;
                $total += $total_linea;
            }
            
            // Actualizar totales de la factura
            $this->facturaModel->updateTotalesFactura($factura_id, $subtotal, $impuesto, $total);
            
            // Finalizar la transacción
            // Si todo sale bien, se confirma la transacción
            // Si hay un error, se lanza una excepción y se hace rollback
            $_SESSION['success'] = "Factura #".$_POST['numero_factura']." creada correctamente";
            header('Location: index.php?module=facturas&action=list');
            exit;
            
        } catch (Exception $e) {
            $_SESSION['error'] = $e->getMessage();
            $_SESSION['form_data'] = $_POST;
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
        $connection = $this->facturaModel->getConnection();
        
        try {
            // Cerrar cualquier transacción activa existente
            if ($connection->inTransaction()) {
                $connection->commit();
            }
            
            // Iniciar nueva transacción
            $connection->beginTransaction();
            
            // Obtener líneas de factura para actualizar el stock
            $lineas = $this->facturaModel->getLineasFactura($id);
            
            // Revertir el stock de los productos
            foreach ($lineas as $linea) {
                $this->productoModel->incrementarStock($linea['producto_id'], $linea['cantidad']);
            }
            
            // Eliminar la factura
            $result = $this->facturaModel->deleteFactura($id);
            
            $connection->commit();
            
            if ($result) {
                $_SESSION['success'] = "Factura eliminada correctamente";
            } else {
                $_SESSION['error'] = "Error al eliminar la factura";
            }
            
        } catch (Exception $e) {
            if (isset($connection) && $connection->inTransaction()) {
                try {
                    $connection->rollBack();
                } catch (Exception $rollbackEx) {
                    // Silenciar error de rollback
                }
            }
            $_SESSION['error'] = "Error al eliminar la factura: " . $e->getMessage();
        }
        
        header('Location: index.php?module=facturas&action=list');
        exit;
    }
}
?>