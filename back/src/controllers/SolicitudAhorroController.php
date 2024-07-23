<?php

require_once __DIR__ . '/../models/SolicitudAhorroModel.php';

class SolicitudAhorroController {

    /**
     * Crea una nueva solicitud de ahorro.
     * @param array $datos Datos de la solicitud de ahorro a crear.
     * @return int|null ID de la solicitud de ahorro creada.
     */
    public function crear($datos) {
        $solicitud = new SolicitudAhorro(
            null, // El id se genera automáticamente al guardar
            $datos['idUsuario'],
            $datos['montoTotalAhorrar'],
            $datos['quincena'],
            $datos['mes'],
            null,
            []
        );

        // Guardar las líneas de ahorro si están presentes en los datos
        if (isset($datos['lineas'])) {
            foreach ($datos['lineas'] as $lineaData) {
                $linea = new SolicitudAhorroLinea(
                    null,
                    $solicitud->id,
                    $lineaData['idLineaAhorro'],
                    $lineaData['montoAhorrar']
                );
                $solicitud->lineas[] = $linea;
            }
        }

        $solicitud->guardar();
        
        return $solicitud->id;
    }

    /**
     * Actualiza una solicitud de ahorro existente.
     * @param int $id ID de la solicitud de ahorro a actualizar.
     * @param array $datos Nuevos datos de la solicitud de ahorro.
     * @return bool True si la actualización fue exitosa, false si falló o no se encontró la solicitud de ahorro.
     */
    public function actualizar($id, $datos) {
        $solicitud = SolicitudAhorro::obtenerPorId($id);
        if (!$solicitud) {
            return false;
        }

        $solicitud->montoTotalAhorrar = $datos['montoTotalAhorrar'];
        $solicitud->quincena = $datos['quincena'];
        $solicitud->mes = $datos['mes'];

        // Actualizar las líneas de ahorro asociadas si están presentes en los datos
        if (isset($datos['lineas'])) {
            // Eliminar las líneas existentes antes de actualizar
            foreach ($solicitud->lineas as $linea) {
                $linea->eliminar();
            }
            $solicitud->lineas = [];

            // Guardar las nuevas líneas de ahorro
            foreach ($datos['lineas'] as $lineaData) {
                $linea = new SolicitudAhorroLinea(
                    null,
                    $solicitud->id,
                    $lineaData['idLineaAhorro'],
                    $lineaData['montoAhorrar']
                );
                $solicitud->lineas[] = $linea;
            }
        }

        $solicitud->guardar();

        return true;
    }

    /**
     * Obtiene una solicitud de ahorro por su ID.
     * @param int $id ID de la solicitud de ahorro a obtener.
     * @return SolicitudAhorro|array La solicitud de ahorro encontrada o un array con un mensaje de error si no se encuentra.
     */
    public function obtenerPorId($id) {
        $solicitud = SolicitudAhorro::obtenerPorId($id);
        if ($solicitud) {
            return $solicitud;
        } else {
            http_response_code(404);
            return array("message" => "Solicitud de ahorro no encontrada.");
        }
    }

    /**
     * Obtiene las solicitud de ahorro por ID de usuario.
     * @param int $idUsuario ID del usuario a obtener.
     * @return SolicitudAhorro|array Las solicitudes de ahorro encontradas o un array con un mensaje de error si no se encuentra.
     */
    public function obtenerPorIdUsuario($idUsuario) {
        $solicitud = SolicitudAhorro::obtenerPorIdUsuario($idUsuario);
        if ($solicitud) {
            return $solicitud;
        } else {
            http_response_code(404);
            return array("message" => "Solicitudes de ahorro no encontradas.");
        }
    }

    /**
     * Obtiene todas las solicitudes de ahorro disponibles.
     * @return array|array[] Todas las solicitudes de ahorro encontradas o un array con un mensaje de error si no se encuentran.
     */
    public function obtenerTodos() {
        $solicitudes = SolicitudAhorro::obtenerTodos();
        if ($solicitudes) {
            return $solicitudes;
        } else {
            http_response_code(404);
            return array("message" => "No se encontraron solicitudes de ahorro.");
        }
    }

    public function obtenerConPaginacion($page, $size, $search) {
        return SolicitudAhorro::obtenerConPaginacion($page, $size, $search);
    }    

    /**
     * Elimina una solicitud de ahorro por su ID.
     * @param int $id ID de la solicitud de ahorro a eliminar.
     * @return bool True si la eliminación fue exitosa, false si falló o no se encontró la solicitud de ahorro.
     */
    public function eliminar($id) {
        $solicitud = SolicitudAhorro::obtenerPorId($id);
        if (!$solicitud) {
            return false;
        }

        $solicitud->eliminar();

        return true;
    }
}
?>