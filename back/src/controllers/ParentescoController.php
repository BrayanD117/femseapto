<?php

require_once '../models/ParentescoModel.php';

class ParentescoController {

    /**
     * Crea un nuevo parentesco.
     * @param array $datos Datos del parentesco a crear.
     * @return int|null ID del parentesco creado.
     */
    public function crear($datos) {
        $parentesco = new Parentesco(
            null, // El id se genera automáticamente al guardar
            $datos['nombre']
        );

        $parentesco->guardar();
        
        return $parentesco->id;
    }

    /**
     * Actualiza un parentesco existente.
     * @param int $id ID del parentesco a actualizar.
     * @param array $datos Nuevos datos del parentesco.
     * @return bool True si la actualización fue exitosa, false si falló o no se encontró el parentesco.
     */
    public function actualizar($id, $datos) {
        $parentesco = Parentesco::obtenerPorId($id);
        if (!$parentesco) {
            return false;
        }

        $parentesco->nombre = $datos['nombre'];

        $parentesco->guardar();

        return true;
    }

    /**
     * Obtiene un parentesco por su ID.
     * @param int $id ID del parentesco a obtener.
     * @return Parentesco|array El parentesco encontrado o un array con un mensaje de error si no se encuentra.
     */
    public function obtenerPorId($id) {
        $parentesco = Parentesco::obtenerPorId($id);
        if ($parentesco) {
            return $parentesco;
        } else {
            http_response_code(404);
            return array("message" => "Parentesco no encontrado.");
        }
    }

    /**
     * Obtiene todos los parentescos disponibles.
     * @return array|array[] Todos los parentescos encontrados o un array con un mensaje de error si no se encuentran.
     */
    public function obtenerTodos() {
        $parentescos = Parentesco::obtenerTodos();
        if ($parentescos) {
            return $parentescos;
        } else {
            http_response_code(404);
            return array("message" => "No se encontraron parentescos.");
        }
    }

    /**
     * Elimina un parentesco por su ID.
     * @param int $id ID del parentesco a eliminar.
     * @return bool True si la eliminación fue exitosa, false si falló o no se encontró el parentesco.
     */
    public function eliminar($id) {
        $parentesco = Parentesco::obtenerPorId($id);
        if (!$parentesco) {
            return false;
        }

        $parentesco->eliminar();

        return true;
    }
}
?>