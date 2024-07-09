<?php

require_once __DIR__ . '/../models/PersonaExpuestaPublicamenteModel.php';

class PersonaExpuestaPublicamenteController {

    public function crear($datos) {
        $persona = new PersonaExpuestaPublicamente(
            null,
            $datos['idUsuario'],
            $datos['poderPublico'],
            $datos['manejaRecPublicos'],
            $datos['reconocimientoPublico'],
            $datos['funcionesPublicas'],
            $datos['actividadPublica'],
            $datos['funcionarioPublicoExtranjero'],
            $datos['famFuncionarioPublico'],
            $datos['socioFuncionarioPublico']
        );

        $persona->guardar();

        return $persona->id;
    }

    public function actualizar($id, $datos) {
        $persona = PersonaExpuestaPublicamente::obtenerPorId($id);
        if (!$persona) {
            return false;
        }

        $persona->poderPublico = $datos['poderPublico'];
        $persona->manejaRecPublicos = $datos['manejaRecPublicos'];
        $persona->reconocimientoPublico = $datos['reconocimientoPublico'];
        $persona->funcionesPublicas = $datos['funcionesPublicas'];
        $persona->actividadPublica = $datos['actividadPublica'];
        $persona->funcionarioPublicoExtranjero = $datos['funcionarioPublicoExtranjero'];
        $persona->famFuncionarioPublico = $datos['famFuncionarioPublico'];
        $persona->socioFuncionarioPublico = $datos['socioFuncionarioPublico'];

        $persona->guardar();

        return true;
    }

    public function obtenerPorId($id) {
        $persona = PersonaExpuestaPublicamente::obtenerPorId($id);
        if ($persona) {
            return $persona;
        } else {
            http_response_code(404);
            return array("message" => "Persona no encontrada.");
        }
    }

    public function obtenerPorIdUsuario($idUsuario) {
        $persona = PersonaExpuestaPublicamente::obtenerPorIdUsuario($idUsuario);
        if ($persona) {
            return $persona;
        } else {
            http_response_code(404);
            return array("message" => "Persona no encontrada.");
        }
    }

    public function obtenerTodos() {
        $personas = PersonaExpuestaPublicamente::obtenerTodos();
        if ($personas) {
            return $personas;
        } else {
            http_response_code(404);
            return array("message" => "No se encontraron personas expuestas públicamente.");
        }
    }

    public function eliminar($id) {
        $persona = PersonaExpuestaPublicamente::obtenerPorId($id);
        if (!$persona) {
            return array("message" => "Persona no encontrada.");
        }

        $persona->eliminar();
        return array("message" => "Persona eliminada correctamente.");
    }
}
?>