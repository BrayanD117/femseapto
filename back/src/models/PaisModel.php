<?php
require_once 'config.php';

class Pais {
    public $id;
    public $nombre;

    public function __construct($id = '', $nombre = '') {
        $this->id = $id;
        $this->nombre = $nombre;
    }

    public static function obtenerPorId($id) {
        $db = getDB();
        $query = $db->prepare("SELECT id, nombre FROM paises WHERE id = ?");
        $query->bind_param("s", $id);
        $query->execute();
        $query->bind_result($id, $nombre);
        $paises = null;
        if ($query->fetch()) {
            $paises = new Pais($id, $nombre);
        }
        $query->close();
        $db->close();
        return $paises;
    }

    public static function obtenerTodos() {
        $db = getDB();
        $query = "SELECT id, nombre FROM paises";
        $result = $db->query($query);
        $paises = [];
        while ($row = $result->fetch_assoc()) {
            $paises[] = new Pais($row['id'], $row['nombre']);
        }
        $db->close();
        return $paises;
    }
}
?>