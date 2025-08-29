<?php
$controllerDir = __DIR__;
require_once $controllerDir . '/../DAL/FacturaDAL.php';
require_once $controllerDir . '/../DAL/ClienteDAL.php';
require_once $controllerDir . '/../DAL/ProductoDAL.php';
require_once $controllerDir . '/../models/Factura.php';
require_once $controllerDir . '/../models/LineaFactura.php';

class FacturaController {
    private $facturaDAL;
    private $clienteDAL;
    private $productoDAL;

    public function __construct() {
        $this->facturaDAL = new FacturaDAL();
        $this->clienteDAL = new ClienteDAL();
        $this->productoDAL = new ProductoDAL();
    }

    public function listarFacturas($fecha_desde = null, $fecha_hasta = null) {
        return $this->facturaDAL->listarFacturas($fecha_desde, $fecha_hasta);
    }

    public function obtenerFactura($factura_id) {
        return $this->facturaDAL->obtenerFacturaPorId($factura_id);
    }

    public function crearFactura($data) {
        $factura = new Factura();
        $factura->cliente_id = $data['cliente_id'];
        $factura->fecha = $data['fecha'];
        $factura->estado = 'PENDIENTE';
        
        $subtotal = 0;
        $impuesto = 0;
        
        // Procesar líneas de factura
        if (isset($data['lineas']) && is_array($data['lineas'])) {
            foreach ($data['lineas'] as $lineaData) {
                if (!empty($lineaData['producto_id']) && !empty($lineaData['cantidad'])) {
                    $producto = $this->productoDAL->getById($lineaData['producto_id']);
                    
                    if ($producto && $producto->stock >= $lineaData['cantidad']) {
                        $linea = new LineaFactura();
                        $linea->producto_id = $lineaData['producto_id'];
                        $linea->cantidad = $lineaData['cantidad'];
                        $linea->precio_unitario = $producto->precio_unitario;
                        $linea->porcentaje_impuesto = $producto->porcentaje_impuesto;
                        $linea->subtotal = $linea->cantidad * $linea->precio_unitario;
                        $linea->monto_impuesto = $linea->subtotal * ($linea->porcentaje_impuesto / 100);
                        $linea->total_linea = $linea->subtotal + $linea->monto_impuesto;
                        
                        $subtotal += $linea->subtotal;
                        $impuesto += $linea->monto_impuesto;
                        
                        $factura->lineas[] = $linea;
                    }
                }
            }
        }
        
        $factura->subtotal = $subtotal;
        $factura->impuesto = $impuesto;
        $factura->total = $subtotal + $impuesto;
        
        return $this->facturaDAL->crearFactura($factura);
    }

    public function actualizarEstadoFactura($factura_id, $estado) {
        return $this->facturaDAL->actualizarEstadoFactura($factura_id, $estado);
    }

    public function obtenerClientes() {
        return $this->clienteDAL->listarClientes();
    }

    public function obtenerProductos() {
        return $this->productoDAL->listar();
    }
}
?>