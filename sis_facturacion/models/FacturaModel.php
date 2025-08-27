<?php
class Factura {
    public $id;
    public $cliente_id;
    public $fecha;
    public $total;

    public function __construct($id, $cliente_id, $fecha, $total) {
        $this->id = $id;
        $this->cliente_id = $cliente_id;
        $this->fecha = $fecha;
        $this->total = $total;
    }
}
