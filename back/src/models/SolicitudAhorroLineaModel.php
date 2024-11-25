<?php
require_once __DIR__ . '/../../config/config.php';

class SolicitudAhorroLinea {
    public $id;
    public $idSolicitudAhorro;
    public $idLineaAhorro;
    public $montoAhorrar;

    public function __construct($id = null, $idSolicitudAhorro = null, $idLineaAhorro = null, $montoAhorrar = null) {
        $this->id = $id;
        $this->idSolicitudAhorro = $idSolicitudAhorro;
        $this->idLineaAhorro = $idLineaAhorro;
        $this->montoAhorrar = $montoAhorrar;
    }

    public function guardar() {
        $db = getDB();
        if ($this->id === null) {
            $query = $db->prepare("INSERT INTO solicitudes_ahorro_lineas (id_solicitud_ahorro, id_linea_ahorro, monto_ahorrar) VALUES (?, ?, ?)");
            $query->bind_param("iid", $this->idSolicitudAhorro, $this->idLineaAhorro, $this->montoAhorrar);
            $query->execute();
            $this->id = $query->insert_id;
            $query->close();
        } else {
            $query = $db->prepare("UPDATE solicitudes_ahorro_lineas SET id_solicitud_ahorro = ?, id_linea_ahorro = ?, monto_ahorrar = ? WHERE id = ?");
            $query->bind_param("iidi", $this->idSolicitudAhorro, $this->idLineaAhorro, $this->montoAhorrar, $this->id);
            $query->execute();
            $query->close();
        }
        $db->close();
    }

    public static function obtenerPorId($id) {
        $db = getDB();
        $query = $db->prepare(
            "SELECT
                id,
                id_solicitud_ahorro,
                id_linea_ahorro,
                monto_ahorrar
            FROM solicitudes_ahorro_lineas
            WHERE id = ?");
        $query->bind_param("i", $id);
        $query->execute();
        $query->bind_result($id, $idSolicitudAhorro, $idLineaAhorro, $montoAhorrar);
        $solicitudLinea = null;
        if ($query->fetch()) {
            $solicitudLinea = new SolicitudAhorroLinea($id, $idSolicitudAhorro, $idLineaAhorro, $montoAhorrar);
        }
        $query->close();
        $db->close();
        return $solicitudLinea;
    }

    public static function obtenerPorSolicitudId($idSolicitudAhorro) {
        $db = getDB();
        $query = $db->prepare(
            "SELECT
                id,
                id_solicitud_ahorro,
                id_linea_ahorro,
                monto_ahorrar
            FROM solicitudes_ahorro_lineas
            WHERE id_solicitud_ahorro = ?");
        $query->bind_param("i", $idSolicitudAhorro);
        $query->execute();
        $query->bind_result($id, $idSolicitudAhorro, $idLineaAhorro, $montoAhorrar);
        
        $solAhorros = [];

        while ($query->fetch()) {
            $solAhorros[] = new SolicitudAhorroLinea($id, $idSolicitudAhorro, $idLineaAhorro, $montoAhorrar);
        }
        
        $query->close();
        $db->close();
        
        return $solAhorros;
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