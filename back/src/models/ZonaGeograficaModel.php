<?php
require_once __DIR__ . '/../../config/config.php';

class ZonaGeografica {
    public $id;
    public $nombre;

    public function __construct($id = null, $nombre = '') {
        $this->id = $id;
        $this->nombre = $nombre;
    }

    public static function obtenerPorId($id) {
        $db = getDB();
        $query = $db->prepare("SELECT id, nombre FROM zonas_geograficas WHERE id = ?");
        $query->bind_param("i", $id);
        $query->execute();
        $query->bind_result($id, $nombre);
        $zonas = null;
        if ($query->fetch()) {
            $zonas = new ZonaGeografica($id, $nombre);
        }
        $query->close();
        $db->close();
        return $zonas;
    }

    public static function obtenerTodos() {
        $db = getDB();
        $query = "SELECT id, nombre FROM zonas_geograficas";
        $result = $db->query($query);
        $zonas = [];
        while ($row = $result->fetch_assoc()) {
            $zonas[] = new ZonaGeografica($row['id'], $row['nombre']);
        }
        $db->close();
        return $zonas;
    }
}
?>