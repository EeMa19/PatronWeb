<?php
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);
require_once '../Models/Pedido.php';

class PedidoController
{
    public function index($filtroEstado, $fecha, $busqueda)
    {
        $pedido = new Pedido();

        // Obtener los pedidos filtrados según los parámetros
        $pedidos = $pedido->getPedidos($filtroEstado, $fecha, $busqueda);
        // Al obtener los pedidos, asegúrate de usar json_encode para convertirlos a JSON
        echo json_encode($pedidos);

    }

    // En el controlador PedidoController
    public function cambiarEstado()
    {
        // Verificar que los datos necesarios estén presentes en el POST
        if (isset($_POST['id']) && isset($_POST['estado'])) {
            $idPedido = $_POST['id'];
            $nuevoEstado = $_POST['estado'];

            $pedido = new Pedido();
            $resultado = $pedido->cambiarEstado($idPedido, $nuevoEstado);

            // Retornar el resultado en formato JSON
            echo json_encode(['success' => $resultado]);
        } else {
            // En caso de que falten datos, enviar un mensaje de error
            echo json_encode(['success' => false, 'message' => 'Faltan parámetros']);
        }
    }
    public function crear()
    {
        $input = json_decode(file_get_contents("php://input"), true);

        if (isset($input['cliente_id']) && isset($input['productos'])) {
            $cliente_id = $input['cliente_id'];
            $productos = $input['productos']; // ya viene decodificado como array

            if (empty($productos)) {
                echo json_encode(['success' => false, 'message' => 'Lista de productos vacía']);
                return;
            }

            $pedido = new Pedido();
            $resultado = $pedido->crearPedido($cliente_id, $productos);
            if ($resultado) {
                echo json_encode(['success' => true, 'message' => 'Pedido registrado con éxito']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Error al registrar el pedido']);
            }
        } else {
            echo json_encode(['success' => false, 'message' => 'Datos incompletos']);
        }
    }


}

// Obtener la acción desde la URL
$accion = $_REQUEST['accion'] ?? '';



// Obtener los filtros de la petición (si los hay)
$filtroEstado = isset($_GET['estado']) ? $_GET['estado'] : '';
$fecha = isset($_GET['fecha']) ? $_GET['fecha'] : '';
$busqueda = isset($_GET['busqueda']) ? $_GET['busqueda'] : '';

$pedidos = new PedidoController();

switch ($accion) {
    case 'index':
        // Llamar al método index y pasar los filtros
        echo $pedidos->index($filtroEstado, $fecha, $busqueda);
        break;

    case 'cambiarEstado':
        // Llamar al método cambiarEstado
        echo $pedidos->cambiarEstado($_POST['id'], $_POST['estado']);
        break;

    case 'crear':
        $pedidos->crear();
        break;
    default:
        // Acción por defecto o 404
        echo "Acción no encontrada";
        break;
}
?>