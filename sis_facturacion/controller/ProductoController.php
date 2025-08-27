<?php
require_once __DIR__ . '/../DAL/ProductoDAL.php';

class ProductoController {
    private $productoDAL;

    public function __construct() {
        $this->productoDAL = new ProductoDAL();
    }

        public function listar() {
        try {
            $productos = $this->productoDAL->obtenerProductos();
            
            // Incluir la vista
            require_once __DIR__ . '/../includes/header.php';
            echo '<h2>Listado de Productos</h2>';
            
            if (empty($productos)) {
                echo '<div class="alert alert-info">No hay productos registrados.</div>';
            } else {
                echo '<table class="table table-striped">';
                echo '<thead><tr><th>ID</th><th>Nombre</th><th>Precio</th></tr></thead>';
                echo '<tbody>';
                foreach ($productos as $producto) {
                    echo '<tr>';
                    echo '<td>' . $producto->producto_id . '</td>';
                    echo '<td>' . htmlspecialchars($producto->nombre) . '</td>';
                    echo '<td>$' . number_format($producto->precio_unitario, 2) . '</td>';
                    echo '</tr>';
                }
                echo '</tbody></table>';
            }
            
            require_once __DIR__ . '/../includes/footer.php';
            
        } catch (Exception $e) {
            error_log("Error al listar productos: " . $e->getMessage());
            echo '<div class="alert alert-danger">Error al cargar los productos.</div>';
        }
    }

    public function listarProductos() {
        return $this->productoDAL->obtenerProductos();
    }

    public function agregarProducto($nombre, $precio) {
        $producto = new Producto(null, $nombre, $precio);
        return $this->productoDAL->crearProducto($producto);
    }
}
