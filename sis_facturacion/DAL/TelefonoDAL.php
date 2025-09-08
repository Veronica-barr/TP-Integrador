<?php
require_once __DIR__ . '/../models/Telefono.php';

class TelefonoDAL {
    private $usuario = 'root';
    private $contrasena = '00001';
    private $servidor = 'localhost';
    private $basededatos = 'sis_facturacionbd';

    private function conectar() {
        $conexion = mysqli_connect(
            $this->servidor,
            $this->usuario,
            $this->contrasena,
            $this->basededatos
        );

        if (!$conexion) {
            die("Error de conexión: " . mysqli_connect_error());
        }

        mysqli_set_charset($conexion, 'utf8');
        return $conexion;
    }

    public function insertarTelefono(Telefono $telefono) {
        $conexion = $this->conectar();

        $sql = sprintf(
            "INSERT INTO telefonoscliente (idCliente, telefono, tipoTelefono) VALUES (%d, %d, '%s')",
            $telefono->getIdCliente(),
            $telefono->getTelefono(),
            mysqli_real_escape_string($conexion, $telefono->getTipo())
        );

        mysqli_query($conexion, $sql);
        mysqli_close($conexion);
    }

public function obtenerTelefonosPorCliente(int $idCliente): array {
    $conexion = $this->conectar();

    $sql = "SELECT * FROM telefonoscliente WHERE idCliente = $idCliente";
    $resultado = mysqli_query($conexion, $sql);

    $telefonos = [];
    if ($resultado) {
        while ($row = mysqli_fetch_assoc($resultado)) {
            $telefono = new Telefono(
                (int)$row['idCliente'],
                (int)$row['telefono'],
                $row['tipoTelefono']
            );
            $telefonos[] = $telefono;
        }
        mysqli_free_result($resultado);
    }

    mysqli_close($conexion);
    return $telefonos;
}


}
?>
