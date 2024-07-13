<?php

require_once __DIR__ . '/../models/OperacionesInternacionalesModel.php';

class OperacionesInternacionalesController {

    public function crear($datos) {
        $operacion = new OperacionesInternacionales(
            null,
            $datos['idUsuario'],
            $datos['transaccionesMonedaExtranjera'],
            $datos['transMonedaExtranjera'] ?? null,
            $datos['otrasOperaciones'] ?? null,
            $datos['cuentasMonedaExtranjera'],
            $datos['bancoCuentaExtranjera'] ?? null,
            $datos['cuentaMonedaExtranjera'] ?? null,
            $datos['monedaCuenta'] ?? null,
            $datos['idPaisCuenta'] ?? null,
            $datos['ciudadCuenta'] ?? null,
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

        $operacion->transaccionesMonedaExtranjera = $datos['transaccionesMonedaExtranjera'];
        $operacion->transMonedaExtranjera = $datos['transMonedaExtranjera'] ?? null;
        $operacion->otrasOperaciones = $datos['otrasOperaciones'] ?? null;
        $operacion->cuentasMonedaExtranjera = $datos['cuentasMonedaExtranjera'];
        $operacion->bancoCuentaExtranjera = $datos['bancoCuentaExtranjera'] ?? null;
        $operacion->cuentaMonedaExtranjera = $datos['cuentaMonedaExtranjera'] ?? null;
        $operacion->monedaCuenta = $datos['monedaCuenta'] ?? null;
        $operacion->idPaisCuenta = $datos['idPaisCuenta'] ?? null;
        $operacion->ciudadCuenta = $datos['ciudadCuenta'] ?? null;

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