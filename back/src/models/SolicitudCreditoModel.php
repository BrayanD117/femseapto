<?php
require_once __DIR__ . '/../../config/config.php';

class SolicitudCredito {
    public $id;
    public $idUsuario;
    public $montoSolicitado;
    public $plazoQuincenal;
    public $valorCuotaQuincenal;
    public $idLineaCredito;
    public $reestructurado;
    public $periocidadPago;
    public $tasaInteres;
    public $rutaDocumento;
    public $fechaSolicitud;

    public function __construct($id = null, $idUsuario = null, $montoSolicitado = null, $plazoQuincenal = null, $valorCuotaQuincenal = null, $idLineaCredito = null, $reestructurado = null, $periocidadPago = null, $tasaInteres = null, $rutaDocumento = null, $fechaSolicitud = null) {
        $this->id = $id;
        $this->idUsuario = $idUsuario;
        $this->montoSolicitado = $montoSolicitado;
        $this->plazoQuincenal = $plazoQuincenal;
        $this->valorCuotaQuincenal = $valorCuotaQuincenal;
        $this->idLineaCredito = $idLineaCredito;
        $this->reestructurado = $reestructurado;
        $this->periocidadPago = $periocidadPago;
        $this->tasaInteres = $tasaInteres;
        $this->rutaDocumento = $rutaDocumento;
        $this->fechaSolicitud = $fechaSolicitud;
    }

    public function guardar() {
        $db = getDB();
        if ($this->id === null) {
            $query = $db->prepare("INSERT INTO solicitudes_credito (id_usuario, monto_solicitado, plazo_quincenal, valor_cuota_quincenal, id_linea_credito, reestructurado, periocidad_pago, tasa_interes, ruta_documento) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $query->bind_param("ididissss", $this->idUsuario, $this->montoSolicitado, $this->plazoQuincenal, $this->valorCuotaQuincenal, $this->idLineaCredito, $this->reestructurado, $this->periocidadPago, $this->tasaInteres, $this->rutaDocumento);
        } else {
            $query = $db->prepare("UPDATE solicitudes_credito SET monto_solicitado = ?, plazo_quincenal = ?, valor_cuota_quincenal = ?, id_linea_credito = ?, reestructurado = ?, periocidad_pago = ?, tasa_interes = ?, ruta_documento = ? WHERE id = ?");
            $query->bind_param("didissssi", $this->montoSolicitado, $this->plazoQuincenal, $this->valorCuotaQuincenal, $this->idLineaCredito, $this->reestructurado, $this->periocidadPago, $this->tasaInteres, $this->rutaDocumento, $this->id);
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
        $query = $db->prepare(
            "SELECT
                id,
                id_usuario,
                monto_solicitado,
                plazo_quincenal,
                valor_cuota_quincenal,
                id_linea_credito,
                reestructurado,
                periocidad_pago,
                tasa_interes,
                ruta_documento,
                CONVERT_TZ(fecha_solicitud, '+00:00', '-05:00') AS fecha_solicitud 
            FROM solicitudes_credito
            WHERE id = ?");
        $query->bind_param("i", $id);
        $query->execute();
        $query->bind_result($id, $idUsuario, $montoSolicitado, $plazoQuincenal, $valorCuotaQuincenal, $idLineaCredito, $reestructurado, $periocidadPago, $tasaInteres, $rutaDocumento, $fechaSolicitud);
        $solicitud = null;
        if ($query->fetch()) {
            $solicitud = new SolicitudCredito($id, $idUsuario, $montoSolicitado, $plazoQuincenal, $valorCuotaQuincenal, $idLineaCredito, $reestructurado, $periocidadPago, $tasaInteres, $rutaDocumento, $fechaSolicitud);
        }
        $query->close();
        $db->close();
        return $solicitud;
    }

    public static function obtenerPorIdUsuario($idUsuario) {
        $db = getDB();
        $query = $db->prepare(
            "SELECT
                id,
                id_usuario,
                monto_solicitado,
                plazo_quincenal,
                valor_cuota_quincenal,
                id_linea_credito,
                reestructurado,
                periocidad_pago,
                tasa_interes,
                ruta_documento,
                CONVERT_TZ(fecha_solicitud, '+00:00', '-05:00') AS fecha_solicitud 
            FROM solicitudes_credito
            WHERE id_usuario = ?");
        $query->bind_param("i", $idUsuario);
        $query->execute();
        $query->bind_result($id, $idUsuario, $montoSolicitado, $plazoQuincenal, $valorCuotaQuincenal, $idLineaCredito, $reestructurado, $periocidadPago, $tasaInteres, $rutaDocumento, $fechaSolicitud);
        
        $solCred = [];

        while ($query->fetch()) {
            $solCred[] = new SolicitudCredito($id, $idUsuario, $montoSolicitado, $plazoQuincenal, $valorCuotaQuincenal, $idLineaCredito, $reestructurado, $periocidadPago, $tasaInteres, $rutaDocumento, $fechaSolicitud);
        }
        
        $query->close();
        $db->close();
        
        return $solCred;
    }

    public static function obtenerTodos() {
        $db = getDB();
        $query = "SELECT
                    id,
                    id_usuario,
                    monto_solicitado,
                    plazo_quincenal,
                    valor_cuota_quincenal,
                    id_linea_credito,
                    reestructurado,
                    periocidad_pago,
                    tasa_interes,
                    ruta_documento,
                    CONVERT_TZ(fecha_solicitud, '+00:00', '-05:00') AS fecha_solicitud
                FROM solicitudes_credito";
        $result = $db->query($query);
        $solicitudes = [];
        while ($row = $result->fetch_assoc()) {
            $solicitudes[] = new SolicitudCredito($row['id'], $row['id_usuario'], $row['monto_solicitado'], $row['plazo_quincenal'], $row['valor_cuota_quincenal'], $row['id_linea_credito'], $row['reestructurado'], $row['periocidad_pago'], $row['tasa_interes'], $row['ruta_documento'], $row['fecha_solicitud']);
        }
        $db->close();
        return $solicitudes;
    }

    public static function obtenerConPaginacion($page, $size, $search) {
        $db = getDB();
        $offset = ($page - 1) * $size;
        $searchQuery = !empty($search) ? "WHERE nombre LIKE '%$search%' OR estado LIKE '%$search%'" : "";
        $query = "SELECT
                    id,
                    id_usuario,
                    monto_solicitado,
                    plazo_quincenal,
                    valor_cuota_quincenal,
                    id_linea_credito,
                    reestructurado,
                    periocidad_pago,
                    tasa_interes,
                    ruta_documento,
                    CONVERT_TZ(fecha_solicitud, '+00:00', '-05:00') AS fecha_solicitud 
                FROM solicitudes_credito
                $searchQuery
                ORDER BY fecha_solicitud
                DESC
                LIMIT ?
                OFFSET ?";
        $stmt = $db->prepare($query);
        $stmt->bind_param("ii", $size, $offset);
        $stmt->execute();
        $result = $stmt->get_result();
        $solicitudes = [];
        while ($row = $result->fetch_assoc()) {
            $solicitudes[] = new SolicitudCredito($row['id'], $row['id_usuario'], $row['monto_solicitado'], $row['plazo_quincenal'], $row['valor_cuota_quincenal'], $row['id_linea_credito'], $row['reestructurado'], $row['periocidad_pago'], $row['tasa_interes'], $row['ruta_documento'], $row['fecha_solicitud']);
        }
        
        $countQuery = "SELECT COUNT(*) as total FROM solicitudes_credito $searchQuery";
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
            $query = $db->prepare("DELETE FROM solicitudes_credito WHERE id = ?");
            $query->bind_param("i", $this->id);
            $query->execute();
            $query->close();
        }
        $db->close();
    }

    public static function obtenerPorRangoDeFechas($startDate, $endDate) {
        $db = getDB();
        
        $startDateTime = $startDate . ' 00:00:00';
        $endDateTime = $endDate . ' 23:59:59';
        
        $query = $db->prepare(
            "SELECT
                id,
                id_usuario,
                monto_solicitado,
                plazo_quincenal,
                valor_cuota_quincenal,
                id_linea_credito,
                reestructurado,
                periocidad_pago,
                tasa_interes,
                ruta_documento,
                CONVERT_TZ(fecha_solicitud, '+00:00', '-05:00') AS fecha_solicitud
            FROM solicitudes_credito
            WHERE fecha_solicitud
            BETWEEN ?
            AND ?");
        $query->bind_param("ss", $startDateTime, $endDateTime);
        $query->execute();
        $result = $query->get_result();
    
        $solicitudes = [];
        while ($row = $result->fetch_assoc()) {
            $solicitudes[] = new SolicitudCredito(
                $row['id'],
                $row['id_usuario'],
                $row['monto_solicitado'],
                $row['plazo_quincenal'],
                $row['valor_cuota_quincenal'],
                $row['id_linea_credito'],
                $row['reestructurado'],
                $row['periocidad_pago'],
                $row['tasa_interes'],
                $row['ruta_documento'],
                $row['fecha_solicitud']
            );
        }
        error_log("Número de solicitudes encontradas: " . count($solicitudes));
    
        $query->close();
        $db->close();
        return $solicitudes;
    }     
}
?>