<?php
require_once 'config.php';

class InformacionFinanciera {
    public $id;
    public $id_usuario;
    public $ingresos_mensuales;
    public $otros_ingresos_mensuales;
    public $concepto_otros_ingresos_mens;
    public $total_ingresos_mensuales;
    public $egresos_mensuales;
    public $otros_egresos_mensuales;
    public $total_egresos_mensuales;
    public $total_activos;
    public $total_pasivos;
    public $total_patrimonio;

    public function __construct($id = null, $id_usuario = null, $ingresos_mensuales = 0.00, $otros_ingresos_mensuales = 0.00, $concepto_otros_ingresos_mens = '', $total_ingresos_mensuales = 0.00, $egresos_mensuales = 0.00, $otros_egresos_mensuales = 0.00, $total_egresos_mensuales = 0.00, $total_activos = 0.00, $total_pasivos = 0.00, $total_patrimonio = 0.00) {
        $this->id = $id;
        $this->id_usuario = $id_usuario;
        $this->ingresos_mensuales = $ingresos_mensuales;
        $this->otros_ingresos_mensuales = $otros_ingresos_mensuales;
        $this->concepto_otros_ingresos_mens = $concepto_otros_ingresos_mens;
        $this->total_ingresos_mensuales = $total_ingresos_mensuales;
        $this->egresos_mensuales = $egresos_mensuales;
        $this->otros_egresos_mensuales = $otros_egresos_mensuales;
        $this->total_egresos_mensuales = $total_egresos_mensuales;
        $this->total_activos = $total_activos;
        $this->total_pasivos = $total_pasivos;
        $this->total_patrimonio = $total_patrimonio;
    }

    public function guardar() {
        $db = getDB();
        if ($this->id === null) {
            $query = $db->prepare("INSERT INTO informacion_financiera (id_usuario, ingresos_mensuales, otros_ingresos_mensuales, concepto_otros_ingresos_mens, total_ingresos_mensuales, egresos_mensuales, otros_egresos_mensuales, total_egresos_mensuales, total_activos, total_pasivos, total_patrimonio) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $query->bind_param("idddddd", $this->id_usuario, $this->ingresos_mensuales, $this->otros_ingresos_mensuales, $this->concepto_otros_ingresos_mens, $this->total_ingresos_mensuales, $this->egresos_mensuales, $this->otros_egresos_mensuales, $this->total_egresos_mensuales, $this->total_activos, $this->total_pasivos, $this->total_patrimonio);
        } else {
            $query = $db->prepare("UPDATE informacion_financiera SET id_usuario = ?, ingresos_mensuales = ?, otros_ingresos_mensuales = ?, concepto_otros_ingresos_mens = ?, total_ingresos_mensuales = ?, egresos_mensuales = ?, otros_egresos_mensuales = ?, total_egresos_mensuales = ?, total_activos = ?, total_pasivos = ?, total_patrimonio = ? WHERE id = ?");
            $query->bind_param("iddddddddi", $this->id_usuario, $this->ingresos_mensuales, $this->otros_ingresos_mensuales, $this->concepto_otros_ingresos_mens, $this->total_ingresos_mensuales, $this->egresos_mensuales, $this->otros_egresos_mensuales, $this->total_egresos_mensuales, $this->total_activos, $this->total_pasivos, $this->total_patrimonio, $this->id);
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
        $query = $db->prepare("SELECT * FROM informacion_financiera WHERE id = ?");
        $query->bind_param("i", $id);
        $query->execute();
        $query->bind_result($id, $id_usuario, $ingresos_mensuales, $otros_ingresos_mensuales, $concepto_otros_ingresos_mens, $total_ingresos_mensuales, $egresos_mensuales, $otros_egresos_mensuales, $total_egresos_mensuales, $total_activos, $total_pasivos, $total_patrimonio);
        $infoFinanciera = null;
        if ($query->fetch()) {
            $infoFinanciera = new InformacionFinanciera($id, $id_usuario, $ingresos_mensuales, $otros_ingresos_mensuales, $concepto_otros_ingresos_mens, $total_ingresos_mensuales, $egresos_mensuales, $otros_egresos_mensuales, $total_egresos_mensuales, $total_activos, $total_pasivos, $total_patrimonio);
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
            $informacionFinanciera[] = new InformacionFinanciera($row['id'], $row['id_usuario'], $row['ingresos_mensuales'], $row['otros_ingresos_mensuales'], $row['concepto_otros_ingresos_mens'], $row['total_ingresos_mensuales'], $row['egresos_mensuales'], $row['otros_egresos_mensuales'], $row['total_egresos_mensuales'], $row['total_activos'], $row['total_pasivos'], $row['total_patrimonio']);
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
