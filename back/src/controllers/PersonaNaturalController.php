<?php
require_once __DIR__ . '/../models/PersonaNaturalModel.php';

class PersonaNaturalController {
    
    public function crear($datos) {
        $personaNatural = new PersonaNatural(
            null,
            $datos['idUsuario'],
            $datos['idGenero'],
            $datos['fechaExpDoc'],
            $datos['idDeptoExpDoc'],
            $datos['mpioExpDoc'],
            $datos['fechaNacimiento'],
            $datos['paisNacimiento'],
            $datos['idDeptoNacimiento'] ?? null,
            $datos['mpioNacimiento'] ?? null,
            $datos['otroLugarNacimiento'] ?? null,
            $datos['idDeptoResidencia'],
            $datos['mpioResidencia'],
            $datos['idZonaResidencia']?? null,
            $datos['idTipoVivienda'],
            $datos['estrato'],
            $datos['direccionResidencia'],
            $datos['aniosAntigVivienda'],
            $datos['idEstadoCivil'],
            $datos['cabezaFamilia'],
            $datos['personasACargo'],
            $datos['tieneHijos'] ?? null,
            $datos['numeroHijos'] ?? null,
            $datos['correoElectronico'],
            $datos['telefono'],
            $datos['celular'],
            $datos['telefonoOficina'],
            $datos['idNivelEducativo'],
            $datos['profesion'] ?? null,
            $datos['ocupacionOficio'] ?? null,
            $datos['idEmpresaLabor'] ?? null,
            $datos['idTipoContrato'],
            $datos['dependenciaEmpresa'],
            $datos['cargoOcupa'] ?? null,
            $datos['jefeInmediato'],
            $datos['aniosAntigEmpresa'],
            $datos['mesesAntigEmpresa']  ?? null,
            $datos['mesSaleVacaciones'],
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

        $personaNatural->idGenero = $datos['idGenero'];
        $personaNatural->fechaExpDoc = $datos['fechaExpDoc'];
        $personaNatural->idDeptoExpDoc = $datos['idDeptoExpDoc'];
        $personaNatural->mpioExpDoc = $datos['mpioExpDoc'];
        $personaNatural->fechaNacimiento = $datos['fechaNacimiento'];
        $personaNatural->paisNacimiento = $datos['paisNacimiento'];
        $personaNatural->idDeptoNacimiento = $datos['idDeptoNacimiento'] ?? null;
        $personaNatural->mpioNacimiento = $datos['mpioNacimiento'] ?? null;
        $personaNatural->otroLugarNacimiento = $datos['otroLugarNacimiento'] ?? null;
        $personaNatural->idDeptoResidencia = $datos['idDeptoResidencia'];
        $personaNatural->mpioResidencia = $datos['mpioResidencia'];
        $personaNatural->idZonaResidencia = $datos['idZonaResidencia'] ?? null;
        $personaNatural->idTipoVivienda = $datos['idTipoVivienda'];
        $personaNatural->estrato = $datos['estrato'];
        $personaNatural->direccionResidencia = $datos['direccionResidencia'];
        $personaNatural->aniosAntigVivienda = $datos['aniosAntigVivienda'];
        $personaNatural->idEstadoCivil = $datos['idEstadoCivil'];
        $personaNatural->cabezaFamilia = $datos['cabezaFamilia'];
        $personaNatural->personasACargo = $datos['personasACargo'];
        $personaNatural->tieneHijos = $datos['tieneHijos'] ?? null;
        $personaNatural->numeroHijos = $datos['numeroHijos'] ?? null;
        $personaNatural->correoElectronico = $datos['correoElectronico'];
        $personaNatural->telefono = $datos['telefono'];
        $personaNatural->celular = $datos['celular'];
        $personaNatural->telefonoOficina = $datos['telefonoOficina'];
        $personaNatural->idNivelEducativo = $datos['idNivelEducativo'];
        $personaNatural->profesion = $datos['profesion'] ?? null;
        $personaNatural->ocupacionOficio = $datos['ocupacionOficio'] ?? null;
        $personaNatural->idEmpresaLabor = $datos['idEmpresaLabor'] ?? null;
        $personaNatural->idTipoContrato = $datos['idTipoContrato'];
        $personaNatural->dependenciaEmpresa = $datos['dependenciaEmpresa'];
        $personaNatural->cargoOcupa = $datos['cargoOcupa'] ?? null;
        $personaNatural->jefeInmediato = $datos['jefeInmediato'];
        $personaNatural->aniosAntigEmpresa = $datos['aniosAntigEmpresa'];
        $personaNatural->mesesAntigEmpresa = $datos['mesesAntigEmpresa'] ?? null;
        $personaNatural->mesSaleVacaciones = $datos['mesSaleVacaciones'];
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