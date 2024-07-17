<?php
require_once '../config/config.php';

class InformacionFinanciera {
    public $id;
    public $idUsuario;
    public $nombreBanco;
    public $idTipoCuentaBanc;
    public $numeroCuentaBanc;
    public $ingresosMensuales;
    public $primaProductividad;
    public $otrosIngresosMensuales;
    public $conceptoOtrosIngresosMens;
    public $totalIngresosMensuales;
    public $egresosMensuales;
    public $obligacionFinanciera;
    public $otrosEgresosMensuales;
    public $totalEgresosMensuales;
    public $totalActivos;
    public $totalPasivos;
    public $totalPatrimonio;
    public $montoMaxAhorro;
    public $creadoEl;
    public $actualizadoEl;

    public function __construct($id = null, $idUsuario = null, $nombreBanco = '',
        $idTipoCuentaBanc = null, $numeroCuentaBanc = '', $ingresosMensuales = 0.00, 
        $primaProductividad = 0.00, $otrosIngresosMensuales = 0.00, $conceptoOtrosIngresosMens = '',
        $totalIngresosMensuales = 0.00, $egresosMensuales = 0.00, $obligacionFinanciera = 0.00,
        $otrosEgresosMensuales = 0.00, $totalEgresosMensuales = 0.00,
        $totalActivos = 0.00, $totalPasivos = 0.00, $totalPatrimonio = 0.00,
        $montoMaxAhorro = 0.00, $creadoEl = '', $actualizadoEl = '') {
        $this->id = $id;
        $this->idUsuario = $idUsuario;
        $this->nombreBanco = $nombreBanco;
        $this->idTipoCuentaBanc = $idTipoCuentaBanc;
        $this->numeroCuentaBanc = $numeroCuentaBanc;
        $this->ingresosMensuales = $ingresosMensuales;
        $this->primaProductividad = $primaProductividad;
        $this->otrosIngresosMensuales = $otrosIngresosMensuales;
        $this->conceptoOtrosIngresosMens = $conceptoOtrosIngresosMens;
        $this->totalIngresosMensuales = $totalIngresosMensuales;
        $this->egresosMensuales = $egresosMensuales;
        $this->obligacionFinanciera = $obligacionFinanciera;
        $this->otrosEgresosMensuales = $otrosEgresosMensuales;
        $this->totalEgresosMensuales = $totalEgresosMensuales;
        $this->totalActivos = $totalActivos;
        $this->totalPasivos = $totalPasivos;
        $this->totalPatrimonio = $totalPatrimonio;
        $this->montoMaxAhorro = $montoMaxAhorro;
        $this->creadoEl = $creadoEl;
        $this->actualizadoEl = $actualizadoEl;
    }

    public function guardar() {
        $db = getDB();
        if ($this->id === null) {
            $query = $db->prepare("INSERT INTO informacion_financiera (
                                    id_usuario, nombre_banco, id_tipo_cuenta_banc, numero_cuenta_banc,
                                    ingresos_mensuales, prima_productividad, otros_ingresos_mensuales,
                                    concepto_otros_ingresos_mens, total_ingresos_mensuales,
                                    egresos_mensuales, obligacion_financiera, otros_egresos_mensuales,
                                    total_egresos_mensuales, total_activos, total_pasivos, total_patrimonio)
                                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $query->bind_param("isisdddsdddddddd", $this->idUsuario, $this->nombreBanco,
                                $this->idTipoCuentaBanc, $this->numeroCuentaBanc, $this->ingresosMensuales,
                                $this->primaProductividad, $this->otrosIngresosMensuales,
                                $this->conceptoOtrosIngresosMens, $this->totalIngresosMensuales,
                                $this->egresosMensuales, $this->obligacionFinanciera,
                                $this->otrosEgresosMensuales, $this->totalEgresosMensuales,
                                $this->totalActivos, $this->totalPasivos, $this->totalPatrimonio);
        } else {
            $query = $db->prepare("UPDATE informacion_financiera SET nombre_banco = ?, id_tipo_cuenta_banc = ?, numero_cuenta_banc = ?, ingresos_mensuales = ?, prima_productividad = ?, otros_ingresos_mensuales = ?, concepto_otros_ingresos_mens = ?, total_ingresos_mensuales = ?, egresos_mensuales = ?, obligacion_financiera = ?, otros_egresos_mensuales = ?, total_egresos_mensuales = ?, total_activos = ?, total_pasivos = ?, total_patrimonio = ? WHERE id = ?");
            $query->bind_param("sisdddsddddddddi", $this->nombreBanco, $this->idTipoCuentaBanc, $this->numeroCuentaBanc, $this->ingresosMensuales, $this->primaProductividad, $this->otrosIngresosMensuales, $this->conceptoOtrosIngresosMens, $this->totalIngresosMensuales, $this->egresosMensuales, $this->obligacionFinanciera, $this->otrosEgresosMensuales, $this->totalEgresosMensuales, $this->totalActivos, $this->totalPasivos, $this->totalPatrimonio, $this->id);
        }
        $query->execute();
        if ($this->id === null) {
            $this->id = $query->insert_id;
        }
        $query->close();
        $db->close();
    }

    public static function validarInformacionFinanciera($id) {
        $db = getDB();
        $query = $db->prepare("SELECT validarInformacionFinancieraUsuario(?) AS isValid");
        $query->bind_param("i", $id);
        $query->execute();
        $query->bind_result($isValid);
        $query->fetch();
        $query->close();
        $db->close();
        return (bool)$isValid;
    }

    public static function obtenerPorIdUsuario($idUsuario) {
        $db = getDB();
        $query = $db->prepare("SELECT * FROM informacion_financiera WHERE id_usuario = ?");
        $query->bind_param("i", $idUsuario);
        $query->execute();
        $query->bind_result($id, $idUsuario, $nombreBanco, $idTipoCuentaBanc, $numeroCuentaBanc,
                            $ingresosMensuales, $primaProductividad, $otrosIngresosMensuales,
                            $conceptoOtrosIngresosMens, $totalIngresosMensuales,
                            $egresosMensuales, $obligacionFinanciera, $otrosEgresosMensuales,
                            $totalEgresosMensuales, $totalActivos, $totalPasivos, $totalPatrimonio,
                            $montoMaxAhorro, $creadoEl, $actualizadoEl);
        $infoFinanciera = null;
        if ($query->fetch()) {
            $infoFinanciera = new InformacionFinanciera($id, $idUsuario, $nombreBanco, $idTipoCuentaBanc, $numeroCuentaBanc,
                                $ingresosMensuales, $primaProductividad, $otrosIngresosMensuales,
                                $conceptoOtrosIngresosMens, $totalIngresosMensuales,
                                $egresosMensuales, $obligacionFinanciera, $otrosEgresosMensuales,
                                $totalEgresosMensuales, $totalActivos, $totalPasivos, $totalPatrimonio,
                                $montoMaxAhorro, $creadoEl, $actualizadoEl);
        }
        $query->close();
        $db->close();
        return $infoFinanciera;
    }

    public static function obtenerTodos() {
        $db = getDB();
        $query = "SELECT * FROM informacion_financiera";
        $result = $db->query($query);
        $informacionFinanciera = [];
        while ($row = $result->fetch_assoc()) {
            $informacionFinanciera[] = new InformacionFinanciera($row['id'], $row['id_usuario'],
                                        $row['nombre_banco'], $row['id_tipo_cuenta_banc'],
                                        $row['numero_cuenta_banc'], $row['ingresos_mensuales'],
                                        $row['prima_productividad'], $row['otros_ingresos_mensuales'],
                                        $row['concepto_otros_ingresos_mens'], $row['total_ingresos_mensuales'],
                                        $row['egresos_mensuales'], $row['obligacion_financiera'],
                                        $row['otros_egresos_mensuales'], $row['total_egresos_mensuales'],
                                        $row['total_activos'], $row['total_pasivos'], $row['total_patrimonio'],
                                        $row['monto_max_ahorro'], $row['creado_el'], $row['actualizado_el']);
        }
        $db->close();
        return $informacionFinanciera;
    }

    public function eliminar() {
        $db = getDB();
        if ($this->id !== null) {
            $query = $db->prepare("DELETE FROM informacion_financiera WHERE id = ?");
            $query->bind_param("i", $this->id);
            $query->execute();
            $query->close();
        }
        $db->close();
    }
}
?>