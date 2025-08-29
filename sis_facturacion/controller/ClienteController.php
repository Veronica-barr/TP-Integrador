<?php
$controllerDir = __DIR__;
require_once $controllerDir . '/../DAL/ClienteDAL.php';
require_once $controllerDir . '/../models/Cliente.php';
require_once $controllerDir . '/../models/Telefono.php';
require_once $controllerDir . '/../models/Direccion.php';

class ClienteController {
    private $clienteDAL;

    public function __construct() {
        $this->clienteDAL = new ClienteDAL();
    }

    public function listarClientes() {
        return $this->clienteDAL->listarClientes();
    }

    public function obtenerCliente($cliente_id) {
        return $this->clienteDAL->obtenerClientePorId($cliente_id);
    }

    public function crearCliente($data) {
        $cliente = new Cliente();
        $cliente->nombre = $data['nombre'];
        $cliente->apellido = $data['apellido'];
        $cliente->cuil = $data['cuil'];
        
        // Procesar teléfonos
        if (isset($data['telefonos']) && is_array($data['telefonos'])) {
            foreach ($data['telefonos'] as $telData) {
                if (!empty($telData['numero'])) {
                    $telefono = new Telefono();
                    $telefono->tipo = $telData['tipo'];
                    $telefono->codigo_area = $telData['codigo_area'];
                    $telefono->numero = $telData['numero'];
                    $cliente->telefonos[] = $telefono;
                }
            }
        }
        
        // Procesar direcciones
        if (isset($data['direcciones']) && is_array($data['direcciones'])) {
            foreach ($data['direcciones'] as $dirData) {
                if (!empty($dirData['calle'])) {
                    $direccion = new Direccion();
                    $direccion->calle = $dirData['calle'];
                    $direccion->numero = $dirData['numero'];
                    $direccion->piso = $dirData['piso'] ?? '';
                    $direccion->departamento = $dirData['departamento'] ?? '';
                    $direccion->localidad_id = $dirData['localidad_id'];
                    $cliente->direcciones[] = $direccion;
                }
            }
        }
        
        return $this->clienteDAL->crearCliente($cliente);
    }

    public function actualizarCliente($cliente_id, $data) {
        $cliente = new Cliente();
        $cliente->cliente_id = $cliente_id;
        $cliente->nombre = $data['nombre'];
        $cliente->apellido = $data['apellido'];
        $cliente->cuil = $data['cuil'];
        
        // Procesar teléfonos
        if (isset($data['telefonos']) && is_array($data['telefonos'])) {
            foreach ($data['telefonos'] as $telData) {
                if (!empty($telData['numero'])) {
                    $telefono = new Telefono();
                    $telefono->telefono_id = $telData['telefono_id'] ?? 0;
                    $telefono->tipo = $telData['tipo'];
                    $telefono->codigo_area = $telData['codigo_area'];
                    $telefono->numero = $telData['numero'];
                    $cliente->telefonos[] = $telefono;
                }
            }
        }
        
        // Procesar direcciones
        if (isset($data['direcciones']) && is_array($data['direcciones'])) {
            foreach ($data['direcciones'] as $dirData) {
                if (!empty($dirData['calle'])) {
                    $direccion = new Direccion();
                    $direccion->direccion_id = $dirData['direccion_id'] ?? 0;
                    $direccion->calle = $dirData['calle'];
                    $direccion->numero = $dirData['numero'];
                    $direccion->piso = $dirData['piso'] ?? '';
                    $direccion->departamento = $dirData['departamento'] ?? '';
                    $direccion->localidad_id = $dirData['localidad_id'];
                    $cliente->direcciones[] = $direccion;
                }
            }
        }
        
        return $this->clienteDAL->actualizarCliente($cliente);
    }

    public function eliminarCliente($cliente_id) {
        return $this->clienteDAL->eliminarCliente($cliente_id);
    }
}
?>