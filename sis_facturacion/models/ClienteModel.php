<?php
class ClienteModel {
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    public function getAllClientes() {
        $query = "SELECT * FROM clientes WHERE activo = TRUE";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    public function getClienteById($id) {
        $query = "SELECT * FROM clientes WHERE cliente_id = ?";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(1, $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getLastInsertId() {
        return $this->db->lastInsertId();
    }

    public function createCliente($nombre, $apellido, $cuil) {
    // Normalizar CUIL (eliminar guiones y espacios)
    $cuil = preg_replace('/[^0-9]/', '', $cuil);
    
    // Validar formato (11 dígitos)
    if (strlen($cuil) !== 11) {
        return ['error' => 'El CUIL/CUIT debe contener exactamente 11 dígitos'];
    }
    
    // Verificar si el CUIL ya existe
    $queryCheck = "SELECT COUNT(*) FROM clientes WHERE REPLACE(REPLACE(cuil, '-', ''), ' ', '') = ?";
    $stmtCheck = $this->db->prepare($queryCheck);
    $stmtCheck->bindParam(1, $cuil);
    $stmtCheck->execute();
    
    if ($stmtCheck->fetchColumn() > 0) {
        return ['error' => 'El CUIL/CUIT ya está registrado'];
    }
    
    // Formatear CUIL con guiones
    $cuil_formateado = substr($cuil, 0, 2).'-'.substr($cuil, 2, 8).'-'.substr($cuil, 10, 1);
    
    // Insertar en la base de datos
    $query = "INSERT INTO clientes (nombre, apellido, cuil) VALUES (?, ?, ?)";
    $stmt = $this->db->prepare($query);
    $stmt->bindParam(1, $nombre);
    $stmt->bindParam(2, $apellido);
    $stmt->bindParam(3, $cuil_formateado);
    
    if ($stmt->execute()) {
        return ['success' => true, 'id' => $this->db->lastInsertId()];
    }
    
    return ['error' => 'Error al registrar el cliente'];
}

    public function existeCuil($cuil) {
        $cuil = preg_replace('/[^0-9]/', '', $cuil);
        $query = "SELECT COUNT(*) FROM clientes WHERE REPLACE(REPLACE(cuil, '-', ''), ' ', '') = ?";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(1, $cuil);
        $stmt->execute();
        return $stmt->fetchColumn() > 0;
    }

    public function updateCliente($id, $nombre, $apellido, $cuil) {
        // Normalizar CUIL
        $cuil = preg_replace('/[^0-9]/', '', $cuil);
        
        if (strlen($cuil) !== 11) {
            throw new Exception("El CUIL/CUIT debe contener 11 dígitos");
        }
        
        // Formatear CUIL con guiones
        $cuil_formateado = substr($cuil, 0, 2).'-'.substr($cuil, 2, 8).'-'.substr($cuil, 10, 1);
        
        $query = "UPDATE clientes SET nombre = ?, apellido = ?, cuil = ? WHERE cliente_id = ?";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(1, $nombre);
        $stmt->bindParam(2, $apellido);
        $stmt->bindParam(3, $cuil_formateado);
        $stmt->bindParam(4, $id);
        return $stmt->execute();
    }

    public function deleteCliente($id) {
        $query = "UPDATE clientes SET activo = FALSE WHERE cliente_id = ?";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(1, $id);
        return $stmt->execute();
    }

    public function getTelefonosByCliente($cliente_id) {
        $query = "SELECT * FROM telefonos WHERE cliente_id = ?";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(1, $cliente_id);
        $stmt->execute();
        return $stmt;
    }

    public function addTelefono($cliente_id, $tipo, $codigo_area, $numero) {
        $query = "INSERT INTO telefonos (cliente_id, tipo, codigo_area, numero) VALUES (?, ?, ?, ?)";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(1, $cliente_id);
        $stmt->bindParam(2, $tipo);
        $stmt->bindParam(3, $codigo_area);
        $stmt->bindParam(4, $numero);
        return $stmt->execute();
    }

    public function getDireccionesByCliente($cliente_id) {
        $query = "SELECT d.*, l.nombre as localidad, p.nombre as provincia 
                  FROM direcciones d 
                  JOIN localidades l ON d.localidad_id = l.localidad_id 
                  JOIN provincias p ON l.provincia_id = p.provincia_id 
                  WHERE d.cliente_id = ?";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(1, $cliente_id);
        $stmt->execute();
        return $stmt;
    }

    public function addDireccion($cliente_id, $calle, $numero, $piso, $departamento, $provincia, $codigo_postal, $localidad) {
    try {
        // Iniciar transacción
        $this->db->beginTransaction();
        
        // 1. Insertar o obtener provincia
        $queryProvincia = "INSERT INTO provincias (nombre) VALUES (?) 
                          ON DUPLICATE KEY UPDATE provincia_id=LAST_INSERT_ID(provincia_id)";
        $stmtProvincia = $this->db->prepare($queryProvincia);
        $stmtProvincia->execute([$provincia]);
        $provincia_id = $this->db->lastInsertId();
        
        // 2. Insertar o obtener localidad (incluyendo código postal)
        $queryLocalidad = "INSERT INTO localidades (nombre, provincia_id, codigo_postal) VALUES (?, ?, ?) 
                          ON DUPLICATE KEY UPDATE localidad_id=LAST_INSERT_ID(localidad_id)";
        $stmtLocalidad = $this->db->prepare($queryLocalidad);
        $stmtLocalidad->execute([$localidad, $provincia_id, $codigo_postal]);
        $localidad_id = $this->db->lastInsertId();
        
        // 3. Insertar dirección (sin provincia_id, solo localidad_id)
        $queryDireccion = "INSERT INTO direcciones 
                          (cliente_id, calle, numero, piso, departamento, localidad_id) 
                          VALUES (?, ?, ?, ?, ?, ?)";
        $stmtDireccion = $this->db->prepare($queryDireccion);
        $stmtDireccion->execute([
            $cliente_id,
            $calle,
            $numero,
            $piso,
            $departamento,
            $localidad_id
        ]);
        
        // Confirmar transacción
        $this->db->commit();
        return true;
        
    } catch (PDOException $e) {
        // Revertir transacción en caso de error
        $this->db->rollBack();
        throw new Exception("Error al guardar la dirección: " . $e->getMessage());
    }
}
    
}
?>