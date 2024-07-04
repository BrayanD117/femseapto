<?php
require_once '../config/config.php';

class ReferenciaPersonalComercialBancaria {
    public $id;
    public $idUsuario;
    public $nombreRazonSocial;
    public $parentesco;
    public $idTipoReferencia;
    public $idMunicipio;
    public $direccion;
    public $telefono;
    public $correoElectronico;
    public $creadoEl;
    public $actualizadoEl;

    public function __construct($id = null, $idUsuario = '', $nombreRazonSocial = '', $parentesco = '', $idTipoReferencia = '',
                    $idMunicipio = '', $direccion = '', $telefono = '', $correoElectronico = '', $creadoEl = '', $actualizadoEl = '') {
        $this->id = $id;
        $this->idUsuario = $idUsuario;
        $this->nombreRazonSocial = $nombreRazonSocial;
        $this->parentesco = $parentesco;
        $this->idTipoReferencia = $idTipoReferencia;
        $this->idMunicipio = $idMunicipio;
        $this->direccion = $direccion;
        $this->telefono = $telefono;
        $this->correoElectronico = $correoElectronico;
        $this->creadoEl = $creadoEl;
        $this->actualizadoEl = $actualizadoEl;
    }

    public function guardar() {
        $db = getDB();
        if ($this->id === null) {
            $query = $db->prepare("INSERT INTO referencias_personales_comerciales_bancarias (id_usuario, nombre_razon_social, parentesco, id_tipo_referencia, id_mpio, direccion, telefono, correo_electronico) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
            $query->bind_param("ississss", $this->idUsuario, $this->nombreRazonSocial, $this->parentesco, $this->idTipoReferencia, $this->idMunicipio, $this->direccion, $this->telefono, $this->correoElectronico);
        } else {
            $query = $db->prepare("UPDATE referencias_personales_comerciales_bancarias SET nombre_razon_social = ?, parentesco = ?, id_tipo_referencia = ?, id_mpio = ?, direccion = ?, telefono = ?, correo_electronico = ? WHERE id = ?");
            $query->bind_param("ssissssi", $this->nombreRazonSocial, $this->idTipoReferencia, $this->idMunicipio, $this->direccion, $this->telefono, $this->id);
        }
        $query->execute();
        if ($this->id === null) {
            $this->id = $query->insert_id;
        }
        $query->close();
        $db->close();
    }

    public static function obtenerPorIdUsuario($idUsuario) {
        $db = getDB();
        $query = $db->prepare("SELECT * FROM referencias_personales_comerciales_bancarias WHERE id_usuario = ?");
        $query->bind_param("i", $idUsuario);
        $query->execute();
        $query->bind_result($id, $idUsuario, $nombreRazonSocial, $parentesco, $idTipoReferencia, $idMunicipio, $direccion, $telefono, $correoElectronico, $creadoEl, $actualizadoEl);
        
        $referencias = [];

        while ($query->fetch()) {
            $referencias[] = new ReferenciaPersonalComercialBancaria($id, $idUsuario, $nombreRazonSocial, $parentesco, $idTipoReferencia, $idMunicipio, $direccion, $telefono, $correoElectronico, $creadoEl, $actualizadoEl);
        }
        
        $query->close();
        $db->close();
        
        return $referencias;
    }
    

    public static function obtenerTodos() {
        $db = getDB();
        $query = "SELECT * FROM referencias_personales_comerciales_bancarias";
        $result = $db->query($query);
        $referencias = [];
        while ($row = $result->fetch_assoc()) {
            $referencias[] = new ReferenciaPersonalComercialBancaria($row['id'], $row['id_usuario'], $row['nombre_razon_social'], $row['parentesco'], $row['id_tipo_referencia'], $row['id_mpio'], $row['direccion'], $row['telefono'], $row['correo_electronico'], $row['creado_el'], $row['actualizado_el']);
        }
        $db->close();
        return $referencias;
    }

    public function eliminar() {
        $db = getDB();
        if ($this->id !== null) {
            $query = $db->prepare("DELETE FROM referencias_personales_comerciales_bancarias WHERE id = ?");
            $query->bind_param("i", $this->id);
            $query->execute();
            $query->close();
        }
        $db->close();
    }
}
?>