<?php

require_once __DIR__ . '/../models/TipoCuentaBancariaModel.php';

class TipoCuentaBancariaController {

    public function crear($datos) {
        $tipoCuenta = new TipoCuentaBancaria(
            null, // El id se genera automáticamente al guardar
            $datos['nombre']
        );

        $tipoCuenta->guardar();
        
        return $tipoCuenta->id;
    }

    public function actualizar($id, $datos) {
        $tipoCuenta = TipoCuentaBancaria::obtenerPorId($id);
        if (!$tipoCuenta) {
            return false;
        }

        $tipoCuenta->nombre = $datos['nombre'];

        $tipoCuenta->guardar();

        return true;
    }

    public function obtenerPorId($id) {
        $tipoCuenta = TipoCuentaBancaria::obtenerPorId($id);
        if ($tipoCuenta) {
            return $tipoCuenta;
        } else {
            http_response_code(404);
            return array("message" => "Tipo de cuenta bancaria no encontrado.");
        }
    }

    public function obtenerTodos() {
        $tiposCuenta = TipoCuentaBancaria::obtenerTodos();
        if ($tiposCuenta) {
            return $tiposCuenta;
        } else {
            http_response_code(404);
            return array("message" => "No se encontraron tipos de cuenta bancaria.");
        }
    }

    public function eliminar($id) {
        $tipoCuenta = TipoCuentaBancaria::obtenerPorId($id);
        if (!$tipoCuenta) {
            return false;
        }

        $tipoCuenta->eliminar();

        return true;
    }
}
?>