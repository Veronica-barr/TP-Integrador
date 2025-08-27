<?php
class Cliente {
    public $cliente_id;
    public $nombre;
    public $apellido;
    public $cuil;
    public $fecha_registro;
    public $activo;
    public $telefonos = [];
    public $direcciones = [];

    public function __construct($id = null, $nombre = null, $apellido = null, $cuil = null) {
        $this->cliente_id = $id;
        $this->nombre = $nombre;
        $this->apellido = $apellido;
        $this->cuil = $cuil;
    }

    public function agregarTelefono($telefono) {
        $this->telefonos[] = $telefono;
    }

    public function agregarDireccion($direccion) {
        $this->direcciones[] = $direccion;
    }

    public function validar() {
        $errores = [];
        
        if (empty($this->nombre)) {
            $errores[] = "El nombre es requerido";
        }
        
        if (empty($this->apellido)) {
            $errores[] = "El apellido es requerido";
        }
        
        if (empty($this->cuil)) {
            $errores[] = "El CUIL es requerido";
        } elseif (strlen(preg_replace('/[^0-9]/', '', $this->cuil)) !== 11) {
            $errores[] = "El CUIL debe contener exactamente 11 dígitos";
        }
        
        return $errores;
    }
}
?>tion __construct($cliente_id, $nombre, $apellido, $cuil, $activo, $fecha_registro = null) {
        $this->cliente_id = $cliente_id;
        $this->nombre = $nombre;
        $this->apellido = $apellido;
        $this->cuil = $cuil;
        $this->activo = $activo;
        $this->fecha_registro = $fecha_registro;
    }
}
?>