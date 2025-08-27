<?php
require_once __DIR__ . '/../DAL/ClienteDAL.php';
require_once __DIR__ . '/../models/ClienteModel.php';

class ClienteController {
    private $clienteDAL;

    public function __construct() {
        $this->clienteDAL = new ClienteDAL();
    }

    public function listar() {
        try {
            return $this->clienteDAL->obtenerClientes();
        } catch (Exception $e) {
            error_log("Error al listar clientes: " . $e->getMessage());
            return [];
        }
    }

    public function agregarCliente($nombre, $apellido, $cuil, $activo = 1) {
        try {
            $cliente = new Cliente(null, $nombre, $apellido, $cuil, $activo);
            return $this->clienteDAL->crearCliente($cliente);
        } catch (Exception $e) {
            error_log("Error al agregar cliente: " . $e->getMessage());
            return false;
        }
    }
}