<?php
require_once __DIR__ . '/../../config/config.php';

class SolicitudRetiroAhorro {
    public $id;
    public $idUsuario;
    public $idLineaAhorro;
    public $montoRetirar;
    public $banco;
    public $numeroCuenta;
    public $devolucionCaja;
    public $observaciones;
    public $continuarAhorro;
    public $fechaSolicitud;

    public function __construct($id = null, $idUsuario = null, $idLineaAhorro = null,
        $montoRetirar = null, $banco = null, $numeroCuenta = null, $devolucionCaja = null,
        $observaciones = null, $continuarAhorro = null, $fechaSolicitud = null) {
        $this->id = $id;
        $this->idUsuario = $idUsuario;
        $this->idLineaAhorro = $idLineaAhorro;
        $this->montoRetirar = $montoRetirar;
        $this->banco = $banco;
        $this->numeroCuenta = $numeroCuenta;
        $this->devolucionCaja = $devolucionCaja;
        $this->observaciones = $observaciones;
        $this->continuarAhorro = $continuarAhorro;
        $this->fechaSolicitud = $fechaSolicitud;
    }

    public function guardar() {
        $db = getDB();
        if ($this->id === null) {
            $query = $db->prepare("INSERT INTO solicitudes_retiro_ahorro (id_usuario, id_linea_ahorro, monto_retirar, banco, numero_cuenta, devolucion_caja, observaciones, continuar_ahorro) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
            $query->bind_param("iiisssss", $this->idUsuario, $this->idLineaAhorro, $this->montoRetirar, $this->banco, $this->numeroCuenta, $this->devolucionCaja, $this->observaciones, $this->continuarAhorro);
        } else {
            $query = $db->prepare("UPDATE solicitudes_retiro_ahorro SET id_linea_ahorro = ?, monto_retirar = ?, banco = ?, numero_cuenta = ?, devolucion_caja = ?, observaciones = ?, continuar_ahorro = ? WHERE id = ?");
            $query->bind_param("iisssssi", $this->idLineaAhorro, $this->montoRetirar, $this->banco, $this->numeroCuenta, $this->devolucionCaja, $this->observaciones, $this->continuarAhorro, $this->id);
        }
        $query->execute();
        if ($query->error) {
            die('Error en la consulta: ' . $query->error);
        }
        if($this->id === null) {
            $this->id = $query->insert_id;
        }
        $query->close();
        $db->close();
    }

    public static function obtenerPorId($id) {
        $db = getDB();
        $query = $db->prepare("SELECT * FROM solicitudes_retiro_ahorro WHERE id = ?");
        $query->bind_param("i", $id);
        $query->execute();
        $query->bind_result($id, $idUsuario, $idLineaAhorro, $montoRetirar, $banco, $numeroCuenta, $devolucionCaja, $observaciones, $continuarAhorro, $fechaSolicitud);
        $solicitud = null;
        if ($query->fetch()) {
            $solicitud = new SolicitudRetiroAhorro($id, $idUsuario, $idLineaAhorro, $montoRetirar, $banco, $numeroCuenta, $devolucionCaja, $observaciones, $continuarAhorro, $fechaSolicitud);
        }
        $query->close();
        $db->close();
        return $solicitud;
    }

    public static function obtenerPorIdUsuario($idUsuario) {
        $db = getDB();
        $query = $db->prepare("SELECT id, id_usuario, id_linea_ahorro, monto_retirar, banco, numero_cuenta, devolucion_caja, observaciones, continuar_ahorro, DATE_FORMAT(fecha_solicitud, '%d/%m/%Y') as fecha_solicitud FROM solicitudes_retiro_ahorro WHERE id_usuario = ? ORDER BY fecha_solicitud DESC");
        $query->bind_param("i", $idUsuario);
        $query->execute();
        $query->bind_result($id, $idUsuario, $idLineaAhorro, $montoRetirar, $banco, $numeroCuenta, $devolucionCaja, $observaciones, $continuarAhorro, $fechaSolicitud);
        
        $solicitudes = [];

        while ($query->fetch()) {
            $solicitudes[] = new SolicitudRetiroAhorro($id, $idUsuario, $idLineaAhorro, $montoRetirar, $banco, $numeroCuenta, $devolucionCaja, $observaciones, $continuarAhorro, $fechaSolicitud);
        }
        
        $query->close();
        $db->close();
        
        return $solicitudes;
    }

    public static function obtenerTodos() {
        $db = getDB();
        $query = "SELECT * FROM solicitudes_retiro_ahorro";
        $result = $db->query($query);
        $solicitudes = [];
        while ($row = $result->fetch_assoc()) {
            $solicitudes[] = new SolicitudRetiroAhorro($row['id'], $row['id_usuario'], $row['id_linea_ahorro'], $row['monto_retirar'], $row['banco'], $row['numero_cuenta'], $row['devolucion_caja'], $row['observaciones'], $row['continuar_ahorro'], $row['fecha_solicitud']);
        }
        $db->close();
        return $solicitudes;
    }

    public static function obtenerConPaginacion($page, $size, $search) {
        $db = getDB();
        $offset = ($page - 1) * $size;
        $searchQuery = !empty($search) ? "WHERE nombre LIKE '%$search%' OR estado LIKE '%$search%'" : "";
        $query = "SELECT * FROM solicitudes_retiro_ahorro $searchQuery  ORDER BY fecha_solicitud DESC LIMIT ? OFFSET ?";
        $stmt = $db->prepare($query);
        $stmt->bind_param("ii", $size, $offset);
        $stmt->execute();
        $result = $stmt->get_result();
        $solicitudes = [];
        while ($row = $result->fetch_assoc()) {
            $solicitudes[] = new SolicitudRetiroAhorro($row['id'], $row['id_usuario'], $row['id_linea_ahorro'], $row['monto_retirar'], $row['banco'], $row['numero_cuenta'], $row['devolucion_caja'], $row['observaciones'], $row['continuar_ahorro'], $row['fecha_solicitud']);
        }
        
        $countQuery = "SELECT COUNT(*) as total FROM solicitudes_retiro_ahorro $searchQuery";
        $countResult = $db->query($countQuery);
        $total = $countResult->fetch_assoc()['total'];

        $db->close();
        return [
            'data' => $solicitudes,
            'total' => $total
        ];
    }

    public function eliminar() {
        $db = getDB();
        if ($this->id !== null) {
            $query = $db->prepare("DELETE FROM solicitudes_retiro_ahorro WHERE id = ?");
            $query->bind_param("i", $this->id);
            $query->execute();
            $query->close();
        }
        $db->close();
    }
}
?>