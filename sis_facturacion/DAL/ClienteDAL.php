<?php
class ClienteDAL extends BaseDAL {
    
    public function getAll() {
        $query = "SELECT * FROM clientes WHERE activo = TRUE";
        return $this->executeQuery($query);
    }

    public function getById($id) {
        $query = "SELECT * FROM clientes WHERE cliente_id = ?";
        $stmt = $this->executeQuery($query, [$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function create($nombre, $apellido, $cuil) {
        $query = "INSERT INTO clientes (nombre, apellido, cuil) VALUES (?, ?, ?)";
        $this->executeNonQuery($query, [$nombre, $apellido, $cuil]);
        return $this->getLastInsertId();
    }

    public function update($id, $nombre, $apellido, $cuil) {
        $query = "UPDATE clientes SET nombre = ?, apellido = ?, cuil = ? WHERE cliente_id = ?";
        return $this->executeNonQuery($query, [$nombre, $apellido, $cuil, $id]);
    }

    public function delete($id) {
        $query = "UPDATE clientes SET activo = FALSE WHERE cliente_id = ?";
        return $this->executeNonQuery($query, [$id]);
    }

    public function existsCuil($cuil) {
        $query = "SELECT COUNT(*) FROM clientes WHERE REPLACE(REPLACE(cuil, '-', ''), ' ', '') = ?";
        $stmt = $this->executeQuery($query, [$cuil]);
        return $stmt->fetchColumn() > 0;
    }

    public function getTelefonos($cliente_id) {
        $query = "SELECT * FROM telefonos WHERE cliente_id = ?";
        return $this->executeQuery($query, [$cliente_id]);
    }

    public function addTelefono($cliente_id, $tipo, $codigo_area, $numero) {
        $query = "INSERT INTO telefonos (cliente_id, tipo, codigo_area, numero) VALUES (?, ?, ?, ?)";
        return $this->executeNonQuery($query, [$cliente_id, $tipo, $codigo_area, $numero]);
    }

    public function getDirecciones($cliente_id) {
        $query = "SELECT d.*, l.nombre as localidad, p.nombre as provincia, l.codigo_postal 
                  FROM direcciones d 
                  JOIN localidades l ON d.localidad_id = l.localidad_id 
                  JOIN provincias p ON l.provincia_id = p.provincia_id 
                  WHERE d.cliente_id = ?";
        return $this->executeQuery($query, [$cliente_id]);
    }

    public function addDireccion($cliente_id, $calle, $numero, $piso, $departamento, $localidad_id) {
        $query = "INSERT INTO direcciones (cliente_id, calle, numero, piso, departamento, localidad_id) 
                  VALUES (?, ?, ?, ?, ?, ?)";
        return $this->executeNonQuery($query, [$cliente_id, $calle, $numero, $piso, $departamento, $localidad_id]);
    }
}
?>