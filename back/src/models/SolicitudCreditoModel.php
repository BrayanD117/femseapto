<?php
require_once __DIR__ . '/../../config/config.php';

class SolicitudCredito {
    public $id;
    public $id_usuario;
    public $monto_solicitado;
    public $plazo_quincenal;
    public $valor_cuota_quincenal;
    public $id_linea_credito;
    public $reestructurado;
    public $periocidad_pago;
    public $tasa_interes;
    public $fecha_solicitud;

    public function __construct($id = null, $id_usuario = null, $monto_solicitado = null, $plazo_quincenal = null, $valor_cuota_quincenal = null, $id_linea_credito = null, $reestructurado = null, $periocidad_pago = null, $tasa_interes = null, $fecha_solicitud = null) {
        $this->id = $id;
        $this->id_usuario = $id_usuario;
        $this->monto_solicitado = $monto_solicitado;
        $this->plazo_quincenal = $plazo_quincenal;
        $this->valor_cuota_quincenal = $valor_cuota_quincenal;
        $this->id_linea_credito = $id_linea_credito;
        $this->reestructurado = $reestructurado;
        $this->periocidad_pago = $periocidad_pago;
        $this->tasa_interes = $tasa_interes;
        $this->fecha_solicitud = $fecha_solicitud;
    }

    public function guardar() {
        $db = getDB();
        if ($this->id === null) {
            $query = $db->prepare("INSERT INTO solicitudes_credito (id_usuario, monto_solicitado, plazo_quincenal, valor_cuota_quincenal, id_linea_credito, reestructurado, periocidad_pago, tasa_interes) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
            $query->bind_param("ididisss", $this->id_usuario, $this->monto_solicitado, $this->plazo_quincenal, $this->valor_cuota_quincenal, $this->id_linea_credito, $this->reestructurado, $this->periocidad_pago, $this->tasa_interes);
        } else {
            $query = $db->prepare("UPDATE solicitudes_credito SET monto_solicitado = ?, plazo_quincenal = ?, valor_cuota_quincenal = ?, id_linea_credito = ?, reestructurado = ?, periocidad_pago = ?, tasa_interes = ? WHERE id = ?");
            $query->bind_param("didisssi", $this->id_usuario, $this->monto_solicitado, $this->plazo_quincenal, $this->valor_cuota_quincenal, $this->id_linea_credito, $this->reestructurado, $this->periocidad_pago, $this->tasa_interes, $this->id);
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
        $query = $db->prepare("SELECT id, id_usuario, monto_solicitado, plazo_quincenal, valor_cuota_quincenal, id_linea_credito, reestructurado, periocidad_pago, tasa_interes, fecha_solicitud FROM solicitudes_credito WHERE id = ?");
        $query->bind_param("i", $id);
        $query->execute();
        $query->bind_result($id, $id_usuario, $monto_solicitado, $plazo_quincenal, $valor_cuota_quincenal, $id_linea_credito, $reestructurado, $periocidad_pago, $tasa_interes, $fecha_solicitud);
        $solicitud = null;
        if ($query->fetch()) {
            $solicitud = new SolicitudCredito($id, $id_usuario, $monto_solicitado, $plazo_quincenal, $valor_cuota_quincenal, $id_linea_credito, $reestructurado, $periocidad_pago, $tasa_interes, $fecha_solicitud);
        }
        $query->close();
        $db->close();
        return $solicitud;
    }

    public static function obtenerTodos() {
        $db = getDB();
        $query = "SELECT id, id_usuario, monto_solicitado, plazo_quincenal, valor_cuota_quincenal, id_linea_credito, reestructurado, periocidad_pago, tasa_interes, fecha_solicitud FROM solicitudes_credito";
        $result = $db->query($query);
        $solicitudes = [];
        while ($row = $result->fetch_assoc()) {
            $solicitudes[] = new SolicitudCredito($row['id'], $row['id_usuario'], $row['monto_solicitado'], $row['plazo_quincenal'], $row['valor_cuota_quincenal'], $row['id_linea_credito'], $row['reestructurado'], $row['periocidad_pago'], $row['tasa_interes'], $row['fecha_solicitud']);
        }
        $db->close();
        return $solicitudes;
    }

    public function eliminar() {
        $db = getDB();
        if ($this->id !== null) {
            $query = $db->prepare("DELETE FROM solicitudes_credito WHERE id = ?");
            $query->bind_param("i", $this->id);
            $query->execute();
            $query->close();
        }
        $db->close();
    }
}
?>