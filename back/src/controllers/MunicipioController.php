<?php

require_once __DIR__ . '/../models/MunicipioModel.php';

class MunicipioController {
    
    public function crear($datos) {
        $mpio = new Municipio(
            $datos['id'],
            $datos['idDepartamento'],
            $datos['nombre'],
        );

        $mpio->guardar();
        
        return $mpio->id;
    }

    public function actualizar($id, $datos) {

        $mpio = Municipio::obtenerPorId($id);
        if (!$mpio) {
            return false;
        }

        $mpio->id = $datos['id'];
        $mpio->idDepartamento = $datos['idDepartamento'];
        $mpio->nombre = $datos['nombre'];

        $mpio->guardar();

        return true;
    }

    public function obtenerPorId($id) {
        $mpio = Municipio::obtenerPorId($id);
        if ($mpio) {
            return $mpio;
        } else {
            http_response_code(404);
            return array("message" => "Municipio no encontrado.");
        }
    }

    public function obtenerTodos() {
        $mpios = Municipio::obtenerTodos();
        if ($mpios) {
            return $mpios;
        } else {
            http_response_code(404);
            return array("message" => "No se encontraron municipios.");
        }
    }

    public function obtenerPorIdDpto($idDpto) {
        $mpios = Municipio::obtenerPorIdDpto($idDpto);
        if ($mpios) {
            return $mpios;
        } else {
            http_response_code(404);
            return array("message" => "No se encontraron municipios.");
        }
    }
}
?>