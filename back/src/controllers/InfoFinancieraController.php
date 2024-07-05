<?php

require_once __DIR__ . '/../models/InfoFinancieraModel.php';

class InfoFinancieraController {
    
    public function crear($datos) {
        $infoFinanciera = new InformacionFinanciera(
            $datos['id'],
            $datos['id_usuario'],
            $datos['nombre_banco'],
            $datos['id_tipo_cuenta_banc'],
            $datos['numero_cuenta_banc'],
            $datos['ingresos_mensuales'],
            $datos['prima_productividad'],
            $datos['otros_ingresos_mensuales'],
            $datos['concepto_otros_ingresos_mens'],
            $datos['total_ingresos_mensuales'],
            $datos['egresos_mensuales'],
            $datos['obligacion_financiera'],
            $datos['otros_egresos_mensuales'],
            $datos['total_egresos_mensuales'],
            $datos['total_activos'],
            $datos['total_pasivos'],
            $datos['total_patrimonio'],
            $datos['monto_max_ahorro']
        );

        $infoFinanciera->guardar();
        
        return $infoFinanciera->id;
    }

    public function actualizar($idUsuario, $datos) {

        $infoFinanciera = InformacionFinanciera::obtenerPorIdUsuario($idUsuario);
        if (!$infoFinanciera) {
            return false;
        }

        $infoFinanciera->id = $datos['id'];
        $infoFinanciera->id_usuario = $datos['id_usuario'];
        $infoFinanciera->nombre_banco = $datos['nombre_banco'];
        $infoFinanciera->id_tipo_cuenta_banc = $datos['id_tipo_cuenta_banc'];
        $infoFinanciera->numero_cuenta_banc = $datos['numero_cuenta_banc'];
        $infoFinanciera->ingresos_mensuales = $datos['ingresos_mensuales'];
        $infoFinanciera->prima_productividad = $datos['prima_productividad'];
        $infoFinanciera->otros_ingresos_mensuales = $datos['otros_ingresos_mensuales'];
        $infoFinanciera->concepto_otros_ingresos_mens = $datos['concepto_otros_ingresos_mens'];
        $infoFinanciera->total_ingresos_mensuales = $datos['total_ingresos_mensuales'];
        $infoFinanciera->egresos_mensuales = $datos['egresos_mensuales'];
        $infoFinanciera->obligacion_financiera = $datos['obligacion_financiera'];
        $infoFinanciera->otros_egresos_mensuales = $datos['otros_egresos_mensuales'];
        $infoFinanciera->total_egresos_mensuales = $datos['total_egresos_mensuales'];
        $infoFinanciera->total_activos = $datos['total_activos'];
        $infoFinanciera->total_pasivos = $datos['total_pasivos'];
        $infoFinanciera->total_patrimonio = $datos['total_patrimonio'];
        $infoFinanciera->monto_max_ahorro = $datos['monto_max_ahorro'];

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