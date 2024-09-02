<?php
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/SolicitudAhorroLineaModel.php';

class SolicitudAhorro {
    public $id;
    public $idUsuario;
    public $montoTotalAhorrar;
    public $quincena;
    public $mes;
    public $fechaSolicitud;
    public $lineas;

    public function __construct($id = null, $idUsuario = null, $montoTotalAhorrar = null, $quincena = null, $mes = null, $fechaSolicitud = null, $lineas = []) {
        $this->id = $id;
        $this->idUsuario = $idUsuario;
        $this->montoTotalAhorrar = $montoTotalAhorrar;
        $this->quincena = $quincena;
        $this->mes = $mes;
        $this->fechaSolicitud = $fechaSolicitud;
        $this->lineas = $lineas;
    }

    public function guardar() {
        $db = getDB();
        if ($this->id === null) {
            $query = $db->prepare("INSERT INTO solicitudes_ahorro (id_usuario, monto_total_ahorrar, quincena, mes, fecha_solicitud) VALUES (?, ?, ?, ?, CURRENT_TIMESTAMP)");
            $query->bind_param("idss", $this->idUsuario, $this->montoTotalAhorrar, $this->quincena, $this->mes);
            $query->execute();
            $this->id = $query->insert_id;
            $query->close();
        } else {
            $query = $db->prepare("UPDATE solicitudes_ahorro SET monto_total_ahorrar = ?, quincena = ?, mes = ? WHERE id = ?");
            $query->bind_param("dssi", $this->montoTotalAhorrar, $this->quincena, $this->mes, $this->id);
            $query->execute();
            $query->close();
        }

        // Guardar las líneas de ahorro asociadas
        foreach ($this->lineas as $linea) {
            $linea->idSolicitudAhorro = $this->id;
            $linea->guardar();
        }

        $db->close();
    }

    public static function obtenerPorId($id) {
        $db = getDB();
        $query = $db->prepare("SELECT id, id_usuario, monto_total_ahorrar, quincena, mes, fecha_solicitud FROM solicitudes_ahorro WHERE id = ?");
        $query->bind_param("i", $id);
        $query->execute();
        $query->bind_result($id, $idUsuario, $montoTotalAhorrar, $quincena, $mes, $fechaSolicitud);
        $solicitud = null;
        if ($query->fetch()) {
            $lineas = SolicitudAhorroLinea::obtenerPorSolicitudId($id);
            $solicitud = new SolicitudAhorro($id, $idUsuario, $montoTotalAhorrar, $quincena, $mes, $fechaSolicitud, $lineas);
        }
        $query->close();
        $db->close();
        return $solicitud;
    }

    public static function obtenerPorIdUsuario($idUsuario) {
        $db = getDB();
        $query = $db->prepare("SELECT id, id_usuario, monto_total_ahorrar, quincena, mes, DATE_FORMAT(fecha_solicitud, '%d/%m/%Y') as fecha_solicitud FROM solicitudes_ahorro WHERE id_usuario = ? ORDER BY fecha_solicitud DESC");
        $query->bind_param("i", $idUsuario);
        $query->execute();
        $query->bind_result($id, $idUsuario, $montoTotalAhorrar, $quincena, $mes, $fechaSolicitud);
        
        $solAhorros = [];

        while ($query->fetch()) {
            $lineas = SolicitudAhorroLinea::obtenerPorSolicitudId($id);
            $solAhorros[] = new SolicitudAhorro($id, $idUsuario, $montoTotalAhorrar, $quincena, $mes, $fechaSolicitud, $lineas);
        }
        
        $query->close();
        $db->close();
        
        return $solAhorros;
    }

    public static function obtenerTodos() {
        $db = getDB();
        $query = "SELECT id, id_usuario, monto_total_ahorrar, quincena, mes, fecha_solicitud FROM solicitudes_ahorro";
        $result = $db->query($query);
        $solicitudes = [];
        while ($row = $result->fetch_assoc()) {
            $id = $row['id'];
            $lineas = SolicitudAhorroLinea::obtenerPorSolicitudId($id);
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

    public static function obtenerConPaginacion($page, $size, $search) {
        $db = getDB();
        $offset = ($page - 1) * $size;
        $searchQuery = !empty($search) ? "WHERE nombre LIKE '%$search%' OR estado LIKE '%$search%'" : "";
        $query = "SELECT * FROM solicitudes_ahorro $searchQuery ORDER BY fecha_solicitud DESC LIMIT ? OFFSET ?";
        $stmt = $db->prepare($query);
        $stmt->bind_param("ii", $size, $offset);
        $stmt->execute();
        $result = $stmt->get_result();
        $solicitudes = [];
        while ($row = $result->fetch_assoc()) {
            $id = $row['id'];
            $lineas = SolicitudAhorroLinea::obtenerPorSolicitudId($id);
            $solicitudes[] = new SolicitudAhorro($row['id'], $row['id_usuario'], $row['monto_total_ahorrar'], $row['quincena'], $row['mes'], $row['fecha_solicitud'], $lineas);
        }
    
        $countQuery = "SELECT COUNT(*) as total FROM solicitudes_ahorro $searchQuery";
        $countResult = $db->query($countQuery);
        $total = $countResult->fetch_assoc()['total'];
    
        $db->close();
        return [
            'data' => $solicitudes,
            'total' => $total
        ];
    }
    
}
?>