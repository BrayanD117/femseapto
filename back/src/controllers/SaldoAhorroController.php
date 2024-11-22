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
        $db = getDB();
        try {
            $db->begin_transaction();

            // Crear tabla temporal
            $db->query("CREATE TEMPORARY TABLE temp_saldo_ahorros (
                numero_documento VARCHAR(50),
                id_linea_ahorro INT,
                ahorro_quincenal DECIMAL(10,2),
                valor_saldo DECIMAL(10,2),
                fecha_corte DATE,
                PRIMARY KEY (numero_documento, id_linea_ahorro)
            )");

            // Preparar inserción en la tabla temporal
            $stmt = $db->prepare("INSERT INTO temp_saldo_ahorros (numero_documento, id_linea_ahorro, ahorro_quincenal, valor_saldo, fecha_corte) VALUES (?, ?, ?, ?, ?)");

            foreach ($datos as $dato) {
                $stmt->bind_param('sidds', $dato['numeroDocumento'], $dato['idLineaAhorro'], $dato['ahorroQuincenal'], $dato['valorSaldo'], $dato['fechaCorte']);
                $stmt->execute();

                if ($stmt->error) {
                    throw new Exception("Error al insertar en temp_saldo_ahorros: " . $stmt->error);
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
            $result = $db->query("SELECT DISTINCT numero_documento FROM temp_saldo_ahorros");
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

            // Insertar o actualizar saldo_ahorros para usuarios existentes
            $db->query("INSERT INTO saldo_ahorros (id_usuario, id_linea_ahorro, ahorro_quincenal, valor_saldo, fecha_corte)
                        SELECT u.id, tsa.id_linea_ahorro, tsa.ahorro_quincenal, tsa.valor_saldo, tsa.fecha_corte
                        FROM temp_saldo_ahorros tsa
                        INNER JOIN usuarios u ON tsa.numero_documento = u.numero_documento
                        ON DUPLICATE KEY UPDATE
                            ahorro_quincenal = VALUES(ahorro_quincenal),
                            valor_saldo = VALUES(valor_saldo),
                            fecha_corte = VALUES(fecha_corte)");

            if ($db->error) {
                throw new Exception("Error al insertar o actualizar en saldo_ahorros: " . $db->error);
            }

            // Eliminar registros en saldo_ahorros que no están en temp_saldo_ahorros y pertenecen a usuarios existentes
            $db->query("DELETE sa FROM saldo_ahorros sa
                        INNER JOIN usuarios u ON sa.id_usuario = u.id
                        LEFT JOIN (
                            SELECT u.id AS id_usuario, tsa.id_linea_ahorro
                            FROM temp_saldo_ahorros tsa
                            INNER JOIN usuarios u ON tsa.numero_documento = u.numero_documento
                        ) t ON sa.id_usuario = t.id_usuario AND sa.id_linea_ahorro = t.id_linea_ahorro
                        WHERE t.id_usuario IS NULL");

            if ($db->error) {
                throw new Exception("Error al eliminar registros en saldo_ahorros: " . $db->error);
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