<?php

require_once __DIR__ . '/../models/UsuarioModel.php';

class UsuarioController {
    
    public function crear($datos) {
        $usuario = new Usuario(
            null,
            $datos['id_rol'],
            $datos['usuario'],
            $datos['contrasenia'],
            $datos['primerNombre'],
            $datos['segundoNombre'] ?? null,
            $datos['primerApellido'],
            $datos['segundoApellido'] ?? null,
            $datos['idTipoDocumento'],
            $datos['numeroDocumento'],
            $datos['id_tipo_asociado'],
            $datos['activo'] ?? null,
            null,
            null
        );

        $usuario->guardar();
        
        return $usuario->id;
    }

    public function actualizar($id, $datos) {
        $usuario = Usuario::obtenerPorId($id);
        if (!$usuario) {
            return false;
        }

        $usuario->id_rol = $datos['id_rol'];
        $usuario->usuario = $datos['usuario'];
        $usuario->contrasenia = $datos['contrasenia'];
        $usuario->primerNombre = $datos['primerNombre'];
        $usuario->segundoNombre = $datos['segundoNombre'] ?? null;
        $usuario->primerApellido = $datos['primerApellido'];
        $usuario->segundoApellido = $datos['segundoApellido'] ?? null;
        $usuario->idTipoDocumento = $datos['idTipoDocumento'];
        $usuario->numeroDocumento = $datos['numeroDocumento'];
        $usuario->id_tipo_asociado = $datos['id_tipo_asociado'];
        $usuario->activo = $datos['activo'] ?? null;

        $usuario->guardar();

        return true;
    }

    public function obtenerPorId($id) {
        $usuario = Usuario::obtenerPorId($id);
        if ($usuario) {
            return $usuario;
        } else {
            http_response_code(404);
            return array("message" => "Usuario no encontrado.");
        }
    }

    public function obtenerTodos() {
        $usuarios = Usuario::obtenerTodos();
        if ($usuarios) {
            return $usuarios;
        } else {
            http_response_code(404);
            return array("message" => "No se encontraron usuarios.");
        }
    }

    public function obtenerConPaginacion($page, $size, $search) {
        return Usuario::obtenerConPaginacion($page, $size, $search);
    }

    public function eliminar($id) {
        $usuario = Usuario::obtenerPorId($id);
        if (!$usuario) {
            return false;
        }

        $usuario->eliminar();

        return true;
    }

    public function buscarPorUsuario($usuario) {
        $usuarioEncontrado = Usuario::buscarPorUsuario($usuario);
        if ($usuarioEncontrado) {
            return $usuarioEncontrado;
        } else {
            http_response_code(404);
            return array("message" => "Usuario no encontrado.");
        }
    }
}
?>