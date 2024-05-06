<?php
require_once 'config.php';

class EstadoCivil {
    public $id;
    public $nombre;

    public function __construct($id = null, $nombre = '') {
        $this->id = $id;
        $this->nombre = $nombre;
    }

    public function guardar() {
        $db = getDB();
        if ($this->id === null) {
            $query = $db->prepare("INSERT INTO estados_civiles (nombre) VALUES (?)");
            $query->bind_param("s", $this->nombre);
        } else {
            $query = $db->prepare("UPDATE estados_civiles SET nombre = ? WHERE id = ?");
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
        $query = $db->prepare("SELECT id, nombre FROM estados_civiles WHERE id = ?");
        $query->bind_param("i", $id);
        $query->execute();
        $query->bind_result($id, $nombre);
        $estadoCivil = null;
        if ($query->fetch()) {
            $estadoCivil = new EstadoCivil($id, $nombre);
        }
        $query->close();
        $db->close();
        return $estadoCivil;
    }

    public static function obtenerTodos() {
        $db = getDB();
        $query = "SELECT id, nombre FROM estados_civiles";
        $result = $db->query($query);
        $estadosCiviles = [];
        while ($row = $result->fetch_assoc()) {
            $estadosCiviles[] = new EstadoCivil($row['id'], $row['nombre']);
        }
        $db->close();
        return $estadosCiviles;
    }

    public function eliminar() {
        $db = getDB();
        if ($this->id !== null) {
            $query = $db->prepare("DELETE FROM estados_civiles WHERE id = ?");
            $query->bind_param("i", $this->id);
            $query->execute();
            $query->close();
        }
        $db->close();
    }
}
?>
