<?php
require_once __DIR__ . '/../models/Direccion.php';

class DireccionDAL {
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

    public function insertarDireccion(Direccion $direccion) {
        $conexion = $this->conectar();

        $sql = sprintf(
            "INSERT INTO direccionescliente (idCliente, calle, numero, piso, dpto, ciudad, provincia, cp, tipoDireccion)
            VALUES (%d, '%s', %d, '%s', '%s', '%s', '%s', %d, '%s')",
            $direccion->getIdCliente(),
            mysqli_real_escape_string($conexion, $direccion->getCalle()),
            $direccion->getNumero(),
            mysqli_real_escape_string($conexion, $direccion->getPiso()),
            mysqli_real_escape_string($conexion, $direccion->getDpto()),
            mysqli_real_escape_string($conexion, $direccion->getCiudad()),
            mysqli_real_escape_string($conexion, $direccion->getProvincia()),
            $direccion->getCp(),
            mysqli_real_escape_string($conexion, $direccion->getTipo())
        );

        mysqli_query($conexion, $sql);
        mysqli_close($conexion);
    }

    public function obtenerDireccionesPorCliente(int $idCliente): array {
    $conexion = $this->conectar();

    $sql = "SELECT * FROM direccionescliente WHERE idCliente = $idCliente";
    $resultado = mysqli_query($conexion, $sql);

    $direcciones = [];
    if ($resultado) {
        while ($row = mysqli_fetch_assoc($resultado)) {
            $direccion = new Direccion(
                (int)$row['idCliente'],
                $row['calle'],
                (int)$row['numero'],
                $row['piso'],
                $row['dpto'],
                $row['ciudad'],
                $row['provincia'],
                (int)$row['cp'],
                $row['tipoDireccion']
            );
            $direcciones[] = $direccion;
        }
        mysqli_free_result($resultado);
    }

    mysqli_close($conexion);
    return $direcciones;
}

}
?>
