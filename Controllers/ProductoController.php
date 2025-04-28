<?php
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);

require_once '../Models/Producto.php';

$Producto = new Producto();

$accion = $_POST['accion'] ?? $_GET['accion'] ?? '';

switch ($accion) {
    case 'index':
        $filtro = $_POST['busqueda'] ?? '';
        $productos = $Producto->obtenerTodos($filtro);
        echo json_encode($productos);
        break;

    case 'store':
        $nombre = $_POST['nombre'] ?? '';
        $precio = $_POST['precio'] ?? 0;
        $tipo = $_POST['tipo'] ?? '';

        // Validación básica
        if ($nombre && $precio > 0 && $tipo) {
            $exito = $Producto->crear($nombre, $precio, $tipo);
            echo json_encode(['success' => $exito]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Datos inválidos']);
        }
        break;


    case 'delete':
        $id = $_POST['id'] ?? 0;
        $exito = $Producto->eliminar($id);
        echo json_encode(['success' => $exito]);
        break;

    case 'show':
        $id = $_GET['id'] ?? 0;
        $producto = $Producto->obtenerPorId($id);
        echo json_encode($producto);
        break;

    default:
        echo json_encode(['error' => 'Acción no válida']);
        break;
}
