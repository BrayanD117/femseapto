<?php

require_once __DIR__ . '/../models/PaisModel.php';

class PaisController {

    public function obtenerPorId($id) {
        $pais = Pais::obtenerPorId($id);
        if ($pais) {
            return $pais;
        } else {
            http_response_code(404);
            return array("message" => "País no encontrado.");
        }
    }

    public function obtenerTodos() {
        $paises = Pais::obtenerTodos();
        if ($paises) {
            return $paises;
        } else {
            http_response_code(404);
            return array("message" => "No se encontraron Paises.");
        }
    }
}
?>