<?php
class Factura {
    public $factura_id;
    public $cliente_id;
    public $numero_factura;
    public $fecha;
    public $subtotal;
    public $impuesto;
    public $total;
    public $estado;
    public $cliente_nombre;
    public $cliente_apellido;
    public $cliente_cuil;
    public $lineas = array();
}
?>