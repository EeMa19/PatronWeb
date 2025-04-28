<?php
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);
// Incluimos el controlador y modelo necesarios
require_once '../Models/Dashboard.php';

header('Content-Type: application/json');

// Creamos un objeto de Dashboard para obtener los datos
$dashboard = new Dashboard();

// Obtenemos los datos
$pedidosDelDia = $dashboard->getPedidosDelDia();
$ventasDelDia = $dashboard->getVentasDelDia();
$tacosMasVendidos = $dashboard->getTacosMasVendidos();
$pedidosRecientes = $dashboard->getPedidosRecientes();

// Preparamos la respuesta
$response = [
    'pedidosDelDia' => $pedidosDelDia,
    'ventasDelDia' => $ventasDelDia,
    'tacosMasVendidos' => $tacosMasVendidos,
    'pedidosRecientes' => $pedidosRecientes
];

// Enviamos la respuesta en formato JSON
echo json_encode($response);
?>