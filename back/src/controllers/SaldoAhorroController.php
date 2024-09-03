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
        $batchSize = 100;
        $db = getDB();
        $db->begin_transaction();

        try {
            $usuariosIds = array_column($datos, 'numeroDocumento');
            $usuariosIds = implode(',', array_map([$db, 'real_escape_string'], $usuariosIds));

            $query = $db->query("SELECT id_usuario, id_linea_ahorro FROM saldo_ahorros WHERE id_usuario IN ($usuariosIds)");
            $saldosExistentes = [];

            while ($row = $query->fetch_assoc()) {
                $saldosExistentes[$row['id_usuario']][$row['id_linea_ahorro']] = true;
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
                        $idLineaAhorro = $batch[$key]['idLineaAhorro'];

                        if (isset($saldosExistentes[$idUsuario][$idLineaAhorro])) {
                            $saldoAhorro = SaldoAhorro::obtenerPorIdUsuarioYLineaAhorro($idUsuario, $idLineaAhorro);
                            $batch[$key]['id'] = $saldoAhorro->id;
                        }
                    }
                }
                SaldoAhorro::guardarEnLote($batch);
            }

            $db->commit();
        } catch (Exception $e) {
            $db->rollback();
            throw $e;
        }
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