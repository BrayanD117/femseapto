<?php

require_once __DIR__ . '/../models/GeneroModel.php';

class GeneroController {
    
    public function crear($datos) {
        $genero = new Genero(
            $datos['id'],
            $datos['nombre']
        );

        $genero->guardar();
        
        return $genero->id;
    }

    public function actualizar($id, $datos) {

        $genero = Genero::obtenerPorId($id);
        if (!$genero) {
            return false;
        }

        $genero->id = $datos['id'];
        $genero->nombre = $datos['nombre'];

        $genero->guardar();

        return true;
    }

    public function obtenerPorId($id) {
        $genero = Genero::obtenerPorId($id);
        if ($genero) {
            return $genero;
        } else {
            http_response_code(404);
            return array("message" => "Género no encontrado.");
        }
    }

    public function obtenerTodos() {
        $generos = Genero::obtenerTodos();
        if ($generos) {
            return $generos;
        } else {
            http_response_code(404);
            return array("message" => "No se encontraron géneros.");
        }
    }
}
?>