<?php
class Telefono {
    private int $idCliente;
    private int $telefono;
    private string $tipo;

    public function __construct(int $idCliente, int $telefono, string $tipo){
        $this->idCliente = $idCliente;
        $this->telefono = $telefono;
        $this->tipo = $tipo;

    }

        public function getIdCliente() {
        return $this->idCliente;
    }

    public function getTelefono() {
        return $this->telefono;
    }

    public function getTipo() {
        return $this->tipo;
    }
}
?>