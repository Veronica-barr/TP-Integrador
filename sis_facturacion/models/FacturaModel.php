<?php
class FacturaModel {
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    // Obtiene todas las facturas
    // Este método devuelve un conjunto de resultados con todas las facturas registradas
    public function getAllFacturas() {
        $query = "SELECT f.*, CONCAT(c.nombre, ' ', c.apellido) as cliente_nombre 
                  FROM facturas f 
                  JOIN clientes c ON f.cliente_id = c.cliente_id 
                  ORDER BY f.fecha DESC";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    // Obtiene las facturas por rango de fechas
    // Este método busca las facturas entre dos fechas y devuelve un conjunto de resultados
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

    // Obtiene una factura por su ID
    // Este método busca una factura específica por su ID y devuelve sus detalles
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

    // Obtiene las líneas de una factura
    // Este método busca las líneas de una factura específica por su ID y devuelve un conjunto de resultados
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

    // Crea una nueva factura
    // Este método inserta una nueva factura en la base de datos y devuelve su ID
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
            
            $factura_id = $this->db->lastInsertId();// Obtener el ID de la factura recién creada
            $this->db->commit();// Confirmar la transacción
            
            // Devolver el ID de la factura
            return $factura_id;
        } catch (Exception $e) {
            $this->db->rollBack();// Revertir la transacción en caso de error
            throw $e;
        }
    }

    // Agrega una línea a una factura
    // Este método inserta una nueva línea de producto en una factura existente
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

    // Actualiza el stock de un producto
    // Este método decrementa el stock de un producto después de una venta
    public function updateStockProducto($producto_id, $cantidad) {
        $query = "UPDATE productos SET stock = stock - ? WHERE producto_id = ?";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(1, $cantidad);
        $stmt->bindParam(2, $producto_id);
        return $stmt->execute();
    }

    // Actualiza los totales de una factura
    // Este método actualiza los totales de una factura después de agregar líneas
    public function updateTotalesFactura($factura_id, $subtotal, $impuesto, $total) {
    $query = "UPDATE facturas SET subtotal = ?, impuesto = ?, total = ? WHERE factura_id = ?";
    $stmt = $this->db->prepare($query);
    $stmt->bindParam(1, $subtotal);
    $stmt->bindParam(2, $impuesto);
    $stmt->bindParam(3, $total);
    $stmt->bindParam(4, $factura_id);
    return $stmt->execute();
}

// Elimina las líneas de una factura
// Este método elimina todas las líneas asociadas a una factura específica
public function deleteLineasFactura($factura_id) {
    $query = "DELETE FROM lineas_factura WHERE factura_id = ?";
    $stmt = $this->db->prepare($query);
    $stmt->bindParam(1, $factura_id);
    return $stmt->execute();
}

// Elimina una factura
// Este método elimina una factura específica y sus líneas asociadas
public function deleteFactura($factura_id) {
    // Primero eliminar líneas
    $this->deleteLineasFactura($factura_id);
    
    // Luego eliminar factura
    $query = "DELETE FROM facturas WHERE factura_id = ?";
    $stmt = $this->db->prepare($query);
    $stmt->bindParam(1, $factura_id);
    return $stmt->execute();
}

// Obtiene la conexión a la base de datos
public function getConnection() {
    return $this->db;
}
}
?>