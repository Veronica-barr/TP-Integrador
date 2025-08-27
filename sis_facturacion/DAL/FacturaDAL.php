<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../models/FacturaModel.php';

class FacturaDAL {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    public function crearFactura(Factura $factura) {
        $stmt = $this->db->prepare("INSERT INTO facturas (cliente_id, fecha, total) VALUES (?, ?, ?)");
        $stmt->bind_param("isd", $factura->cliente_id, $factura->fecha, $factura->total);
        return $stmt->execute();
    }

    public function obtenerFacturas() {
        $result = $this->db->query("SELECT * FROM facturas");
        $facturas = [];
        while ($row = $result->fetch_assoc()) {
            $facturas[] = new Factura($row['id'], $row['cliente_id'], $row['fecha'], $row['total']);
        }
        return $facturas;
    }
}
