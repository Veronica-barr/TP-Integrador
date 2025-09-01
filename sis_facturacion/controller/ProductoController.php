<?php



require_once __DIR__ . "/../DAL/ProductoDAL.php";

class ProductoController {
    private $dal;

    public function __construct() {
        $this->dal = new ProductoDAL();
    }

    public function listar() {
        return $this->dal->listar();
    }

    public function ver($id) {
        return $this->dal->getById($id);
    }

    public function crear($data) {
        $producto = new Producto(
            null,
            $data["codigo"],
            $data["nombre"],
            $data["descripcion"],
            $data["precio_unitario"],
            $data["porcentaje_impuesto"],
            $data["stock"],
            1
        );
        return $this->dal->insert($producto);
    }

    public function actualizar($id, $data) {
        $producto = new Producto(
            $id,
            $data["codigo"],
            $data["nombre"],
            $data["descripcion"],
            $data["precio_unitario"],
            $data["porcentaje_impuesto"],
            $data["stock"],
            $data["activo"]
        );
        return $this->dal->update($producto);
    }

    public function eliminar($id) {
        return $this->dal->delete($id);
    }
}
