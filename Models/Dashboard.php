<?php
require_once '../Classes/Database.php';

class Dashboard
{
    private $db;

    public function __construct()
    {
        $this->db = (new Database())->getConnection();
    }

    // Obtener el total de pedidos del día
    public function getPedidosDelDia()
    {
        $stmt = $this->db->prepare("SELECT COUNT(*) AS total FROM pedidos WHERE fecha = CURDATE()");
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['total'] ?? 0;
    }

    // Obtener el total de ventas del día
    public function getVentasDelDia()
    {
        $stmt = $this->db->prepare("SELECT SUM(total) AS total FROM pedidos WHERE fecha = CURDATE()");
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['total'] ?? 0;
    }

    // Obtener los tacos más vendidos del día
    public function getTacosMasVendidos()
    {
        $stmt = $this->db->prepare("
            SELECT p.nombre AS producto, SUM(dp.cantidad) AS cantidad_vendida
            FROM detalle_pedidos dp
            JOIN pedidos o ON o.id = dp.pedido_id
            JOIN productos p ON p.id = dp.producto_id
            WHERE o.fecha = CURDATE() AND p.tipo = 'taco'
            GROUP BY p.nombre
            ORDER BY cantidad_vendida DESC
            LIMIT 1
        ");
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ? $result['producto'] : 'N/A';
    }

    // Obtener los pedidos recientes
    public function getPedidosRecientes()
    {
        $stmt = $this->db->prepare("
            SELECT 
                p.id,
                c.nombre AS cliente,
                GROUP_CONCAT(CONCAT(dp.cantidad, 'x ', pr.nombre) SEPARATOR ', ') AS productos,
                p.total,
                p.hora
            FROM pedidos p
            JOIN clientes c ON c.id = p.cliente_id
            JOIN detalle_pedidos dp ON dp.pedido_id = p.id
            JOIN productos pr ON pr.id = dp.producto_id
            WHERE p.fecha = CURDATE()
            GROUP BY p.id
            ORDER BY p.hora DESC
            LIMIT 5
        ");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>
