<?php
require_once '../config/config.php';

class InformacionFinanciera {
    public $id;
    public $id_usuario;
    public $nombre_banco;
    public $id_tipo_cuenta_banc;
    public $numero_cuenta_banc;
    public $ingresos_mensuales;
    public $prima_productividad;
    public $otros_ingresos_mensuales;
    public $concepto_otros_ingresos_mens;
    public $total_ingresos_mensuales;
    public $egresos_mensuales;
    public $obligacion_financiera;
    public $otros_egresos_mensuales;
    public $total_egresos_mensuales;
    public $total_activos;
    public $total_pasivos;
    public $total_patrimonio;
    public $monto_max_ahorro;
    public $creadoEl;
    public $actualizadoEl;

    public function __construct($id = null, $id_usuario = null, $nombre_banco = '',
        $id_tipo_cuenta_banc = null, $numero_cuenta_banc = '', $ingresos_mensuales = 0.00, 
        $prima_productividad = 0.00, $otros_ingresos_mensuales = 0.00, $concepto_otros_ingresos_mens = '',
        $total_ingresos_mensuales = 0.00, $egresos_mensuales = 0.00, $obligacion_financiera = 0.00,
        $otros_egresos_mensuales = 0.00, $total_egresos_mensuales = 0.00,
        $total_activos = 0.00, $total_pasivos = 0.00, $total_patrimonio = 0.00,
        $monto_max_ahorro = 0.00, $creadoEl = '', $actualizadoEl = '') {
        $this->id = $id;
        $this->id_usuario = $id_usuario;
        $this->nombre_banco = $nombre_banco;
        $this->id_tipo_cuenta_banc = $id_tipo_cuenta_banc;
        $this->numero_cuenta_banc = $numero_cuenta_banc;
        $this->ingresos_mensuales = $ingresos_mensuales;
        $this->prima_productividad = $prima_productividad;
        $this->otros_ingresos_mensuales = $otros_ingresos_mensuales;
        $this->concepto_otros_ingresos_mens = $concepto_otros_ingresos_mens;
        $this->total_ingresos_mensuales = $total_ingresos_mensuales;
        $this->egresos_mensuales = $egresos_mensuales;
        $this->obligacion_financiera = $obligacion_financiera;
        $this->otros_egresos_mensuales = $otros_egresos_mensuales;
        $this->total_egresos_mensuales = $total_egresos_mensuales;
        $this->total_activos = $total_activos;
        $this->total_pasivos = $total_pasivos;
        $this->total_patrimonio = $total_patrimonio;
        $this->monto_max_ahorro = $monto_max_ahorro;
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
                                    total_egresos_mensuales, total_activos, total_pasivos, total_patrimonio, monto_max_ahorro)
                                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $query->bind_param("isisdddsddddddddd", $this->id_usuario, $this->nombre_banco,
                                $this->id_tipo_cuenta_banc, $this->numero_cuenta_banc, $this->ingresos_mensuales,
                                $this->prima_productividad, $this->otros_ingresos_mensuales,
                                $this->concepto_otros_ingresos_mens, $this->total_ingresos_mensuales,
                                $this->egresos_mensuales, $this->obligacion_financiera,
                                $this->otros_egresos_mensuales, $this->total_egresos_mensuales,
                                $this->total_activos, $this->total_pasivos, $this->total_patrimonio, $this->monto_max_ahorro);
        } else {
            $query = $db->prepare("UPDATE informacion_financiera SET nombre_banco = ?, id_tipo_cuenta_banc = ?, numero_cuenta_banc = ?, ingresos_mensuales = ?, prima_productividad = ? otros_ingresos_mensuales = ?, concepto_otros_ingresos_mens = ?, total_ingresos_mensuales = ?, egresos_mensuales = ?, obligacion_financiera = ?, otros_egresos_mensuales = ?, total_egresos_mensuales = ?, total_activos = ?, total_pasivos = ?, total_patrimonio = ?, monto_max_ahorro = ? WHERE id = ?");
            $query->bind_param("sisdddsdddddddddi", $this->id_usuario, $this->ingresos_mensuales, $this->otros_ingresos_mensuales, $this->concepto_otros_ingresos_mens, $this->total_ingresos_mensuales, $this->egresos_mensuales, $this->otros_egresos_mensuales, $this->total_egresos_mensuales, $this->total_activos, $this->total_pasivos, $this->total_patrimonio, $this->monto_max_ahorro, $this->id);
        }
        $query->execute();
        if ($this->id === null) {
            $this->id = $query->insert_id;
        }
        $query->close();
        $db->close();
    }

    public static function obtenerPorIdUsuario($idUsuario) {
        $db = getDB();
        $query = $db->prepare("SELECT * FROM informacion_financiera WHERE id_usuario = ?");
        $query->bind_param("i", $idUsuario);
        $query->execute();
        $query->bind_result($id, $id_usuario, $nombre_banco, $id_tipo_cuenta_banc, $numero_cuenta_banc,
                            $ingresos_mensuales, $prima_productividad, $otros_ingresos_mensuales,
                            $concepto_otros_ingresos_mens, $total_ingresos_mensuales,
                            $egresos_mensuales, $obligacion_financiera, $otros_egresos_mensuales,
                            $total_egresos_mensuales, $total_activos, $total_pasivos, $total_patrimonio,
                            $monto_max_ahorro, $creadoEl, $actualizadoEl);
        $infoFinanciera = null;
        if ($query->fetch()) {
            $infoFinanciera = new InformacionFinanciera($id, $id_usuario, $nombre_banco, $id_tipo_cuenta_banc, $numero_cuenta_banc,
                                $ingresos_mensuales, $prima_productividad, $otros_ingresos_mensuales,
                                $concepto_otros_ingresos_mens, $total_ingresos_mensuales,
                                $egresos_mensuales, $obligacion_financiera, $otros_egresos_mensuales,
                                $total_egresos_mensuales, $total_activos, $total_pasivos, $total_patrimonio,
                                $monto_max_ahorro, $creadoEl, $actualizadoEl);
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
