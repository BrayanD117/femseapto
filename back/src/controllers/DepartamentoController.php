<?php

require_once __DIR__ . '/../models/DepartamentoModel.php';

class DepartamentoController {
    
    public function crear($datos) {
        $departamento = new Departamento(
            $datos['id'],
            $datos['nombre'],
        );

        $departamento->guardar();
        
        return $departamento->id;
    }

    public function actualizar($id, $datos) {

        $departamento = Departamento::obtenerPorId($id);
        if (!$departamento) {
            return false;
        }

        $departamento->id = $datos['id'];
        $departamento->nombre = $datos['nombre'];

        $departamento->guardar();

        return true;
    }

    public function obtenerPorId($id) {
        $departamento = Departamento::obtenerPorId($id);
        if ($departamento) {
            return $departamento;
        } else {
            http_response_code(404);
            return array("message" => "Departamento no encontrado.");
        }
    }

    public function obtenerTodos() {
        $departamentos = Departamento::obtenerTodos();
        if ($departamentos) {
            return $departamentos;
        } else {
            http_response_code(404);
            return array("message" => "No se encontraron departamentos.");
        }
    }
}
?>