<?php
require_once '../config/config.php';

class Usuario {
    public $id;
    public $id_rol;
    public $usuario;
    public $contrasenia;
    public $primerNombre;
    public $segundoNombre;
    public $primerApellido;
    public $segundoApellido;
    public $idTipoDocumento;
    public $numeroDocumento;
    public $id_tipo_asociado;
    public $activo;
    public $creadoEl;
    public $actualizadoEl;

    public function __construct($id = null, $id_rol = null, $usuario = '', $contrasenia = '',
        $primerNombre = '', $segundoNombre = null, $primerApellido = '',
        $segundoApellido = null, $idTipoDocumento = '', $numeroDocumento = '', $id_tipo_asociado = '', $activo = null, $creadoEl = '', $actualizadoEl = '') {
        $this->id = $id;
        $this->id_rol = $id_rol;
        $this->usuario = $usuario;
        $this->contrasenia = $contrasenia;
        $this->primerNombre = $primerNombre;
        $this->segundoNombre = $segundoNombre;
        $this->primerApellido = $primerApellido;
        $this->segundoApellido = $segundoApellido;
        $this->idTipoDocumento = $idTipoDocumento;
        $this->numeroDocumento = $numeroDocumento;
        $this->id_tipo_asociado = $id_tipo_asociado;
        $this->activo = $activo;
        $this->creadoEl = $creadoEl;
        $this->actualizadoEl = $actualizadoEl;
    }

    public function guardar() {
        $db = getDB();
        if ($this->id === null) {
            $query = $db->prepare("INSERT INTO usuarios (id_rol, usuario, contrasenia, primer_nombre, segundo_nombre, primer_apellido, segundo_apellido, id_tipo_documento, numero_documento, id_tipo_asociado, activo) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $query->bind_param("issssssissi", $this->id_rol, $this->usuario, $this->contrasenia, $this->primerNombre, $this->segundoNombre, $this->primerApellido, $this->segundoApellido, $this->idTipoDocumento, $this->numeroDocumento, $this->id_tipo_asociado, $this->activo);
        } else {
            $query = $db->prepare("UPDATE usuarios SET id_rol = ?, usuario = ?, contrasenia = ?, primer_nombre = ?, segundo_nombre = ?, primer_apellido = ?, segundo_apellido = ?, id_tipo_documento = ?, numero_documento = ?, id_tipo_asociado = ?, activo = ? WHERE id = ?");
            $query->bind_param("issssssissii", $this->id_rol, $this->usuario, $this->contrasenia, $this->primerNombre, $this->segundoNombre, $this->primerApellido, $this->segundoApellido, $this->idTipoDocumento, $this->numeroDocumento, $this->id_tipo_asociado, $this->activo, $this->id);
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
        $query = $db->prepare("SELECT * FROM usuarios WHERE id = ?");
        $query->bind_param("i", $id);
        $query->execute();
        $query->bind_result($id, $id_rol, $usuario, $contrasenia, $primerNombre, $segundoNombre, $primerApellido, $segundoApellido, $idTipoDocumento, $numeroDocumento, $id_tipo_asociado, $activo, $creadoEl, $actualizadoEl);
        $user = null;
        if ($query->fetch()) {
            $user = new Usuario($id, $id_rol, $usuario, $contrasenia, $primerNombre, $segundoNombre, $primerApellido, $segundoApellido, $idTipoDocumento, $numeroDocumento, $id_tipo_asociado, $activo, $creadoEl, $actualizadoEl);
        }
        $query->close();
        $db->close();
        return $user;
    }

    public static function obtenerTodos() {
        $db = getDB();
        $query = "SELECT * FROM usuarios";
        $result = $db->query($query);
        $users = [];
        while ($row = $result->fetch_assoc()) {
            $users[] = new Usuario($row['id'], $row['id_rol'], $row['usuario'], $row['contrasenia'], $row['primer_nombre'], $row['segundo_nombre'], $row['primer_apellido'], $row['segundo_apellido'], $row['id_tipo_documento'], $row['numero_documento'], $row['id_tipo_asociado'], $row['activo'], $row['creado_el'], $row['actualizado_el']);
        }
        $db->close();
        return $users;
    }

    public function eliminar() {
        $db = getDB();
        if ($this->id !== null) {
            $query = $db->prepare("DELETE FROM usuarios WHERE id = ?");
            $query->bind_param("i", $this->id);
            $query->execute();
            $query->close();
        }
        $db->close();
    }

    // User Login
    public static function buscarPorUsuario($usuario) {
        $db = getDB();
        $query = $db->prepare("SELECT * FROM usuarios WHERE usuario = ?");
        $query->bind_param("s", $usuario);
        $query->execute();
        $query->bind_result($id, $id_rol, $usuario, $contrasenia, $primerNombre, $segundoNombre, $primerApellido, $segundoApellido, $idTipoDocumento, $numeroDocumento, $id_tipo_asociado, $activo, $creadoEl, $actualizadoEl);
        $usuarioObj = null;
        if ($query->fetch()) {
            $usuarioObj = new Usuario($id, $id_rol, $usuario, $contrasenia, $primerNombre, $segundoNombre, $primerApellido, $segundoApellido, $idTipoDocumento, $numeroDocumento, $id_tipo_asociado, $activo, $creadoEl, $actualizadoEl);
        }
        $query->close();
        $db->close();
        return $usuarioObj;
    }
}
?>