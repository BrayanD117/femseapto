<?php
require_once __DIR__ . '/../../config/config.php';

class Rol {
    public $id;
    public $nombre;

    public function __construct($id = null, $nombre = '') {
        $this->id = $id;
        $this->nombre = $nombre;
    }

    public function guardar() {
        $db = getDB();
        if ($this->id === null) {
            $query = $db->prepare("INSERT INTO roles (nombre) VALUES (?)");
            $query->bind_param("s", $this->nombre);
        } else {
            $query = $db->prepare("UPDATE roles SET nombre = ? WHERE id = ?");
            $query->bind_param("si", $this->nombre, $this->id);
        }
        $query->execute();
        if ($this->id === null) {
            $this->id = $query->insert_id;
        }
        $query->close();
        $db->close();
    }

    public static function obtenerPorId($id) {
        $db = getDB();
        $query = $db->prepare("SELECT id, nombre FROM roles WHERE id = ?");
        $query->bind_param("i", $id);
        $query->execute();
        $query->bind_result($id, $nombre);
        $roles = null;
        if ($query->fetch()) {
            $roles = new Rol($id, $nombre);
        }
        $query->close();
        $db->close();
        return $roles;
    }

    public static function obtenerTodos() {
        $db = getDB();
        $query = "SELECT id, nombre FROM roles";
        $result = $db->query($query);
        $roles = [];
        while ($row = $result->fetch_assoc()) {
            $roles[] = new Rol($row['id'], $row['nombre']);
        }
        $db->close();
        return $roles;
    }

    public function eliminar() {
        $db = getDB();
        if ($this->id !== null) {
            $query = $db->prepare("DELETE FROM roles WHERE id = ?");
            $query->bind_param("i", $this->id);
            $query->execute();
            $query->close();
        }
        $db->close();
    }
}
?>