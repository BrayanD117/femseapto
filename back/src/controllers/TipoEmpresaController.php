<?php

require_once __DIR__ . '/../models/TipoEmpresaModel.php';

class TipoEmpresaController {

    public function crear($datos) {
        $tipoEmpresa = new TipoEmpresa(
            null, // El id se genera automáticamente al guardar
            $datos['nombre']
        );

        $tipoEmpresa->guardar();
        
        return $tipoEmpresa->id;
    }

    public function actualizar($id, $datos) {
        $tipoEmpresa = TipoEmpresa::obtenerPorId($id);
        if (!$tipoEmpresa) {
            return false;
        }

        $tipoEmpresa->nombre = $datos['nombre'];

        $tipoEmpresa->guardar();

        return true;
    }

    public function obtenerPorId($id) {
        $tipoEmpresa = TipoEmpresa::obtenerPorId($id);
        if ($tipoEmpresa) {
            return $tipoEmpresa;
        } else {
            http_response_code(404);
            return array("message" => "Tipo de empresa no encontrado.");
        }
    }

    public function obtenerTodos() {
        $tiposEmpresa = TipoEmpresa::obtenerTodos();
        if ($tiposEmpresa) {
            return $tiposEmpresa;
        } else {
            http_response_code(404);
            return array("message" => "No se encontraron tipos de empresa.");
        }
    }

    public function eliminar($id) {
        $tipoEmpresa = TipoEmpresa::obtenerPorId($id);
        if (!$tipoEmpresa) {
            return false;
        }

        $tipoEmpresa->eliminar();

        return true;
    }
}
?>