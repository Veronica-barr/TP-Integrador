<?php
class FacturaModel {
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    public function getAllFacturas() {
        $query = "SELECT f.*, CONCAT(c.nombre, ' ', c.apellido) as cliente_nombre 
                  FROM facturas f 
                  JOIN clientes c ON f.cliente_id = c.cliente_id 
                  ORDER BY f.fecha DESC";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    public function getFacturasByDateRange($fecha_inicio, $fecha_fin) {
        $query = "SELECT f.*, CONCAT(c.nombre, ' ', c.apellido) as cliente_nombre 
                  FROM facturas f 
                  JOIN clientes c ON f.cliente_id = c.cliente_id 
                  WHERE f.fecha BETWEEN ? AND ? 
                  ORDER BY f.fecha DESC";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(1, $fecha_inicio);
        $stmt->bindParam(2, $fecha_fin);
        $stmt->execute();
        return $stmt;
    }

    public function getFacturaById($id) {
        $query = "SELECT f.*, CONCAT(c.nombre, ' ', c.apellido) as cliente_nombre, c.cuil 
                  FROM facturas f 
                  JOIN clientes c ON f.cliente_id = c.cliente_id 
                  WHERE f.factura_id = ?";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(1, $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getLineasFactura($factura_id) {
        $query = "SELECT lf.*, p.nombre as producto_nombre, p.codigo as producto_codigo 
                  FROM lineas_factura lf 
                  JOIN productos p ON lf.producto_id = p.producto_id 
                  WHERE lf.factura_id = ?";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(1, $factura_id);
        $stmt->execute();
        return $stmt;
    }

    public function createFactura($cliente_id, $numero_factura, $fecha, $subtotal, $impuesto, $total) {
        $this->db->beginTransaction();
        
        try {
            // Insertar factura
            $query = "INSERT INTO facturas (cliente_id, numero_factura, fecha, subtotal, impuesto, total) 
                      VALUES (?, ?, ?, ?, ?, ?)";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(1, $cliente_id);
            $stmt->bindParam(2, $numero_factura);
            $stmt->bindParam(3, $fecha);
            $stmt->bindParam(4, $subtotal);
            $stmt->bindParam(5, $impuesto);
            $stmt->bindParam(6, $total);
            $stmt->execute();
            
            $factura_id = $this->db->lastInsertId();
            $this->db->commit();
            
            return $factura_id;
        } catch (Exception $e) {
            $this->db->rollBack();
            throw $e;
        }
    }

    public function addLineaFactura($factura_id, $producto_id, $cantidad, $precio_unitario, $porcentaje_impuesto, $subtotal, $monto_impuesto, $total_linea) {
        $query = "INSERT INTO lineas_factura (factura_id, producto_id, cantidad, precio_unitario, porcentaje_impuesto, subtotal, monto_impuesto, total_linea) 
                  VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(1, $factura_id);
        $stmt->bindParam(2, $producto_id);
        $stmt->bindParam(3, $cantidad);
        $stmt->bindParam(4, $precio_unitario);
        $stmt->bindParam(5, $porcentaje_impuesto);
        $stmt->bindParam(6, $subtotal);
        $stmt->bindParam(7, $monto_impuesto);
        $stmt->bindParam(8, $total_linea);
        return $stmt->execute();
    }

    public function updateStockProducto($producto_id, $cantidad) {
        $query = "UPDATE productos SET stock = stock - ? WHERE producto_id = ?";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(1, $cantidad);
        $stmt->bindParam(2, $producto_id);
        return $stmt->execute();
    }

    public function updateTotalesFactura($factura_id, $subtotal, $impuesto, $total) {
    $query = "UPDATE facturas SET subtotal = ?, impuesto = ?, total = ? WHERE factura_id = ?";
    $stmt = $this->db->prepare($query);
    $stmt->bindParam(1, $subtotal);
    $stmt->bindParam(2, $impuesto);
    $stmt->bindParam(3, $total);
    $stmt->bindParam(4, $factura_id);
    return $stmt->execute();
}

public function getConnection() {
    return $this->db;
}
}
?>