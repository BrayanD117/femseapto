<?php
require_once '../config/config.php';

class OperacionesInternacionales {
    public $id;
    public $idUsuario;
    public $transaccionesMonedaExtranjera;
    public $transMonedaExtranjera;
    public $otrasOperaciones;
    public $cuentasMonedaExtranjera;
    public $bancoCuentaExtranjera;
    public $cuentaMonedaExtranjera;
    public $monedaCuenta;
    public $idPaisCuenta;
    public $ciudadCuenta;
    public $creadoEl;
    public $actualizadoEl;

    public function __construct($id = null, $idUsuario = null, $transaccionesMonedaExtranjera = '', $transMonedaExtranjera = '', $otrasOperaciones = '', $cuentasMonedaExtranjera = '', $bancoCuentaExtranjera = '', $cuentaMonedaExtranjera = '', $monedaCuenta = '', $idPaisCuenta = '', $ciudadCuenta = '', $creadoEl = '', $actualizadoEl = '') {
        $this->id = $id;
        $this->idUsuario = $idUsuario;
        $this->transaccionesMonedaExtranjera = $transaccionesMonedaExtranjera;
        $this->transMonedaExtranjera = $transMonedaExtranjera;
        $this->otrasOperaciones = $otrasOperaciones;
        $this->cuentasMonedaExtranjera = $cuentasMonedaExtranjera;
        $this->bancoCuentaExtranjera = $bancoCuentaExtranjera;
        $this->cuentaMonedaExtranjera = $cuentaMonedaExtranjera;
        $this->monedaCuenta = $monedaCuenta;
        $this->idPaisCuenta = $idPaisCuenta;
        $this->ciudadCuenta = $ciudadCuenta;
        $this->creadoEl = $creadoEl;
        $this->actualizadoEl = $actualizadoEl;
    }

    public function guardar() {
        $db = getDB();
        if ($this->id === null) {
            $query = $db->prepare("INSERT INTO operaciones_internacionales (id_usuario, transacciones_moneda_extranjera, trans_moneda_extranjera, otras_operaciones, cuentas_moneda_extranjera, banco_cuenta_extranjera, cuenta_moneda_extranjera, moneda_cuenta, id_pais_cuenta, ciudad_cuenta) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $query->bind_param("isssssssss", $this->idUsuario, $this->transaccionesMonedaExtranjera, $this->transMonedaExtranjera, $this->otrasOperaciones, $this->cuentasMonedaExtranjera, $this->bancoCuentaExtranjera, $this->cuentaMonedaExtranjera, $this->monedaCuenta, $this->idPaisCuenta, $this->ciudadCuenta);
        } else {
            $query = $db->prepare("UPDATE operaciones_internacionales SET transacciones_moneda_extranjera = ?, trans_moneda_extranjera = ?, otras_operaciones = ?, cuentas_moneda_extranjera = ?, banco_cuenta_extranjera = ?, cuenta_moneda_extranjera = ?, moneda_cuenta = ?, id_pais_cuenta = ?, ciudad_cuenta = ? WHERE id = ?");
            $query->bind_param("sssssssssi", $this->transaccionesMonedaExtranjera, $this->transMonedaExtranjera, $this->otrasOperaciones, $this->cuentasMonedaExtranjera, $this->bancoCuentaExtranjera, $this->cuentaMonedaExtranjera, $this->monedaCuenta, $this->idPaisCuenta, $this->ciudadCuenta, $this->id);
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
        $query->bind_result($id, $idUsuario, $transaccionesMonedaExtranjera, $transMonedaExtranjera, $otrasOperaciones, $cuentasMonedaExtranjera, $bancoCuentaExtranjera, $cuentaMonedaExtranjera, $monedaCuenta, $idPaisCuenta, $ciudadCuenta, $creadoEl, $actualizadoEl);
        $operacion = null;
        if ($query->fetch()) {
            $operacion = new OperacionesInternacionales($id, $idUsuario, $transaccionesMonedaExtranjera, $transMonedaExtranjera, $otrasOperaciones, $cuentasMonedaExtranjera, $bancoCuentaExtranjera, $cuentaMonedaExtranjera, $monedaCuenta, $idPaisCuenta, $ciudadCuenta, $creadoEl, $actualizadoEl);
        }
        $query->close();
        $db->close();
        return $operacion;
    }

    public static function obtenerPorIdUsuario($idUsuario) {
        $db = getDB();
        $query = $db->prepare("SELECT * FROM operaciones_internacionales WHERE id_usuario = ?");
        $query->bind_param("i", $idUsuario);
        $query->execute();
        $query->bind_result($id, $idUsuario, $transaccionesMonedaExtranjera, $transMonedaExtranjera, $otrasOperaciones, $cuentasMonedaExtranjera, $bancoCuentaExtranjera, $cuentaMonedaExtranjera, $monedaCuenta, $idPaisCuenta, $ciudadCuenta, $creadoEl, $actualizadoEl);
        $operacion = null;
        if ($query->fetch()) {
            $operacion = new OperacionesInternacionales($id, $idUsuario, $transaccionesMonedaExtranjera, $transMonedaExtranjera, $otrasOperaciones, $cuentasMonedaExtranjera, $bancoCuentaExtranjera, $cuentaMonedaExtranjera, $monedaCuenta, $idPaisCuenta, $ciudadCuenta, $creadoEl, $actualizadoEl);
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
            $operaciones[] = new OperacionesInternacionales($row['id'], $row['id_usuario'], $row['transacciones_moneda_extranjera'], $row['trans_moneda_extranjera'], $row['otras_operaciones'], $row['cuentas_moneda_extranjera'], $row['banco_cuenta_extranjera'], $row['cuenta_moneda_extranjera'], $row['moneda_cuenta'], $row['id_pais_cuenta'], $row['ciudad_cuenta'], $row['creado_el'], $row['actualizado_el']);
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