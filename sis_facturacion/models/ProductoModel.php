<?php
class Producto {
    public $producto_id;
    public $nombre;
    public $precio_unitario;

    public function __construct($id, $nombre, $precio) {
        $this->producto_id = $id;
        $this->nombre = $nombre;
        $this->precio_unitario = $precio;
    }
}
