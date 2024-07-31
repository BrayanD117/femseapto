<?php
require_once __DIR__ . '/../models/PersonaNaturalModel.php';

class PersonaNaturalController {
    
    public function crear($datos) {
        $personaNatural = new PersonaNatural(
            null,
            $datos['idUsuario'],
            $datos['idGenero'] ?? null,
            $datos['fechaExpDoc'] ?? null,
            $datos['idDeptoExpDoc'] ?? null,
            $datos['mpioExpDoc'] ?? null,
            $datos['fechaNacimiento'] ?? null,
            $datos['paisNacimiento'] ?? null,
            $datos['idDeptoNacimiento'] ?? null,
            $datos['mpioNacimiento'] ?? null,
            $datos['otroLugarNacimiento'] ?? null,
            $datos['idDeptoResidencia'] ?? null,
            $datos['mpioResidencia'] ?? null,
            $datos['idZonaResidencia']?? null,
            $datos['idTipoVivienda'] ?? null,
            $datos['estrato'] ?? null,
            $datos['direccionResidencia'] ?? null,
            $datos['aniosAntigVivienda'] ?? null,
            $datos['idEstadoCivil'] ?? null,
            $datos['cabezaFamilia'] ?? null,
            $datos['personasACargo'] ?? null,
            $datos['tieneHijos'] ?? null,
            $datos['numeroHijos'] ?? null,
            $datos['correoElectronico'] ?? null,
            $datos['telefono'] ?? null,
            $datos['celular'] ?? null,
            $datos['telefonoOficina'] ?? null,
            $datos['idNivelEducativo'] ?? null,
            $datos['profesion'] ?? null,
            $datos['ocupacionOficio'] ?? null,
            $datos['idEmpresaLabor'] ?? null,
            $datos['idTipoContrato'] ?? null,
            $datos['dependenciaEmpresa'] ?? null,
            $datos['cargoOcupa'] ?? null,
            $datos['jefeInmediato'] ?? null,
            $datos['aniosAntigEmpresa'] ?? null,
            $datos['mesesAntigEmpresa']  ?? null,
            $datos['mesSaleVacaciones'] ?? null,
            $datos['nombreEmergencia'] ?? null,
            $datos['numeroCedulaEmergencia'] ?? null,
            $datos['numeroCelularEmergencia'] ?? null
        );

        $personaNatural->guardar();
        
        return $personaNatural->id;
    }

    public function actualizar($id, $datos) {

        $personaNatural = PersonaNatural::obtenerPorId($id);
        if (!$personaNatural) {
            return false; // Si no existe, devolver false
        }

        $personaNatural->idGenero = $datos['idGenero'] ?? null;
        $personaNatural->fechaExpDoc = $datos['fechaExpDoc'] ?? null;
        $personaNatural->idDeptoExpDoc = $datos['idDeptoExpDoc'] ?? null;
        $personaNatural->mpioExpDoc = $datos['mpioExpDoc'] ?? null;
        $personaNatural->fechaNacimiento = $datos['fechaNacimiento'] ?? null;
        $personaNatural->paisNacimiento = $datos['paisNacimiento'] ?? null;
        $personaNatural->idDeptoNacimiento = $datos['idDeptoNacimiento'] ?? null;
        $personaNatural->mpioNacimiento = $datos['mpioNacimiento'] ?? null;
        $personaNatural->otroLugarNacimiento = $datos['otroLugarNacimiento'] ?? null;
        $personaNatural->idDeptoResidencia = $datos['idDeptoResidencia'] ?? null;
        $personaNatural->mpioResidencia = $datos['mpioResidencia'] ?? null;
        $personaNatural->idZonaResidencia = $datos['idZonaResidencia'] ?? null;
        $personaNatural->idTipoVivienda = $datos['idTipoVivienda'] ?? null;
        $personaNatural->estrato = $datos['estrato'] ?? null;
        $personaNatural->direccionResidencia = $datos['direccionResidencia'] ?? null;
        $personaNatural->aniosAntigVivienda = $datos['aniosAntigVivienda'] ?? null;
        $personaNatural->idEstadoCivil = $datos['idEstadoCivil'] ?? null;
        $personaNatural->cabezaFamilia = $datos['cabezaFamilia'] ?? null;
        $personaNatural->personasACargo = $datos['personasACargo'] ?? null;
        $personaNatural->tieneHijos = $datos['tieneHijos'] ?? null;
        $personaNatural->numeroHijos = $datos['numeroHijos'] ?? null;
        $personaNatural->correoElectronico = $datos['correoElectronico'] ?? null;
        $personaNatural->telefono = $datos['telefono'] ?? null;
        $personaNatural->celular = $datos['celular'] ?? null;
        $personaNatural->telefonoOficina = $datos['telefonoOficina'] ?? null;
        $personaNatural->idNivelEducativo = $datos['idNivelEducativo'] ?? null;
        $personaNatural->profesion = $datos['profesion'] ?? null;
        $personaNatural->ocupacionOficio = $datos['ocupacionOficio'] ?? null;
        $personaNatural->idEmpresaLabor = $datos['idEmpresaLabor'] ?? null;
        $personaNatural->idTipoContrato = $datos['idTipoContrato'] ?? null;
        $personaNatural->dependenciaEmpresa = $datos['dependenciaEmpresa'] ?? null;
        $personaNatural->cargoOcupa = $datos['cargoOcupa'] ?? null;
        $personaNatural->jefeInmediato = $datos['jefeInmediato'] ?? null;
        $personaNatural->aniosAntigEmpresa = $datos['aniosAntigEmpresa'] ?? null;
        $personaNatural->mesesAntigEmpresa = $datos['mesesAntigEmpresa'] ?? null;
        $personaNatural->mesSaleVacaciones = $datos['mesSaleVacaciones'] ?? null;
        $personaNatural->nombreEmergencia = $datos['nombreEmergencia'] ?? null;
        $personaNatural->numeroCedulaEmergencia = $datos['numeroCedulaEmergencia'] ?? null;
        $personaNatural->numeroCelularEmergencia = $datos['numeroCelularEmergencia'] ?? null;

        $personaNatural->guardar();

        return true;
    }

    public function obtenerPorId($id) {
        $persona = PersonaNatural::obtenerPorId($id);
        if ($persona) {
            return $persona;
        } else {
            http_response_code(404);
            return array("message" => "Persona no encontrada.");
        }
    }

    public function validarPersonaNatural($idUsuario) {
        $persona = PersonaNatural::validarPersonaNatural($idUsuario);
        if ($persona) {
            return $persona;
        } else {
            http_response_code(404);
            return array("message" => "Persona no encontrada.");
        }
    }

    public function obtenerPorIdUsuario($idUsuario) {
        $persona = PersonaNatural::obtenerPorIdUsuario($idUsuario);
        if ($persona) {
            return $persona;
        } else {
            http_response_code(404);
            return array("message" => "Persona no encontrada.");
        }
    }
}
?>