<?php

require_once __DIR__ . '/../models/RolModel.php';

class RolController {
    
    public function crear($datos) {
        $rol = new Rol(
            $datos['id'],
            $datos['nombre']
        );

        $rol->guardar();
        
        return $rol->id;
    }

    public function actualizar($id, $datos) {

        $rol = Rol::obtenerPorId($id);
        if (!$rol) {
            return false;
        }

        $rol->nombre = $datos['nombre'];

        $rol->guardar();

        return true;
    }

    public function obtenerPorId($id) {
        $rol = Rol::obtenerPorId($id);
        if ($rol) {
            return $rol;
        } else {
            http_response_code(404);
            return array("message" => "Rol no encontrado.");
        }
    }

    public function obtenerTodos() {
        $roles = Rol::obtenerTodos();
        if ($roles) {
            return $roles;
        } else {
            http_response_code(404);
            return array("message" => "No se encontraron roles.");
        }
    }
}
?>