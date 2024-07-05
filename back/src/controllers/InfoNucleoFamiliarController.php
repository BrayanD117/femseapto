<?php
require_once __DIR__ . '/../models/InfoNucleoFamiliarModel.php';

class InformacionNucleoFamiliarController {

    public function crear($datos) {
        $infoFamiliar = new InformacionNucleoFamiliar(
            null,
            $datos['id_usuario'],
            $datos['nombre_completo'],
            $datos['id_tipo_documento'],
            $datos['numero_documento'],
            $datos['id_mpio_exp_doc'],
            $datos['id_parentesco'],
            $datos['id_genero'],
            $datos['fecha_nacimiento'],
            $datos['id_nivel_educativo'],
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

        $infoFamiliar->nombre_completo = $datos['nombre_completo'];
        $infoFamiliar->id_tipo_documento = $datos['id_tipo_documento'];
        $infoFamiliar->numero_documento = $datos['numero_documento'];
        $infoFamiliar->id_mpio_exp_doc = $datos['id_mpio_exp_doc'];
        $infoFamiliar->id_parentesco = $datos['id_parentesco'];
        $infoFamiliar->id_genero = $datos['id_genero'];
        $infoFamiliar->fecha_nacimiento = $datos['fecha_nacimiento'];
        $infoFamiliar->id_nivel_educativo = $datos['id_nivel_educativo'];
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