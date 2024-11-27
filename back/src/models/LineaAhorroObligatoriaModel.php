<?php
require_once __DIR__ . '/../../config/config.php';

class LineaAhorroObligatoria {
    public $id;
    public $nombre;

    public function __construct($id = null, $nombre = '') {
        $this->id = $id;
        $this->nombre = $nombre;
    }

    public function guardar() {
        $db = getDB();
        if ($this->id === null) {
            $query = $db->prepare("INSERT INTO lineas_ahorro_obligatorias (nombre) VALUES (?)");
            $query->bind_param("s", $this->nombre);
        } else {
            $query = $db->prepare("UPDATE lineas_ahorro_obligatorias SET nombre = ? WHERE id = ?");
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
        $query = $db->prepare("SELECT * FROM lineas_ahorro_obligatorias WHERE id = ?");
        $query->bind_param("i", $id);
        $query->execute();
        $query->bind_result($id, $nombre);
        $lineaAhorroOblig = null;
        if ($query->fetch()) {
            $lineaAhorroOblig = new LineaAhorroObligatoria($id, $nombre);
        }
        $query->close();
        $db->close();
        return $lineaAhorroOblig;
    }

    public static function obtenerTodos() {
        $db = getDB();
        $query = "SELECT * FROM lineas_ahorro_obligatorias";
        $result = $db->query($query);
        $lineasAhorroOblig = [];
        while ($row = $result->fetch_assoc()) {
            $lineasAhorroOblig[] = new LineaAhorroObligatoria($row['id'], $row['nombre']);
        }
        $db->close();
        return $lineasAhorroOblig;
    }

    public function eliminar() {
        $db = getDB();
        if ($this->id !== null) {
            $query = $db->prepare("DELETE FROM lineasAhorroOblig WHERE id = ?");
            $query->bind_param("i", $this->id);
            $query->execute();
            $query->close();
        }
        $db->close();
    }
}
?>