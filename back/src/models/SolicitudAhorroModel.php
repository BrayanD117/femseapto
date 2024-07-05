<?php
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/SolicitudAhorroLinea.php';

class SolicitudAhorro {
    public $id;
    public $id_usuario;
    public $monto_total_ahorrar;
    public $quincena;
    public $mes;
    public $fecha_solicitud;
    public $lineas;

    public function __construct($id = null, $id_usuario = null, $monto_total_ahorrar = null, $quincena = null, $mes = null, $fecha_solicitud = null, $lineas = []) {
        $this->id = $id;
        $this->id_usuario = $id_usuario;
        $this->monto_total_ahorrar = $monto_total_ahorrar;
        $this->quincena = $quincena;
        $this->mes = $mes;
        $this->fecha_solicitud = $fecha_solicitud;
        $this->lineas = $lineas;
    }

    public function guardar() {
        $db = getDB();
        if ($this->id === null) {
            $query = $db->prepare("INSERT INTO solicitudes_ahorro (id_usuario, monto_total_ahorrar, quincena, mes, fecha_solicitud) VALUES (?, ?, ?, ?, CURRENT_TIMESTAMP)");
            $query->bind_param("idss", $this->id_usuario, $this->monto_total_ahorrar, $this->quincena, $this->mes);
            $query->execute();
            $this->id = $query->insert_id;
            $query->close();
        } else {
            $query = $db->prepare("UPDATE solicitudes_ahorro SET monto_total_ahorrar = ?, quincena = ?, mes = ? WHERE id = ?");
            $query->bind_param("dssi", $this->monto_total_ahorrar, $this->quincena, $this->mes, $this->id);
            $query->execute();
            $query->close();
        }

        // Guardar las líneas de ahorro asociadas
        foreach ($this->lineas as $linea) {
            $linea->id_solicitud_ahorro = $this->id;
            $linea->guardar();
        }

        $db->close();
    }

    public static function obtenerPorId($id) {
        $db = getDB();
        $query = $db->prepare("SELECT id, id_usuario, monto_total_ahorrar, quincena, mes, fecha_solicitud FROM solicitudes_ahorro WHERE id = ?");
        $query->bind_param("i", $id);
        $query->execute();
        $query->bind_result($id, $id_usuario, $monto_total_ahorrar, $quincena, $mes, $fecha_solicitud);
        $solicitud = null;
        if ($query->fetch()) {
            $lineas = SolicitudAhorroLinea::obtenerPorSolicitudId($id);
            $solicitud = new SolicitudAhorro($id, $id_usuario, $monto_total_ahorrar, $quincena, $mes, $fecha_solicitud, $lineas);
        }
        $query->close();
        $db->close();
        return $solicitud;
    }

    public static function obtenerTodos() {
        $db = getDB();
        $query = "SELECT id, id_usuario, monto_total_ahorrar, quincena, mes, fecha_solicitud FROM solicitudes_ahorro";
        $result = $db->query($query);
        $solicitudes = [];
        while ($row = $result->fetch_assoc()) {
            $lineas = SolicitudAhorroLinea::obtenerPorSolicitudId($row['id']);
            $solicitudes[] = new SolicitudAhorro($row['id'], $row['id_usuario'], $row['monto_total_ahorrar'], $row['quincena'], $row['mes'], $row['fecha_solicitud'], $lineas);
        }
        $db->close();
        return $solicitudes;
    }

    public function eliminar() {
        $db = getDB();
        if ($this->id !== null) {
            $query = $db->prepare("DELETE FROM solicitudes_ahorro WHERE id = ?");
            $query->bind_param("i", $this->id);
            $query->execute();
            $query->close();
            foreach ($this->lineas as $linea) {
                $linea->eliminar();
            }
        }
        $db->close();
    }
}
?>