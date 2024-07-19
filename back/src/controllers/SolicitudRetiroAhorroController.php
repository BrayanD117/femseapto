<?php

require_once __DIR__ . '/../models/SolicitudRetiroAhorroModel.php';

class SolicitudRetiroAhorroController {

    /**
     * Crea una nueva solicitud de retiro de ahorro.
     * @param array $datos Datos de la solicitud de retiro de ahorro a crear.
     * @return int|null ID de la solicitud de retiro de ahorro creada.
     */
    public function crear($datos) {
        $solicitud = new SolicitudRetiroAhorro(
            null, // El id se genera automáticamente al guardar
            $datos['idUsuario'],
            $datos['idLineaAhorro'],
            $datos['montoRetirar'],
            $datos['banco'] ?? null,
            $datos['numeroCuenta'] ?? null,
            $datos['devolucionCaja'],
            $datos['observaciones'],
            $datos['continuarAhorro'],
            null
        );

        $solicitud->guardar();
        
        return $solicitud->id;
    }

    /**
     * Actualiza una solicitud de retiro de ahorro existente.
     * @param int $id ID de la solicitud de retiro de ahorro a actualizar.
     * @param array $datos Nuevos datos de la solicitud de retiro de ahorro.
     * @return bool True si la actualización fue exitosa, false si falló o no se encontró la solicitud de retiro de ahorro.
     */
    public function actualizar($id, $datos) {
        $solicitud = SolicitudRetiroAhorro::obtenerPorId($id);
        if (!$solicitud) {
            return false;
        }

        $solicitud->idLineaAhorro = $datos['idLineaAhorro'];
        $solicitud->montoRetirar = $datos['montoRetirar'];
        $solicitud->banco = $datos['banco'] ?? null;
        $solicitud->numeroCuenta = $datos['numeroCuenta'] ?? null;
        $solicitud->devolucionCaja = $datos['devolucionCaja'];
        $solicitud->observaciones = $datos['observaciones'];
        $solicitud->continuarAhorro = $datos['continuarAhorro'];

        $solicitud->guardar();

        return true;
    }

    /**
     * Obtiene una solicitud de retiro de ahorro por su ID.
     * @param int $id ID de la solicitud de retiro de ahorro a obtener.
     * @return SolicitudCredito|array La solicitud de retiro de ahorro encontrada o un array con un mensaje de error si no se encuentra.
     */
    public function obtenerPorId($id) {
        $solicitud = SolicitudRetiroAhorro::obtenerPorId($id);
        if ($solicitud) {
            return $solicitud;
        } else {
            http_response_code(404);
            return array("message" => "Solicitud de retiro de ahorro no encontrada.");
        }
    }

    /**
     * Obtiene las solicitudes de retiro de ahorro por ID de usuario.
     * @param int $idUsuario ID del usuario.
     * @return SolicitudCredito|array Las solicitudes de retiro de ahorro encontradas o un array con un mensaje de error si no se encuentra.
     */
    public function obtenerPorIdUsuario($idUsuario) {
        $solicitudes = SolicitudRetiroAhorro::obtenerPorIdUsuario($idUsuario);
        if ($solicitudes) {
            return $solicitudes;
        } else {
            http_response_code(404);
            return array("message" => "Solicitudes de retiro de ahorro no encontradas.");
        }
    }

    /**
     * Obtiene todas las solicitudes de retiro de ahorro disponibles.
     * @return array|array[] Todas las solicitudes de retiro de ahorro encontradas o un array con un mensaje de error si no se encuentran.
     */
    public function obtenerTodos() {
        $solicitudes = SolicitudRetiroAhorro::obtenerTodos();
        if ($solicitudes) {
            return $solicitudes;
        } else {
            http_response_code(404);
            return array("message" => "No se encontraron solicitudes de retiro de ahorro.");
        }
    }

    /**
     * Obtiene las solicitudes de retiro de ahorro con paginación.
     * @param int $page Número de página.
     * @param int $size Tamaño de la página.
     * @param string $search Término de búsqueda.
     * @return array|array[] Las solicitudes de retiro de ahorro encontradas o un array con un mensaje de error si no se encuentran.
     */
    public function obtenerConPaginacion($page, $size, $search) {
        return SolicitudRetiroAhorro::obtenerConPaginacion($page, $size, $search);
    }

    /**
     * Elimina una solicitud de retiro de ahorro por su ID.
     * @param int $id ID de la solicitud de retiro de ahorro a eliminar.
     * @return bool True si la eliminación fue exitosa, false si falló o no se encontró la solicitud de retiro de ahorro.
     */
    public function eliminar($id) {
        $solicitud = SolicitudRetiroAhorro::obtenerPorId($id);
        if (!$solicitud) {
            return false;
        }

        $solicitud->eliminar();

        return true;
    }
}
?>