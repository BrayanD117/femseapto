<?php
require_once __DIR__ . '/../../config/config.php';

class TipoAsociado {
    public $id;
    public $nombre;

    public function __construct($id = null, $nombre = '') {
        $this->id = $id;
        $this->nombre = $nombre;
    }

    public function guardar() {
        $db = getDB();
        if ($this->id === null) {
            $query = $db->prepare("INSERT INTO tipos_asociados (nombre) VALUES (?)");
            $query->bind_param("s", $this->nombre);
        } else {
            $query = $db->prepare("UPDATE tipos_asociados SET nombre = ? WHERE id = ?");
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
        $query = $db->prepare("SELECT * FROM tipos_asociados WHERE id = ?");
        $query->bind_param("i", $id);
        $query->execute();
        $query->bind_result($id, $nombre);
        $tAsociado = null;
        if ($query->fetch()) {
            $tAsociado = new TipoAsociado($id, $nombre);
        }
        $query->close();
        $db->close();
        return $tAsociado;
    }

    public static function obtenerTodos() {
        $db = getDB();
        $query = "SELECT * FROM tipos_asociados";
        $result = $db->query($query);
        $tAsociados = [];
        while ($row = $result->fetch_assoc()) {
            $tAsociados[] = new TipoAsociado($row['id'], $row['nombre']);
        }
        $db->close();
        return $tAsociados;
    }

    public function eliminar() {
        $db = getDB();
        if ($this->id !== null) {
            $query = $db->prepare("DELETE FROM tipos_asociados WHERE id = ?");
            $query->bind_param("i", $this->id);
            $query->execute();
            $query->close();
        }
        $db->close();
    }
}
?>