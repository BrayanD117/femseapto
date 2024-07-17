<?php
require_once __DIR__ . '/../../config/config.php';

class InformacionNucleoFamiliar {
    public $id;
    public $idUsuario;
    public $nombreCompleto;
    public $idTipoDocumento;
    public $numeroDocumento;
    public $idMpioExpDoc;
    public $idParentesco;
    public $idGenero;
    public $fechaNacimiento;
    public $idNivelEducativo;
    public $trabaja;
    public $celular;
    public $creadoEl;
    public $actualizadoEl;

    public function __construct($id = null, $idUsuario = null, $nombreCompleto = '', $idTipoDocumento = null, $numeroDocumento = '', $idMpioExpDoc = null, $idParentesco = null, $idGenero = null, $fechaNacimiento = null, $idNivelEducativo = null, $trabaja = '', $celular = '', $creadoEl = '', $actualizadoEl = '') {
        $this->id = $id;
        $this->idUsuario = $idUsuario;
        $this->nombreCompleto = $nombreCompleto;
        $this->idTipoDocumento = $idTipoDocumento;
        $this->numeroDocumento = $numeroDocumento;
        $this->idMpioExpDoc = $idMpioExpDoc;
        $this->idParentesco = $idParentesco;
        $this->idGenero = $idGenero;
        $this->fechaNacimiento = $fechaNacimiento;
        $this->idNivelEducativo = $idNivelEducativo;
        $this->trabaja = $trabaja;
        $this->celular = $celular;
        $this->creadoEl = $creadoEl;
        $this->actualizadoEl = $actualizadoEl;
    }

    public function guardar() {
        $db = getDB();
        if ($this->id === null) {
            $query = $db->prepare("INSERT INTO informacion_nucleo_familiar (id_usuario, nombre_completo, id_tipo_documento, numero_documento, id_mpio_exp_doc, id_parentesco, id_genero, fecha_nacimiento, id_nivel_educativo, trabaja, celular) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $query->bind_param("isissiisiss", $this->idUsuario, $this->nombreCompleto, $this->idTipoDocumento, $this->numeroDocumento, $this->idMpioExpDoc, $this->idParentesco, $this->idGenero, $this->fechaNacimiento, $this->idNivelEducativo, $this->trabaja, $this->celular);
        } else {
            $query = $db->prepare("UPDATE informacion_nucleo_familiar SET nombre_completo = ?, id_tipo_documento = ?, numero_documento = ?, id_mpio_exp_doc = ?, id_parentesco = ?, id_genero = ?, fecha_nacimiento = ?, id_nivel_educativo = ?, trabaja = ?, celular = ? WHERE id = ?");
            $query->bind_param("sissiisissi", $this->nombreCompleto, $this->idTipoDocumento, $this->numeroDocumento, $this->idMpioExpDoc, $this->idParentesco, $this->idGenero, $this->fechaNacimiento, $this->idNivelEducativo, $this->trabaja, $this->celular, $this->id);
        }
        $query->execute();
        if ($this->id === null) {
            $this->id = $query->insert_id;
        }
        $query->close();
        $db->close();
    }

    public static function validarInformacionNucleoFamiliar($id) {
        $db = getDB();
        $query = $db->prepare("SELECT validarInformacionNucleoFamiliarUsuario(?) AS isValid");
        $query->bind_param("i", $id);
        $query->execute();
        $query->bind_result($isValid);
        $query->fetch();
        $query->close();
        $db->close();
        return (bool)$isValid;
    }

    public static function obtenerPorId($id) {
        $db = getDB();
        $query = $db->prepare("SELECT * FROM informacion_nucleo_familiar WHERE id = ?");
        $query->bind_param("i", $id);
        $query->execute();
        $query->bind_result($id, $idUsuario, $nombreCompleto, $idTipoDocumento, $numeroDocumento, $idMpioExpDoc, $idParentesco, $idGenero, $fechaNacimiento, $idNivelEducativo, $trabaja, $celular, $creadoEl, $actualizadoEl);
        $infoFamiliar = null;
        if ($query->fetch()) {
            $infoFamiliar = new InformacionNucleoFamiliar($id, $idUsuario, $nombreCompleto, $idTipoDocumento, $numeroDocumento, $idMpioExpDoc, $idParentesco, $idGenero, $fechaNacimiento, $idNivelEducativo, $trabaja, $celular, $creadoEl, $actualizadoEl);
        }
        $query->close();
        $db->close();
        return $infoFamiliar;
    }

    public static function obtenerPorIdUsuario($idUsuario) {
        $db = getDB();
        $query = $db->prepare("SELECT * FROM informacion_nucleo_familiar WHERE id_usuario = ?");
        $query->bind_param("i", $idUsuario);
        $query->execute();
        $query->bind_result($id, $idUsuario, $nombreCompleto, $idTipoDocumento, $numeroDocumento, $idMpioExpDoc, $idParentesco, $idGenero, $fechaNacimiento, $idNivelEducativo, $trabaja, $celular, $creadoEl, $actualizadoEl);
        
        $infoFamiliar = [];
    
        while ($query->fetch()) {
            $infoFamiliar[] = new InformacionNucleoFamiliar($id, $idUsuario, $nombreCompleto, $idTipoDocumento, $numeroDocumento, $idMpioExpDoc, $idParentesco, $idGenero, $fechaNacimiento, $idNivelEducativo, $trabaja, $celular, $creadoEl, $actualizadoEl);
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
            $infoFamiliarArray[] = new InformacionNucleoFamiliar($row['id'], $row['id_usuario'], $row['nombre_completo'], $row['id_tipo_documento'], $row['numero_documento'], $row['id_mpio_exp_doc'], $row['id_parentesco'], $row['id_genero'], $row['fecha_nacimiento'], $row['id_nivel_educativo'], $row['trabaja'], $row['celular'], $row['creado_el'], $row['actualizado_el']);
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
?>