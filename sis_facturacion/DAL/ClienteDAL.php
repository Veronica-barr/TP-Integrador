<?php
require_once __DIR__ . '/../config/database.php';

require_once __DIR__ . '/../models/Cliente.php';

class ClienteDAL {
    private $conexion;

    public function __construct() {
        $this->conexion = Database::conectar();
    }

    // --------------------
    // OBTENER TODOS LOS CLIENTES
    // --------------------
    public function obtenerTodos(): array {
        $query = "SELECT * FROM clientes";
        $result = mysqli_query($this->conexion, $query);

        $clientes = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $id = (int)$row['idCliente'];
            $cliente = new Cliente(
                $id,
                $row['nombre'],
                $row['apellido'],
                $row['cuil'],
                $row['email']
            );
            $cliente->setTelefonos($this->obtenerTelefonos($id));
            $cliente->setDirecciones($this->obtenerDirecciones($id));
            $clientes[] = $cliente;
        }

        return $clientes;
    }

    public function obtenerPorId(int $id): ?Cliente {
        $query = "SELECT * FROM clientes WHERE idCliente = $id";
        $result = mysqli_query($this->conexion, $query);
        if ($row = mysqli_fetch_assoc($result)) {
            $cliente = new Cliente(
                (int)$row['idCliente'],
                $row['nombre'],
                $row['apellido'],
                $row['cuil'],
                $row['email']
            );
            $cliente->setTelefonos($this->obtenerTelefonos($id));
            $cliente->setDirecciones($this->obtenerDirecciones($id));
            return $cliente;
        }
        return null;
    }

    // --------------------
    // TELEFONOS Y DIRECCIONES
    // --------------------
    private function obtenerTelefonos(int $idCliente): array {
        $telefonos = [];
        $query = "SELECT telefono, tipoTelefono FROM telefonoscliente WHERE idCliente = $idCliente";
        $result = mysqli_query($this->conexion, $query);
        while ($row = mysqli_fetch_assoc($result)) {
            $telefonos[] = $row['telefono'] . ' (' . $row['tipoTelefono'] . ')';
        }
        return $telefonos;
    }

    private function obtenerDirecciones(int $idCliente): array {
        $direcciones = [];
        $query = "SELECT CONCAT(calle,' ',numero,' ',piso,' ',dpto,', ',ciudad,', ',provincia,' ',cp,' (',tipoDireccion,')') as direccion 
                  FROM direccionescliente WHERE idCliente = $idCliente";
        $result = mysqli_query($this->conexion, $query);
        while ($row = mysqli_fetch_assoc($result)) {
            $direcciones[] = $row['direccion'];
        }
        return $direcciones;
    }

    // --------------------
    // INSERTAR CLIENTE
    // --------------------
    public function insertar(array $datos): bool {
        $nombre = mysqli_real_escape_string($this->conexion, $datos['nombre']);
        $apellido = mysqli_real_escape_string($this->conexion, $datos['apellido']);
        $cuil = mysqli_real_escape_string($this->conexion, $datos['cuil']);
        $email = mysqli_real_escape_string($this->conexion, $datos['email']);

        $query = "INSERT INTO clientes (nombre, apellido, cuil, email) VALUES ('$nombre','$apellido','$cuil','$email')";
        if (!mysqli_query($this->conexion, $query)) return false;

        $idCliente = mysqli_insert_id($this->conexion);

        // Insertar teléfonos
        foreach ($datos['telefono'] as $index => $tel) {
            $tipo = $datos['tipoTelefono'][$index] ?? 'celular';
            $tel = mysqli_real_escape_string($this->conexion, $tel);
            mysqli_query($this->conexion, "INSERT INTO telefonoscliente (idCliente, telefono, tipoTelefono) VALUES ($idCliente,'$tel','$tipo')");
        }

        // Insertar direcciones
        foreach ($datos['calle'] as $index => $calle) {
            $num = $datos['numero'][$index] ?? '';
            $piso = $datos['piso'][$index] ?? '';
            $dpto = $datos['dpto'][$index] ?? '';
            $ciudad = $datos['ciudad'][$index] ?? '';
            $provincia = $datos['provincia'][$index] ?? '';
            $cp = $datos['cp'][$index] ?? '';
            $tipoDir = $datos['tipoDireccion'][$index] ?? 'envio';

            $queryDir = "INSERT INTO direccionescliente 
                (idCliente, calle, numero, piso, dpto, ciudad, provincia, cp, tipoDireccion) 
                VALUES ($idCliente,'$calle','$num','$piso','$dpto','$ciudad','$provincia','$cp','$tipoDir')";
            mysqli_query($this->conexion, $queryDir);
        }

        return true;
    }

    // --------------------
    // ACTUALIZAR CLIENTE
    // --------------------
    public function actualizar(array $datos, int $idCliente): bool {
        $nombre = mysqli_real_escape_string($this->conexion, $datos['nombre']);
        $apellido = mysqli_real_escape_string($this->conexion, $datos['apellido']);
        $cuil = mysqli_real_escape_string($this->conexion, $datos['cuil']);
        $email = mysqli_real_escape_string($this->conexion, $datos['email']);

        $query = "UPDATE clientes SET nombre='$nombre', apellido='$apellido', cuil='$cuil', email='$email' WHERE idCliente=$idCliente";
        return mysqli_query($this->conexion, $query);
    }

    // --------------------
    // ELIMINAR CLIENTE
    // --------------------
    public function eliminar(int $idCliente): bool {
        return mysqli_query($this->conexion, "DELETE FROM clientes WHERE idCliente=$idCliente");
    }
}
