<?php
class ProductoController {
    private $model;

    // Constructor que recibe el modelo de producto
    // Se inyecta el modelo para que el controlador pueda interactuar con la base de datos
    // El modelo es una instancia de ProductoModel que se pasa al controlador
    public function __construct($model) {
        $this->model = $model;
    }

    // Lista todos los productos
    // Este método obtiene todos los productos del modelo y los muestra en la vista
    public function list() {
        $productos = $this->model->getAllProductos();
        require_once 'views/productos/list.php';
    }

    // Muestra el formulario para crear un nuevo producto
    public function create() {
        require_once 'views/productos/create.php';
    }

    // Guarda un nuevo producto en la base de datos
    public function store() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $codigo = $_POST['codigo'];
            $nombre = $_POST['nombre'];
            $descripcion = $_POST['descripcion'];
            $precio_unitario = $_POST['precio_unitario'];
            $porcentaje_impuesto = $_POST['porcentaje_impuesto'];
            $stock = $_POST['stock'];

            // Validar datos
            if ($this->model->createProducto($codigo, $nombre, $descripcion, $precio_unitario, $porcentaje_impuesto, $stock)) {
                header('Location: index.php?module=productos&action=list');
            } else {
                echo "Error al crear el producto";
            }
        }
    }

    // Edita un producto existente
    public function edit() {
        $id = $_GET['id'];
        $producto = $this->model->getProductoById($id);
        require_once 'views/productos/edit.php';
    }

    // Actualiza un producto existente
    public function update() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = $_POST['id'];
            $codigo = $_POST['codigo'];
            $nombre = $_POST['nombre'];
            $descripcion = $_POST['descripcion'];
            $precio_unitario = $_POST['precio_unitario'];
            $porcentaje_impuesto = $_POST['porcentaje_impuesto'];
            $stock = $_POST['stock'];

            // Validar datos
            if ($this->model->updateProducto($id, $codigo, $nombre, $descripcion, $precio_unitario, $porcentaje_impuesto, $stock)) {
                header('Location: index.php?module=productos&action=list');
            } else { // Si la actualización falla, muestra un mensaje de error
                echo "Error al actualizar el producto";
            }
        }
    }

    // Elimina un producto
    // Este método recibe el ID del producto a eliminar y lo elimina de la base de datos
    public function delete() {
        $id = $_GET['id'];
        if ($this->model->deleteProducto($id)) {
            header('Location: index.php?module=productos&action=list');
        } else {
            echo "Error al eliminar el producto";
        }
    }

    // Muestra los detalles de un producto
    // Este método obtiene los detalles del producto por ID y los muestra en una vista
    public function show() {
        $id = $_GET['id'];
        $producto = $this->model->getProductoById($id);
        require_once 'views/productos/show.php';
    }
}
?>