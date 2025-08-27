<?php
class LocalidadDAL extends BaseDAL {
    
    public function getOrCreateProvincia($nombre) {
        $query = "INSERT INTO provincias (nombre) VALUES (?) 
                  ON DUPLICATE KEY UPDATE provincia_id=LAST_INSERT_ID(provincia_id)";
        $this->executeNonQuery($query, [$nombre]);
        return $this->getLastInsertId();
    }

    public function getOrCreateLocalidad($nombre, $provincia_id, $codigo_postal) {
        $query = "INSERT INTO localidades (nombre, provincia_id, codigo_postal) VALUES (?, ?, ?) 
                  ON DUPLICATE KEY UPDATE localidad_id=LAST_INSERT_ID(localidad_id)";
        $this->executeNonQuery($query, [$nombre, $provincia_id, $codigo_postal]);
        return $this->getLastInsertId();
    }
}
?>