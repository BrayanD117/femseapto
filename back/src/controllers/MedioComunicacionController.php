<?php

require_once __DIR__ . '/../models/MedioComunicacionModel.php';

class MedioComunicacionController { 
    
    public function crear($datos) {
        $medioComunicacion = new MedioComunicacion(
            $datos['id'],
            $datos['nombre']
        );

        $medioComunicacion->guardar();
        
        return $medioComunicacion->id;
    }

    public function actualizar($id, $datos) {

        $medioComunicacion = MedioComunicacion::obtenerPorId($id);
        if (!$medioComunicacion) {
            return false;
        }

        $medioComunicacion->id = $datos['id'];
        $medioComunicacion->nombre = $datos['nombre'];

        $medioComunicacion->guardar();

        return true;
    }

    public function obtenerPorId($id) {
        $medioComunicacion = MedioComunicacion::obtenerPorId($id);
        if ($medioComunicacion) {
            return $medioComunicacion;
        } else {
            http_response_code(404);
            return array("message" => "Medio de comunicacion no encontrado.");
        }
    }

    public function obtenerTodos() {
        $mediosComunicacion = MedioComunicacion::obtenerTodos();
        if ($mediosComunicacion) {
            return $mediosComunicacion;
        } else {
            http_response_code(404);
            return array("message" => "No se encontraron medios de comunicacion.");
        }
    }
}
?>