<?php
// Incluir el modelo de PersonaExpuestaPublicamente
require_once '../models/PersonaNaturalModel.php';

class PersonaNaturalController {
    
    public function crear($datos) {
        $personaNatural = new PersonaNatural(
            null,
            $datos['idUsuario'],
            $datos['nombres'],
            $datos['primerApellido'],
            $datos['segundoApellido'],
            $datos['idGenero'],
            $datos['idTipoDocumento'],
            $datos['numeroDocumento'],
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
            $datos['personasACargo'],
            $datos['tieneHijos'],
            $datos['numeroHijos'],
            $datos['correoElectronico'],
            $datos['telefono'],
            $datos['celular'],
            $datos['idNivelEducativo'],
            $datos['profesion'],
            $datos['ocupacionOficio'],
            $datos['idEmpresaLabor'],
            $datos['cargoOcupa'],
            $datos['nombreEmergencia'],
            $datos['numeroCedulaEmergencia'],
            $datos['numeroCelularEmergencia']
        );

        $personaNatural->guardar();
        
        return $personaNatural->id;
    }

    public function actualizar($id, $datos) {

        $personaNatural = PersonaNatural::obtenerPorId($id);
        if (!$personaNatural) {
            return false; // Si no existe, devolver false
        }

        $personaNatural->id = $datos['id'];
        $personaNatural->idUsuario = $datos['idUsuario'];
        $personaNatural->nombres = $datos['nombres'];
        $personaNatural->primerApellido = $datos['primerApellido'];
        $personaNatural->segundoApellido = $datos['segundoApellido'];
        $personaNatural->idGenero = $datos['idGenero'];
        $personaNatural->idTipoDocumento = $datos['idTipoDocumento'];
        $personaNatural->numeroDocumento = $datos['numeroDocumento'];
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
        $personaNatural->personasACargo = $datos['personasACargo'];
        $personaNatural->tieneHijos = $datos['tieneHijos'];
        $personaNatural->numeroHijos = $datos['numeroHijos'];
        $personaNatural->correoElectronico = $datos['correoElectronico'];
        $personaNatural->telefono = $datos['telefono'];
        $personaNatural->celular = $datos['celular'];
        $personaNatural->idNivelEducativo = $datos['idNivelEducativo'];
        $personaNatural->profesion = $datos['profesion'];
        $personaNatural->ocupacionOficio = $datos['ocupacionOficio'];
        $personaNatural->idEmpresaLabor = $datos['idEmpresaLabor'];
        $personaNatural->cargoOcupa = $datos['cargoOcupa'];
        $personaNatural->nombreEmergencia = $datos['nombreEmergencia'];
        $personaNatural->numeroCedulaEmergencia = $datos['numeroCedulaEmergencia'];
        $personaNatural->numeroCelularEmergencia = $datos['numeroCelularEmergencia'];

        $personaNatural->guardar();

        return true;
    }

    public function obtenerPorId($idUsuario) {
        $persona = PersonaNatural::obtenerPorId($idUsuario);
        if ($persona) {
            return $persona;
        } else {
            http_response_code(404);
            return array("message" => "Persona no encontrada.");
        }
    }
}
?>