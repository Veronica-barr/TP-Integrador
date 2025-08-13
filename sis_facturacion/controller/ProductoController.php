<?php
class ProductoController {
    private $model;

    public function __construct($model) {
        $this->model = $model;
    }

    public function list() {
        $productos = $this->model->getAllProductos();
        require_once 'views/productos/list.php';
    }

    public function create() {
        require_once 'views/productos/create.php';
    }

    public function store() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $codigo = $_POST['codigo'];
            $nombre = $_POST['nombre'];
            $descripcion = $_POST['descripcion'];
            $precio_unitario = $_POST['precio_unitario'];
            $porcentaje_impuesto = $_POST['porcentaje_impuesto'];
            $stock = $_POST['stock'];

            if ($this->model->createProducto($codigo, $nombre, $descripcion, $precio_unitario, $porcentaje_impuesto, $stock)) {
                header('Location: index.php?module=productos&action=list');
            } else {
                echo "Error al crear el producto";
            }
        }
    }

    public function edit() {
        $id = $_GET['id'];
        $producto = $this->model->getProductoById($id);
        require_once 'views/productos/edit.php';
    }

    public function update() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = $_POST['id'];
            $codigo = $_POST['codigo'];
            $nombre = $_POST['nombre'];
            $descripcion = $_POST['descripcion'];
            $precio_unitario = $_POST['precio_unitario'];
            $porcentaje_impuesto = $_POST['porcentaje_impuesto'];
            $stock = $_POST['stock'];

            if ($this->model->updateProducto($id, $codigo, $nombre, $descripcion, $precio_unitario, $porcentaje_impuesto, $stock)) {
                header('Location: index.php?module=productos&action=list');
            } else {
                echo "Error al actualizar el producto";
            }
        }
    }

    public function delete() {
        $id = $_GET['id'];
        if ($this->model->deleteProducto($id)) {
            header('Location: index.php?module=productos&action=list');
        } else {
            echo "Error al eliminar el producto";
        }
    }

    public function show() {
        $id = $_GET['id'];
        $producto = $this->model->getProductoById($id);
        require_once 'views/productos/show.php';
    }
}
?>