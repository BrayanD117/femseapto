<?php
require_once __DIR__ . '/../../config/config.php';

class OperacionesInternacionales {
    public $id;
    public $id_usuario;
    public $transacciones_moneda_extranjera;
    public $trans_moneda_extranjera;
    public $otras_operaciones;
    public $cuentas_moneda_extranjera;
    public $banco_cuenta_extranjera;
    public $cuenta_moneda_extranjera;
    public $moneda_cuenta;
    public $id_pais_cuenta;
    public $ciudad_cuenta;

    public function __construct($id = null, $id_usuario = null, $transacciones_moneda_extranjera = '', $trans_moneda_extranjera = '', $otras_operaciones = '', $cuentas_moneda_extranjera = '', $banco_cuenta_extranjera = '', $cuenta_moneda_extranjera = '', $moneda_cuenta = '', $id_pais_cuenta = '', $ciudad_cuenta = '') {
        $this->id = $id;
        $this->id_usuario = $id_usuario;
        $this->transacciones_moneda_extranjera = $transacciones_moneda_extranjera;
        $this->trans_moneda_extranjera = $trans_moneda_extranjera;
        $this->otras_operaciones = $otras_operaciones;
        $this->cuentas_moneda_extranjera = $cuentas_moneda_extranjera;
        $this->banco_cuenta_extranjera = $banco_cuenta_extranjera;
        $this->cuenta_moneda_extranjera = $cuenta_moneda_extranjera;
        $this->moneda_cuenta = $moneda_cuenta;
        $this->id_pais_cuenta = $id_pais_cuenta;
        $this->ciudad_cuenta = $ciudad_cuenta;
    }

    public function guardar() {
        $db = getDB();
        if ($this->id === null) {
            $query = $db->prepare("INSERT INTO operaciones_internacionales (id_usuario, transacciones_moneda_extranjera, trans_moneda_extranjera, otras_operaciones, cuentas_moneda_extranjera, banco_cuenta_extranjera, cuenta_moneda_extranjera, moneda_cuenta, id_pais_cuenta, ciudad_cuenta) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $query->bind_param("isssssssss", $this->id_usuario, $this->transacciones_moneda_extranjera, $this->trans_moneda_extranjera, $this->otras_operaciones, $this->cuentas_moneda_extranjera, $this->banco_cuenta_extranjera, $this->cuenta_moneda_extranjera, $this->moneda_cuenta, $this->id_pais_cuenta, $this->ciudad_cuenta);
        } else {
            $query = $db->prepare("UPDATE operaciones_internacionales SET id_usuario = ?, transacciones_moneda_extranjera = ?, trans_moneda_extranjera = ?, otras_operaciones = ?, cuentas_moneda_extranjera = ?, banco_cuenta_extranjera = ?, cuenta_moneda_extranjera = ?, moneda_cuenta = ?, id_pais_cuenta = ?, ciudad_cuenta = ? WHERE id = ?");
            $query->bind_param("isssssssssi", $this->id_usuario, $this->transacciones_moneda_extranjera, $this->trans_moneda_extranjera, $this->otras_operaciones, $this->cuentas_moneda_extranjera, $this->banco_cuenta_extranjera, $this->cuenta_moneda_extranjera, $this->moneda_cuenta, $this->id_pais_cuenta, $this->ciudad_cuenta, $this->id);
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
        $query = $db->prepare("SELECT * FROM operaciones_internacionales WHERE id = ?");
        $query->bind_param("i", $id);
        $query->execute();
        $query->bind_result($id, $id_usuario, $transacciones_moneda_extranjera, $trans_moneda_extranjera, $otras_operaciones, $cuentas_moneda_extranjera, $banco_cuenta_extranjera, $cuenta_moneda_extranjera, $moneda_cuenta, $id_pais_cuenta, $ciudad_cuenta);
        $operacion = null;
        if ($query->fetch()) {
            $operacion = new OperacionesInternacionales($id, $id_usuario, $transacciones_moneda_extranjera, $trans_moneda_extranjera, $otras_operaciones, $cuentas_moneda_extranjera, $banco_cuenta_extranjera, $cuenta_moneda_extranjera, $moneda_cuenta, $id_pais_cuenta, $ciudad_cuenta);
        }
        $query->close();
        $db->close();
        return $operacion;
    }

    public static function obtenerTodos() {
        $db = getDB();
        $query = "SELECT * FROM operaciones_internacionales";
        $result = $db->query($query);
        $operaciones = [];
        while ($row = $result->fetch_assoc()) {
            $operaciones[] = new OperacionesInternacionales($row['id'], $row['id_usuario'], $row['transacciones_moneda_extranjera'], $row['trans_moneda_extranjera'], $row['otras_operaciones'], $row['cuentas_moneda_extranjera'], $row['banco_cuenta_extranjera'], $row['cuenta_moneda_extranjera'], $row['moneda_cuenta'], $row['id_pais_cuenta'], $row['ciudad_cuenta']);
        }
        $db->close();
        return $operaciones;
    }

    public function eliminar() {
        $db = getDB();
        if ($this->id !== null) {
            $query = $db->prepare("DELETE FROM operaciones_internacionales WHERE id = ?");
            $query->bind_param("i", $this->id);
            $query->execute();
            $query->close();
        }
        $db->close();
    }
}
?>
