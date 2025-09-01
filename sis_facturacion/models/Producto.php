<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

class Producto {
    public $producto_id;
    public $codigo;
    public $nombre;
    public $descripcion;
    public $precio_unitario;
    public $porcentaje_impuesto;
    public $stock;
    public $activo;

    public function __construct(
        $producto_id = null,
        $codigo = "",
        $nombre = "",
        $descripcion = "",
        $precio_unitario = 0.0,
        $porcentaje_impuesto = 21.0,
        $stock = 0,
        $activo = true
    ) {
        $this->producto_id = $producto_id;
        $this->codigo = $codigo;
        $this->nombre = $nombre;
        $this->descripcion = $descripcion;
        $this->precio_unitario = $precio_unitario;
        $this->porcentaje_impuesto = $porcentaje_impuesto;
        $this->stock = $stock;
        $this->activo = $activo;
    }
}
