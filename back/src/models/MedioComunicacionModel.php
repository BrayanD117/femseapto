<?php
require_once __DIR__ . '/../../config/config.php';

class MedioComunicacion {
    public $id;
    public $nombre;

    public function __construct($id = null, $nombre = '') {
        $this->id = $id;
        $this->nombre = $nombre;
    }

    public function guardar() {
        $db = getDB();
        if ($this->id === null) {
            $query = $db->prepare("INSERT INTO medios_comunicacion (nombre) VALUES (?)");
            $query->bind_param("s", $this->nombre);
        } else {
            $query = $db->prepare("UPDATE medios_comunicacion SET nombre = ? WHERE id = ?");
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
        $query = $db->prepare("SELECT id, nombre FROM medios_comunicacion WHERE id = ?");
        $query->bind_param("i", $id);
        $query->execute();
        $query->bind_result($id, $nombre);
        $medioComunicacion = null;
        if ($query->fetch()) {
            $medioComunicacion = new MedioComunicacion($id, $nombre);
        }
        $query->close();
        $db->close();
        return $medioComunicacion;
    }

    public static function obtenerTodos() {
        $db = getDB();
        $query = "SELECT id, nombre FROM medios_comunicacion";
        $result = $db->query($query);
        $mediosComunicacion = [];
        while ($row = $result->fetch_assoc()) {
            $mediosComunicacion[] = new MedioComunicacion($row['id'], $row['nombre']);
        }
        $db->close();
        return $mediosComunicacion;
    }

    public function eliminar() {
        $db = getDB();
        if ($this->id !== null) {
            $query = $db->prepare("DELETE FROM medios_comunicacion WHERE id = ?");
            $query->bind_param("i", $this->id);
            $query->execute();
            $query->close();
        }
        $db->close();
    }
}
?>