<?php
class ClienteModel {
    private $db;

    // Constructor que recibe la conexión a la base de datos
    // Se inyecta el objeto de conexión para que el modelo pueda interactuar con la base de datos
    public function __construct($db) {
        $this->db = $db;
    }


// Obtiene todos los clientes activos
// Este método devuelve un conjunto de resultados con todos los clientes que están activos
    public function getAllClientes() {
        $query = "SELECT * FROM clientes WHERE activo = TRUE";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        return $stmt;// Devuelve un objeto PDOStatement, PDOStatement es un objeto que representa una sentencia preparada y ejecutada
    }

    // Obtiene un cliente por su ID
    // Este método busca un cliente específico por su ID y devuelve sus datos
    public function getClienteById($id) {
        $query = "SELECT * FROM clientes WHERE cliente_id = ?";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(1, $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);// Devuelve un array asociativo con los datos del cliente
    }

    // Obtiene el último ID insertado
    // Este método devuelve el último ID generado por una inserción en la base de datos
    public function getLastInsertId() {
        return $this->db->lastInsertId();
    }

    // Crea un nuevo cliente
    // Este método inserta un nuevo cliente en la base de datos
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
    
    if ($stmtCheck->fetchColumn() > 0) {// Si el CUIL ya existe, retorna un error
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
    
    // Si la inserción es exitosa, retorna el ID del cliente recién creado
    // Si falla, retorna un mensaje de error
    if ($stmt->execute()) {
        return ['success' => true, 'id' => $this->db->lastInsertId()];
    }
    
    return ['error' => 'Error al registrar el cliente'];
}

// Verifica si un CUIL ya existe en la base de datos
// Este método busca un CUIL en la base de datos y retorna true si ya existe, false si no
    public function existeCuil($cuil) {
        $cuil = preg_replace('/[^0-9]/', '', $cuil);
        $query = "SELECT COUNT(*) FROM clientes WHERE REPLACE(REPLACE(cuil, '-', ''), ' ', '') = ?";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(1, $cuil);
        $stmt->execute();
        return $stmt->fetchColumn() > 0;
    }

    // Actualiza un cliente existente
    // Este método actualiza los datos de un cliente en la base de datos
    public function updateCliente($id, $nombre, $apellido, $cuil) {
        // Normalizar CUIL
        $cuil = preg_replace('/[^0-9]/', '', $cuil);
        
        if (strlen($cuil) !== 11) {// Verifica que el CUIL tenga 11 dígitos
            throw new Exception("El CUIL/CUIT debe contener 11 dígitos");
        }
        
        // Formatear CUIL con guiones
        $cuil_formateado = substr($cuil, 0, 2).'-'.substr($cuil, 2, 8).'-'.substr($cuil, 10, 1);
        
        // Verificar si el CUIL ya existe (excluyendo el cliente actual)
        $query = "UPDATE clientes SET nombre = ?, apellido = ?, cuil = ? WHERE cliente_id = ?";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(1, $nombre);
        $stmt->bindParam(2, $apellido);
        $stmt->bindParam(3, $cuil_formateado);
        $stmt->bindParam(4, $id);
        return $stmt->execute();
    }


// Elimina un cliente
// Este método marca un cliente como inactivo en la base de datos
    public function deleteCliente($id) {
        $query = "UPDATE clientes SET activo = FALSE WHERE cliente_id = ?";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(1, $id);
        return $stmt->execute();
    }

    // Obtiene los teléfonos asociados a un cliente
    // Este método busca los teléfonos de un cliente específico por su ID y devuelve un conjunto de resultados
    public function getTelefonosByCliente($cliente_id) {
        $query = "SELECT * FROM telefonos WHERE cliente_id = ?";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(1, $cliente_id);
        $stmt->execute();
        return $stmt;
    }

    // Agrega un nuevo teléfono a un cliente
    // Este método inserta un nuevo teléfono asociado a un cliente en la base de datos
    public function addTelefono($cliente_id, $tipo, $codigo_area, $numero) {
        $query = "INSERT INTO telefonos (cliente_id, tipo, codigo_area, numero) VALUES (?, ?, ?, ?)";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(1, $cliente_id);
        $stmt->bindParam(2, $tipo);
        $stmt->bindParam(3, $codigo_area);
        $stmt->bindParam(4, $numero);
        return $stmt->execute();
    }

    // Obtiene las direcciones asociadas a un cliente
    // Este método busca las direcciones de un cliente específico por su ID y devuelve un conjunto
public function getDireccionesByCliente($cliente_id) {
    $query = "SELECT d.*, l.nombre as localidad, p.nombre as provincia, l.codigo_postal 
              FROM direcciones d 
              JOIN localidades l ON d.localidad_id = l.localidad_id 
              JOIN provincias p ON l.provincia_id = p.provincia_id 
              WHERE d.cliente_id = ?";
    $stmt = $this->db->prepare($query);
    $stmt->bindParam(1, $cliente_id);
    $stmt->execute();
    return $stmt;
}

// Agrega una nueva dirección a un cliente
// Este método inserta una nueva dirección asociada a un cliente en la base de datos
    public function addDireccion($cliente_id, $calle, $numero, $piso, $departamento, $provincia, $codigo_postal, $localidad) {
    try {
        // Iniciar transacción
        $this->db->beginTransaction();
        
        //  Insertar o obtener provincia
        $queryProvincia = "INSERT INTO provincias (nombre) VALUES (?) 
                          ON DUPLICATE KEY UPDATE provincia_id=LAST_INSERT_ID(provincia_id)";
        $stmtProvincia = $this->db->prepare($queryProvincia);
        $stmtProvincia->execute([$provincia]);
        $provincia_id = $this->db->lastInsertId();
        
        //  Insertar o obtener localidad (incluyendo código postal)
        $queryLocalidad = "INSERT INTO localidades (nombre, provincia_id, codigo_postal) VALUES (?, ?, ?) 
                          ON DUPLICATE KEY UPDATE localidad_id=LAST_INSERT_ID(localidad_id)";
        $stmtLocalidad = $this->db->prepare($queryLocalidad);
        $stmtLocalidad->execute([$localidad, $provincia_id, $codigo_postal]);
        $localidad_id = $this->db->lastInsertId();
        
        //  Insertar dirección (sin provincia_id, solo localidad_id)
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
        
    } catch (PDOException $e) {// Si ocurre un error, se captura la excepción
        // Revertir transacción en caso de error
        $this->db->rollBack();
        throw new Exception("Error al guardar la dirección: " . $e->getMessage());
    }
}
    
}
?>