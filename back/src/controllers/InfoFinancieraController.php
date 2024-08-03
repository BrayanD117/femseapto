<?php

require_once __DIR__ . '/../models/InfoFinancieraModel.php';
require_once __DIR__ . '/../models/UsuarioModel.php';

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
            $datos['conceptoOtrosIngresosMens'] ?? null,
            $datos['totalIngresosMensuales'],
            $datos['egresosMensuales'],
            $datos['obligacionFinanciera'],
            $datos['otrosEgresosMensuales'],
            $datos['totalEgresosMensuales'],
            $datos['totalActivos'],
            $datos['totalPasivos'],
            $datos['totalPatrimonio']
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
        $infoFinanciera->conceptoOtrosIngresosMens = $datos['conceptoOtrosIngresosMens'] ?? null;
        $infoFinanciera->totalIngresosMensuales = $datos['totalIngresosMensuales'];
        $infoFinanciera->egresosMensuales = $datos['egresosMensuales'];
        $infoFinanciera->obligacionFinanciera = $datos['obligacionFinanciera'];
        $infoFinanciera->otrosEgresosMensuales = $datos['otrosEgresosMensuales'];
        $infoFinanciera->totalEgresosMensuales = $datos['totalEgresosMensuales'];
        $infoFinanciera->totalActivos = $datos['totalActivos'];
        $infoFinanciera->totalPasivos = $datos['totalPasivos'];
        $infoFinanciera->totalPatrimonio = $datos['totalPatrimonio'];

        $infoFinanciera->guardar();

        return true;
    }

    public function crearOActualizar($datos) {
        foreach ($datos as $dato) {
            $numeroDocumento = $dato['numeroDocumento'];
            $usuario = Usuario::obtenerPorNumeroDocumento($numeroDocumento);
            
            if ($usuario) {
                $dato['idUsuario'] = $usuario->id;
                unset($dato['numeroDocumento']);
                
                $infoFinanciera = InformacionFinanciera::obtenerPorIdUsuario($usuario->id);
                
                if ($infoFinanciera) {
                    InformacionFinanciera::actualizarMontoMaximoAhorro($usuario->id, $dato['montoMaxAhorro']);
                } else {
                    InformacionFinanciera::crearMontoMaximoAhorro($usuario->id, $dato['montoMaxAhorro']);
                }
            }
        }
    }

    public function upload() {
        header('Content-Type: application/json');
        try {
            $data = json_decode(file_get_contents("php://input"), true);
            if (isset($data['data'])) {
                $this->crearOActualizar($data['data']);
                http_response_code(200);
                echo json_encode(array("message" => "Datos procesados exitosamente."));
            } else {
                http_response_code(400);
                echo json_encode(array("message" => "Datos no válidos."));
            }
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(array("message" => "Server error: " . $e->getMessage()));
        }
    }

    public function validarInformacionFinanciera($idUsuario) {
        $infoFinanciera = InformacionFinanciera::validarInformacionFinanciera($idUsuario);
        if ($infoFinanciera) {
            return $infoFinanciera;
        } else {
            http_response_code(404);
            return array("message" => "Información financiera no encontrada.");
        }
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