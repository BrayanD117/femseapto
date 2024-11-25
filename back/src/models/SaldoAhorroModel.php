<?php
require_once __DIR__ . '/../../config/config.php';

class SaldoAhorro
{
    public $id;
    public $idUsuario;
    public $idLineaAhorro;
    public $ahorroQuincenal;
    public $valorSaldo;
    public $fechaCorte;
    public $creadoEl;
    public $actualizadoEl;

    public function __construct($id = null, $idUsuario = null, $idLineaAhorro = null, $ahorroQuincenal = null, $valorSaldo = null, $fechaCorte = null, $creadoEl = null, $actualizadoEl = null)
    {
        $this->id = $id;
        $this->idUsuario = $idUsuario;
        $this->idLineaAhorro = $idLineaAhorro;
        $this->ahorroQuincenal = $ahorroQuincenal;
        $this->valorSaldo = $valorSaldo;
        $this->fechaCorte = $fechaCorte;
        $this->creadoEl = $creadoEl;
        $this->actualizadoEl = $actualizadoEl;
    }

    public function guardar()
    {
        $db = getDB();
        $db->begin_transaction();
        try {
            $queryStr = $this->id === null ?
                "INSERT INTO saldo_ahorros (id_usuario, id_linea_ahorro, ahorro_quincenal, valor_saldo, fecha_corte) VALUES (?, ?, ?, ?, ?)" :
                "UPDATE saldo_ahorros SET id_linea_ahorro = ?, ahorro_quincenal = ?, valor_saldo = ?, fecha_corte = ? WHERE id = ?";

            $stmt = $db->prepare($queryStr);

            if ($this->id === null) {
                $stmt->bind_param("iidds", $this->idUsuario, $this->idLineaAhorro, $this->ahorroQuincenal, $this->valorSaldo, $this->fechaCorte);
            } else {
                $stmt->bind_param("iddsi", $this->idLineaAhorro, $this->ahorroQuincenal, $this->valorSaldo, $this->fechaCorte, $this->id);
            }

            $stmt->execute();

            if ($stmt->error) {
                throw new Exception("Error en la consulta: " . $stmt->error);
            }

            if ($this->id === null) {
                $this->id = $stmt->insert_id;
            }

            $db->commit();
        } catch (Exception $e) {
            $db->rollback();
            error_log($e->getMessage());
            throw $e;
        } finally {
            $stmt->close();
            $db->close();
        }
    }

    public static function guardarEnLote($datos)
    {
        $db = getDB();
        $db->begin_transaction();
        try {
            $stmt = $db->prepare(
                "INSERT INTO saldo_ahorros (id_usuario, id_linea_ahorro, ahorro_quincenal, valor_saldo, fecha_corte) 
            VALUES (?, ?, ?, ?, ?) 
            ON DUPLICATE KEY UPDATE ahorro_quincenal = VALUES(ahorro_quincenal), valor_saldo = VALUES(valor_saldo), fecha_corte = VALUES(fecha_corte)"
            );

            foreach ($datos as $dato) {
                if (isset($dato['idUsuario'], $dato['idLineaAhorro'], $dato['ahorroQuincenal'], $dato['valorSaldo'], $dato['fechaCorte'])) {
                    $stmt->bind_param("iidds", $dato['idUsuario'], $dato['idLineaAhorro'], $dato['ahorroQuincenal'], $dato['valorSaldo'], $dato['fechaCorte']);
                    $stmt->execute();

                    if ($stmt->error) {
                        throw new Exception("Error en la consulta: " . $stmt->error);
                    }
                } else {
                    error_log("Datos incompletos: " . json_encode($dato));
                }
            }

            $db->commit();
        } catch (Exception $e) {
            $db->rollback();
            error_log($e->getMessage());
            throw $e;
        } finally {
            $stmt->close();
            $db->close();
        }
    }

    public static function obtenerPorId($id)
    {
        $db = getDB();
        $stmt = $db->prepare(
            "SELECT
                id,
                id_usuario,
                id_linea_ahorro,
                ahorro_quincenal,
                valor_saldo,
                fecha_corte,
                CONVERT_TZ(creado_el, '+00:00', '-05:00') AS creado_el,
                CONVERT_TZ(actualizado_el, '+00:00', '-05:00') AS actualizado_el
            FROM saldo_ahorros
            WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();

        $stmt->bind_result($id, $idUsuario, $idLineaAhorro, $ahorroQuincenal, $valorSaldo, $fechaCorte, $creadoEl, $actualizadoEl);

        $saldoAhorro = null;
        if ($stmt->fetch()) {
            $saldoAhorro = new SaldoAhorro($id, $idUsuario, $idLineaAhorro, $ahorroQuincenal, $valorSaldo, $fechaCorte, $creadoEl, $actualizadoEl);
        }

        $stmt->close();
        $db->close();

        return $saldoAhorro;
    }

    public static function obtenerPorIdUsuario($idUsuario)
    {
        $db = getDB();
        $stmt = $db->prepare(
            "SELECT 
                id,
                id_usuario,
                id_linea_ahorro,
                ahorro_quincenal,
                valor_saldo,
                fecha_corte,
                CONVERT_TZ(creado_el, '+00:00', '-05:00') AS creado_el,
                CONVERT_TZ(actualizado_el, '+00:00', '-05:00') AS actualizado_el
            FROM saldo_ahorros
            WHERE id_usuario = ?");
        $stmt->bind_param("i", $idUsuario);
        $stmt->execute();
        $stmt->bind_result($id, $idUsuario, $idLineaAhorro, $ahorroQuincenal, $valorSaldo, $fechaCorte, $creadoEl, $actualizadoEl);

        $saldos = [];
        while ($stmt->fetch()) {
            $saldos[] = new SaldoAhorro($id, $idUsuario, $idLineaAhorro, $ahorroQuincenal, $valorSaldo, $fechaCorte, $creadoEl, $actualizadoEl);
        }

        $stmt->close();
        $db->close();

        return $saldos;
    }

    public static function obtenerPorIdUsuarioYLineaAhorro($idUsuario, $idLineaAhorro)
    {
        $db = getDB();
        $stmt = $db->prepare(
            "SELECT
                id,
                id_usuario,
                id_linea_ahorro,
                ahorro_quincenal,
                valor_saldo,
                fecha_corte,
                CONVERT_TZ(creado_el, '+00:00', '-05:00') AS creado_el,
                CONVERT_TZ(actualizado_el, '+00:00', '-05:00') AS actualizado_el
            FROM saldo_ahorros
            WHERE id_usuario = ? 
            AND id_linea_ahorro = ?");
        $stmt->bind_param("ii", $idUsuario, $idLineaAhorro);
        $stmt->execute();
        $stmt->bind_result($id, $idUsuario, $idLineaAhorro, $ahorroQuincenal, $valorSaldo, $fechaCorte, $creadoEl, $actualizadoEl);

        $saldoAhorro = null;
        if ($stmt->fetch()) {
            $saldoAhorro = new SaldoAhorro($id, $idUsuario, $idLineaAhorro, $ahorroQuincenal, $valorSaldo, $fechaCorte, $creadoEl, $actualizadoEl);
        }

        $stmt->close();
        $db->close();

        return $saldoAhorro;
    }

    public static function obtenerTodos()
    {
        $db = getDB();
        $result = $db->query(
            "SELECT
                id,
                id_usuario,
                id_linea_ahorro,
                ahorro_quincenal,
                valor_saldo,
                fecha_corte,
                CONVERT_TZ(creado_el, '+00:00', '-05:00') AS creado_el,
                CONVERT_TZ(actualizado_el, '+00:00', '-05:00') AS actualizado_el
            FROM saldo_ahorros");

        $saldos = [];
        while ($row = $result->fetch_assoc()) {
            $saldos[] = new SaldoAhorro($row['id'], $row['id_usuario'], $row['id_linea_ahorro'], $row['ahorro_quincenal'], $row['valor_saldo'], $row['fecha_corte'], $row['creado_el'], $row['actualizado_el']);
        }

        $db->close();

        return $saldos;
    }

    public function eliminar()
    {
        $db = getDB();
        $stmt = $db->prepare("DELETE FROM saldo_ahorros WHERE id = ?");
        $stmt->bind_param("i", $this->id);
        $stmt->execute();

        if ($stmt->error) {
            error_log("Error in SaldoAhorro::eliminar - " . $stmt->error);
            throw new Exception("Database Error: " . $stmt->error);
        }

        $stmt->close();
        $db->close();
    }
}