<?php
require_once '../Classes/Database.php';

class Pedido
{
    private $db;

    public function __construct()
    {
        $this->db = (new Database())->getConnection();
    }

    // Obtener todos los pedidos
    public function getPedidos($filtroEstado = '', $fecha = '', $busqueda = '')
    {
        $sql = "SELECT p.id, c.nombre AS cliente, p.hora, p.total, p.estado,
                   GROUP_CONCAT(pr.nombre) AS productos,
                   GROUP_CONCAT(dp.cantidad) AS cantidades,
                   GROUP_CONCAT(pr.precio) AS precios
            FROM pedidos p
            JOIN clientes c ON c.id = p.cliente_id
            LEFT JOIN detalle_pedidos dp ON dp.pedido_id = p.id
            LEFT JOIN productos pr ON pr.id = dp.producto_id
            WHERE 1=1";

        // Filtros
        if ($filtroEstado) {
            $sql .= " AND p.estado = :estado";
        }

        if ($fecha) {
            $sql .= " AND p.fecha = :fecha";
        }

        if ($busqueda) {
            $sql .= " AND (c.nombre LIKE :busqueda OR p.id LIKE :busqueda)";
        }

        $sql .= " GROUP BY p.id"; // Para agrupar los resultados por pedido

        $stmt = $this->db->prepare($sql);

        // Vincular parámetros
        if ($filtroEstado) {
            $stmt->bindParam(':estado', $filtroEstado);
        }

        if ($fecha) {
            $stmt->bindParam(':fecha', $fecha);
        }

        if ($busqueda) {
            $busqueda = "%$busqueda%";
            $stmt->bindParam(':busqueda', $busqueda);
        }

        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Cambiar el estado de un pedido
    // En el modelo Pedido
    public function cambiarEstado($idPedido, $nuevoEstado)
    {
        // Actualizar el estado en la tabla 'pedidos'
        $sql = "UPDATE pedidos SET estado = :estado WHERE id = :id";

        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':estado', $nuevoEstado);
        $stmt->bindParam(':id', $idPedido);

        return $stmt->execute();
    }
    public function crearPedido($cliente_id, $productos)
    {
        try {
            $this->db->beginTransaction();

            // Primero obtenemos los precios de los productos y calculamos el total
            $total = 0;
            foreach ($productos as $producto) {
                $sqlPrecio = "SELECT precio FROM productos WHERE id = ?";
                $stmtPrecio = $this->db->prepare($sqlPrecio);
                $stmtPrecio->execute([$producto['producto_id']]);
                $precio = $stmtPrecio->fetchColumn();

                if ($precio === false) {
                    throw new Exception("Producto con ID " . $producto['producto_id'] . " no encontrado.");
                }

                $total += $precio * $producto['cantidad'];
            }

            // Insertamos el pedido con el total calculado
            $sql = "INSERT INTO pedidos (cliente_id, fecha, estado, total) VALUES (?, NOW(), 'Pendiente', ?)";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$cliente_id, $total]);
            $pedido_id = $this->db->lastInsertId();

            // Insertamos los productos del pedido
            $sqlDetalle = "INSERT INTO detalle_pedidos (pedido_id, producto_id, cantidad) VALUES (?, ?, ?)";
            $stmtDetalle = $this->db->prepare($sqlDetalle);

            foreach ($productos as $producto) {
                $stmtDetalle->execute([$pedido_id, $producto['producto_id'], $producto['cantidad']]);
            }

            $this->db->commit();
            return true;
        } catch (Exception $e) {
            $this->db->rollBack();
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
            return false;
        }
    }
}
?>