<?php

require_once __DIR__ . '/../models/InfoFinancieraModel.php.php';

class InfoFinancieraController {
    
    public function crear($datos) {
        $infoFinanciera = new InformacionFinanciera(
            $datos['id'],
            $datos['id_usuario'],
            $datos['ingresos_mensuales'],
            $datos['otros_ingresos_mensuales'],
            $datos['concepto_otros_ingresos_mens'],
            $datos['total_ingresos_mensuales'],
            $datos['egresos_mensuales'],
            $datos['otros_egresos_mensuales'],
            $datos['total_egresos_mensuales'],
            $datos['total_activos'],
            $datos['total_pasivos'],
            $datos['total_patrimonio']
        );

        $infoFinanciera->guardar();
        
        return $infoFinanciera->id;
    }

    public function actualizar($id, $datos) {

        $infoFinanciera = InformacionFinanciera::obtenerPorId($id);
        if (!$infoFinanciera) {
            return false;
        }

        $infoFinanciera->id = $datos['id'];
        $infoFinanciera->id_usuario = $datos['id_usuario'];
        $infoFinanciera->ingresos_mensuales = $datos['ingresos_mensuales'];
        $infoFinanciera->otros_ingresos_mensuales = $datos['otros_ingresos_mensuales'];
        $infoFinanciera->concepto_otros_ingresos_mens = $datos['concepto_otros_ingresos_mens'];
        $infoFinanciera->total_ingresos_mensuales = $datos['total_ingresos_mensuales'];
        $infoFinanciera->egresos_mensuales = $datos['egresos_mensuales'];
        $infoFinanciera->otros_egresos_mensuales = $datos['otros_egresos_mensuales'];
        $infoFinanciera->total_egresos_mensuales = $datos['total_egresos_mensuales'];
        $infoFinanciera->total_activos = $datos['total_activos'];
        $infoFinanciera->total_pasivos = $datos['total_pasivos'];
        $infoFinanciera->total_patrimonio = $datos['total_patrimonio'];

        $infoFinanciera->guardar();

        return true;
    }

    public function obtenerPorId($id) {
        $infoFinanciera = InformacionFinanciera::obtenerPorId($id);
        if ($infoFinanciera) {
            return $infoFinanciera;
        } else {
            http_response_code(404);
            return array("message" => "Información financiera no encontrada.");
        }
    }

    public function obtenerTodos() {
        $generos = Genero::obtenerTodos();
        if ($generos) {
            return $generos;
        } else {
            http_response_code(404);
            return array("message" => "No se encontraron géneros.");
        }
    }
}
?>