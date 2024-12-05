<?php

require_once __DIR__ . '/../models/SaldoAhorroObligatorioModel.php';
require_once __DIR__ . '/../models/UsuarioModel.php';

class SaldoAhorroObligatorioController {

    /**
     * Crea un nuevo saldo de ahorro obligatorio.
     * @param array $datos Datos del saldo de ahorro obligatorio a crear.
     * @return int|null ID del saldo de ahorro obligatorio creado.
     */
    public function crear($datos) {
        $saldoAhorroOblig = new SaldoAhorroObligatorio(
            null,
            $datos['idUsuario'],
            $datos['idLineaAhorroObligatoria'],
            null,
            $datos['valorSaldo'],
            $datos['fechaCorte']
        );

        $saldoAhorroOblig->guardar();
        
        return $saldoAhorroOblig->id;
    }

    /**
     * Actualiza un saldo de ahorro obligatorio existente.
     * @param int $id ID del saldo de ahorro obligatorio a actualizar.
     * @param array $datos Nuevos datos del saldo de ahorro obligatorio.
     * @return bool True si la actualización fue exitosa, false si falló o no se encontró el saldo de ahorro obligatorio.
     */
    public function actualizar($id, $datos) {
        $saldoAhorroOblig = SaldoAhorroObligatorio::obtenerPorId($id);
        if (!$saldoAhorroOblig) {
            return false;
        }

        $saldoAhorroOblig->idLineaAhorroObligatoria = $datos['idLineaAhorroObligatoria'];
        $saldoAhorroOblig->valorSaldo = $datos['valorSaldo'];
        $saldoAhorroOblig->fechaCorte = $datos['fechaCorte'];

        $saldoAhorroOblig->guardar();

        return true;
    }

    public function crearOActualizar($datos) {
        $db = getDB();
        try {
            $db->begin_transaction();

            $db->query("CREATE TEMPORARY TABLE temp_saldo_ahorros_obligatorios (
                numero_documento VARCHAR(50),
                id_linea_ahorro_obligatoria INT,
                valor_saldo DECIMAL(15,2),
                fecha_corte DATE,
                PRIMARY KEY (numero_documento, id_linea_ahorro_obligatoria)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;");

            $stmt = $db->prepare("INSERT INTO temp_saldo_ahorros_obligatorios (numero_documento, id_linea_ahorro_obligatoria, valor_saldo, fecha_corte) VALUES (?, ?, ?, ?)");

            foreach ($datos as $dato) {
                $stmt->bind_param('sids', $dato['numeroDocumento'], $dato['idLineaAhorroObligatoria'], $dato['valorSaldo'], $dato['fechaCorte']);
                $stmt->execute();

                if ($stmt->error) {
                    throw new Exception("Error al insertar en temp_saldo_ahorros_obligatorios: " . $stmt->error);
                }
            }

            $stmt->close();

            $usuariosExistentes = [];
            $result = $db->query("SELECT numero_documento, id FROM usuarios");
            while ($row = $result->fetch_assoc()) {
                $usuariosExistentes[$row['numero_documento']] = $row['id'];
            }

            $result = $db->query("SELECT DISTINCT numero_documento FROM temp_saldo_ahorros_obligatorios");
            $missingUsuarios = [];
            while ($row = $result->fetch_assoc()) {
                if (!isset($usuariosExistentes[$row['numero_documento']])) {
                    $missingUsuarios[] = $row['numero_documento'];
                }
            }

            if (!empty($missingUsuarios)) {
                error_log("Los siguientes números de documento no fueron encontrados en usuarios: " . implode(', ', $missingUsuarios));
            }

            $db->query("INSERT INTO saldo_ahorros_obligatorios (id_usuario, id_linea_ahorro_obligatoria, valor_saldo, fecha_corte)
                        SELECT u.id, tsao.id_linea_ahorro_obligatoria, tsao.valor_saldo, tsao.fecha_corte
                        FROM temp_saldo_ahorros_obligatorios tsao
                        INNER JOIN usuarios u ON tsao.numero_documento = u.numero_documento
                        ON DUPLICATE KEY UPDATE
                            valor_saldo = VALUES(valor_saldo),
                            fecha_corte = VALUES(fecha_corte)");

            if ($db->error) {
                throw new Exception("Error al insertar o actualizar en saldo_ahorros_obligatorios: " . $db->error);
            }

            $db->query("DELETE sao FROM saldo_ahorros_obligatorios sao
                        INNER JOIN usuarios u ON sao.id_usuario = u.id
                        LEFT JOIN (
                            SELECT u.id AS id_usuario, tsao.id_linea_ahorro_obligatoria
                            FROM temp_saldo_ahorros_obligatorios tsao
                            INNER JOIN usuarios u ON tsao.numero_documento = u.numero_documento
                        ) t ON sao.id_usuario = t.id_usuario AND sao.id_linea_ahorro_obligatoria = t.id_linea_ahorro_obligatoria
                        WHERE t.id_usuario IS NULL");

            if ($db->error) {
                throw new Exception("Error al eliminar registros en saldo_ahorros_obligatorios: " . $db->error);
            }

            $db->commit();

        } catch (Exception $e) {
            $db->rollback();
            throw $e;
        } finally {
            $db->close();
        }
    }

    /**
     * Obtiene un saldo de ahorro obligatorio por su ID.
     * @param int $id ID del saldo de ahorro obligatorio a obtener.
     * @return SaldoAhorroObligatorio|array El saldo de ahorro obligatorio encontrado o un array con un mensaje de error si no se encuentra.
     */
    public function obtenerPorId($id) {
        $saldoAhorroOblig = SaldoAhorroObligatorio::obtenerPorId($id);
        if ($saldoAhorroOblig) {
            return $saldoAhorroOblig;
        } else {
            http_response_code(404);
            return array("message" => "Saldo de ahorro no encontrado.");
        }
    }

    /**
     * Obtiene los saldos de ahorro obligatorio por ID de usuario.
     * @param int $idUsuario ID del usuario a obtener.
     * @return array|array[] Los saldos de ahorro obligatorio encontrados o un array con un mensaje de error si no se encuentran.
     */
    public function obtenerPorIdUsuario($idUsuario) {
        $saldos = SaldoAhorroObligatorio::obtenerPorIdUsuario($idUsuario);
        if ($saldos) {
            return $saldos;
        } else {
            http_response_code(404);
            return array("message" => "Saldos de ahorro no encontrados.");
        }
    }

    /**
     * Obtiene todos los saldos de ahorro obligatorio disponibles.
     * @return array|array[] Todos los saldos de ahorro obligatorio encontrados o un array con un mensaje de error si no se encuentran.
     */
    public function obtenerTodos() {
        $saldos = SaldoAhorroObligatorio::obtenerTodos();
        if ($saldos) {
            return $saldos;
        } else {
            http_response_code(404);
            return array("message" => "No se encontraron saldos de ahorro obligatorio.");
        }
    }

    /**
     * Elimina un saldo de ahorro obligatorio por su ID.
     * @param int $id ID del saldo de ahorro obligatorio a eliminar.
     * @return bool True si la eliminación fue exitosa, false si falló o no se encontró el saldo de ahorro obligatorio.
     */
    public function eliminar($id) {
        $saldoAhorroOblig = SaldoAhorroObligatorio::obtenerPorId($id);
        if (!$saldoAhorroOblig) {
            return false;
        }

        $saldoAhorroOblig->eliminar();

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