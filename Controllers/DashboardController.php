<?php
require_once '../Models/Dashboard.php';

class DashboardController
{
    public function index()
    {
        $dashboard = new Dashboard();

        // Obtener estadísticas del Dashboard
        $pedidosDelDia = $dashboard->getPedidosDelDia();
        $ventasDelDia = $dashboard->getVentasDelDia();
        $tacosMasVendidos = $dashboard->getTacosMasVendidos();
        $pedidosRecientes = $dashboard->getPedidosRecientes();

        // Incluir la vista y pasarle los datos
    }
}
?>