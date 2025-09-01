<?php
$dalDir = __DIR__;
require_once $dalDir . '/../config/database.php';
require_once $dalDir . '/../models/Factura.php';
require_once $dalDir . '/../models/LineaFactura.php';

class FacturaDAL {
    private $conn;

    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    public function listarFacturas($fecha_desde = null, $fecha_hasta = null) {
        $query = "SELECT f.*, c.nombre as cliente_nombre, c.apellido as cliente_apellido, c.cuil as cliente_cuil 
                  FROM facturas f 
                  INNER JOIN clientes c ON f.cliente_id = c.cliente_id 
                  WHERE 1=1";
        
        if ($fecha_desde) {
            if (is_numeric($fecha_desde)) {
                $fecha_desde = date('Y-m-d', strtotime($fecha_desde . ' days ago'));
            }
            if (DateTime::createFromFormat('Y-m-d', $fecha_desde) !== false) {
                $query .= " AND f.fecha >= :fecha_desde";
            }
        }
        
        if ($fecha_hasta) {
            if (is_numeric($fecha_hasta)) {
                $fecha_hasta = date('Y-m-d', strtotime($fecha_hasta . ' days ago'));
            }
            if (DateTime::createFromFormat('Y-m-d', $fecha_hasta) !== false) {
                $query .= " AND f.fecha <= :fecha_hasta";
            }
        }
        
        $query .= " ORDER BY f.fecha DESC, f.numero_factura DESC";
        
        $stmt = $this->conn->prepare($query);
        
        if ($fecha_desde && DateTime::createFromFormat('Y-m-d', $fecha_desde) !== false) {
            $stmt->bindParam(':fecha_desde', $fecha_desde);
        }
        if ($fecha_hasta && DateTime::createFromFormat('Y-m-d', $fecha_hasta) !== false) {
            $stmt->bindParam(':fecha_hasta', $fecha_hasta);
        }
        
        $stmt->execute();
        
        $facturas = array();
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $factura = new Factura();
            $factura->factura_id = $row['factura_id'];
            $factura->cliente_id = $row['cliente_id'];
            $factura->numero_factura = $row['numero_factura'];
            $factura->fecha = $row['fecha'];
            $factura->subtotal = $row['subtotal'];
            $factura->impuesto = $row['impuesto'];
            $factura->total = $row['total'];
            $factura->estado = $row['estado'];
            $factura->cliente_nombre = $row['cliente_nombre'];
            $factura->cliente_apellido = $row['cliente_apellido'];
            $factura->cliente_cuil = $row['cliente_cuil'];
            
            $facturas[] = $factura;
        }
        return $facturas;
    }

    public function obtenerFacturaPorId($factura_id) {
        try {
            if (!is_numeric($factura_id) || $factura_id <= 0) {
                throw new InvalidArgumentException("ID de factura inválido");
            }
            
            error_log("Buscando factura con ID: " . $factura_id);
            
            $query = "SELECT f.*, c.nombre as cliente_nombre, c.apellido as cliente_apellido, c.cuil as cliente_cuil 
                      FROM facturas f 
                      INNER JOIN clientes c ON f.cliente_id = c.cliente_id 
                      WHERE f.factura_id = :factura_id";
            
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':factura_id', $factura_id, PDO::PARAM_INT);
            
            if (!$stmt->execute()) {
                throw new Exception("Error al ejecutar la consulta");
            }
            
            error_log("Número de filas encontradas: " . $stmt->rowCount());
            
            if ($stmt->rowCount() == 0) {
                return null;
            }
            
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            $factura = new Factura();
            
            $factura->factura_id = $row['factura_id'];
            $factura->cliente_id = $row['cliente_id'];
            $factura->numero_factura = $row['numero_factura'];
            $factura->fecha = $row['fecha'];
            $factura->subtotal = $row['subtotal'];
            $factura->impuesto = $row['impuesto'];
            $factura->total = $row['total'];
            $factura->estado = $row['estado'];
            $factura->cliente_nombre = $row['cliente_nombre'];
            $factura->cliente_apellido = $row['cliente_apellido'];
            $factura->cliente_cuil = $row['cliente_cuil'];
            
            $factura->lineas = $this->obtenerLineasFactura($factura_id);
            
            return $factura;
            
        } catch (PDOException $e) {
            error_log("Error PDO en obtenerFacturaPorId: " . $e->getMessage());
            throw new Exception("Error al obtener la factura");
        } catch (Exception $e) {
            error_log("Error en obtenerFacturaPorId: " . $e->getMessage());
            throw $e;
        }
    }

    private function obtenerLineasFactura($factura_id) {
        $query = "SELECT lf.*, p.nombre as producto_nombre, p.codigo as producto_codigo 
                  FROM lineas_factura lf 
                  INNER JOIN productos p ON lf.producto_id = p.producto_id 
                  WHERE lf.factura_id = :factura_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':factura_id', $factura_id);
        $stmt->execute();
        
        $lineas = array();
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $linea = new LineaFactura();
            $linea->linea_id = $row['linea_id'];
            $linea->factura_id = $row['factura_id'];
            $linea->producto_id = $row['producto_id'];
            $linea->cantidad = $row['cantidad'];
            $linea->precio_unitario = $row['precio_unitario'];
            $linea->porcentaje_impuesto = $row['porcentaje_impuesto'];
            $linea->subtotal = $row['subtotal'];
            $linea->monto_impuesto = $row['monto_impuesto'];
            $linea->total_linea = $row['total_linea'];
            $linea->producto_nombre = $row['producto_nombre'];
            $linea->producto_codigo = $row['producto_codigo'];
            $lineas[] = $linea;
        }
        return $lineas;
    }

    public function crearFactura(Factura $factura) {
        try {
            $this->conn->beginTransaction();
            
            $numero_factura = $this->generarNumeroFactura();
            
            $query = "INSERT INTO facturas (cliente_id, numero_factura, fecha, subtotal, impuesto, total, estado) 
                      VALUES (:cliente_id, :numero_factura, :fecha, :subtotal, :impuesto, :total, :estado)";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':cliente_id', $factura->cliente_id);
            $stmt->bindParam(':numero_factura', $numero_factura);
            $stmt->bindParam(':fecha', $factura->fecha);
            $stmt->bindParam(':subtotal', $factura->subtotal);
            $stmt->bindParam(':impuesto', $factura->impuesto);
            $stmt->bindParam(':total', $factura->total);
            $stmt->bindParam(':estado', $factura->estado);
            $stmt->execute();
            
            $factura_id = $this->conn->lastInsertId();
            
            foreach ($factura->lineas as $linea) {
                $query = "INSERT INTO lineas_factura (factura_id, producto_id, cantidad, precio_unitario, 
                          porcentaje_impuesto, subtotal, monto_impuesto, total_linea) 
                          VALUES (:factura_id, :producto_id, :cantidad, :precio_unitario, :porcentaje_impuesto, 
                          :subtotal, :monto_impuesto, :total_linea)";
                $stmt = $this->conn->prepare($query);
                $stmt->bindParam(':factura_id', $factura_id);
                $stmt->bindParam(':producto_id', $linea->producto_id);
                $stmt->bindParam(':cantidad', $linea->cantidad);
                $stmt->bindParam(':precio_unitario', $linea->precio_unitario);
                $stmt->bindParam(':porcentaje_impuesto', $linea->porcentaje_impuesto);
                $stmt->bindParam(':subtotal', $linea->subtotal);
                $stmt->bindParam(':monto_impuesto', $linea->monto_impuesto);
                $stmt->bindParam(':total_linea', $linea->total_linea);
                $stmt->execute();
                
                $query = "UPDATE productos SET stock = stock - :cantidad WHERE producto_id = :producto_id";
                $stmt = $this->conn->prepare($query);
                $stmt->bindParam(':cantidad', $linea->cantidad);
                $stmt->bindParam(':producto_id', $linea->producto_id);
                $stmt->execute();
            }
            
            $this->conn->commit();
            return $factura_id;
        } catch (Exception $e) {
            $this->conn->rollBack();
            throw $e;
        }
    }

    private function generarNumeroFactura() {
        $year = date('Y');
        $query = "SELECT COUNT(*) as total FROM facturas WHERE YEAR(fecha) = :year";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':year', $year);
        $stmt->execute();
        
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        $numero = $row['total'] + 1;
        
        return $year . str_pad($numero, 8, '0', STR_PAD_LEFT);
    }

    public function actualizarEstadoFactura($factura_id, $estado) {
        $query = "UPDATE facturas SET estado = :estado WHERE factura_id = :factura_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':estado', $estado);
        $stmt->bindParam(':factura_id', $factura_id);
        return $stmt->execute();
    }

    // Nuevo método para actualizar factura
    public function actualizarFactura($factura_id, Factura $factura) {
        try {
            $this->conn->beginTransaction();
            
            // Eliminar las líneas existentes
            $query = "DELETE FROM lineas_factura WHERE factura_id = :factura_id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':factura_id', $factura_id);
            $stmt->execute();
            
            // Actualizar la factura
            $query = "UPDATE facturas SET cliente_id = :cliente_id, fecha = :fecha, 
                      subtotal = :subtotal, impuesto = :impuesto, total = :total 
                      WHERE factura_id = :factura_id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':cliente_id', $factura->cliente_id);
            $stmt->bindParam(':fecha', $factura->fecha);
            $stmt->bindParam(':subtotal', $factura->subtotal);
            $stmt->bindParam(':impuesto', $factura->impuesto);
            $stmt->bindParam(':total', $factura->total);
            $stmt->bindParam(':factura_id', $factura_id);
            $stmt->execute();
            
            // Insertar las nuevas líneas
            foreach ($factura->lineas as $linea) {
                $query = "INSERT INTO lineas_factura (factura_id, producto_id, cantidad, precio_unitario, 
                          porcentaje_impuesto, subtotal, monto_impuesto, total_linea) 
                          VALUES (:factura_id, :producto_id, :cantidad, :precio_unitario, :porcentaje_impuesto, 
                          :subtotal, :monto_impuesto, :total_linea)";
                $stmt = $this->conn->prepare($query);
                $stmt->bindParam(':factura_id', $factura_id);
                $stmt->bindParam(':producto_id', $linea->producto_id);
                $stmt->bindParam(':cantidad', $linea->cantidad);
                $stmt->bindParam(':precio_unitario', $linea->precio_unitario);
                $stmt->bindParam(':porcentaje_impuesto', $linea->porcentaje_impuesto);
                $stmt->bindParam(':subtotal', $linea->subtotal);
                $stmt->bindParam(':monto_impuesto', $linea->monto_impuesto);
                $stmt->bindParam(':total_linea', $linea->total_linea);
                $stmt->execute();
            }
            
            $this->conn->commit();
            return true;
        } catch (Exception $e) {
            $this->conn->rollBack();
            throw $e;
        }
    }

    // Nuevo método para eliminar factura
    public function eliminarFactura($factura_id) {
        try {
            $this->conn->beginTransaction();
            
            // Eliminar las líneas de la factura
            $query = "DELETE FROM lineas_factura WHERE factura_id = :factura_id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':factura_id', $factura_id);
            $stmt->execute();
            
            // Eliminar la factura
            $query = "DELETE FROM facturas WHERE factura_id = :factura_id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':factura_id', $factura_id);
            $stmt->execute();
            
            $this->conn->commit();
            return true;
        } catch (Exception $e) {
            $this->conn->rollBack();
            throw $e;
        }
    }
}
?>