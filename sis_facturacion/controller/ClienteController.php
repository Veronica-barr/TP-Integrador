<?php
require_once __DIR__ . '/../DAL/ClienteDAL.php';
require_once __DIR__ . '/../models/Cliente.php';

class ClienteController {
    private ClienteDAL $clienteDAL;

    public function __construct() {
        $this->clienteDAL = new ClienteDAL();
    }

    public function obtenerTodosLosClientes(): array {
        return $this->clienteDAL->obtenerTodos();
    }

    public function obtenerClientePorId(int $id): ?Cliente {
        return $this->clienteDAL->obtenerPorId($id);
    }

    public function crearCliente(array $datos): bool {
        return $this->clienteDAL->insertar($datos);
    }

    public function editarCliente(int $idCliente, array $datos): bool {
        return $this->clienteDAL->actualizar($datos, $idCliente);
    }

    public function eliminarCliente(int $idCliente): bool {
        return $this->clienteDAL->eliminar($idCliente);
    }
}
?>