<?php
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);
// ClientesController.php
require_once '../Models/Clientes.php';

class ClientesController
{
    private $clienteModel;

    public function __construct()
    {
        $this->clienteModel = new Clientes();
    }

    // Mostrar todos los clientes con filtro de búsqueda
    public function index($busqueda = '')
    {
        $clientes = $this->clienteModel->getClientes($busqueda);
        echo json_encode($clientes);
    }

    // Crear un nuevo cliente
    public function crear()
    {
        $nombre = isset($_POST['nombre']) ? $_POST['nombre'] : '';
        $telefono = isset($_POST['telefono']) ? $_POST['telefono'] : '';

        if ($this->clienteModel->crearCliente($nombre, $telefono)) {
            echo json_encode(['success' => true, 'message' => 'Cliente creado con éxito']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Error al crear el cliente']);
        }
    }

    // Eliminar un cliente
    public function eliminar()
    {
        $id = isset($_POST['id']) ? $_POST['id'] : '';

        if ($this->clienteModel->eliminarCliente($id)) {
            echo json_encode(['success' => true, 'message' => 'Cliente eliminado con éxito']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Error al eliminar el cliente']);
        }
    }
    public function registrar()
    {
        $data = json_decode(file_get_contents("php://input"));
        $nombre = isset($data->nombre) ? $data->nombre : '';
        $telefono = isset($data->telefono) ? $data->telefono : '';
        $password = isset($data->password) ? $data->password : '';  // Agregamos el password

        if (empty($nombre) || empty($telefono) || empty($password)) {
            echo json_encode(['success' => false, 'message' => 'Nombre, teléfono y contraseña son obligatorios']);
            return;
        }

        // Verificar si el teléfono ya está registrado
        $clienteExistente = $this->clienteModel->buscarPorTelefono($telefono);
        if ($clienteExistente) {
            echo json_encode(['success' => false, 'message' => 'Este número ya está registrado']);
            return;
        }

        // Registrar al cliente con contraseña
        if ($this->clienteModel->registrarDesdeApp($nombre, $telefono, $password)) {
            echo json_encode(['success' => true, 'message' => 'Cliente registrado exitosamente']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Error al registrar el cliente']);
        }
    }
    public function login()
    {
        $data = json_decode(file_get_contents("php://input"));
        $telefono = isset($data->telefono) ? $data->telefono : '';
        $password = isset($data->password) ? $data->password : '';

        if (empty($telefono) || empty($password)) {
            echo json_encode(['success' => false, 'message' => 'Teléfono y contraseña son obligatorios']);
            return;
        }

        // Verificar las credenciales del cliente
        $cliente = $this->clienteModel->login($telefono, $password);

        if ($cliente) {
            echo json_encode(['success' => true, 'cliente' => $cliente]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Teléfono o contraseña incorrectos']);
        }
    }
}

// Obtener la acción desde la URL
$accion = isset($_GET['accion']) ? $_GET['accion'] : '';

// Obtener el filtro de búsqueda
$busqueda = isset($_GET['busqueda']) ? $_GET['busqueda'] : '';

// Crear una nueva instancia del controlador de clientes
$clientes = new ClientesController();

switch ($accion) {
    case 'index':
        // Llamar al método index y pasar el filtro de búsqueda
        $clientes->index($busqueda);
        break;

    case 'crear':
        // Llamar al método crear
        $clientes->crear();
        break;

    case 'eliminar':
        // Llamar al método eliminar
        $clientes->eliminar();
        break;
    case 'registrar':
        // Llamar al método registrar
        $clientes->registrar();
        break;

    case 'login':
        // Llamar al método login
        $clientes->login();
        break;
    default:
        // Acción por defecto o 404
        echo "Acción no encontrada";
        break;
}
?>