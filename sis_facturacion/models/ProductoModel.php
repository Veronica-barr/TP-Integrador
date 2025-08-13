<?php
class ProductoModel {
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    public function getAllProductos() {
        $query = "SELECT * FROM productos WHERE activo = TRUE";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    public function getProductoById($id) {
        $query = "SELECT * FROM productos WHERE producto_id = ?";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(1, $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function createProducto($codigo, $nombre, $descripcion, $precio_unitario, $porcentaje_impuesto, $stock) {
        $query = "INSERT INTO productos (codigo, nombre, descripcion, precio_unitario, porcentaje_impuesto, stock) 
                  VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(1, $codigo);
        $stmt->bindParam(2, $nombre);
        $stmt->bindParam(3, $descripcion);
        $stmt->bindParam(4, $precio_unitario);
        $stmt->bindParam(5, $porcentaje_impuesto);
        $stmt->bindParam(6, $stock);
        return $stmt->execute();
    }

    public function updateProducto($id, $codigo, $nombre, $descripcion, $precio_unitario, $porcentaje_impuesto, $stock) {
        $query = "UPDATE productos SET codigo = ?, nombre = ?, descripcion = ?, precio_unitario = ?, 
                  porcentaje_impuesto = ?, stock = ? WHERE producto_id = ?";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(1, $codigo);
        $stmt->bindParam(2, $nombre);
        $stmt->bindParam(3, $descripcion);
        $stmt->bindParam(4, $precio_unitario);
        $stmt->bindParam(5, $porcentaje_impuesto);
        $stmt->bindParam(6, $stock);
        $stmt->bindParam(7, $id);
        return $stmt->execute();
    }

    public function deleteProducto($id) {
        $query = "UPDATE productos SET activo = FALSE WHERE producto_id = ?";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(1, $id);
        return $stmt->execute();
    }
}
?>