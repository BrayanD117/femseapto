<?php
require_once '../config/config.php';

class Usuario {
    public $id;
    public $id_rol;
    public $usuario;
    public $contrasenia;
    public $activo;

    public function __construct($id = null, $id_rol = null, $usuario = '', $contrasenia = '', $activo = null) {
        $this->id = $id;
        $this->id_rol = $id_rol;
        $this->usuario = $usuario;
        $this->contrasenia = $contrasenia;
        $this->activo = $activo;
    }

    public static function buscarPorUsuario($usuario) {
        $db = getDB();
        $query = $db->prepare("SELECT * FROM usuarios WHERE usuario = ?");
        $query->bind_param("s", $usuario);
        $query->execute();
        $query->bind_result($id, $id_rol, $usuario, $contrasenia, $activo);
        $usuarioObj = null;
        if ($query->fetch()) {
            $usuarioObj = new Usuario($id, $id_rol, $usuario, $contrasenia, $activo);
        }
        $query->close();
        $db->close();
        return $usuarioObj;
    }
}
?>
