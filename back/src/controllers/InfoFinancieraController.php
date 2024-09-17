<?php

require_once __DIR__ . '/../models/InfoFinancieraModel.php';
require_once __DIR__ . '/../models/UsuarioModel.php';
require_once __DIR__ . '/../../utils/DataUtils.php';

class InfoFinancieraController {
    
    public function crear($datos) {

        $datos = DataUtils::convertirDatos($datos);

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

        $datos = DataUtils::convertirDatos($datos);

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
        $maxRetries = 5;
        $attempt = 0;
    
        while ($attempt < $maxRetries) {
            $attempt++;
            $db = getDB();
    
            try {
                $numerosDocumento = array_unique(array_column($datos, 'numeroDocumento'));
                $placeholders = implode(',', array_fill(0, count($numerosDocumento), '?'));

                $stmt = $db->prepare("SELECT id, numero_documento FROM usuarios WHERE numero_documento IN ($placeholders)");

                $stmt->bind_param(str_repeat('s', count($numerosDocumento)), ...$numerosDocumento);
                $stmt->execute();
                $result = $stmt->get_result();

                $mapaUsuarios = [];
                while ($row = $result->fetch_assoc()) {
                    $mapaUsuarios[$row['numero_documento']] = $row['id'];
                }
    
                $stmt->close();

                if (count($mapaUsuarios) < count($numerosDocumento)) {
                    error_log("Algunos usuarios no fueron encontrados.");
                }

                $datosProcesados = [];
                foreach ($datos as $dato) {
                    $numeroDocumento = $dato['numeroDocumento'];
                    if (isset($mapaUsuarios[$numeroDocumento])) {
                        $idUsuario = $mapaUsuarios[$numeroDocumento];
                        $dato['idUsuario'] = $idUsuario;
                        unset($dato['numeroDocumento']);
                        $datosProcesados[] = $dato;
                    } else {
                        error_log("Usuario con numero_documento $numeroDocumento no encontrado. Registro saltado.");
                        continue;
                    }
                }
    
                if (empty($datosProcesados)) {
                    throw new Exception("No se encontraron usuarios correspondientes a los números de documento proporcionados.");
                }

                usort($datosProcesados, function($a, $b) {
                    return $a['idUsuario'] - $b['idUsuario'];
                });

                $batchSize = 50;
                $dataBatches = array_chunk($datosProcesados, $batchSize);

                $db->begin_transaction();
    
                foreach ($dataBatches as $batch) {
                    $this->insertarOModificarEnLote($batch, $db);
                }
    
                $db->commit();
                break;
    
            } catch (Exception $e) {
                $db->rollback();
                if ($db->errno == 1213 || strpos($e->getMessage(), 'Deadlock found when trying to get lock') !== false) {
                    error_log("Deadlock detectado, intento $attempt de $maxRetries");
                    usleep(500000);
                    continue;
                } else {
                    error_log("Error en crearOActualizar: " . $e->getMessage());
                    throw $e;
                }
            } finally {
                $db->close();
            }
        }
    
        if ($attempt == $maxRetries) {
            throw new Exception("La transacción falló después de $maxRetries intentos debido a un deadlock.");
        }
    }
    
    private function insertarOModificarEnLote($datos, $db) {
        if (empty($datos)) {
            return;
        }
    
        $queryParts = [];
        $params = [];
        $types = '';
    
        foreach ($datos as $dato) {
            if (isset($dato['idUsuario'], $dato['montoMaxAhorro'])) {
                $queryParts[] = "(?, ?)";
                $params[] = $dato['idUsuario'];
                $params[] = $dato['montoMaxAhorro'];
                $types .= 'i';
                $types .= 'd';
            } else {
                error_log("Datos incompletos: " . json_encode($dato));
            }
        }
    
        if (empty($queryParts)) {
            throw new Exception("No hay datos válidos para insertar o actualizar.");
        }
    
        $query = "
            INSERT INTO informacion_financiera (id_usuario, monto_max_ahorro)
            VALUES " . implode(", ", $queryParts) . "
            ON DUPLICATE KEY UPDATE monto_max_ahorro = VALUES(monto_max_ahorro)
        ";
    
        $stmt = $db->prepare($query);
    
        $stmt->bind_param($types, ...$params);

        $stmt->execute();
        if ($stmt->error) {
            throw new Exception("Error en la ejecución de la consulta: " . $stmt->error);
        }
    
        $stmt->close();
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