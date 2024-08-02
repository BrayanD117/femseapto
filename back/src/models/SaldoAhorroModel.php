<?php
require_once __DIR__ . '/../../config/config.php';

class SaldoAhorro {
    public $id;
    public $idUsuario;
    public $idLineaAhorro;
    public $valorSaldo;
    public $creadoEl;
    public $actualizadoEl;

    public function __construct($id = null, $idUsuario = null, $idLineaAhorro = null, $valorSaldo = null, $creadoEl = null, $actualizadoEl = null) {
        $this->id = $id;
        $this->idUsuario = $idUsuario;
        $this->idLineaAhorro = $idLineaAhorro;
        $this->valorSaldo = $valorSaldo;
        $this->creadoEl = $creadoEl;
        $this->actualizadoEl = $actualizadoEl;
    }

    public function guardar() {
        $db = getDB();
        if ($this->id === null) {
            $query = $db->prepare("INSERT INTO saldo_ahorros (id_usuario, id_linea_ahorro, valor_saldo) VALUES (?, ?, ?)");
            $query->bind_param("iid", $this->idUsuario, $this->idLineaAhorro, $this->valorSaldo);
        } else {
            $query = $db->prepare("UPDATE saldo_ahorros SET id_linea_ahorro = ?, valor_saldo = ? WHERE id = ?");
            $query->bind_param("idi", $this->idLineaAhorro, $this->valorSaldo, $this->id);
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
        $query = $db->prepare("SELECT id, id_usuario, id_linea_ahorro, valor_saldo, creado_el, actualizado_el FROM saldo_ahorros WHERE id = ?");
        $query->bind_param("i", $id);
        $query->execute();
        $query->bind_result($id, $idUsuario, $idLineaAhorro, $valorSaldo, $creadoEl, $actualizadoEl);
        $saldoAhorro = null;
        if ($query->fetch()) {
            $saldoAhorro = new SaldoAhorro($id, $idUsuario, $idLineaAhorro, $valorSaldo, $creadoEl, $actualizadoEl);
        }
        $query->close();
        $db->close();
        return $saldoAhorro;
    }

    public static function obtenerPorIdUsuario($idUsuario) {
        $db = getDB();
        $query = $db->prepare("SELECT id, id_usuario, id_linea_ahorro, valor_saldo, creado_el, actualizado_el FROM saldo_ahorros WHERE id_usuario = ?");
        $query->bind_param("i", $idUsuario);
        $query->execute();
        $query->bind_result($id, $idUsuario, $idLineaAhorro, $valorSaldo, $creadoEl, $actualizadoEl);
        
        $saldos = [];

        while ($query->fetch()) {
            $saldos[] = new SaldoAhorro($id, $idUsuario, $idLineaAhorro, $valorSaldo, $creadoEl, $actualizadoEl);
        }
        
        $query->close();
        $db->close();
        
        return $saldos;
    }

    public static function obtenerPorIdUsuarioYLineaAhorro($idUsuario, $idLineaAhorro) {
        $db = getDB();
        $query = $db->prepare("SELECT id, id_usuario, id_linea_ahorro, valor_saldo, creado_el, actualizado_el FROM saldo_ahorros WHERE id_usuario = ? AND id_linea_ahorro = ?");
        $query->bind_param("ii", $idUsuario, $idLineaAhorro);
        $query->execute();
        $query->bind_result($id, $idUsuario, $idLineaAhorro, $valorSaldo, $creadoEl, $actualizadoEl);
        $saldoAhorro = null;
        if ($query->fetch()) {
            $saldoAhorro = new SaldoAhorro($id, $idUsuario, $idLineaAhorro, $valorSaldo, $creadoEl, $actualizadoEl);
        }
        $query->close();
        $db->close();
        return $saldoAhorro;
    }

    public static function obtenerTodos() {
        $db = getDB();
        $query = "SELECT id, id_usuario, id_linea_ahorro, valor_saldo, creado_el, actualizado_el FROM saldo_ahorros";
        $result = $db->query($query);
        $saldos = [];
        while ($row = $result->fetch_assoc()) {
            $saldos[] = new SaldoAhorro($row['id'], $row['id_usuario'], $row['id_linea_ahorro'], $row['valor_saldo'], $row['creado_el'], $row['actualizado_el']);
        }
        $db->close();
        return $saldos;
    }

    public function eliminar() {
        $db = getDB();
        if ($this->id !== null) {
            $query = $db->prepare("DELETE FROM saldo_ahorros WHERE id = ?");
            $query->bind_param("i", $this->id);
            $query->execute();
            $query->close();
        }
        $db->close();
    }
}
?>