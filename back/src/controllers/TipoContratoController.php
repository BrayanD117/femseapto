<?php

require_once __DIR__ . '/../models/TipoContratoModel.php';

class TipoContratoController {

    public function crear($datos) {
        $tipoContrato = new TipoContrato(
            null, // El id se genera automáticamente al guardar
            $datos['nombre']
        );

        $tipoContrato->guardar();
        
        return $tipoContrato->id;
    }

    public function actualizar($id, $datos) {
        $tipoContrato = TipoContrato::obtenerPorId($id);
        if (!$tipoContrato) {
            return false;
        }

        $tipoContrato->nombre = $datos['nombre'];

        $tipoContrato->guardar();

        return true;
    }

    public function obtenerPorId($id) {
        $tipoContrato = TipoContrato::obtenerPorId($id);
        if ($tipoContrato) {
            return $tipoContrato;
        } else {
            http_response_code(404);
            return array("message" => "Tipo de contrato no encontrado.");
        }
    }

    public function obtenerTodos() {
        $tiposContrato = TipoContrato::obtenerTodos();
        if ($tiposContrato) {
            return $tiposContrato;
        } else {
            http_response_code(404);
            return array("message" => "No se encontraron tipos de contrato.");
        }
    }

    public function eliminar($id) {
        $tipoContrato = TipoContrato::obtenerPorId($id);
        if (!$tipoContrato) {
            return false;
        }

        $tipoContrato->eliminar();

        return true;
    }
}
?>