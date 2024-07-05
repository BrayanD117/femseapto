<?php
require_once __DIR__ . '/../../config/config.php';

class LineaAhorro {
    public $id;
    public $nombre;

    public function __construct($id = null, $nombre = '') {
        $this->id = $id;
        $this->nombre = $nombre;
    }

    public function guardar() {
        $db = getDB();
        if ($this->id === null) {
            $query = $db->prepare("INSERT INTO lineas_ahorro (nombre) VALUES (?)");
            $query->bind_param("s", $this->nombre);
        } else {
            $query = $db->prepare("UPDATE lineas_ahorro SET nombre = ? WHERE id = ?");
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
        $query = $db->prepare("SELECT * FROM lineas_ahorro WHERE id = ?");
        $query->bind_param("i", $id);
        $query->execute();
        $query->bind_result($id, $nombre);
        $lineaAhorro = null;
        if ($query->fetch()) {
            $lineaAhorro = new LineaAhorro($id, $nombre);
        }
        $query->close();
        $db->close();
        return $lineaAhorro;
    }

    public static function obtenerTodos() {
        $db = getDB();
        $query = "SELECT * FROM lineas_ahorro";
        $result = $db->query($query);
        $lineasAhorro = [];
        while ($row = $result->fetch_assoc()) {
            $lineasAhorro[] = new LineaAhorro($row['id'], $row['nombre']);
        }
        $db->close();
        return $lineasAhorro;
    }

    public function eliminar() {
        $db = getDB();
        if ($this->id !== null) {
            $query = $db->prepare("DELETE FROM lineas_ahorro WHERE id = ?");
            $query->bind_param("i", $this->id);
            $query->execute();
            $query->close();
        }
        $db->close();
    }
}
?>
