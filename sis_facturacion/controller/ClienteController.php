<?php
class ClienteController {
    private $model;

    public function __construct($model) {
        $this->model = $model;
    }

    public function list() {
        $clientes = $this->model->getAllClientes();
        require_once 'views/clientes/list.php';
    }

    public function create() {
        require_once 'views/clientes/create.php';
    }

    public function store() {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Recoger y limpiar datos
        $nombre = trim($_POST['nombre']);
        $apellido = trim($_POST['apellido']);
        $cuil = trim($_POST['cuil']);
        
        // Validación básica de campos requeridos
        if (empty($nombre) || empty($apellido) || empty($cuil)) {
            $_SESSION['form_error'] = 'Todos los campos obligatorios deben completarse';
            $_SESSION['form_data'] = $_POST;
            header('Location: index.php?module=clientes&action=create');
            exit;
        }
        
        // Intentar crear el cliente
        $result = $this->model->createCliente($nombre, $apellido, $cuil);
        
        if (isset($result['error'])) {
            // Hubo un error, redirigir con mensaje
            $_SESSION['form_error'] = $result['error'];
            $_SESSION['form_data'] = $_POST;
            header('Location: index.php?module=clientes&action=create');
            exit;
        }
        
        // Cliente creado exitosamente, procesar teléfonos y direcciones
        $cliente_id = $result['id'];
        
        // Procesar teléfonos si existen
        if (!empty($_POST['telefonos'])) {
            foreach ($_POST['telefonos'] as $telefono) {
                $this->model->addTelefono(
                    $cliente_id,
                    $telefono['tipo'],
                    $telefono['codigo_area'],
                    $telefono['numero']
                );
            }
        }
        
        // Procesar direcciones si existen
        if (!empty($_POST['direcciones'])) {
            foreach ($_POST['direcciones'] as $direccion) {
                $this->model->addDireccion(
                    $cliente_id,
                    $direccion['calle'],
                    $direccion['numero'],
                    $direccion['piso'] ?? null,
                    $direccion['departamento'] ?? null,
                    $direccion['provincia'],
                    $direccion['codigo_postal'],
                    $direccion['localidad']
                );
            }
        }
        
        // Éxito - redirigir al listado
        $_SESSION['form_success'] = 'Cliente registrado exitosamente';
        header('Location: index.php?module=clientes&action=list');
        exit;
    }
}

    public function edit() {
        $id = $_GET['id'];
        $cliente = $this->model->getClienteById($id);
        require_once 'views/clientes/edit.php';
    }

    public function update() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = $_POST['id'];
            $nombre = $_POST['nombre'];
            $apellido = $_POST['apellido'];
            $cuil = $_POST['cuil'];

            if ($this->model->updateCliente($id, $nombre, $apellido, $cuil)) {
                header('Location: index.php?module=clientes&action=list');
            } else {
                echo "Error al actualizar el cliente";
            }
        }
    }

    public function delete() {
        $id = $_GET['id'];
        if ($this->model->deleteCliente($id)) {
            header('Location: index.php?module=clientes&action=list');
        } else {
            echo "Error al eliminar el cliente";
        }
    }

    public function show() {
        $id = $_GET['id'];
        $cliente = $this->model->getClienteById($id);
        $telefonos = $this->model->getTelefonosByCliente($id);
        $direcciones = $this->model->getDireccionesByCliente($id);
        require_once 'views/clientes/show.php';
    }
}
?>