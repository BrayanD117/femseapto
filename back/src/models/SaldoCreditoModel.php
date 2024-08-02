<?php
require_once __DIR__ . '/../../config/config.php';

class SaldoCredito {
    public $id;
    public $idUsuario;
    public $idLineaCredito;
    public $cuotaActual;
    public $cuotasTotales;
    public $valorSolicitado;
    public $valorPagado;
    public $valorSaldo;
    public $creadoEl;
    public $actualizadoEl;

    public function __construct($id = null, $idUsuario = null, $idLineaCredito = null, $cuotaActual = null, $cuotasTotales = null, $valorSolicitado = null, $valorPagado = null, $valorSaldo = null, $creadoEl = null, $actualizadoEl = null) {
        $this->id = $id;
        $this->idUsuario = $idUsuario;
        $this->idLineaCredito = $idLineaCredito;
        $this->cuotaActual = $cuotaActual;
        $this->cuotasTotales = $cuotasTotales;
        $this->valorSolicitado = $valorSolicitado;
        $this->valorPagado = $valorPagado;
        $this->valorSaldo = $valorSaldo;
        $this->creadoEl = $creadoEl;
        $this->actualizadoEl = $actualizadoEl;
    }

    public function guardar() {
        $db = getDB();
        if ($this->id === null) {
            $query = $db->prepare("INSERT INTO saldo_creditos (id_usuario, id_linea_credito, cuota_actual, cuotas_totales, valor_solicitado, valor_pagado, valor_saldo) VALUES (?, ?, ?, ?, ?, ?, ?)");
            $query->bind_param("iiiiddd", $this->idUsuario, $this->idLineaCredito, $this->cuotaActual, $this->cuotasTotales, $this->valorSolicitado, $this->valorPagado, $this->valorSaldo);
        } else {
            $query = $db->prepare("UPDATE saldo_creditos SET id_linea_credito = ?, cuota_actual = ?, cuotas_totales = ?, valor_solicitado = ?, valor_pagado = ?, valor_saldo = ? WHERE id = ?");
            $query->bind_param("iiidddi", $this->idLineaCredito, $this->cuotaActual, $this->cuotasTotales, $this->valorSolicitado, $this->valorPagado, $this->valorSaldo, $this->id);
        }
        $query->execute();
        if ($this->id === null) {
            $this->id = $query->insert_id;
        }
        $query->close();
        $db->close();
    }

    public static function obtenerPorId($id) {
        $db = getDB();
        $query = $db->prepare("SELECT id, id_usuario, id_linea_credito, cuota_actual, cuotas_totales, valor_solicitado, valor_pagado, valor_saldo, creado_el, actualizado_el FROM saldo_creditos WHERE id = ?");
        $query->bind_param("i", $id);
        $query->execute();
        $query->bind_result($id, $idUsuario, $idLineaCredito, $cuotaActual, $cuotasTotales, $valorSolicitado, $valorPagado, $valorSaldo, $creadoEl, $actualizadoEl);
        $saldoCredito = null;
        if ($query->fetch()) {
            $saldoCredito = new SaldoCredito($id, $idUsuario, $idLineaCredito, $cuotaActual, $cuotasTotales, $valorSolicitado, $valorPagado, $valorSaldo, $creadoEl, $actualizadoEl);
        }
        $query->close();
        $db->close();
        return $saldoCredito;
    }

    public static function obtenerPorIdUsuario($idUsuario) {
        $db = getDB();
        $query = $db->prepare("SELECT id, id_usuario, id_linea_credito, cuota_actual, cuotas_totales, valor_solicitado, valor_pagado, valor_saldo, DATE_FORMAT(creado_el, '%d/%m/%Y') as creado_el, DATE_FORMAT(creado_el, '%d/%m/%Y') as actualizado_el FROM saldo_creditos WHERE id_usuario = ?");
        $query->bind_param("i", $idUsuario);
        $query->execute();
        $query->bind_result($id, $idUsuario, $idLineaCredito, $cuotaActual, $cuotasTotales, $valorSolicitado, $valorPagado, $valorSaldo, $creadoEl, $actualizadoEl);
        
        $saldos = [];

        while ($query->fetch()) {
            $saldos[] = new SaldoCredito($id, $idUsuario, $idLineaCredito, $cuotaActual, $cuotasTotales, $valorSolicitado, $valorPagado, $valorSaldo, $creadoEl, $actualizadoEl);
        }
        
        $query->close();
        $db->close();
        
        return $saldos;
    }

    public static function obtenerPorIdUsuarioYLineaCredito($idUsuario, $idLineaCredito) {
        $db = getDB();
        $query = $db->prepare("SELECT id, id_usuario, id_linea_credito, cuota_actual, cuotas_totales, valor_solicitado, valor_pagado, valor_saldo, creado_el, actualizado_el FROM saldo_creditos WHERE id_usuario = ? AND id_linea_credito = ?");
        $query->bind_param("ii", $idUsuario, $idLineaCredito);
        $query->execute();
        $query->bind_result($id, $idUsuario, $idLineaCredito, $cuotaActual, $cuotasTotales, $valorSolicitado, $valorPagado, $valorSaldo, $creadoEl, $actualizadoEl);
        $saldoCredito = null;
        if ($query->fetch()) {
            $saldoCredito = new SaldoCredito($id, $idUsuario, $idLineaCredito, $cuotaActual, $cuotasTotales, $valorSolicitado, $valorPagado, $valorSaldo, $creadoEl, $actualizadoEl);
        }
        $query->close();
        $db->close();
        return $saldoCredito;
    }
    

    public static function obtenerTodos() {
        $db = getDB();
        $query = "SELECT id, id_usuario, id_linea_credito, cuota_actual, cuotas_totales, valor_solicitado, valor_pagado, valor_saldo, creado_el, actualizado_el FROM saldo_creditos";
        $result = $db->query($query);
        $saldos = [];
        while ($row = $result->fetch_assoc()) {
            $saldos[] = new SaldoCredito($row['id'], $row['id_usuario'], $row['id_linea_credito'], $row['cuota_actual'], $row['cuotas_totales'], $row['valor_solicitado'], $row['valor_pagado'], $row['valor_saldo'], $row['creado_el'], $row['actualizado_el']);
        }
        $db->close();
        return $saldos;
    }

    public function eliminar() {
        $db = getDB();
        if ($this->id !== null) {
            $query = $db->prepare("DELETE FROM saldo_creditos WHERE id = ?");
            $query->bind_param("i", $this->id);
            $query->execute();
            $query->close();
        }
        $db->close();
    }
}
?>