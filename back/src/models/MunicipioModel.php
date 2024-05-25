<?php
require_once __DIR__ . '/../../config/config.php';

class Municipio {
    public $id;
    public $id_departamento;
    public $nombre;

    public function __construct($id = null, $id_departamento = null, $nombre = '') {
        $this->id = $id;
        $this->id_departamento = $id_departamento;
        $this->nombre = $nombre;
    }

    public function guardar() {
        $db = getDB();
        if ($this->id === null) {
            $query = $db->prepare("INSERT INTO municipios (id, id_departamento, nombre) VALUES (?, ?, ?)");
            $query->bind_param("sss", $this->id, $this->id_departamento, $this->nombre);
        } else {
            $query = $db->prepare("UPDATE municipios SET id_departamento = ?, nombre = ? WHERE id = ?");
            $query->bind_param("sss", $this->id_departamento, $this->nombre, $this->id);
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
        $query = $db->prepare("SELECT id, id_departamento, nombre FROM municipios WHERE id = ?");
        $query->bind_param("s", $id);
        $query->execute();
        $query->bind_result($id, $id_departamento, $nombre);
        $municipio = null;
        if ($query->fetch()) {
            $municipio = new Municipio($id, $id_departamento, $nombre);
        }
        $query->close();
        $db->close();
        return $municipio;
    }

    public static function obtenerTodos() {
        $db = getDB();
        $query = "SELECT id, id_departamento, nombre FROM municipios";
        $result = $db->query($query);
        $municipios = [];
        while ($row = $result->fetch_assoc()) {
            $municipios[] = new Municipio($row['id'], $row['id_departamento'], $row['nombre']);
        }
        $db->close();
        return $municipios;
    }

    public static function obtenerPorIdDpto($idDpto) {
        $db = getDB();
        $query = $db->prepare("SELECT id, id_departamento, nombre FROM municipios WHERE id_departamento = ?");
        $query->bind_param("s", $idDpto);
        $query->execute();
        $result = $query->get_result();
        $municipios = [];
        while ($row = $result->fetch_assoc()) {
            $municipios[] = new Municipio($row['id'], $row['id_departamento'], $row['nombre']);
        }
        $query->close();
        $db->close();
        return $municipios;
    }

    public function eliminar() {
        $db = getDB();
        if ($this->id !== null) {
            $query = $db->prepare("DELETE FROM municipios WHERE id = ?");
            $query->bind_param("s", $this->id);
            $query->execute();
            $query->close();
        }
        $db->close();
    }
}
?>
