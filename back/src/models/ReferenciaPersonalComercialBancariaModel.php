<?php
require_once __DIR__ . '/../../config/config.php';

class ReferenciaPersonalComercialBancaria {
    public $id;
    public $idUsuario;
    public $nombreRazonSocial;
    public $parentesco;
    public $idTipoReferencia;
    public $idDpto;
    public $idMunicipio;
    public $direccion;
    public $telefono;
    public $correoElectronico;
    public $creadoEl;
    public $actualizadoEl;

    public function __construct($id = null, $idUsuario = '', $nombreRazonSocial = '', $parentesco = '', $idTipoReferencia = '', $idDpto = '',
                    $idMunicipio = '', $direccion = '', $telefono = '', $correoElectronico = null, $creadoEl = '', $actualizadoEl = '') {
        $this->id = $id;
        $this->idUsuario = $idUsuario;
        $this->nombreRazonSocial = $nombreRazonSocial;
        $this->parentesco = $parentesco;
        $this->idTipoReferencia = $idTipoReferencia;
        $this->idDpto = $idDpto;
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
            $query = $db->prepare("INSERT INTO referencias_personales_comerciales_bancarias (id_usuario, nombre_razon_social, parentesco, id_tipo_referencia, id_dpto, id_mpio, direccion, telefono, correo_electronico) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $query->bind_param("ississsss", $this->idUsuario, $this->nombreRazonSocial, $this->parentesco, $this->idTipoReferencia, $this->idDpto, $this->idMunicipio, $this->direccion, $this->telefono, $this->correoElectronico);
        } else {
            $query = $db->prepare("UPDATE referencias_personales_comerciales_bancarias SET nombre_razon_social = ?, parentesco = ?, id_tipo_referencia = ?, id_dpto = ?, id_mpio = ?, direccion = ?, telefono = ?, correo_electronico = ? WHERE id = ?");
            $query->bind_param("ssisssssi", $this->nombreRazonSocial, $this->parentesco, $this->idTipoReferencia, $this->idDpto, $this->idMunicipio, $this->direccion, $this->telefono, $this->correoElectronico, $this->id);
        }
        $query->execute();
        if ($this->id === null) {
            $this->id = $query->insert_id;
        }
        $query->close();
        $db->close();
    }

    public static function validarReferenciasFamiliares($id) {
        $db = getDB();
        $query = $db->prepare("SELECT validarReferenciasFamiliaresUsuario(?) AS isValid");
        $query->bind_param("i", $id);
        $query->execute();
        $query->bind_result($isValid);
        $query->fetch();
        $query->close();
        $db->close();
        return (bool)$isValid;
    }

    public static function validarReferenciasPersonales($id) {
        $db = getDB();
        $query = $db->prepare("SELECT validarReferenciasPersonalesUsuario(?) AS isValid");
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
        $query = $db->prepare(
            "SELECT
                id,
                id_usuario,
                nombre_razon_social,
                parentesco,
                id_tipo_referencia,
                id_dpto,
                id_mpio,
                direccion,
                telefono,
                correo_electronico,
                CONVERT_TZ(creado_el, '+00:00', '-05:00') AS creado_el,
                CONVERT_TZ(actualizado_el, '+00:00', '-05:00') AS actualizado_el
            FROM referencias_personales_comerciales_bancarias
            WHERE id = ?");
        $query->bind_param("i", $id);
        $query->execute();
        $query->bind_result($id, $idUsuario, $nombreRazonSocial, $parentesco, $idTipoReferencia, $idDpto, $idMunicipio, $direccion, $telefono, $correoElectronico, $creadoEl, $actualizadoEl);
        $referencia = null;
        if ($query->fetch()) {
            $referencia = new ReferenciaPersonalComercialBancaria($id, $idUsuario, $nombreRazonSocial, $parentesco, $idTipoReferencia, $idDpto, $idMunicipio, $direccion, $telefono, $correoElectronico, $creadoEl, $actualizadoEl);
        }
        $query->close();
        $db->close();
        return $referencia;
    }

    public static function obtenerPorIdUsuario($idUsuario) {
        $db = getDB();
        $query = $db->prepare(
            "SELECT
                id,
                id_usuario,
                nombre_razon_social,
                parentesco,
                id_tipo_referencia,
                id_dpto,
                id_mpio,
                direccion,
                telefono,
                correo_electronico,
                CONVERT_TZ(creado_el, '+00:00', '-05:00') AS creado_el,
                CONVERT_TZ(actualizado_el, '+00:00', '-05:00') AS actualizado_el
            FROM referencias_personales_comerciales_bancarias
            WHERE id_usuario = ?");
        $query->bind_param("i", $idUsuario);
        $query->execute();
        $query->bind_result($id, $idUsuario, $nombreRazonSocial, $parentesco, $idTipoReferencia, $idDpto, $idMunicipio, $direccion, $telefono, $correoElectronico, $creadoEl, $actualizadoEl);
        
        $referencias = [];

        while ($query->fetch()) {
            $referencias[] = new ReferenciaPersonalComercialBancaria($id, $idUsuario, $nombreRazonSocial, $parentesco, $idTipoReferencia, $idDpto, $idMunicipio, $direccion, $telefono, $correoElectronico, $creadoEl, $actualizadoEl);
        }
        
        $query->close();
        $db->close();
        
        return $referencias;
    }
    

    public static function obtenerTodos() {
        $db = getDB();
        $query = "SELECT
                    id,
                    id_usuario,
                    nombre_razon_social,
                    parentesco,
                    id_tipo_referencia,
                    id_dpto,
                    id_mpio,
                    direccion,
                    telefono,
                    correo_electronico,
                    CONVERT_TZ(creado_el, '+00:00', '-05:00') AS creado_el,
                    CONVERT_TZ(actualizado_el, '+00:00', '-05:00') AS actualizado_el
                FROM referencias_personales_comerciales_bancarias";
        $result = $db->query($query);
        $referencias = [];
        while ($row = $result->fetch_assoc()) {
            $referencias[] = new ReferenciaPersonalComercialBancaria($row['id'], $row['id_usuario'], $row['nombre_razon_social'], $row['parentesco'], $row['id_tipo_referencia'], $row['id_dpto'], $row['id_mpio'], $row['direccion'], $row['telefono'], $row['correo_electronico'], $row['creado_el'], $row['actualizado_el']);
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