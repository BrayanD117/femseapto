<?php
require_once __DIR__ . '/../../config/config.php';

class SolicitudAhorroLinea {
    public $id;
    public $id_solicitud_ahorro;
    public $id_linea_ahorro;
    public $monto_ahorrar;

    public function __construct($id = null, $id_solicitud_ahorro = null, $id_linea_ahorro = null, $monto_ahorrar = null) {
        $this->id = $id;
        $this->id_solicitud_ahorro = $id_solicitud_ahorro;
        $this->id_linea_ahorro = $id_linea_ahorro;
        $this->monto_ahorrar = $monto_ahorrar;
    }

    public function guardar() {
        $db = getDB();
        if ($this->id === null) {
            $query = $db->prepare("INSERT INTO solicitudes_ahorro_lineas (id_solicitud_ahorro, id_linea_ahorro, monto_ahorrar) VALUES (?, ?, ?)");
            $query->bind_param("iid", $this->id_solicitud_ahorro, $this->id_linea_ahorro, $this->monto_ahorrar);
            $query->execute();
            $this->id = $query->insert_id;
            $query->close();
        } else {
            $query = $db->prepare("UPDATE solicitudes_ahorro_lineas SET id_solicitud_ahorro = ?, id_linea_ahorro = ?, monto_ahorrar = ? WHERE id = ?");
            $query->bind_param("iidi", $this->id_solicitud_ahorro, $this->id_linea_ahorro, $this->monto_ahorrar, $this->id);
            $query->execute();
            $query->close();
        }
        $db->close();
    }

    public static function obtenerPorId($id) {
        $db = getDB();
        $query = $db->prepare("SELECT id, id_solicitud_ahorro, id_linea_ahorro, monto_ahorrar FROM solicitudes_ahorro_lineas WHERE id = ?");
        $query->bind_param("i", $id);
        $query->execute();
        $query->bind_result($id, $id_solicitud_ahorro, $id_linea_ahorro, $monto_ahorrar);
        $solicitudLinea = null;
        if ($query->fetch()) {
            $solicitudLinea = new SolicitudAhorroLinea($id, $id_solicitud_ahorro, $id_linea_ahorro, $monto_ahorrar);
        }
        $query->close();
        $db->close();
        return $solicitudLinea;
    }

    public static function obtenerPorSolicitudId($id_solicitud_ahorro) {
        $db = getDB();
        $query = $db->prepare("SELECT id, id_solicitud_ahorro, id_linea_ahorro, monto_ahorrar FROM solicitudes_ahorro_lineas WHERE id_solicitud_ahorro = ?");
        $query->bind_param("i", $id_solicitud_ahorro);
        $query->execute();
        $result = $query->get_result();
        $solicitudesLineas = [];
        while ($row = $result->fetch_assoc()) {
            $solicitudesLineas[] = new SolicitudAhorroLinea($row['id'], $row['id_solicitud_ahorro'], $row['id_linea_ahorro'], $row['monto_ahorrar']);
        }
        $query->close();
        $db->close();
        return $solicitudesLineas;
    }

    public function eliminar() {
        $db = getDB();
        if ($this->id !== null) {
            $query = $db->prepare("DELETE FROM solicitudes_ahorro_lineas WHERE id = ?");
            $query->bind_param("i", $this->id);
            $query->execute();
            $query->close();
        }
        $db->close();
    }
}
?>