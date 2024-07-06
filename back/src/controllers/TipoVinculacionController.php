<?php

require_once __DIR__ . '/../models/TipoVinculacionModel.php'; // Asegúrate de incluir tu modelo TipoVinculacion

class TipoVinculacionController {

    public function crear($datos) {
        $tipoVinculacion = new TipoVinculacion(
            null, // El id se genera automáticamente al guardar
            $datos['nombre']
        );

        $tipoVinculacion->guardar();
        
        return $tipoVinculacion->id;
    }

    public function actualizar($id, $datos) {
        $tipoVinculacion = TipoVinculacion::obtenerPorId($id);
        if (!$tipoVinculacion) {
            return false;
        }

        $tipoVinculacion->nombre = $datos['nombre'];

        $tipoVinculacion->guardar();

        return true;
    }

    public function obtenerPorId($id) {
        $tipoVinculacion = TipoVinculacion::obtenerPorId($id);
        if ($tipoVinculacion) {
            return $tipoVinculacion;
        } else {
            http_response_code(404);
            return array("message" => "Tipo de vinculación no encontrada.");
        }
    }

    public function obtenerTodos() {
        $tiposVinculacion = TipoVinculacion::obtenerTodos();
        if ($tiposVinculacion) {
            return $tiposVinculacion;
        } else {
            http_response_code(404);
            return array("message" => "No se encontraron tipos de vinculación.");
        }
    }

    public function eliminar($id) {
        $tipoVinculacion = TipoVinculacion::obtenerPorId($id);
        if (!$tipoVinculacion) {
            return false;
        }

        $tipoVinculacion->eliminar();

        return true;
    }
}

?>