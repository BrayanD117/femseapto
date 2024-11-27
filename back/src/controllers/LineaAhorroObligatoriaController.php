<?php

require_once __DIR__ . '/../models/LineaAhorroObligatoriaModel.php';

class LineaAhorroObligatoriaController {

    /**
     * Crea una nueva línea de ahorro obligatoria.
     * @param array $datos Datos de la línea de ahorro obligatoria a crear.
     * @return int|null ID de la línea de ahorro obligatoria creada.
     */
    public function crear($datos) {
        $lineaAhorroOblig = new LineaAhorroObligatoria(
            null,
            $datos['nombre']
        );

        $lineaAhorroOblig->guardar();
        
        return $lineaAhorroOblig->id;
    }

    /**
     * Actualiza una línea de ahorro obligatoria existente.
     * @param int $id ID de la línea de ahorro obligatoria a actualizar.
     * @param array $datos Nuevos datos de la línea de ahorro obligatoria.
     * @return bool True si la actualización fue exitosa, false si falló o no se encontró la línea de ahorro obligatoria.
     */
    public function actualizar($id, $datos) {
        $lineaAhorroOblig = LineaAhorroObligatoria::obtenerPorId($id);
        if (!$lineaAhorroOblig) {
            return false;
        }

        $lineaAhorroOblig->nombre = $datos['nombre'];

        $lineaAhorroOblig->guardar();

        return true;
    }

    /**
     * Obtiene una línea de ahorro obligatoria por su ID.
     * @param int $id ID de la línea de ahorro obligatoria a obtener.
     * @return LineaAhorroObligatoria|array La línea de ahorro obligatoria encontrada o un array con un mensaje de error si no se encuentra.
     */
    public function obtenerPorId($id) {
        $lineaAhorroOblig = LineaAhorroObligatoria::obtenerPorId($id);
        if ($lineaAhorroOblig) {
            return $lineaAhorroOblig;
        } else {
            http_response_code(404);
            return array("message" => "Línea de ahorro obligatoria no encontrada.");
        }
    }

    /**
     * Obtiene todas las líneas de ahorro obligatorias disponibles.
     * @return array|array[] Todas las líneas de ahorro obligatorias encontradas o un array con un mensaje de error si no se encuentran.
     */
    public function obtenerTodos() {
        $lineasAhorroOblig = LineaAhorroObligatoria::obtenerTodos();
        if ($lineasAhorroOblig) {
            return $lineasAhorroOblig;
        } else {
            http_response_code(404);
            return array("message" => "No se encontraron líneas de ahorro.");
        }
    }

    /**
     * Elimina una línea de ahorro obligatoria por su ID.
     * @param int $id ID de la línea de ahorro obligatoria a eliminar.
     * @return bool True si la eliminación fue exitosa, false si falló o no se encontró la línea de ahorro obligatoria.
     */
    public function eliminar($id) {
        $lineaAhorroOblig = LineaAhorroObligatoria::obtenerPorId($id);
        if (!$lineaAhorroOblig) {
            return false;
        }

        $lineaAhorroOblig->eliminar();

        return true;
    }
}
?>