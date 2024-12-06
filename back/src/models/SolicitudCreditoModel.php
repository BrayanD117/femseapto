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

    public static function obtenerConPaginacion($page, $size, $search = null, $fechaSolicitud = null) {
        $db = getDB();
        $offset = ($page - 1) * $size;
    
        $baseQuery = "
            FROM solicitudes_credito sc
            INNER JOIN usuarios u ON sc.id_usuario = u.id
            INNER JOIN lineas_credito lc ON sc.id_linea_credito = lc.id
        ";
    
        $selectQuery = "
            SELECT 
                sc.id,
                sc.id_usuario,
                sc.monto_solicitado,
                sc.plazo_quincenal,
                sc.valor_cuota_quincenal,
                sc.id_linea_credito,
                sc.reestructurado,
                sc.periocidad_pago,
                sc.tasa_interes,
                sc.ruta_documento,
                CONVERT_TZ(sc.fecha_solicitud, '+00:00', '-05:00') AS fecha_solicitud,
                u.primer_nombre,
                u.segundo_nombre,
                u.primer_apellido,
                u.segundo_apellido,
                u.numero_documento,
                lc.nombre
        ";
    
        $countQuery = "SELECT COUNT(*) AS total";
    
        $whereConditions = [];
        $params = [];
        $types = "";
    
        if (!empty($search)) {
            $whereConditions[] = "(
                sc.monto_solicitado LIKE ?
                OR sc.plazo_quincenal LIKE ?
                OR u.numero_documento LIKE ?
                OR lc.nombre LIKE ?
            )";
            $searchParam = "%" . $search . "%";
            $params = array_merge($params, [$searchParam, $searchParam, $searchParam, $searchParam]);
            $types .= str_repeat("s", 4);
        }
    
        if (!empty($fechaSolicitud)) {
            $fechaConvertida = DateTime::createFromFormat('d/m/Y', $fechaSolicitud);
            if ($fechaConvertida === false) {
                die('Formato de fecha inválido, debe ser DD/MM/YYYY.');
            }
            $fechaSQL = $fechaConvertida->format('Y-m-d');
    
            $whereConditions[] = "DATE(CONVERT_TZ(sc.fecha_solicitud, '+00:00', '-05:00')) = ?";
            $params[] = $fechaSQL;
            $types .= "s";
        }
    
        $whereClause = count($whereConditions) > 0 ? " WHERE " . implode(" AND ", $whereConditions) : "";
    
        $finalSelectQuery = $selectQuery . $baseQuery . $whereClause . " ORDER BY sc.fecha_solicitud DESC LIMIT ? OFFSET ?";
        $finalCountQuery = $countQuery . $baseQuery . $whereClause;
    
        $params[] = $size;
        $params[] = $offset;
        $types .= "ii";
    
        $stmt = $db->prepare($finalSelectQuery);
        if ($stmt === false) {
            die('Error en la preparación de la consulta: ' . $db->error);
        }
        if (!empty($params)) {
            $stmt->bind_param($types, ...$params);
        }
    
        $stmt->execute();
        if ($stmt->errno) {
            die('Error en la ejecución de la consulta: ' . $stmt->error);
        }
    
        $result = $stmt->get_result();
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
    
        $countStmt = $db->prepare($finalCountQuery);
        if ($countStmt === false) {
            die('Error en la preparación de la consulta de conteo: ' . $db->error);
        }
    
        if (!empty($countParams = array_slice($params, 0, count($params) - 2))) {
            $countTypes = substr($types, 0, -2);
            $countStmt->bind_param($countTypes, ...$countParams);
        }
    
        $countStmt->execute();
        if ($countStmt->errno) {
            die('Error en la ejecución de la consulta de conteo: ' . $countStmt->error);
        }
    
        $countResult = $countStmt->get_result();
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