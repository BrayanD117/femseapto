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
        $batchSize = 100;
        $db = getDB();
        $db->begin_transaction();

        try {
            $usuariosIds = array_column($datos, 'numeroDocumento');
            $usuariosIds = implode(',', array_map([$db, 'real_escape_string'], $usuariosIds));

            $query = $db->query("SELECT id_usuario, id_linea_credito FROM saldo_creditos WHERE id_usuario IN ($usuariosIds)");
            $saldosExistentes = [];

            while ($row = $query->fetch_assoc()) {
                $saldosExistentes[$row['id_usuario']][$row['id_linea_credito']] = true;
            }

            $batches = array_chunk($datos, $batchSize);

            foreach ($batches as $batch) {
                foreach ($batch as $key => $dato) {
                    $numeroDocumento = $dato['numeroDocumento'];
                    $usuario = Usuario::obtenerPorNumeroDocumento($numeroDocumento);

                    if ($usuario) {
                        $batch[$key]['idUsuario'] = $usuario->id;
                        unset($batch[$key]['numeroDocumento']);

                        $idUsuario = $batch[$key]['idUsuario'];
                        $idLineaCredito = $batch[$key]['idLineaCredito'];

                        if (isset($saldosExistentes[$idUsuario][$idLineaCredito])) {
                            $saldoCredito = SaldoCredito::obtenerPorIdUsuarioYLineaCredito($idUsuario, $idLineaCredito);
                            $batch[$key]['id'] = $saldoCredito->id;
                        }
                    }
                }
                SaldoCredito::guardarEnLote($batch);
            }

            $db->commit();
        } catch (Exception $e) {
            $db->rollback();
            throw $e;
        }
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
