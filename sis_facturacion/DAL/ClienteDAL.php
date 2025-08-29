<?php
$dalDir = __DIR__;
require_once $dalDir . '/../config/database.php';
require_once $dalDir . '/../models/Cliente.php';
require_once $dalDir . '/../models/Telefono.php';
require_once $dalDir . '/../models/Direccion.php';

class ClienteDAL {
    private $conn;

    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    public function listarClientes() {
        $query = "SELECT c.* FROM clientes c WHERE c.activo = 1 ORDER BY c.apellido, c.nombre";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        
        $clientes = array();
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $cliente = new Cliente();
            $cliente->cliente_id = $row['cliente_id'];
            $cliente->nombre = $row['nombre'];
            $cliente->apellido = $row['apellido'];
            $cliente->cuil = $row['cuil'];
            $cliente->activo = $row['activo'];
            $cliente->fecha_registro = $row['fecha_registro'];
            
            // Obtener teléfonos
            $cliente->telefonos = $this->obtenerTelefonos($cliente->cliente_id);
            
            // Obtener direcciones
            $cliente->direcciones = $this->obtenerDirecciones($cliente->cliente_id);
            
            $clientes[] = $cliente;
        }
        return $clientes;
    }

    private function obtenerTelefonos($cliente_id) {
        $query = "SELECT t.* FROM telefonos t WHERE t.cliente_id = :cliente_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':cliente_id', $cliente_id);
        $stmt->execute();
        
        $telefonos = array();
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $telefono = new Telefono();
            $telefono->telefono_id = $row['telefono_id'];
            $telefono->cliente_id = $row['cliente_id'];
            $telefono->tipo = $row['tipo'];
            $telefono->codigo_area = $row['codigo_area'];
            $telefono->numero = $row['numero'];
            $telefonos[] = $telefono;
        }
        return $telefonos;
    }

    private function obtenerDirecciones($cliente_id) {
        $query = "SELECT d.*, l.nombre as localidad_nombre, p.nombre as provincia_nombre, l.codigo_postal 
                  FROM direcciones d 
                  INNER JOIN localidades l ON d.localidad_id = l.localidad_id 
                  INNER JOIN provincias p ON l.provincia_id = p.provincia_id 
                  WHERE d.cliente_id = :cliente_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':cliente_id', $cliente_id);
        $stmt->execute();
        
        $direcciones = array();
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $direccion = new Direccion();
            $direccion->direccion_id = $row['direccion_id'];
            $direccion->cliente_id = $row['cliente_id'];
            $direccion->calle = $row['calle'];
            $direccion->numero = $row['numero'];
            $direccion->piso = $row['piso'];
            $direccion->departamento = $row['departamento'];
            $direccion->localidad_id = $row['localidad_id'];
            $direccion->localidad_nombre = $row['localidad_nombre'];
            $direccion->provincia_nombre = $row['provincia_nombre'];
            $direccion->codigo_postal = $row['codigo_postal'];
            $direcciones[] = $direccion;
        }
        return $direcciones;
    }

    public function obtenerClientePorId($cliente_id) {
        $query = "SELECT c.* FROM clientes c WHERE c.cliente_id = :cliente_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':cliente_id', $cliente_id);
        $stmt->execute();
        
        if ($stmt->rowCount() == 0) {
            return null;
        }
        
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        $cliente = new Cliente();
        $cliente->cliente_id = $row['cliente_id'];
        $cliente->nombre = $row['nombre'];
        $cliente->apellido = $row['apellido'];
        $cliente->cuil = $row['cuil'];
        $cliente->activo = $row['activo'];
        $cliente->fecha_registro = $row['fecha_registro'];
        
        // Obtener teléfonos
        $cliente->telefonos = $this->obtenerTelefonos($cliente->cliente_id);
        
        // Obtener direcciones
        $cliente->direcciones = $this->obtenerDirecciones($cliente->cliente_id);
        
        return $cliente;
    }

    public function crearCliente(Cliente $cliente) {
        try {
            $this->conn->beginTransaction();
            
            // Insertar cliente
            $query = "INSERT INTO clientes (nombre, apellido, cuil) VALUES (:nombre, :apellido, :cuil)";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':nombre', $cliente->nombre);
            $stmt->bindParam(':apellido', $cliente->apellido);
            $stmt->bindParam(':cuil', $cliente->cuil);
            $stmt->execute();
            
            $cliente_id = $this->conn->lastInsertId();
            
            // Insertar teléfonos
            foreach ($cliente->telefonos as $telefono) {
                $query = "INSERT INTO telefonos (cliente_id, tipo, codigo_area, numero) 
                          VALUES (:cliente_id, :tipo, :codigo_area, :numero)";
                $stmt = $this->conn->prepare($query);
                $stmt->bindParam(':cliente_id', $cliente_id);
                $stmt->bindParam(':tipo', $telefono->tipo);
                $stmt->bindParam(':codigo_area', $telefono->codigo_area);
                $stmt->bindParam(':numero', $telefono->numero);
                $stmt->execute();
            }
            
            // Insertar direcciones
            foreach ($cliente->direcciones as $direccion) {
                $query = "INSERT INTO direcciones (cliente_id, calle, numero, piso, departamento, localidad_id) 
                          VALUES (:cliente_id, :calle, :numero, :piso, :departamento, :localidad_id)";
                $stmt = $this->conn->prepare($query);
                $stmt->bindParam(':cliente_id', $cliente_id);
                $stmt->bindParam(':calle', $direccion->calle);
                $stmt->bindParam(':numero', $direccion->numero);
                $stmt->bindParam(':piso', $direccion->piso);
                $stmt->bindParam(':departamento', $direccion->departamento);
                $stmt->bindParam(':localidad_id', $direccion->localidad_id);
                $stmt->execute();
            }
            
            $this->conn->commit();
            return $cliente_id;
        } catch (Exception $e) {
            $this->conn->rollBack();
            throw $e;
        }
    }

    public function actualizarCliente(Cliente $cliente) {
        try {
            $this->conn->beginTransaction();
            
            // Actualizar cliente
            $query = "UPDATE clientes SET nombre = :nombre, apellido = :apellido, cuil = :cuil 
                      WHERE cliente_id = :cliente_id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':nombre', $cliente->nombre);
            $stmt->bindParam(':apellido', $cliente->apellido);
            $stmt->bindParam(':cuil', $cliente->cuil);
            $stmt->bindParam(':cliente_id', $cliente->cliente_id);
            $stmt->execute();
            
            // Eliminar teléfonos existentes
            $query = "DELETE FROM telefonos WHERE cliente_id = :cliente_id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':cliente_id', $cliente->cliente_id);
            $stmt->execute();
            
            // Insertar nuevos teléfonos
            foreach ($cliente->telefonos as $telefono) {
                $query = "INSERT INTO telefonos (cliente_id, tipo, codigo_area, numero) 
                          VALUES (:cliente_id, :tipo, :codigo_area, :numero)";
                $stmt = $this->conn->prepare($query);
                $stmt->bindParam(':cliente_id', $cliente->cliente_id);
                $stmt->bindParam(':tipo', $telefono->tipo);
                $stmt->bindParam(':codigo_area', $telefono->codigo_area);
                $stmt->bindParam(':numero', $telefono->numero);
                $stmt->execute();
            }
            
            // Eliminar direcciones existentes
            $query = "DELETE FROM direcciones WHERE cliente_id = :cliente_id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':cliente_id', $cliente->cliente_id);
            $stmt->execute();
            
            // Insertar nuevas direcciones
            foreach ($cliente->direcciones as $direccion) {
                $query = "INSERT INTO direcciones (cliente_id, calle, numero, piso, departamento, localidad_id) 
                          VALUES (:cliente_id, :calle, :numero, :piso, :departamento, :localidad_id)";
                $stmt = $this->conn->prepare($query);
                $stmt->bindParam(':cliente_id', $cliente->cliente_id);
                $stmt->bindParam(':calle', $direccion->calle);
                $stmt->bindParam(':numero', $direccion->numero);
                $stmt->bindParam(':piso', $direccion->piso);
                $stmt->bindParam(':departamento', $direccion->departamento);
                $stmt->bindParam(':localidad_id', $direccion->localidad_id);
                $stmt->execute();
            }
            
            $this->conn->commit();
            return true;
        } catch (Exception $e) {
            $this->conn->rollBack();
            throw $e;
        }
    }

    public function eliminarCliente($cliente_id) {
        $query = "UPDATE clientes SET activo = 0 WHERE cliente_id = :cliente_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':cliente_id', $cliente_id);
        return $stmt->execute();
    }
}
?>