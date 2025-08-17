<?php
class ClienteController {
// Modelo de cliente
// Este controlador maneja las operaciones relacionadas con los clientes
    private $model;

    // Constructor que recibe el modelo de cliente
    // Se inyecta el modelo para que el controlador pueda interactuar con la base de datos
    // El modelo es una instancia de ClienteModel que se pasa al controlador
    public function __construct($model) {
        $this->model = $model;
    }

    // Lista todos los clientes activos
    // Este método obtiene todos los clientes activos del modelo y los muestra en la vista
    public function list() {
        $clientes = $this->model->getAllClientes();
        require_once 'views/clientes/list.php';
    }

    // Muestra el formulario para crear un nuevo cliente
    // Este método carga la vista del formulario de creación de cliente
    public function create() {
        require_once 'views/clientes/create.php';
    }

    // Guarda un nuevo cliente en la base de datos
    // Este método procesa el formulario de creación de cliente y guarda los datos en la base de datos
    public function store() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Validar datos
            $requiredFields = ['nombre', 'apellido', 'cuil'];

            // Recorre los campos requeridos y verifica si están vacíos
            // Si algún campo requerido está vacío, redirige al formulario con un mensaje de error
            foreach ($requiredFields as $field) {
                if (empty($_POST[$field])) {
                    $_SESSION['form_error'] = "El campo $field es requerido";
                    $_SESSION['form_data'] = $_POST;
                    header('Location: index.php?module=clientes&action=create');
                    exit;// termina la ejecución aquí si hay error
                }
            }

            // Procesar teléfonos
            $telefonos = [];
            // Si se envían teléfonos, los procesa
            // $_POST['telefonos'] es un array de teléfonos enviados desde el formulario
            if (!empty($_POST['telefonos'])) {
                foreach ($_POST['telefonos'] as $tel) {
                    if (!empty($tel['numero'])) {
                        $telefonos[] = $tel;
                    }
                }
            }

            // Procesar direcciones
            $direcciones = [];
            // Si se envían direcciones, las procesa
            // $_POST['direcciones'] es un array de direcciones enviadas desde el formulario
            if (!empty($_POST['direcciones'])) {
                foreach ($_POST['direcciones'] as $dir) {
                    if (!empty($dir['calle']) && !empty($dir['numero'])) {
                        $direcciones[] = $dir;
                    }
                }
            }

            // Crear cliente
            $result = $this->model->createCliente(
                // Se usa trim para eliminar espacios en blanco al inicio y al final
                // Se usa preg_replace para eliminar caracteres no numéricos del CUIL
                trim($_POST['nombre']),
                trim($_POST['apellido']),
                preg_replace('/[^0-9]/', '', $_POST['cuil'])
            );

            if (isset($result['error'])) {
                // Si hay un error al crear el cliente, redirige al formulario con el mensaje de error
                // $_SESSION['form_error'] almacena el mensaje de error
                $_SESSION['form_error'] = $result['error'];
                $_SESSION['form_data'] = $_POST;
                header('Location: index.php?module=clientes&action=create');
                exit;
            }

            $cliente_id = $result['id'];// ID del cliente recién creado

            // Agregar teléfonos
            foreach ($telefonos as $tel) {
                // Se usa el modelo para agregar cada teléfono asociado al cliente
                // $cliente_id es el ID del cliente recién creado
                $this->model->addTelefono(
                    $cliente_id,
                    $tel['tipo'],
                    $tel['codigo_area'],
                    $tel['numero']
                );
            }

            // Agregar direcciones
            foreach ($direcciones as $dir) {
                // Se usa el modelo para agregar cada dirección asociada al cliente
                $this->model->addDireccion(
                    $cliente_id,
                    $dir['calle'],
                    $dir['numero'],
                    $dir['piso'] ?? null,
                    $dir['departamento'] ?? null,
                    $dir['provincia'],
                    $dir['codigo_postal'],
                    $dir['localidad']
                );
            }

            // Redirige a la lista de clientes con un mensaje de éxito
            $_SESSION['form_success'] = 'Cliente registrado exitosamente';
            header('Location: index.php?module=clientes&action=list');
            exit;
        }
        header('Location: index.php?module=clientes');// Redirige si no es una solicitud POST
    }

    // Muestra el formulario para editar un cliente
    // Este método carga los datos del cliente y muestra el formulario de edición
    public function edit() {
        $id = $_GET['id'];
        $cliente = $this->model->getClienteById($id);
        require_once 'views/clientes/edit.php';
    }

    // Actualiza un cliente existente
    // Este método procesa el formulario de edición y actualiza los datos del cliente en la base de datos
    public function update() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = $_POST['id'];
            $nombre = $_POST['nombre'];
            $apellido = $_POST['apellido'];
            $cuil = $_POST['cuil'];

            // Validar datos
            if ($this->model->updateCliente($id, $nombre, $apellido, $cuil)) {
                header('Location: index.php?module=clientes&action=list');
            } else {
                echo "Error al actualizar el cliente";
            }
        }
    }
// Elimina un cliente
// Este método elimina un cliente de la base de datos
    public function delete() {
        $id = $_GET['id'];
        if ($this->model->deleteCliente($id)) {
            header('Location: index.php?module=clientes&action=list');
        } else {
            echo "Error al eliminar el cliente";
        }
    }

    // Muestra los detalles de un cliente
    // Este método obtiene los detalles del cliente y los muestra en una vista
    public function show() {
        $id = $_GET['id'];
        $cliente = $this->model->getClienteById($id);
        $telefonos = $this->model->getTelefonosByCliente($id);
        $direcciones = $this->model->getDireccionesByCliente($id);
        require_once 'views/clientes/show.php';
    }
}
?>