<?php

require_once __DIR__ . '/../models/ZonaGeograficaModel.php';

class ZonaGeograficaController {

    public function obtenerPorId($id) {
        $zonaGeo = ZonaGeografica::obtenerPorId($id);
        if ($zonaGeo) {
            return $zonaGeo;
        } else {
            http_response_code(404);
            return array("message" => "Zona geográfica no encontrada.");
        }
    }

    public function obtenerTodos() {
        $zonasGeo = ZonaGeografica::obtenerTodos();
        if ($zonasGeo) {
            return $zonasGeo;
        } else {
            http_response_code(404);
            return array("message" => "No se encontraron zonas geográficas.");
        }
    }
}
?>