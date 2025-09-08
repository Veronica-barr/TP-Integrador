<?php
class Direccion {
    private int $idCliente;
    private string $calle;
    private int $numero;
    private string $piso;
    private string $dpto;
    private string $ciudad;
    private string $provincia;
    private int $cp;
    private string $tipo;

    public function __construct(
        int $idCliente,
        string $calle,
        int $numero,
        string $piso,
        string $dpto,
        string $ciudad,
        string $provincia,
        int $cp,
        string $tipo
    ) {
        $this->idCliente = $idCliente;
        $this->calle = $calle;
        $this->numero = $numero;
        $this->piso = $piso;
        $this->dpto = $dpto;
        $this->ciudad = $ciudad;
        $this->provincia = $provincia;
        $this->cp = $cp;
        $this->tipo = $tipo;
    }

    // ✅ Getter correcto para ID del cliente
    public function getIdCliente() {
        return $this->idCliente;
    }

    public function getCalle() {
        return $this->calle;
    }

    public function getNumero() {
        return $this->numero;
    }

    public function getPiso() {
        return $this->piso;
    }

    public function getDpto() {
        return $this->dpto;
    }

    public function getCiudad() {
        return $this->ciudad;
    }

    public function getProvincia() {
        return $this->provincia;
    }

    public function getCp() {
        return $this->cp;
    }

    public function getTipo() {
        return $this->tipo;
    }
}
?>
