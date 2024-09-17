<?php

require_once __DIR__ . '/../models/SaldoAhorroModel.php';
require_once __DIR__ . '/../models/UsuarioModel.php';

class SaldoAhorroController {

    /**
     * Crea un nuevo saldo de ahorro.
     * @param array $datos Datos del saldo de ahorro a crear.
     * @return int|null ID del saldo de ahorro creado.
     */
    public function crear($datos) {
        $saldoAhorro = new SaldoAhorro(
            null,
            $datos['idUsuario'],
            $datos['idLineaAhorro'],
            $datos['ahorroQuincenal'],
            $datos['valorSaldo'],
            $datos['fechaCorte']
        );

        $saldoAhorro->guardar();
        
        return $saldoAhorro->id;
    }

    /**
     * Actualiza un saldo de ahorro existente.
     * @param int $id ID del saldo de ahorro a actualizar.
     * @param array $datos Nuevos datos del saldo de ahorro.
     * @return bool True si la actualización fue exitosa, false si falló o no se encontró el saldo de ahorro.
     */
    public function actualizar($id, $datos) {
        $saldoAhorro = SaldoAhorro::obtenerPorId($id);
        if (!$saldoAhorro) {
            return false;
        }

        $saldoAhorro->idLineaAhorro = $datos['idLineaAhorro'];
        $saldoAhorro->ahorroQuincenal = $datos['ahorroQuincenal'];
        $saldoAhorro->valorSaldo = $datos['valorSaldo'];
        $saldoAhorro->fechaCorte = $datos['fechaCorte'];

        $saldoAhorro->guardar();

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

                $stmt = $db->prepare("SELECT id_usuario, id_linea_ahorro FROM saldo_ahorros WHERE id_usuario IN ($placeholders)");

                $stmt->bind_param(str_repeat('i', count($idUsuarios)), ...$idUsuarios);
                $stmt->execute();
                $result = $stmt->get_result();

                $saldosExistentes = [];
                while ($row = $result->fetch_assoc()) {
                    $claveRegistro = $row['id_usuario'] . '_' . $row['id_linea_ahorro'];
                    $saldosExistentes[$claveRegistro] = true;
                }
    
                $stmt->close();

                $datosInsertar = [];
                $datosActualizar = [];
    
                foreach ($datosProcesados as $dato) {
                    $idUsuario = $dato['idUsuario'];
                    $idLineaAhorro = $dato['idLineaAhorro'];
                    $claveRegistro = $idUsuario . '_' . $idLineaAhorro;
    
                    if (isset($saldosExistentes[$claveRegistro])) {
                        $datosActualizar[] = $dato;
                    } else {
                        $datosInsertar[] = $dato;
                    }
                }
                usort($datosInsertar, function($a, $b) {
                    if ($a['idUsuario'] == $b['idUsuario']) {
                        return $a['idLineaAhorro'] - $b['idLineaAhorro'];
                    }
                    return $a['idUsuario'] - $b['idUsuario'];
                });
    
                usort($datosActualizar, function($a, $b) {
                    if ($a['idUsuario'] == $b['idUsuario']) {
                        return $a['idLineaAhorro'] - $b['idLineaAhorro'];
                    }
                    return $a['idUsuario'] - $b['idUsuario'];
                });

                $batchSize = 50;
                $insertBatches = array_chunk($datosInsertar, $batchSize);
                $updateBatches = array_chunk($datosActualizar, $batchSize);

                $db->begin_transaction();

                foreach ($insertBatches as $batch) {
                    SaldoAhorro::guardarEnLote($batch, $db);
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
            "UPDATE saldo_ahorros SET ahorro_quincenal = ?, valor_saldo = ?, fecha_corte = ? 
            WHERE id_usuario = ? AND id_linea_ahorro = ?"
        );
    
        foreach ($datosActualizar as $dato) {
            if (isset($dato['idUsuario'], $dato['idLineaAhorro'], $dato['ahorroQuincenal'], $dato['valorSaldo'], $dato['fechaCorte'])) {
                $stmt->bind_param(
                    "ddsii",
                    $dato['ahorroQuincenal'],
                    $dato['valorSaldo'],
                    $dato['fechaCorte'],
                    $dato['idUsuario'],
                    $dato['idLineaAhorro']
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

    /**
     * Obtiene un saldo de ahorro por su ID.
     * @param int $id ID del saldo de ahorro a obtener.
     * @return SaldoAhorro|array El saldo de ahorro encontrado o un array con un mensaje de error si no se encuentra.
     */
    public function obtenerPorId($id) {
        $saldoAhorro = SaldoAhorro::obtenerPorId($id);
        if ($saldoAhorro) {
            return $saldoAhorro;
        } else {
            http_response_code(404);
            return array("message" => "Saldo de ahorro no encontrado.");
        }
    }

    /**
     * Obtiene los saldos de ahorro por ID de usuario.
     * @param int $idUsuario ID del usuario a obtener.
     * @return array|array[] Los saldos de ahorro encontrados o un array con un mensaje de error si no se encuentran.
     */
    public function obtenerPorIdUsuario($idUsuario) {
        $saldos = SaldoAhorro::obtenerPorIdUsuario($idUsuario);
        if ($saldos) {
            return $saldos;
        } else {
            http_response_code(404);
            return array("message" => "Saldos de ahorro no encontrados.");
        }
    }

    /**
     * Obtiene todos los saldos de ahorro disponibles.
     * @return array|array[] Todos los saldos de ahorro encontrados o un array con un mensaje de error si no se encuentran.
     */
    public function obtenerTodos() {
        $saldos = SaldoAhorro::obtenerTodos();
        if ($saldos) {
            return $saldos;
        } else {
            http_response_code(404);
            return array("message" => "No se encontraron saldos de ahorro.");
        }
    }

    /**
     * Elimina un saldo de ahorro por su ID.
     * @param int $id ID del saldo de ahorro a eliminar.
     * @return bool True si la eliminación fue exitosa, false si falló o no se encontró el saldo de ahorro.
     */
    public function eliminar($id) {
        $saldoAhorro = SaldoAhorro::obtenerPorId($id);
        if (!$saldoAhorro) {
            return false;
        }

        $saldoAhorro->eliminar();

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
?>