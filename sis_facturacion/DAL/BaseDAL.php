<?php
class BaseDAL {
    protected $db;

    public function __construct($db) {
        $this->db = $db;
    }

    protected function executeQuery($query, $params = []) {
        $stmt = $this->db->prepare($query);
        $stmt->execute($params);
        return $stmt;
    }

    protected function executeNonQuery($query, $params = []) {
        $stmt = $this->db->prepare($query);
        return $stmt->execute($params);
    }

    protected function getLastInsertId() {
        return $this->db->lastInsertId();
    }

    protected function beginTransaction() {
        return $this->db->beginTransaction();
    }

    protected function commit() {
        return $this->db->commit();
    }

    protected function rollBack() {
        return $this->db->rollBack();
    }
}
?>