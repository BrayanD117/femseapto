<?php

require_once __DIR__ . '/../models/TipoViviendaModel.php';

class TipoViviendaController {
    
    public function crear($datos) {
        $tipoVivienda = new TipoVivienda(
            $datos['id'],
            $datos['nombre']
        );

        $tipoVivienda->guardar();
        
        return $tipoVivienda->id;
    }

    public function actualizar($id, $datos) {

        $tipoVivienda = TipoVivienda::obtenerPorId($id);
        if (!$tipoVivienda) {
            return false;
        }

        $tipoVivienda->id = $datos['id'];
        $tipoVivienda->nombre = $datos['nombre'];

        $tipoVivienda->guardar();

        return true;
    }

    public function obtenerPorId($id) {
        $tipoVivienda = TipoVivienda::obtenerPorId($id);
        if ($tipoVivienda) {
            return $tipoVivienda;
        } else {
            http_response_code(404);
            return array("message" => "Tipo vivienda no encontrada.");
        }
    }

    public function obtenerTodos() {
        $tiposVivienda = TipoVivienda::obtenerTodos();
        if ($tiposVivienda) {
            return $tiposVivienda;
        } else {
            http_response_code(404);
            return array("message" => "No se encontraron tipos de vivienda.");
        }
    }
}
?>