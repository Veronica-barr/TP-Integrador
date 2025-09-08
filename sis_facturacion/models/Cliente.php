<?php
class Cliente {
    private ?int $idCliente;
    private string $nombre;
    private string $apellido;
    private string $cuil;
    private string $email;
    private ?string $fechaRegistro;
    private ?string $fechaActualizacion;

    /** @var string[] */
    private array $telefonos = [];

    /** @var string[] */
    private array $direcciones = [];

    public function __construct(
        ?int $idCliente = null,
        string $nombre = "",
        string $apellido = "",
        string $cuil = "",
        string $email = "",
        ?string $fechaRegistro = null,
        ?string $fechaActualizacion = null
    ) {
        $this->idCliente = $idCliente;
        $this->nombre = $nombre;
        $this->apellido = $apellido;
        $this->cuil = $cuil;
        $this->email = $email;
        $this->fechaRegistro = $fechaRegistro;
        $this->fechaActualizacion = $fechaActualizacion;
    }

    // --- GETTERS ---
    public function getIdCliente(): ?int { return $this->idCliente; }
    public function getNombre(): string { return $this->nombre; }
    public function getApellido(): string { return $this->apellido; }
    public function getCuil(): string { return $this->cuil; }
    public function getEmail(): string { return $this->email; }
    public function getFechaRegistro(): ?string { return $this->fechaRegistro; }
    public function getFechaActualizacion(): ?string { return $this->fechaActualizacion; }
    public function getTelefonos(): array { return $this->telefonos; }
    public function getDirecciones(): array { return $this->direcciones; }

    // --- SETTERS ---
    public function setIdCliente(?int $idCliente): void { $this->idCliente = $idCliente; }
    public function setNombre(string $nombre): void { $this->nombre = $nombre; }
    public function setApellido(string $apellido): void { $this->apellido = $apellido; }
    public function setCuil(string $cuil): void { $this->cuil = $cuil; }
    public function setEmail(string $email): void { $this->email = $email; }
    public function setFechaRegistro(?string $fechaRegistro): void { $this->fechaRegistro = $fechaRegistro; }
    public function setFechaActualizacion(?string $fechaActualizacion): void { $this->fechaActualizacion = $fechaActualizacion; }
    public function setTelefonos(array $telefonos): void { $this->telefonos = $telefonos; }
    public function setDirecciones(array $direcciones): void { $this->direcciones = $direcciones; }

    // --- Métodos para agregar elementos ---
    public function addTelefono(string $telefono): void {
        $this->telefonos[] = $telefono;
    }

    public function addDireccion(string $direccion): void {
        $this->direcciones[] = $direccion;
    }
}
?>