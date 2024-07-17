<?php

require_once __DIR__ . '/../models/ReferenciaPersonalComercialBancariaModel.php';

class ReferenciaPersonalComercialBancariaController {

    public function crear($datos) {
        $referencia = new ReferenciaPersonalComercialBancaria(
            null,
            $datos['idUsuario'],
            $datos['nombreRazonSocial'],
            $datos['parentesco'],
            $datos['idTipoReferencia'],
            $datos['idMunicipio'],
            $datos['direccion'],
            $datos['telefono'],
            $datos['correoElectronico']
        );

        $referencia->guardar();

        return $referencia->id;
    }

    public function actualizar($id, $datos) {
        $referencia = ReferenciaPersonalComercialBancaria::obtenerPorId($id);
        if (!$referencia) {
            return false;
        }

        $referencia->nombreRazonSocial = $datos['nombreRazonSocial'];
        $referencia->parentesco = $datos['parentesco'];
        $referencia->idTipoReferencia = $datos['idTipoReferencia'];
        $referencia->idMunicipio = $datos['idMunicipio'];
        $referencia->direccion = $datos['direccion'];
        $referencia->telefono = $datos['telefono'];
        $referencia->correoElectronico = $datos['correoElectronico'];

        $referencia->guardar();

        return true;
    }

    public function obtenerPorId($id) {
        $referencia = ReferenciaPersonalComercialBancaria::obtenerPorId($id);
        if ($referencia) {
            return $referencia;
        } else {
            http_response_code(404);
            return array("message" => "Referencia no encontrada.");
        }
    }

    public function validarReferenciasFamiliares($idUsuario) {
        $referencia = ReferenciaPersonalComercialBancaria::validarReferenciasFamiliares($idUsuario);
        if ($referencia) {
            return $referencia;
        } else {
            http_response_code(404);
            return array("message" => "Referencia no encontrada.");
        }
    }

    public function validarReferenciasPersonales($idUsuario) {
        $referencia = ReferenciaPersonalComercialBancaria::validarReferenciasPersonales($idUsuario);
        if ($referencia) {
            return $referencia;
        } else {
            http_response_code(404);
            return array("message" => "Referencia no encontrada.");
        }
    }

    public function obtenerPorIdUsuario($idUsuario) {
        $referencia = ReferenciaPersonalComercialBancaria::obtenerPorIdUsuario($idUsuario);
        if ($referencia) {
            return $referencia;
        } else {
            http_response_code(404);
            return array("message" => "Referencia no encontrada.");
        }
    }

    public function obtenerTodos() {
        $referencias = ReferenciaPersonalComercialBancaria::obtenerTodos();
        if ($referencias) {
            return $referencias;
        } else {
            http_response_code(404);
            return array("message" => "No se encontraron referencias.");
        }
    }

    public function eliminar($id) {
        $referencia = ReferenciaPersonalComercialBancaria::obtenerPorId($id);
        if (!$referencia) {
            return array("message" => "Referencia no encontrada.");
        }

        $referencia->eliminar();
        return array("message" => "Referencia eliminada correctamente.");
    }
}
?>