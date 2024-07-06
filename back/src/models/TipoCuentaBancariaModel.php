<?php
require_once __DIR__ . '/../../config/config.php';

class TipoCuentaBancaria {
    public $id;
    public $nombre;

    public function __construct($id = null, $nombre = '') {
        $this->id = $id;
        $this->nombre = $nombre;
    }

    public function guardar() {
        $db = getDB();
        if ($this->id === null) {
            $query = $db->prepare("INSERT INTO tipos_cuenta_bancaria (nombre) VALUES (?)");
            $query->bind_param("s", $this->nombre);
        } else {
            $query = $db->prepare("UPDATE tipos_cuenta_bancaria SET nombre = ? WHERE id = ?");
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
        $query = $db->prepare("SELECT id, nombre FROM tipos_cuenta_bancaria WHERE id = ?");
        $query->bind_param("i", $id);
        $query->execute();
        $query->bind_result($id, $nombre);
        $tipoCta = null;
        if ($query->fetch()) {
            $tipoCta = new TipoCuentaBancaria($id, $nombre);
        }
        $query->close();
        $db->close();
        return $tipoCta;
    }

    public static function obtenerTodos() {
        $db = getDB();
        $query = "SELECT id, nombre FROM tipos_cuenta_bancaria";
        $result = $db->query($query);
        $tipoCta = [];
        while ($row = $result->fetch_assoc()) {
            $tipoCta[] = new TipoCuentaBancaria($row['id'], $row['nombre']);
        }
        $db->close();
        return $tipoCta;
    }

    public function eliminar() {
        $db = getDB();
        if ($this->id !== null) {
            $query = $db->prepare("DELETE FROM tipos_cuenta_bancaria WHERE id = ?");
            $query->bind_param("i", $this->id);
            $query->execute();
            $query->close();
        }
        $db->close();
    }
}
?>