<?php
require_once 'config.php';

class InformacionNucleoFamiliar {
    public $id;
    public $id_usuario;
    public $nombre_completo;
    public $id_tipo_documento;
    public $numero_documento;
    public $id_parentesco;
    public $id_genero;
    public $fecha_nacimiento;
    public $id_nivel_educativo;
    public $trabaja;
    public $celular;

    public function __construct($id = null, $id_usuario = null, $nombre_completo = '', $id_tipo_documento = null, $numero_documento = '', $id_parentesco = null, $id_genero = null, $fecha_nacimiento = null, $id_nivel_educativo = null, $trabaja = '', $celular = '') {
        $this->id = $id;
        $this->id_usuario = $id_usuario;
        $this->nombre_completo = $nombre_completo;
        $this->id_tipo_documento = $id_tipo_documento;
        $this->numero_documento = $numero_documento;
        $this->id_parentesco = $id_parentesco;
        $this->id_genero = $id_genero;
        $this->fecha_nacimiento = $fecha_nacimiento;
        $this->id_nivel_educativo = $id_nivel_educativo;
        $this->trabaja = $trabaja;
        $this->celular = $celular;
    }

    public function guardar() {
        $db = getDB();
        if ($this->id === null) {
            $query = $db->prepare("INSERT INTO informacion_nucleo_familiar (id_usuario, nombre_completo, id_tipo_documento, numero_documento, id_parentesco, id_genero, fecha_nacimiento, id_nivel_educativo, trabaja, celular) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $query->bind_param("isisiiisis", $this->id_usuario, $this->nombre_completo, $this->id_tipo_documento, $this->numero_documento, $this->id_parentesco, $this->id_genero, $this->fecha_nacimiento, $this->id_nivel_educativo, $this->trabaja, $this->celular);
        } else {
            $query = $db->prepare("UPDATE informacion_nucleo_familiar SET id_usuario = ?, nombre_completo = ?, id_tipo_documento = ?, numero_documento = ?, id_parentesco = ?, id_genero = ?, fecha_nacimiento = ?, id_nivel_educativo = ?, trabaja = ?, celular = ? WHERE id = ?");
            $query->bind_param("isisiiisisi", $this->id_usuario, $this->nombre_completo, $this->id_tipo_documento, $this->numero_documento, $this->id_parentesco, $this->id_genero, $this->fecha_nacimiento, $this->id_nivel_educativo, $this->trabaja, $this->celular, $this->id);
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
        $query = $db->prepare("SELECT * FROM informacion_nucleo_familiar WHERE id = ?");
        $query->bind_param("i", $id);
        $query->execute();
        $query->bind_result($id, $id_usuario, $nombre_completo, $id_tipo_documento, $numero_documento, $id_parentesco, $id_genero, $fecha_nacimiento, $id_nivel_educativo, $trabaja, $celular);
        $infoFamiliar = null;
        if ($query->fetch()) {
            $infoFamiliar = new InformacionNucleoFamiliar($id, $id_usuario, $nombre_completo, $id_tipo_documento, $numero_documento, $id_parentesco, $id_genero, $fecha_nacimiento, $id_nivel_educativo, $trabaja, $celular);
        }
        $query->close();
        $db->close();
        return $infoFamiliar;
    }

    public static function obtenerTodos() {
        $db = getDB();
        $query = "SELECT * FROM informacion_nucleo_familiar";
        $result = $db->query($query);
        $infoFamiliarArray = [];
        while ($row = $result->fetch_assoc()) {
            $infoFamiliarArray[] = new InformacionNucleoFamiliar($row['id'], $row['id_usuario'], $row['nombre_completo'], $row['id_tipo_documento'], $row['numero_documento'], $row['id_parentesco'], $row['id_genero'], $row['fecha_nacimiento'], $row['id_nivel_educativo'], $row['trabaja'], $row['celular']);
        }
        $db->close();
        return $infoFamiliarArray;
    }

    public function eliminar() {
        $db = getDB();
        if ($this->id !== null) {
            $query = $db->prepare("DELETE FROM informacion_nucleo_familiar WHERE id = ?");
            $query->bind_param("i", $this->id);
            $query->execute();
            $query->close();
        }
        $db->close();
    }
}