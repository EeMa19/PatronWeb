<?php

require_once '../Classes/Database.php';

class Producto
{
    private $db;

    public function __construct()
    {
        $database = new Database();
        $this->db = $database->getConnection();
    }

    public function obtenerTodos($filtro = '')
    {
        $sql = "SELECT * FROM productos WHERE nombre LIKE ? OR tipo LIKE ? ORDER BY created_at DESC";
        $stmt = $this->db->prepare($sql);
        $like = '%' . $filtro . '%';
        $stmt->execute([$like, $like]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function obtenerPorId($id)
    {
        $sql = "SELECT * FROM productos WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function crear($nombre, $precio, $tipo)
    {
        $sql = "INSERT INTO productos (nombre, precio, tipo, created_at) VALUES (?, ?, ?, NOW())";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$nombre, $precio, $tipo]);
    }


    public function eliminar($id)
    {
        $sql = "DELETE FROM productos WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$id]);
    }
}
