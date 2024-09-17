<?php

require_once __DIR__ . '/../models/SaldoCreditoModel.php';
require_once __DIR__ . '/../models/UsuarioModel.php';

class SaldoCreditoController {

    public function crear($datos) {
        $saldoCredito = new SaldoCredito(
            null,
            $datos['idUsuario'],
            $datos['idLineaCredito'],
            $datos['cuotaActual'],
            $datos['cuotasTotales'],
            $datos['valorSolicitado'],
            $datos['cuotaQuincenal'],
            $datos['valorPagado'],
            $datos['valorSaldo'],
            $datos['fechaCorte']
        );

        $saldoCredito->guardar();
        return $saldoCredito->id;
    }

    public function actualizar($id, $datos) {
        $saldoCredito = SaldoCredito::obtenerPorId($id);
        if (!$saldoCredito) {
            return false;
        }

        $saldoCredito->idLineaCredito = $datos['idLineaCredito'];
        $saldoCredito->cuotaActual = $datos['cuotaActual'];
        $saldoCredito->cuotasTotales = $datos['cuotasTotales'];
        $saldoCredito->valorSolicitado = $datos['valorSolicitado'];
        $saldoCredito->cuotaQuincenal = $datos['cuotaQuincenal'];
        $saldoCredito->valorPagado = $datos['valorPagado'];
        $saldoCredito->valorSaldo = $datos['valorSaldo'];
        $saldoCredito->fechaCorte = $datos['fechaCorte'];

        $saldoCredito->guardar();
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

                $idUsuarios = array_unique(array_column($datosProcesados, 'idUsuario'));
                $placeholders = implode(',', array_fill(0, count($idUsuarios), '?'));

                $stmt = $db->prepare("SELECT id_usuario, id_linea_credito FROM saldo_creditos WHERE id_usuario IN ($placeholders)");

                $stmt->bind_param(str_repeat('i', count($idUsuarios)), ...$idUsuarios);
                $stmt->execute();
                $result = $stmt->get_result();

                $saldosExistentes = [];
                while ($row = $result->fetch_assoc()) {
                    $claveRegistro = $row['id_usuario'] . '_' . $row['id_linea_credito'];
                    $saldosExistentes[$claveRegistro] = true;
                }
    
                $stmt->close();

                $datosInsertar = [];
                $datosActualizar = [];
    
                foreach ($datosProcesados as $dato) {
                    $idUsuario = $dato['idUsuario'];
                    $idLineaCredito = $dato['idLineaCredito'];
                    $claveRegistro = $idUsuario . '_' . $idLineaCredito;
    
                    if (isset($saldosExistentes[$claveRegistro])) {
                        $datosActualizar[] = $dato;
                    } else {
                        $datosInsertar[] = $dato;
                    }
                }
                usort($datosInsertar, function($a, $b) {
                    if ($a['idUsuario'] == $b['idUsuario']) {
                        return $a['idLineaCredito'] - $b['idLineaCredito'];
                    }
                    return $a['idUsuario'] - $b['idUsuario'];
                });
    
                usort($datosActualizar, function($a, $b) {
                    if ($a['idUsuario'] == $b['idUsuario']) {
                        return $a['idLineaCredito'] - $b['idLineaCredito'];
                    }
                    return $a['idUsuario'] - $b['idUsuario'];
                });

                $batchSize = 50;
                $insertBatches = array_chunk($datosInsertar, $batchSize);
                $updateBatches = array_chunk($datosActualizar, $batchSize);

                $db->begin_transaction();

                foreach ($insertBatches as $batch) {
                    SaldoCredito::guardarEnLote($batch, $db);
                }

                foreach ($updateBatches as $batch) {
                    $this->actualizarEnLote($batch, $db);
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
        
    private function actualizarEnLote($datosActualizar, $db) {
        $stmt = $db->prepare(
            "UPDATE saldo_creditos SET cuota_actual = ?, cuotas_totales = ?, valor_solicitado = ?, cuota_quincenal = ?, valor_pagado = ?, valor_saldo = ?, fecha_corte = ? 
            WHERE id_usuario = ? AND id_linea_credito = ?"
        );
    
        foreach ($datosActualizar as $dato) {
            if (isset($dato['idUsuario'], $dato['idLineaCredito'], $dato['cuotaActual'], $dato['cuotasTotales'], $dato['valorSolicitado'], $dato['cuotaQuincenal'], $dato['valorPagado'], $dato['valorSaldo'], $dato['fechaCorte'])) {
                $stmt->bind_param(
                    "iiddddsii",
                    $dato['cuotaActual'],
                    $dato['cuotasTotales'],
                    $dato['valorSolicitado'],
                    $dato['cuotaQuincenal'],
                    $dato['valorPagado'],
                    $dato['valorSaldo'],
                    $dato['fechaCorte'],
                    $dato['idUsuario'],
                    $dato['idLineaCredito']
                );
                $stmt->execute();
    
                if ($stmt->error) {
                    throw new Exception("Error al actualizar: " . $stmt->error);
                }
            } else {
                error_log("Datos incompletos para actualización: " . json_encode($dato));
            }
        }
    
        $stmt->close();
    }
    

    public function obtenerPorId($id) {
        $saldoCredito = SaldoCredito::obtenerPorId($id);
        if ($saldoCredito) {
            return $saldoCredito;
        } else {
            http_response_code(404);
            return array("message" => "Saldo de crédito no encontrado.");
        }
    }

    public function obtenerPorIdUsuario($idUsuario) {
        $saldos = SaldoCredito::obtenerPorIdUsuario($idUsuario);
        if ($saldos) {
            return $saldos;
        } else {
            http_response_code(404);
            return array("message" => "Saldos de crédito no encontrados.");
        }
    }

    public function obtenerTodos() {
        $saldos = SaldoCredito::obtenerTodos();
        if ($saldos) {
            return $saldos;
        } else {
            http_response_code(404);
            return array("message" => "No se encontraron saldos de crédito.");
        }
    }

    public function eliminar($id) {
        $saldoCredito = SaldoCredito::obtenerPorId($id);
        if (!$saldoCredito) {
            return false;
        }

        $saldoCredito->eliminar();
        return true;
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
}
