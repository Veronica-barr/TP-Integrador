<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once "BaseDAL.php";
require_once __DIR__ . "/../models/Producto.php";
require_once __DIR__ . "/../config/database.php"; 

class ProductoDAL extends BaseDAL {
    public function __construct() {
        $db = Database::getConnection();
        parent::__construct($db);
    }

    public function listar() {
        $sql = "SELECT * FROM productos WHERE activo = 1";
        $stmt = $this->executeQuery($sql);
        $rows = $stmt->fetchAll();

        $productos = [];
        foreach ($rows as $row) {
            $productos[] = new Producto(
                $row["producto_id"],
                $row["codigo"],
                $row["nombre"],
                $row["descripcion"],
                $row["precio_unitario"],
                $row["porcentaje_impuesto"],
                $row["stock"],
                $row["activo"]
            );
        }
        return $productos;
    }

    public function getById($id) {
        $sql = "SELECT * FROM productos WHERE producto_id = ?";
        $stmt = $this->executeQuery($sql, [$id]);
        $row = $stmt->fetch();

        if ($row) {
            return new Producto(
                $row["producto_id"],
                $row["codigo"],
                $row["nombre"],
                $row["descripcion"],
                $row["precio_unitario"],
                $row["porcentaje_impuesto"],
                $row["stock"],
                $row["activo"]
            );
        }
        return null;
    }

    public function insert(Producto $producto) {
        $sql = "INSERT INTO productos (codigo, nombre, descripcion, precio_unitario, porcentaje_impuesto, stock, activo)
                VALUES (?, ?, ?, ?, ?, ?, ?)";
        return $this->executeNonQuery($sql, [
            $producto->codigo,
            $producto->nombre,
            $producto->descripcion,
            $producto->precio_unitario,
            $producto->porcentaje_impuesto,
            $producto->stock,
            $producto->activo
        ]);
    }

    public function update(Producto $producto) {
        $sql = "UPDATE productos 
                SET codigo = ?, nombre = ?, descripcion = ?, 
                    precio_unitario = ?, porcentaje_impuesto = ?, stock = ? 
                WHERE producto_id = ?";
        return $this->executeNonQuery($sql, [
            $producto->codigo,
            $producto->nombre,
            $producto->descripcion,
            $producto->precio_unitario,
            $producto->porcentaje_impuesto,
            $producto->stock,
            $producto->producto_id
        ]);
    }

    public function delete($id) {
        $sql = "UPDATE productos SET activo = 0 WHERE producto_id = ?";
        return $this->executeNonQuery($sql, [$id]);
    }

    public function actualizarStock($producto_id, $cantidad, $operacion = 'decrementar') {
        $signo = ($operacion === 'incrementar') ? '+' : '-';
        
        $query = "UPDATE productos SET stock = stock $signo ? WHERE producto_id = ?";
        return $this->executeNonQuery($query, [$cantidad, $producto_id]);
    }
}
?>