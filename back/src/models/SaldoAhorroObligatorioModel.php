<?php
require_once __DIR__ . '/../../config/config.php';

class SaldoAhorroObligatorio
{
    public $id;
    public $idUsuario;
    public $idLineaAhorroObligatoria;
    public $ahorroQuincenal;
    public $valorSaldo;
    public $fechaCorte;
    public $creadoEl;
    public $actualizadoEl;

    public function __construct($id = null, $idUsuario = null, $idLineaAhorroObligatoria = null, $ahorroQuincenal = null, $valorSaldo = null, $fechaCorte = null, $creadoEl = null, $actualizadoEl = null)
    {
        $this->id = $id;
        $this->idUsuario = $idUsuario;
        $this->idLineaAhorroObligatoria = $idLineaAhorroObligatoria;
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
                "INSERT INTO saldo_ahorros_obligatorios (id_usuario, id_linea_ahorro_obligatoria, valor_saldo, fecha_corte) VALUES (?, ?, ?, ?)" :
                "UPDATE saldo_ahorros_obligatorios SET id_linea_ahorro_obligatoria = ?, valor_saldo = ?, fecha_corte = ? WHERE id = ?";

            $stmt = $db->prepare($queryStr);

            if ($this->id === null) {
                $stmt->bind_param("iids", $this->idUsuario, $this->idLineaAhorroObligatoria, $this->valorSaldo, $this->fechaCorte);
            } else {
                $stmt->bind_param("idsi", $this->idLineaAhorroObligatoria, $this->valorSaldo, $this->fechaCorte, $this->id);
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
                "INSERT INTO saldo_ahorros_obligatorios (id_usuario, id_linea_ahorro_obligatoria, valor_saldo, fecha_corte) 
            VALUES (?, ?, ?, ?) 
            ON DUPLICATE KEY UPDATE valor_saldo = VALUES(valor_saldo), fecha_corte = VALUES(fecha_corte)"
            );

            foreach ($datos as $dato) {
                if (isset($dato['idUsuario'], $dato['idLineaAhorroObligatoria'], $dato['valorSaldo'], $dato['fechaCorte'])) {
                    $stmt->bind_param("iids", $dato['idUsuario'], $dato['idLineaAhorroObligatoria'], $dato['valorSaldo'], $dato['fechaCorte']);
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
                id_linea_ahorro_obligatoria,
                valor_saldo,
                fecha_corte,
                CONVERT_TZ(creado_el, '+00:00', '-05:00') AS creado_el,
                CONVERT_TZ(actualizado_el, '+00:00', '-05:00') AS actualizado_el
            FROM saldo_ahorros_obligatorios
            WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();

        $stmt->bind_result($id, $idUsuario, $idLineaAhorroObligatoria, $valorSaldo, $fechaCorte, $creadoEl, $actualizadoEl);

        $saldoAhorroOblig = null;
        if ($stmt->fetch()) {
            $saldoAhorroOblig = new SaldoAhorroObligatorio($id, $idUsuario, $idLineaAhorroObligatoria, null, $valorSaldo, $fechaCorte, $creadoEl, $actualizadoEl);
        }

        $stmt->close();
        $db->close();

        return $saldoAhorroOblig;
    }

    public static function obtenerPorIdUsuario($idUsuario)
    {
        $db = getDB();
        $stmt = $db->prepare(
            "SELECT 
                id,
                id_usuario,
                id_linea_ahorro_obligatoria,
                valor_saldo,
                fecha_corte,
                CONVERT_TZ(creado_el, '+00:00', '-05:00') AS creado_el,
                CONVERT_TZ(actualizado_el, '+00:00', '-05:00') AS actualizado_el
            FROM saldo_ahorros_obligatorios
            WHERE id_usuario = ?");
        $stmt->bind_param("i", $idUsuario);
        $stmt->execute();
        $stmt->bind_result($id, $idUsuario, $idLineaAhorroObligatoria, $valorSaldo, $fechaCorte, $creadoEl, $actualizadoEl);

        $saldos = [];
        while ($stmt->fetch()) {
            $saldos[] = new SaldoAhorroObligatorio($id, $idUsuario, $idLineaAhorroObligatoria, null, $valorSaldo, $fechaCorte, $creadoEl, $actualizadoEl);
        }

        $stmt->close();
        $db->close();

        return $saldos;
    }

    public static function obtenerPorIdUsuarioYLineaAhorro($idUsuario, $idLineaAhorroObligatoria)
    {
        $db = getDB();
        $stmt = $db->prepare(
            "SELECT
                id,
                id_usuario,
                id_linea_ahorro_obligatoria,
                valor_saldo,
                fecha_corte,
                CONVERT_TZ(creado_el, '+00:00', '-05:00') AS creado_el,
                CONVERT_TZ(actualizado_el, '+00:00', '-05:00') AS actualizado_el
            FROM saldo_ahorros_obligatorios
            WHERE id_usuario = ? 
            AND id_linea_ahorro = ?");
        $stmt->bind_param("ii", $idUsuario, $idLineaAhorroObligatoria);
        $stmt->execute();
        $stmt->bind_result($id, $idUsuario, $idLineaAhorroObligatoria, $valorSaldo, $fechaCorte, $creadoEl, $actualizadoEl);

        $saldoAhorroOblig = null;
        if ($stmt->fetch()) {
            $saldoAhorroOblig = new SaldoAhorroObligatorio($id, $idUsuario, $idLineaAhorroObligatoria, null, $valorSaldo, $fechaCorte, $creadoEl, $actualizadoEl);
        }

        $stmt->close();
        $db->close();

        return $saldoAhorroOblig;
    }

    public static function obtenerTodos()
    {
        $db = getDB();
        $result = $db->query(
            "SELECT
                id,
                id_usuario,
                id_linea_ahorro_obligatoria,
                valor_saldo,
                fecha_corte,
                CONVERT_TZ(creado_el, '+00:00', '-05:00') AS creado_el,
                CONVERT_TZ(actualizado_el, '+00:00', '-05:00') AS actualizado_el
            FROM saldo_ahorros_obligatorios
        ");

        $saldos = [];
        while ($row = $result->fetch_assoc()) {
            $saldos[] = new SaldoAhorroObligatorio($row['id'], $row['id_usuario'], $row['id_linea_ahorro'], null, $row['valor_saldo'], $row['fecha_corte'], $row['creado_el'], $row['actualizado_el']);
        }

        $db->close();

        return $saldos;
    }

    public function eliminar()
    {
        $db = getDB();
        $stmt = $db->prepare("DELETE FROM saldo_ahorros_obligatorios WHERE id = ?");
        $stmt->bind_param("i", $this->id);
        $stmt->execute();

        if ($stmt->error) {
            error_log("Error in SaldoAhorroObligatorio::eliminar - " . $stmt->error);
            throw new Exception("Database Error: " . $stmt->error);
        }

        $stmt->close();
        $db->close();
    }
}