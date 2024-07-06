<?php

require_once __DIR__ . '/../models/OperacionesInternacionalesModel.php';

class OperacionesInternacionalesController {

    public function crear($datos) {
        $operacion = new OperacionesInternacionales(
            null,
            $datos['idUsuario'],
            $datos['transaccionesMonedaExtranjera'],
            $datos['transMonedaExtranjera'],
            $datos['otrasOperaciones'],
            $datos['cuentasMonedaExtranjera'],
            $datos['bancoCuentaExtranjera'],
            $datos['cuentaMonedaExtranjera'],
            $datos['monedaCuenta'],
            $datos['idPaisCuenta'],
            $datos['ciudadCuenta'],
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
        $operacion->transMonedaExtranjera = $datos['transMonedaExtranjera'];
        $operacion->otrasOperaciones = $datos['otrasOperaciones'];
        $operacion->cuentasMonedaExtranjera = $datos['cuentasMonedaExtranjera'];
        $operacion->bancoCuentaExtranjera = $datos['bancoCuentaExtranjera'];
        $operacion->cuentaMonedaExtranjera = $datos['cuentaMonedaExtranjera'];
        $operacion->monedaCuenta = $datos['monedaCuenta'];
        $operacion->idPaisCuenta = $datos['idPaisCuenta'];
        $operacion->ciudadCuenta = $datos['ciudadCuenta'];

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