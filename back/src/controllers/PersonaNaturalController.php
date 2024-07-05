<?php
require_once __DIR__ . '/../models/PersonaNaturalModel.php';

class PersonaNaturalController {
    
    public function crear($datos) {
        $personaNatural = new PersonaNatural(
            null,
            $datos['idUsuario'],
            $datos['idGenero'],
            $datos['fechaExpedicionDoc'],
            $datos['mpioExpedicionDoc'],
            $datos['fechaNacimiento'],
            $datos['paisNacimiento'],
            $datos['mpioNacimiento'],
            $datos['otroLugarNacimiento'],
            $datos['mpioResidencia'],
            $datos['idZonaResidencia'],
            $datos['idTipoVivienda'],
            $datos['estrato'],
            $datos['direccionResidencia'],
            $datos['aniosAntiguedadVivienda'],
            $datos['idEstadoCivil'],
            $datos['cabezaFamilia'],
            $datos['personasACargo'],
            $datos['tieneHijos'],
            $datos['numeroHijos'],
            $datos['correoElectronico'],
            $datos['telefono'],
            $datos['celular'],
            $datos['telefonoOficina'],
            $datos['idNivelEducativo'],
            $datos['profesion'],
            $datos['ocupacionOficio'],
            $datos['idEmpresaLabor'],
            $datos['idTipoContrato'],
            $datos['dependenciaEmpresa'],
            $datos['cargoOcupa'],
            $datos['aniosAntiguedadEmpresa'],
            $datos['mesesAntiguedadEmpresa'],
            $datos['mesSaleVacaciones'],
            $datos['nombreEmergencia'],
            $datos['numeroCedulaEmergencia'],
            $datos['numeroCelularEmergencia']
        );

        $personaNatural->guardar();
        
        return $personaNatural->id;
    }

    public function actualizar($id, $datos) {

        $personaNatural = PersonaNatural::obtenerPorIdUsuario($id);
        if (!$personaNatural) {
            return false; // Si no existe, devolver false
        }

        $personaNatural->idGenero = $datos['idGenero'];
        $personaNatural->fechaExpDoc = $datos['fechaExpedicionDoc'];
        $personaNatural->mpioExpDoc = $datos['mpioExpedicionDoc'];
        $personaNatural->fechaNacimiento = $datos['fechaNacimiento'];
        $personaNatural->paisNacimiento = $datos['paisNacimiento'];
        $personaNatural->mpioNacimiento = $datos['mpioNacimiento'];
        $personaNatural->otroLugarNacimiento = $datos['otroLugarNacimiento'];
        $personaNatural->mpioResidencia = $datos['mpioResidencia'];
        $personaNatural->idZonaResidencia = $datos['idZonaResidencia'];
        $personaNatural->idTipoVivienda = $datos['idTipoVivienda'];
        $personaNatural->estrato = $datos['estrato'];
        $personaNatural->direccionResidencia = $datos['direccionResidencia'];
        $personaNatural->aniosAntigVivienda = $datos['aniosAntiguedadVivienda'];
        $personaNatural->idEstadoCivil = $datos['idEstadoCivil'];
        $personaNatural->cabezaFamilia = $datos['cabezaFamilia'];
        $personaNatural->personasACargo = $datos['personasACargo'];
        $personaNatural->tieneHijos = $datos['tieneHijos'];
        $personaNatural->numeroHijos = $datos['numeroHijos'];
        $personaNatural->correoElectronico = $datos['correoElectronico'];
        $personaNatural->telefono = $datos['telefono'];
        $personaNatural->celular = $datos['celular'];
        $personaNatural->telefonoOficina = $datos['telefonoOficina'];
        $personaNatural->idNivelEducativo = $datos['idNivelEducativo'];
        $personaNatural->profesion = $datos['profesion'];
        $personaNatural->ocupacionOficio = $datos['ocupacionOficio'];
        $personaNatural->idEmpresaLabor = $datos['idEmpresaLabor'];
        $personaNatural->idTipoContrato = $datos['idTipoContrato'];
        $personaNatural->dependenciaEmpresa = $datos['dependenciaEmpresa'];
        $personaNatural->cargoOcupa = $datos['cargoOcupa'];
        $personaNatural->aniosAntigEmpresa = $datos['aniosAntigEmpresa'];
        $personaNatural->mesesAntigEmpresa = $datos['mesesAntigEmpresa'];
        $personaNatural->mesSaleVacaciones = $datos['mesSaleVacaciones'];
        $personaNatural->nombreEmergencia = $datos['nombreEmergencia'];
        $personaNatural->numeroCedulaEmergencia = $datos['numeroCedulaEmergencia'];
        $personaNatural->numeroCelularEmergencia = $datos['numeroCelularEmergencia'];

        $personaNatural->guardar();

        return true;
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