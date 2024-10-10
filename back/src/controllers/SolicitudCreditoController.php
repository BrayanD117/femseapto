<?php

require_once __DIR__ . '/../models/SolicitudCreditoModel.php';

class SolicitudCreditoController {

    /**
     * Crea una nueva solicitud de crédito.
     * @param array $datos Datos de la solicitud de crédito a crear.
     * @return int|null ID de la solicitud de crédito creada.
     */
    public function crear($datos) {
        $solicitud = new SolicitudCredito(
            null, // El id se genera automáticamente al guardar
            $datos['idUsuario'],
            $datos['montoSolicitado'],
            $datos['plazoQuincenal'],
            $datos['valorCuotaQuincenal'],
            $datos['idLineaCredito'],
            null,//$datos['reestructurado'],
            null,//$datos['periocidadPago'],
            $datos['tasaInteres'],
            $datos['rutaDocumento'] ?? null,
            null
        );

        $solicitud->guardar();
        
        return $solicitud->id;
    }

    /**
     * Actualiza una solicitud de crédito existente.
     * @param int $id ID de la solicitud de crédito a actualizar.
     * @param array $datos Nuevos datos de la solicitud de crédito.
     * @return bool True si la actualización fue exitosa, false si falló o no se encontró la solicitud de crédito.
     */
    public function actualizar($id, $datos) {
        $solicitud = SolicitudCredito::obtenerPorId($id);
        if (!$solicitud) {
            return false;
        }

        $solicitud->montoSolicitado = $datos['montoSolicitado'];
        $solicitud->plazoQuincenal = $datos['plazoQuincenal'];
        $solicitud->valorCuotaQuincenal = $datos['valorCuotaQuincenal'];
        $solicitud->idLineaCredito = $datos['idLineaCredito'];
        $solicitud->reestructurado = $datos['reestructurado'] ?? null;
        $solicitud->periocidadPago = $datos['periocidadPago'] ?? null;
        $solicitud->tasaInteres = $datos['tasaInteres'];
        $solicitud->rutaDocumento = $datos['rutaDocumento'] ?? null;
        $solicitud->fechaSolicitud = $datos['fechaSolicitud'] ?? null;

        $solicitud->guardar();

        return true;
    }

    /**
     * Obtiene una solicitud de crédito por su ID.
     * @param int $id ID de la solicitud de crédito a obtener.
     * @return SolicitudCredito|array La solicitud de crédito encontrada o un array con un mensaje de error si no se encuentra.
     */
    public function obtenerPorId($id) {
        $solicitud = SolicitudCredito::obtenerPorId($id);
        if ($solicitud) {
            return $solicitud;
        } else {
            http_response_code(404);
            return array("message" => "Solicitud de crédito no encontrada.");
        }
    }

    /**
     * Obtiene las solicitudes de crédito por ID de usuario.
     * @param int $idUsuario ID del usuario.
     * @return SolicitudCredito|array Las solicitudes de crédito encontradas o un array con un mensaje de error si no se encuentra.
     */
    public function obtenerPorIdUsuario($idUsuario) {
        $solicitudes = SolicitudCredito::obtenerPorIdUsuario($idUsuario);
        if ($solicitudes) {
            return $solicitudes;
        } else {
            http_response_code(404);
            return array("message" => "Solicitudes de crédito no encontradas.");
        }
    }

    /**
     * Obtiene todas las solicitudes de crédito disponibles.
     * @return array|array[] Todas las solicitudes de crédito encontradas o un array con un mensaje de error si no se encuentran.
     */
    public function obtenerTodos() {
        $solicitudes = SolicitudCredito::obtenerTodos();
        if ($solicitudes) {
            return $solicitudes;
        } else {
            http_response_code(404);
            return array("message" => "No se encontraron solicitudes de crédito.");
        }
    }

    /**
     * Obtiene las solicitudes de crédito con paginación.
     * @param int $page Número de página.
     * @param int $size Tamaño de la página.
     * @param string $search Término de búsqueda.
     * @return array|array[] Las solicitudes de crédito encontradas o un array con un mensaje de error si no se encuentran.
     */
    public function obtenerConPaginacion($page, $size, $search) {
        return SolicitudCredito::obtenerConPaginacion($page, $size, $search);
    }

    /**
     * Elimina una solicitud de crédito por su ID.
     * @param int $id ID de la solicitud de crédito a eliminar.
     * @return bool True si la eliminación fue exitosa, false si falló o no se encontró la solicitud de crédito.
     */
    public function eliminar($id) {
        $solicitud = SolicitudCredito::obtenerPorId($id);
        if (!$solicitud) {
            return false;
        }

        $solicitud->eliminar();

        return true;
    }
    
    public function obtenerPorRangoDeFechas($startDate, $endDate) {
        return SolicitudCredito::obtenerPorRangoDeFechas($startDate, $endDate);
    }  
}
?>