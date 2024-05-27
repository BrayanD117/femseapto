<?php

require_once __DIR__ . '/../models/EstadoCivilModel.php';

class EstadoCivilController {
    
    public function crear($datos) {
        $estadoCivil = new EstadoCivil(
            $datos['id'],
            $datos['nombre']
        );

        $estadoCivil->guardar();
        
        return $estadoCivil->id;
    }

    public function actualizar($id, $datos) {

        $estadoCivil = EstadoCivil::obtenerPorId($id);
        if (!$estadoCivil) {
            return false;
        }

        $estadoCivil->id = $datos['id'];
        $estadoCivil->nombre = $datos['nombre'];

        $estadoCivil->guardar();

        return true;
    }

    public function obtenerPorId($id) {
        $estadoCivil = EstadoCivil::obtenerPorId($id);
        if ($estadoCivil) {
            return $estadoCivil;
        } else {
            http_response_code(404);
            return array("message" => "Estado civil no encontrado.");
        }
    }

    public function obtenerTodos() {
        $estadosCiviles = EstadoCivil::obtenerTodos();
        if ($estadosCiviles) {
            return $estadosCiviles;
        } else {
            http_response_code(404);
            return array("message" => "No se encontraron estados civiles.");
        }
    }
}
?>