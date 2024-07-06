<?php

require_once __DIR__ . '/../models/TipoAsociadoModel.php';

class TipoAsociadoController {

    public function crear($datos) {
        $tipoAsociado = new TipoAsociado(
            null, // El id se genera automáticamente al guardar
            $datos['nombre']
        );

        $tipoAsociado->guardar();
        
        return $tipoAsociado->id;
    }

    public function actualizar($id, $datos) {
        $tipoAsociado = TipoAsociado::obtenerPorId($id);
        if (!$tipoAsociado) {
            return false;
        }

        $tipoAsociado->nombre = $datos['nombre'];

        $tipoAsociado->guardar();

        return true;
    }

    public function obtenerPorId($id) {
        $tipoAsociado = TipoAsociado::obtenerPorId($id);
        if ($tipoAsociado) {
            return $tipoAsociado;
        } else {
            http_response_code(404);
            return array("message" => "Tipo de asociado no encontrado.");
        }
    }

    public function obtenerTodos() {
        $tiposAsociado = TipoAsociado::obtenerTodos();
        if ($tiposAsociado) {
            return $tiposAsociado;
        } else {
            http_response_code(404);
            return array("message" => "No se encontraron tipos de asociados.");
        }
    }

    public function eliminar($id) {
        $tipoAsociado = TipoAsociado::obtenerPorId($id);
        if (!$tipoAsociado) {
            return false;
        }

        $tipoAsociado->eliminar();

        return true;
    }
}
?>