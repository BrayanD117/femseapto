<?php

require_once __DIR__ . '/../models/InfoFinancieraModel.php';

class InfoFinancieraController {
    
    public function crear($datos) {
        $infoFinanciera = new InformacionFinanciera(
            null,
            $datos['idUsuario'],
            $datos['nombreBanco'],
            $datos['idTipoCuentaBanc'],
            $datos['numeroCuentaBanc'],
            $datos['ingresosMensuales'],
            $datos['primaProductividad'],
            $datos['otrosIngresosMensuales'],
            $datos['conceptoOtrosIngresosMens'],
            $datos['totalIngresosMensuales'],
            $datos['egresosMensuales'],
            $datos['obligacionFinanciera'],
            $datos['otrosEgresosMensuales'],
            $datos['totalEgresosMensuales'],
            $datos['totalActivos'],
            $datos['totalPasivos'],
            $datos['totalPatrimonio'],
            $datos['montoMaxAhorro']
        );

        $infoFinanciera->guardar();
        
        return $infoFinanciera->id;
    }

    public function actualizar($idUsuario, $datos) {

        $infoFinanciera = InformacionFinanciera::obtenerPorIdUsuario($idUsuario);
        if (!$infoFinanciera) {
            return false;
        }

        $infoFinanciera->nombreBanco = $datos['nombreBanco'];
        $infoFinanciera->idTipoCuentaBanc = $datos['idTipoCuentaBanc'];
        $infoFinanciera->numeroCuentaBanc = $datos['numeroCuentaBanc'];
        $infoFinanciera->ingresosMensuales = $datos['ingresosMensuales'];
        $infoFinanciera->primaProductividad = $datos['primaProductividad'];
        $infoFinanciera->otrosIngresosMensuales = $datos['otrosIngresosMensuales'];
        $infoFinanciera->conceptoOtrosIngresosMens = $datos['conceptoOtrosIngresosMens'];
        $infoFinanciera->totalIngresosMensuales = $datos['totalIngresosMensuales'];
        $infoFinanciera->egresosMensuales = $datos['egresosMensuales'];
        $infoFinanciera->obligacionFinanciera = $datos['obligacionFinanciera'];
        $infoFinanciera->otrosEgresosMensuales = $datos['otrosEgresosMensuales'];
        $infoFinanciera->totalEgresosMensuales = $datos['totalEgresosMensuales'];
        $infoFinanciera->totalActivos = $datos['totalActivos'];
        $infoFinanciera->totalPasivos = $datos['totalPasivos'];
        $infoFinanciera->totalPatrimonio = $datos['totalPatrimonio'];
        $infoFinanciera->montoMaxAhorro = $datos['montoMaxAhorro'];

        $infoFinanciera->guardar();

        return true;
    }

    public function obtenerPorIdUsuario($idUsuario) {
        $infoFinanciera = InformacionFinanciera::obtenerPorIdUsuario($idUsuario);
        if ($infoFinanciera) {
            return $infoFinanciera;
        } else {
            http_response_code(404);
            return array("message" => "Información financiera no encontrada.");
        }
    }

    public function obtenerTodos() {
        $infoFinanciera = InformacionFinanciera::obtenerTodos();
        if ($infoFinanciera) {
            return $infoFinanciera;
        } else {
            http_response_code(404);
            return array("message" => "No se encontró información.");
        }
    }
}
?>