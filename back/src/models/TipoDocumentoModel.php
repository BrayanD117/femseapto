<?php
require_once __DIR__ . '/../../config/config.php';

class TipoDocumento {
    public $id;
    public $abreviatura;
    public $nombre;

    public function __construct($id = null, $abreviatura = '', $nombre = '') {
        $this->id = $id;
        $this->abreviatura = $abreviatura;
        $this->nombre = $nombre;
    }

    public function guardar() {
        $db = getDB();
        if ($this->id === null) {
            $query = $db->prepare("INSERT INTO tipos_documento (abreviatura, nombre) VALUES (?, ?)");
            $query->bind_param("ss", $this->abreviatura, $this->nombre);
        } else {
            $query = $db->prepare("UPDATE tipos_documento SET abreviatura = ?, nombre = ? WHERE id = ?");
            $query->bind_param("ssi", $this->abreviatura, $this->nombre, $this->id);
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
        $query = $db->prepare("SELECT id, abreviatura, nombre FROM tipos_documento WHERE id = ?");
        $query->bind_param("i", $id);
        $query->execute();
        $query->bind_result($id, $abreviatura, $nombre);
        $tiposDoc = null;
        if ($query->fetch()) {
            $tiposDoc = new TipoDocumento($id, $abreviatura, $nombre);
        }
        $query->close();
        $db->close();
        return $tiposDoc;
    }

    public static function obtenerTodos() {
        $db = getDB();
        $query = "SELECT id, abreviatura, nombre FROM tipos_documento";
        $result = $db->query($query);
        $tiposDoc = [];
        while ($row = $result->fetch_assoc()) {
            $tiposDoc[] = new TipoDocumento($row['id'], $row['abreviatura'], $row['nombre']);
        }
        $db->close();
        return $tiposDoc;
    }

    public function eliminar() {
        $db = getDB();
        if ($this->id !== null) {
            $query = $db->prepare("DELETE FROM tipos_documento WHERE id = ?");
            $query->bind_param("i", $this->id);
            $query->execute();
            $query->close();
        }
        $db->close();
    }
}
?>