<?php

require_once __DIR__ . '/../models/UsuarioModel.php';
require_once __DIR__ . '/../../utils/DataUtils.php';

class UsuarioController {
    
    public function crear($datos) {

        $datos = DataUtils::convertirDatos($datos);

        if($datos['id_rol'] == 1) {
            $randomDocumentNumber = rand(10000, 99999);
            $datos['numeroDocumento'] = $randomDocumentNumber;
            $datos['primerNombre'] = 'Admin';
            $datos['primerApellido'] = 'Admin';
            $datos['idTipoDocumento'] = 1;
            $datos['id_tipo_asociado'] = 3;
        } elseif ($datos['id_rol'] == 2) {
            $datos['usuario'] = $datos['numeroDocumento'];
        } elseif ($datos['id_rol'] == 3) {
            $datos['id_tipo_asociado'] = 3;
        }

        if ($this->existePorNumeroDocumentoYUsuario($datos['numeroDocumento'], $datos['usuario'])) {
            http_response_code(409); // Código de estado HTTP 409 Conflict
            return array("message" => "Usuario ya existe.");
        }
        
        $options = ['cost' => 12];
        if($datos['id_rol'] !== 1) {
            $hashedPassword = password_hash($datos['numeroDocumento'], PASSWORD_BCRYPT, $options);
        } else {
            $hashedPassword = password_hash($datos['contrasenia'], PASSWORD_BCRYPT, $options);
        }
        
        $usuario = new Usuario(
            null,
            $datos['id_rol'],
            $datos['usuario'],
            $hashedPassword,
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

        $datos = DataUtils::convertirDatos($datos);

        $usuario = Usuario::obtenerPorId($id);
        if (!$usuario) {
            return false;
        }

        $usuario->id_rol = $datos['id_rol'];
        $usuario->usuario = $datos['usuario'];
        //$usuario->contrasenia = $datos['contrasenia'];
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

    // Función para cambiar el estado activo del usuario
    public function cambiarEstadoActivo($id) {
        $resultado = Usuario::cambiarEstadoActivo($id);
        if ($resultado) {
            http_response_code(200);
            return array("message" => "Estado del usuario actualizado exitosamente.");
        } else {
            http_response_code(500);
            return array("message" => "Error al actualizar el estado del usuario.");
        }
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

    public function existePorNumeroDocumentoYUsuario($numeroDocumento, $usuario) {
        return Usuario::existePorNumeroDocumentoYUsuario($numeroDocumento, $usuario);
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

    public function obtenerConPaginacion($page, $size, $idRol) {
        return Usuario::obtenerConPaginacion($page, $size, $idRol);
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