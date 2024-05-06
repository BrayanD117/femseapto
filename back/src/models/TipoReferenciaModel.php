<?php
require_once 'config.php';

class TipoReferencia {
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
            $query = $db->prepare("INSERT INTO tipos_referencia (abreviatura, nombre) VALUES (?, ?)");
            $query->bind_param("ss", $this->abreviatura, $this->nombre);
        } else {
            $query = $db->prepare("UPDATE tipos_referencia SET abreviatura = ?, nombre = ? WHERE id = ?");
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
        $query = $db->prepare("SELECT id, abreviatura, nombre FROM tipos_referencia WHERE id = ?");
        $query->bind_param("i", $id);
        $query->execute();
        $query->bind_result($id, $abreviatura, $nombre);
        $tiposRef = null;
        if ($query->fetch()) {
            $tiposRef = new TipoReferencia($id, $abreviatura, $nombre);
        }
        $query->close();
        $db->close();
        return $tiposRef;
    }

    public static function obtenerTodos() {
        $db = getDB();
        $query = "SELECT id, abrevitura, nombre FROM tipos_referencia";
        $result = $db->query($query);
        $tiposRef = [];
        while ($row = $result->fetch_assoc()) {
            $tiposRef[] = new TipoReferencia($row['id'], $row['abreviatura'], $row['nombre']);
        }
        $db->close();
        return $tiposRef;
    }

    public function eliminar() {
        $db = getDB();
        if ($this->id !== null) {
            $query = $db->prepare("DELETE FROM tipos_referencia WHERE id = ?");
            $query->bind_param("i", $this->id);
            $query->execute();
            $query->close();
        }
        $db->close();
    }
}
?>