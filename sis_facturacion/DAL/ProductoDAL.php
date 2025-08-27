<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../models/ProductoModel.php';

class ProductoDAL {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    public function crearProducto(Producto $producto) {
        $stmt = $this->db->prepare("INSERT INTO productos (nombre, precio_unitario) VALUES (?, ?)");
        $stmt->bind_param("sd", $producto->nombre, $producto->precio_unitario);
        return $stmt->execute();
    }

    public function obtenerProductos() {
        $result = $this->db->query("SELECT * FROM productos");
        $productos = [];
        while ($row = $result->fetch_assoc()) {
            $productos[] = new Producto(
                $row['producto_id'], 
                $row['nombre'], 
                $row['precio_unitario']
            );
        }
        return $productos;
    }
}