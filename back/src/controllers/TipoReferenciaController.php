<?php

require_once __DIR__ . '/../models/TipoReferenciaModel.php';

class TipoReferenciaController {

    public function crear($datos) {
        $tipoReferencia = new TipoReferencia(
            null, // El id se genera automáticamente al guardar
            $datos['abreviatura'],
            $datos['nombre']
        );

        $tipoReferencia->guardar();
        
        return $tipoReferencia->id;
    }

    public function actualizar($id, $datos) {
        $tipoReferencia = TipoReferencia::obtenerPorId($id);
        if (!$tipoReferencia) {
            return false;
        }

        $tipoReferencia->abreviatura = $datos['abreviatura'];
        $tipoReferencia->nombre = $datos['nombre'];

        $tipoReferencia->guardar();

        return true;
    }

    public function obtenerPorId($id) {
        $tipoReferencia = TipoReferencia::obtenerPorId($id);
        if ($tipoReferencia) {
            return $tipoReferencia;
        } else {
            http_response_code(404);
            return array("message" => "Tipo de referencia no encontrado.");
        }
    }

    public function obtenerTodos() {
        $tiposReferencia = TipoReferencia::obtenerTodos();
        if ($tiposReferencia) {
            return $tiposReferencia;
        } else {
            http_response_code(404);
            return array("message" => "No se encontraron tipos de referencia.");
        }
    }

    public function eliminar($id) {
        $tipoReferencia = TipoReferencia::obtenerPorId($id);
        if (!$tipoReferencia) {
            return false;
        }

        $tipoReferencia->eliminar();

        return true;
    }
}

?>