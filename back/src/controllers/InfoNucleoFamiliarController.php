<?php
require_once __DIR__ . '/../models/InfoNucleoFamiliarModel.php';

class InformacionNucleoFamiliarController {

    public function crear($datos) {
        $infoFamiliar = new InformacionNucleoFamiliar(
            null,
            $datos['idUsuario'],
            $datos['nombreCompleto'],
            $datos['idTipoDocumento'],
            $datos['numeroDocumento'],
            $datos['idDptoExpDoc'],
            $datos['idMpioExpDoc'],
            $datos['idParentesco'],
            $datos['idGenero'],
            $datos['fechaNacimiento'],
            $datos['idNivelEducativo'],
            $datos['trabaja'],
            $datos['celular'],
            null,
            null
        );

        $infoFamiliar->guardar();

        return $infoFamiliar->id;
    }

    public function actualizar($id, $datos) {
        $infoFamiliar = InformacionNucleoFamiliar::obtenerPorId($id);
        if (!$infoFamiliar) {
            return false;
        }

        $infoFamiliar->nombreCompleto = $datos['nombreCompleto'];
        $infoFamiliar->idTipoDocumento = $datos['idTipoDocumento'];
        $infoFamiliar->numeroDocumento = $datos['numeroDocumento'];
        $infoFamiliar->idDptoExpDoc = $datos['idDptoExpDoc'];
        $infoFamiliar->idMpioExpDoc = $datos['idMpioExpDoc'];
        $infoFamiliar->idParentesco = $datos['idParentesco'];
        $infoFamiliar->idGenero = $datos['idGenero'];
        $infoFamiliar->fechaNacimiento = $datos['fechaNacimiento'];
        $infoFamiliar->idNivelEducativo = $datos['idNivelEducativo'];
        $infoFamiliar->trabaja = $datos['trabaja'];
        $infoFamiliar->celular = $datos['celular'];

        $infoFamiliar->guardar();

        return true;
    }

    public function obtenerPorId($id) {
        $infoFamiliar = InformacionNucleoFamiliar::obtenerPorId($id);
        if ($infoFamiliar) {
            return $infoFamiliar;
        } else {
            http_response_code(404);
            return array("message" => "Información del núcleo familiar no encontrada.");
        }
    }

    public function validarInformacionNucleoFamiliar($idUsuario) {
        $infoFamiliar = InformacionNucleoFamiliar::validarInformacionNucleoFamiliar($idUsuario);
        if ($infoFamiliar) {
            return $infoFamiliar;
        } else {
            http_response_code(404);
            return array("message" => "Información del núcleo familiar no encontrada para el usuario especificado.");
        }
    }

    public function obtenerPorIdUsuario($idUsuario) {
        $infoFamiliar = InformacionNucleoFamiliar::obtenerPorIdUsuario($idUsuario);
        if ($infoFamiliar) {
            return $infoFamiliar;
        } else {
            http_response_code(404);
            return array("message" => "Información del núcleo familiar no encontrada para el usuario especificado.");
        }
    }

    public function obtenerTodos() {
        $infoFamiliar = InformacionNucleoFamiliar::obtenerTodos();
        if ($infoFamiliar) {
            return $infoFamiliar;
        } else {
            http_response_code(404);
            return array("message" => "No se encontró información del núcleo familiar.");
        }
    }

    public function eliminar($id) {
        $infoFamiliar = InformacionNucleoFamiliar::obtenerPorId($id);
        if (!$infoFamiliar) {
            return false;
        }

        $infoFamiliar->eliminar();

        return true;
    }
}
?>