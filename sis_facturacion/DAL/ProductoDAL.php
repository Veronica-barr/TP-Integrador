<?php
require_once "BaseDAL.php";
require_once __DIR__ . "/../models/Producto.php";

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
        $stmt->execute([$id]);
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
        $stmt = $this->executeQuery($sql);
        return $stmt->execute([
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
                SET codigo = ?, nombre = ?, descripcion = ?, precio_unitario = ?, porcentaje_impuesto = ?, stock = ?, activo = ? 
                WHERE producto_id = ?";
        $stmt = $this->executeQuery($sql);
        return $stmt->execute([
            $producto->codigo,
            $producto->nombre,
            $producto->descripcion,
            $producto->precio_unitario,
            $producto->porcentaje_impuesto,
            $producto->stock,
            $producto->activo,
            $producto->producto_id
        ]);
    }

    public function delete($id) {
        // Baja lógica
        $sql = "UPDATE productos SET activo = 0 WHERE producto_id = ?";
        $stmt = $this->executeQuery($sql);
        return $stmt->execute([$id]);
    }
}
