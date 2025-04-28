<?php
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);
include("../classes/Database.php");

class Clientes
{
    private $db;

    public function __construct()
    {
        $this->db = (new Database())->getConnection();
    }

    public function getClientes($busqueda = '')
    {
        $sql = "SELECT c.id, c.nombre, c.telefono, 
                       COUNT(p.id) AS pedidos_count, 
                       SUM(pd.cantidad * pr.precio) AS total_gastado, 
                       MAX(p.fecha) AS ultima_compra
                FROM clientes c
                LEFT JOIN pedidos p ON c.id = p.cliente_id
                LEFT JOIN detalle_pedidos pd ON p.id = pd.pedido_id
                LEFT JOIN productos pr ON pd.producto_id = pr.id
                WHERE 1=1";

        // Si hay una búsqueda, se filtra por nombre o teléfono
        if ($busqueda) {
            $sql .= " AND (c.nombre LIKE :busqueda OR c.telefono LIKE :busqueda)";
        }

        $sql .= " GROUP BY c.id";

        $stmt = $this->db->prepare($sql);

        if ($busqueda) {
            $busqueda = "%$busqueda%";
            $stmt->bindParam(':busqueda', $busqueda);
        }

        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Obtener un cliente por ID
    public function getClienteById($id)
    {
        $sql = "SELECT * FROM clientes WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Crear un nuevo cliente
    public function crearCliente($nombre, $telefono)
    {
        $sql = "INSERT INTO clientes (nombre, telefono, created_at) VALUES (:nombre, :telefono, NOW())";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':nombre', $nombre);
        $stmt->bindParam(':telefono', $telefono);
        return $stmt->execute();
    }

    // Eliminar un cliente
    public function eliminarCliente($id)
    {
        $sql = "DELETE FROM clientes WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':id', $id);
        return $stmt->execute();
    }

    // Registrar con contraseña
    public function registrarDesdeApp($nombre, $telefono, $password)
    {
        $hash = password_hash($password, PASSWORD_BCRYPT);
        $sql = "INSERT INTO clientes (nombre, telefono, password, created_at) VALUES (:nombre, :telefono, :password, NOW())";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':nombre', $nombre);
        $stmt->bindParam(':telefono', $telefono);
        $stmt->bindParam(':password', $hash);
        return $stmt->execute();
    }

    // Buscar cliente por teléfono
    public function buscarPorTelefono($telefono)
    {
        $sql = "SELECT * FROM clientes WHERE telefono = :telefono";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':telefono', $telefono);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    public function login($telefono, $password)
    {
        // Verificar si el cliente existe por teléfono
        $sql = "SELECT * FROM clientes WHERE telefono = :telefono";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':telefono', $telefono);
        $stmt->execute();

        $cliente = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($cliente) {
            // Verificar la contraseña
            if (password_verify($password, $cliente['password'])) {
                return $cliente;  // Devolver los datos del cliente si el login es exitoso
            }
        }
        return false;  // Si no coincide o el cliente no existe
    }

}