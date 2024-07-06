<?php

require_once __DIR__ . '/../models/TipoDocumentoModel.php';

class TipoDocumentoController {
    
    public function crear($datos) {
        $tipoDoc = new TipoDocumento(
            null,
            $datos['abreviatura'],
            $datos['nombre']
        );

        $tipoDoc->guardar();
        
        return $tipoDoc->id;
    }

    public function actualizar($id, $datos) {

        $tipoDoc = TipoDocumento::obtenerPorId($id);
        if (!$tipoDoc) {
            return false;
        }

        $tipoDoc->abreviatura = $datos['abreviatura'];
        $tipoDoc->nombre = $datos['nombre'];

        $tipoDoc->guardar();

        return true;
    }

    public function obtenerPorId($id) {
        $tipoDoc = TipoDocumento::obtenerPorId($id);
        if ($tipoDoc) {
            return $tipoDoc;
        } else {
            http_response_code(404);
            return array("message" => "Tipo Documento no encontrado.");
        }
    }

    public function obtenerTodos() {
        $tiposDoc = TipoDocumento::obtenerTodos();
        if ($tiposDoc) {
            return $tiposDoc;
        } else {
            http_response_code(404);
            return array("message" => "No se encontraron tipos de documento.");
        }
    }
}
?>