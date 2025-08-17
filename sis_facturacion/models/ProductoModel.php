<?php
class ProductoModel {
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    // Obtiene todos los productos activos
    // Este método devuelve un conjunto de resultados con todos los productos que están activos
    public function getAllProductos() {
        $query = "SELECT * FROM productos WHERE activo = TRUE";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    // Obtiene un producto por su ID
    // Este método busca un producto específico por su ID y devuelve sus datos
    public function getProductoById($id) {
        $query = "SELECT * FROM productos WHERE producto_id = ?";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(1, $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Crea un nuevo producto
    // Este método inserta un nuevo producto en la base de datos
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

    // Actualiza un producto existente
    // Este método actualiza los datos de un producto en la base de datos
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

    // Incrementa el stock de un producto
    // Este método incrementa el stock de un producto específico
    public function incrementarStock($producto_id, $cantidad) {
    $query = "UPDATE productos SET stock = stock + ? WHERE producto_id = ?";
    $stmt = $this->db->prepare($query);
    $stmt->bindParam(1, $cantidad);
    $stmt->bindParam(2, $producto_id);
    return $stmt->execute();
}

// Decrementa el stock de un producto
// Este método decrementa el stock de un producto específico después de una venta
public function decrementarStock($producto_id, $cantidad) {
    $query = "UPDATE productos SET stock = stock - ? WHERE producto_id = ?";
    $stmt = $this->db->prepare($query);
    $stmt->bindParam(1, $cantidad);
    $stmt->bindParam(2, $producto_id);
    return $stmt->execute();
}

// Elimina un producto
// Este método marca un producto como inactivo en la base de datos
    public function deleteProducto($id) {
        $query = "UPDATE productos SET activo = FALSE WHERE producto_id = ?";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(1, $id);
        return $stmt->execute();
    }
}
?>