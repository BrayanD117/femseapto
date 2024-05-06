<?php
require_once 'config.php';

class Departamento {
    public $id;
    public $nombre;

    public function __construct($id = null, $nombre = null) {
        $this->id = $id;
        $this->nombre = $nombre;
    }

    // Método para guardar un nuevo departamento o actualizar uno existente
    public function guardar() {
        $db = getDB();
        if ($this->id === null) {
            // Crear un nuevo departamento
            $query = $db->prepare("INSERT INTO departamentos (nombre) VALUES (?)");
            $query->bind_param("s", $this->nombre);
        } else {
            // Actualizar un departamento existente
            $query = $db->prepare("UPDATE departamentos SET nombre = ? WHERE id = ?");
            $query->bind_param("si", $this->nombre, $this->id);
        }
        $query->execute();
        if ($this->id === null) {
            $this->id = $query->insert_id;
        }
        $query->close();
        $db->close();
    }

    // Método para obtener un departamento por ID
    public static function obtenerPorId($id) {
        $db = getDB();
        $query = $db->prepare("SELECT id, nombre FROM departamentos WHERE id = ?");
        $query->bind_param("i", $id);
        $query->execute();
        $query->bind_result($id, $nombre);
        $departamento = null;
        if ($query->fetch()) {
            $departamento = new Departamento($id, $nombre);
        }
        $query->close();
        $db->close();
        return $departamento;
    }

    // Método para obtener todos los departamentos
    public static function obtenerTodos() {
        $db = getDB();
        $query = "SELECT id, nombre FROM departamentos";
        $result = $db->query($query);
        $departamentos = [];
        while ($row = $result->fetch_assoc()) {
            $departamentos[] = new Departamento($row['id'], $row['nombre']);
        }
        $db->close();
        return $departamentos;
    }

    // Método para eliminar un departamento por ID
    public function eliminar() {
        $db = getDB();
        if ($this->id !== null) {
            $query = $db->prepare("DELETE FROM departamentos WHERE id = ?");
            $query->bind_param("i", $this->id);
            $query->execute();
            $query->close();
        }
        $db->close();
    }
}
?>
