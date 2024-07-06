<?php

require_once __DIR__ . '/../models/LineaAhorroModel.php';

class LineaAhorroController {

    /**
     * Crea una nueva línea de ahorro.
     * @param array $datos Datos de la línea de ahorro a crear.
     * @return int|null ID de la línea de ahorro creada.
     */
    public function crear($datos) {
        $lineaAhorro = new LineaAhorro(
            null, // El id se genera automáticamente al guardar
            $datos['nombre']
        );

        $lineaAhorro->guardar();
        
        return $lineaAhorro->id;
    }

    /**
     * Actualiza una línea de ahorro existente.
     * @param int $id ID de la línea de ahorro a actualizar.
     * @param array $datos Nuevos datos de la línea de ahorro.
     * @return bool True si la actualización fue exitosa, false si falló o no se encontró la línea de ahorro.
     */
    public function actualizar($id, $datos) {
        $lineaAhorro = LineaAhorro::obtenerPorId($id);
        if (!$lineaAhorro) {
            return false;
        }

        $lineaAhorro->nombre = $datos['nombre'];

        $lineaAhorro->guardar();

        return true;
    }

    /**
     * Obtiene una línea de ahorro por su ID.
     * @param int $id ID de la línea de ahorro a obtener.
     * @return LineaAhorro|array La línea de ahorro encontrada o un array con un mensaje de error si no se encuentra.
     */
    public function obtenerPorId($id) {
        $lineaAhorro = LineaAhorro::obtenerPorId($id);
        if ($lineaAhorro) {
            return $lineaAhorro;
        } else {
            http_response_code(404);
            return array("message" => "Línea de ahorro no encontrada.");
        }
    }

    /**
     * Obtiene todas las líneas de ahorro disponibles.
     * @return array|array[] Todas las líneas de ahorro encontradas o un array con un mensaje de error si no se encuentran.
     */
    public function obtenerTodos() {
        $lineasAhorro = LineaAhorro::obtenerTodos();
        if ($lineasAhorro) {
            return $lineasAhorro;
        } else {
            http_response_code(404);
            return array("message" => "No se encontraron líneas de ahorro.");
        }
    }

    /**
     * Elimina una línea de ahorro por su ID.
     * @param int $id ID de la línea de ahorro a eliminar.
     * @return bool True si la eliminación fue exitosa, false si falló o no se encontró la línea de ahorro.
     */
    public function eliminar($id) {
        $lineaAhorro = LineaAhorro::obtenerPorId($id);
        if (!$lineaAhorro) {
            return false;
        }

        $lineaAhorro->eliminar();

        return true;
    }
}
?>