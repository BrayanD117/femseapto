<?php
require_once 'config.php';

class NivelEducativo {
    public $id;
    public $nombre;

    public function __construct($id = null, $nombre = '') {
        $this->id = $id;
        $this->nombre = $nombre;
    }

    public function guardar() {
        $db = getDB();
        if ($this->id === null) {
            $query = $db->prepare("INSERT INTO niveles_educativos (nombre) VALUES (?)");
            $query->bind_param("s", $this->nombre);
        } else {
            $query = $db->prepare("UPDATE niveles_educativos SET nombre = ? WHERE id = ?");
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
        $query = $db->prepare("SELECT id, nombre FROM niveles_educativos WHERE id = ?");
        $query->bind_param("i", $id);
        $query->execute();
        $query->bind_result($id, $nombre);
        $nivelEducativo = null;
        if ($query->fetch()) {
            $nivelEducativo = new NivelEducativo($id, $nombre);
        }
        $query->close();
        $db->close();
        return $nivelEducativo;
    }

    public static function obtenerTodos() {
        $db = getDB();
        $query = "SELECT id, nombre FROM niveles_educativos";
        $result = $db->query($query);
        $nivelesEducativos = [];
        while ($row = $result->fetch_assoc()) {
            $nivelesEducativos[] = new NivelEducativo($row['id'], $row['nombre']);
        }
        $db->close();
        return nivelesEducativos;
    }

    public function eliminar() {
        $db = getDB();
        if ($this->id !== null) {
            $query = $db->prepare("DELETE FROM niveles_educativos WHERE id = ?");
            $query->bind_param("i", $this->id);
            $query->execute();
            $query->close();
        }
        $db->close();
    }
}
?>
