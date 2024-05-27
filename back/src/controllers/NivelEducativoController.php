<?php

require_once __DIR__ . '/../models/NivelEducativoModel.php';

class NivelEducativoController {
    
    public function crear($datos) {
        $nivelEduc = new NivelEducativo(
            $datos['id'],
            $datos['nombre']
        );

        $nivelEduc->guardar();
        
        return $nivelEduc->id;
    }

    public function actualizar($id, $datos) {

        $nivelEduc = NivelEducativo::obtenerPorId($id);
        if (!$nivelEduc) {
            return false;
        }

        $nivelEduc->id = $datos['id'];
        $nivelEduc->nombre = $datos['nombre'];

        $nivelEduc->guardar();

        return true;
    }

    public function obtenerPorId($id) {
        $nivelEduc = NivelEducativo::obtenerPorId($id);
        if ($nivelEduc) {
            return $nivelEduc;
        } else {
            http_response_code(404);
            return array("message" => "Nivel educativo no encontrado.");
        }
    }

    public function obtenerTodos() {
        $nivelesEduc = NivelEducativo::obtenerTodos();
        if ($nivelesEduc) {
            return $nivelesEduc;
        } else {
            http_response_code(404);
            return array("message" => "No se encontraron niveles educativos.");
        }
    }
}
?>