<?php
require_once __DIR__ . '/../dal/FacturaDAL.php';

class FacturaController {
    private $facturaDAL;

    public function __construct() {
        $this->facturaDAL = new FacturaDAL();
    }

    public function listarFacturas() {
        return $this->facturaDAL->obtenerFacturas();
    }

    public function agregarFactura($cliente_id, $fecha, $total) {
        $factura = new Factura(null, $cliente_id, $fecha, $total);
        return $this->facturaDAL->crearFactura($factura);
    }
}
