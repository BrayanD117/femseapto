<?php

require_once __DIR__ . '/../models/OperacionesInternacionalesModel.php';

class OperacionesInternacionalesController {

    public function crear($datos) {
        $operacion = new OperacionesInternacionales(
            null,
            $datos['id_usuario'],
            $datos['transacciones_moneda_extranjera'],
            $datos['trans_moneda_extranjera'],
            $datos['otras_operaciones'],
            $datos['cuentas_moneda_extranjera'],
            $datos['banco_cuenta_extranjera'],
            $datos['cuenta_moneda_extranjera'],
            $datos['moneda_cuenta'],
            $datos['id_pais_cuenta'],
            $datos['ciudad_cuenta'],
            null,
            null
        );

        $operacion->guardar();

        return $operacion->id;
    }

    public function actualizar($id, $datos) {
        $operacion = OperacionesInternacionales::obtenerPorId($id);
        if (!$operacion) {
            return false;
        }

        $operacion->transacciones_moneda_extranjera = $datos['transacciones_moneda_extranjera'];
        $operacion->trans_moneda_extranjera = $datos['trans_moneda_extranjera'];
        $operacion->otras_operaciones = $datos['otras_operaciones'];
        $operacion->cuentas_moneda_extranjera = $datos['cuentas_moneda_extranjera'];
        $operacion->banco_cuenta_extranjera = $datos['banco_cuenta_extranjera'];
        $operacion->cuenta_moneda_extranjera = $datos['cuenta_moneda_extranjera'];
        $operacion->moneda_cuenta = $datos['moneda_cuenta'];
        $operacion->id_pais_cuenta = $datos['id_pais_cuenta'];
        $operacion->ciudad_cuenta = $datos['ciudad_cuenta'];

        $operacion->guardar();

        return true;
    }

    public function obtenerPorId($id) {
        $operacion = OperacionesInternacionales::obtenerPorId($id);
        if ($operacion) {
            return $operacion;
        } else {
            http_response_code(404);
            return array("message" => "Operación internacional no encontrada.");
        }
    }

    public function obtenerPorIdUsuario($idUsuario) {
        $operacion = OperacionesInternacionales::obtenerPorIdUsuario($idUsuario);
        if ($operacion) {
            return $operacion;
        } else {
            http_response_code(404);
            return array("message" => "Operación internacional no encontrada.");
        }
    }

    public function obtenerTodos() {
        $operaciones = OperacionesInternacionales::obtenerTodos();
        if ($operaciones) {
            return $operaciones;
        } else {
            http_response_code(404);
            return array("message" => "No se encontraron operaciones internacionales.");
        }
    }

    public function eliminar($id) {
        $operacion = OperacionesInternacionales::obtenerPorId($id);
        if (!$operacion) {
            return array("message" => "Operación internacional no encontrada.");
        }

        $operacion->eliminar();
        return array("message" => "Operación internacional eliminada correctamente.");
    }
}
?>