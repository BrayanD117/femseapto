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
        $db = getDB();
        try {
            $db->begin_transaction();
    
            // Crear tabla temporal
            $db->query("CREATE TEMPORARY TABLE temp_saldo_creditos (
                numero_documento VARCHAR(50),
                id_linea_credito INT,
                cuota_actual INT,
                cuotas_totales INT,
                valor_solicitado DECIMAL(10,2),
                cuota_quincenal DECIMAL(10,2),
                valor_pagado DECIMAL(10,2),
                valor_saldo DECIMAL(10,2),
                fecha_corte DATE,
                PRIMARY KEY (numero_documento, id_linea_credito)
            )");
    
            // Preparar inserción en la tabla temporal
            $stmt = $db->prepare("INSERT INTO temp_saldo_creditos (numero_documento, id_linea_credito, cuota_actual, cuotas_totales, valor_solicitado, cuota_quincenal, valor_pagado, valor_saldo, fecha_corte) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
    
            foreach ($datos as $dato) {
                $stmt->bind_param('siiidddds', $dato['numeroDocumento'], $dato['idLineaCredito'], $dato['cuotaActual'], $dato['cuotasTotales'], $dato['valorSolicitado'], $dato['cuotaQuincenal'], $dato['valorPagado'], $dato['valorSaldo'], $dato['fechaCorte']);
                $stmt->execute();
    
                if ($stmt->error) {
                    throw new Exception("Error al insertar en temp_saldo_creditos: " . $stmt->error);
                }
            }
    
            $stmt->close();
    
            // Obtener los usuarios existentes
            $usuariosExistentes = [];
            $result = $db->query("SELECT numero_documento, id FROM usuarios");
            while ($row = $result->fetch_assoc()) {
                $usuariosExistentes[$row['numero_documento']] = $row['id'];
            }
    
            // Registrar los números de documento que no existen
            $result = $db->query("SELECT DISTINCT numero_documento FROM temp_saldo_creditos");
            $missingUsuarios = [];
            while ($row = $result->fetch_assoc()) {
                if (!isset($usuariosExistentes[$row['numero_documento']])) {
                    $missingUsuarios[] = $row['numero_documento'];
                }
            }
    
            if (!empty($missingUsuarios)) {
                // Registrar los usuarios faltantes sin detener el proceso
                error_log("Los siguientes números de documento no fueron encontrados en usuarios: " . implode(', ', $missingUsuarios));
            }
    
            // Insertar o actualizar saldo_creditos para usuarios existentes
            $db->query("INSERT INTO saldo_creditos (id_usuario, id_linea_credito, cuota_actual, cuotas_totales, valor_solicitado, cuota_quincenal, valor_pagado, valor_saldo, fecha_corte)
                        SELECT u.id, tsc.id_linea_credito, tsc.cuota_actual, tsc.cuotas_totales, tsc.valor_solicitado, tsc.cuota_quincenal, tsc.valor_pagado, tsc.valor_saldo, tsc.fecha_corte
                        FROM temp_saldo_creditos tsc
                        INNER JOIN usuarios u ON tsc.numero_documento = u.numero_documento
                        ON DUPLICATE KEY UPDATE
                            cuota_actual = VALUES(cuota_actual),
                            cuotas_totales = VALUES(cuotas_totales),
                            valor_solicitado = VALUES(valor_solicitado),
                            cuota_quincenal = VALUES(cuota_quincenal),
                            valor_pagado = VALUES(valor_pagado),
                            valor_saldo = VALUES(valor_saldo),
                            fecha_corte = VALUES(fecha_corte)");
    
            if ($db->error) {
                throw new Exception("Error al insertar o actualizar en saldo_creditos: " . $db->error);
            }
    
            // Eliminar registros en saldo_creditos que no están en temp_saldo_creditos y pertenecen a usuarios existentes
            $db->query("DELETE sc FROM saldo_creditos sc
                        INNER JOIN usuarios u ON sc.id_usuario = u.id
                        LEFT JOIN (
                            SELECT u.id AS id_usuario, tsc.id_linea_credito
                            FROM temp_saldo_creditos tsc
                            INNER JOIN usuarios u ON tsc.numero_documento = u.numero_documento
                        ) t ON sc.id_usuario = t.id_usuario AND sc.id_linea_credito = t.id_linea_credito
                        WHERE t.id_usuario IS NULL");
    
            if ($db->error) {
                throw new Exception("Error al eliminar registros en saldo_creditos: " . $db->error);
            }
    
            $db->commit();
    
        } catch (Exception $e) {
            $db->rollback();
            throw $e;
        } finally {
            $db->close();
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
