<?php

require_once __DIR__ . '/../models/UsuarioComunicacionModel.php';

class UsuarioComunicacionController { 
    
    public function crear($datos) {
        $usuarioComunicacion = new UsuarioComunicacion(
            $datos['id'],
            $datos['idUsuario'],
            $datos['idMedioComunicacion']
        );

        $usuarioComunicacion->guardar();
        
        return $usuarioComunicacion->id;
    }

    public function actualizar($id, $datos) {

        $usuarioComunicacion = UsuarioComunicacion::obtenerPorId($id);
        if (!$usuarioComunicacion) {
            return false;
        }

        $usuarioComunicacion->id = $datos['id'];
        $usuarioComunicacion->idUsuario = $datos['idUsuario'];
        $usuarioComunicacion->idMedioComunicacion = $datos['idMedioComunicacion'];

        $usuarioComunicacion->guardar();

        return true;
    }

    public function sincronizarMedios($idUsuario, $idsMediosSeleccionados) {
        $mediosActuales = UsuarioComunicacion::obtenerPorIdUsuario($idUsuario);

        $idsMediosActuales = array_map(fn($uc) => $uc->idMedioComunicacion, $mediosActuales);

        $idsParaEliminar = array_diff($idsMediosActuales, $idsMediosSeleccionados);
        $idsParaAgregar = array_diff($idsMediosSeleccionados, $idsMediosActuales);

        if (!empty($idsParaEliminar)) {
            UsuarioComunicacion::eliminarPorIdUsuarioYMedios($idUsuario, $idsParaEliminar);
        }

        foreach ($idsParaAgregar as $idMedio) {
            $nuevo = new UsuarioComunicacion(null, $idUsuario, $idMedio);
            $nuevo->guardar();
        }
    }

    public function obtenerPorId($id) {
        $usuarioComunicacion = UsuarioComunicacion::obtenerPorId($id);
        if ($usuarioComunicacion) {
            return $usuarioComunicacion;
        } else {
            http_response_code(404);
            return array("message" => "Información no encontrada.");
        }
    }

    public function obtenerPorIdUsuario($idUsuario) {
        $usuarioMedios = UsuarioComunicacion::obtenerPorIdUsuario($idUsuario);
        if ($usuarioMedios) {
            return $usuarioMedios;
        } else {
            http_response_code(404);
            return array("message" => "Información no encontrada para el usuario especificado.");
        }
    }

    public function obtenerTodos() {
        $usuariosComunicacion = UsuarioComunicacion::obtenerTodos();
        if ($usuariosComunicacion) {
            return $usuariosComunicacion;
        } else {
            http_response_code(404);
            return array("message" => "No se encontró información.");
        }
    }
}
?>