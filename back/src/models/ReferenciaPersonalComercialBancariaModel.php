<?php
require_once '../config/config.php';

class ReferenciaPersonalComercialBancaria {
    public $id;
    public $idUsuario;
    public $nombreRazonSocial;
    public $idTipoReferencia;
    public $idMunicipio;
    public $direccion;
    public $telefono;
    public $creadoEl;
    public $actualizadoEl;

    public function __construct($id = null, $idUsuario = '', $nombreRazonSocial = '', $idTipoReferencia = '', $idMunicipio = '', $direccion = '', $telefono = '', $creadoEl = '', $actualizadoEl = '') {
        $this->id = $id;
        $this->idUsuario = $idUsuario;
        $this->nombreRazonSocial = $nombreRazonSocial;
        $this->idTipoReferencia = $idTipoReferencia;
        $this->idMunicipio = $idMunicipio;
        $this->direccion = $direccion;
        $this->telefono = $telefono;
        $this->creadoEl = $creadoEl;
        $this->actualizadoEl = $actualizadoEl;
    }

    public function guardar() {
        $db = getDB();
        if ($this->id === null) {
            $query = $db->prepare("INSERT INTO referencias_personales_comerciales_bancarias (id_usuario, nombre_razon_social, id_tipo_referencia, id_mpio, direccion, telefono) VALUES (?, ?, ?, ?, ?, ?)");
            $query->bind_param("isisss", $this->idUsuario, $this->nombreRazonSocial, $this->idTipoReferencia, $this->idMunicipio, $this->direccion, $this->telefono);
        } else {
            $query = $db->prepare("UPDATE referencias_personales_comerciales_bancarias SET nombre_razon_social = ?, id_tipo_referencia = ?, id_mpio = ?, direccion = ?, telefono = ? WHERE id = ?");
            $query->bind_param("sisssi", $this->nombreRazonSocial, $this->idTipoReferencia, $this->idMunicipio, $this->direccion, $this->telefono, $this->id);
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
        $query->bind_result($id, $idUsuario, $nombreRazonSocial, $idTipoReferencia, $idMunicipio, $direccion, $telefono, $creadoEl, $actualizadoEl);
        
        $referencias = [];

        while ($query->fetch()) {
            $referencias[] = new ReferenciaPersonalComercialBancaria($id, $idUsuario, $nombreRazonSocial, $idTipoReferencia, $idMunicipio, $direccion, $telefono, $creadoEl, $actualizadoEl);
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
            $referencias[] = new ReferenciaPersonalComercialBancaria($row['id'], $row['id_usuario'], $row['nombre_razon_social'], $row['id_tipo_referencia'], $row['id_mpio'], $row['direccion'], $row['telefono'], $row['creado_el'], $row['actualizado_el']);
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