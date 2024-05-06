<?php
require_once 'config.php';

class PersonaNatural {
    public $id;
    public $idUsuario;
    public $nombres;
    public $primerApellido;
    public $segundoApellido;
    public $idGenero;
    public $idTipoDocumento;
    public $numeroDocumento;
    public $fechaExpDoc;
    public $mpioExpDoc;
    public $fechaNacimiento;
    public $paisNacimiento;
    public $mpioNacimiento;
    public $otroLugarNacimiento;
    public $mpioResidencia;
    public $idZonaResidencia;
    public $idTipoVivienda;
    public $estrato;
    public $direccionResidencia;
    public $aniosAntigVivienda;
    public $idEstadoCivil;
    public $personasACargo;
    public $tieneHijos;
    public $numeroHijos;
    public $correoElectronico;
    public $telefono;
    public $celular;
    public $idNivelEducativo;
    public $profesion;
    public $ocupacionOficio;
    public $idEmpresaLabor;
    public $cargoOcupa;
    public $nombreEmergencia;
    public $numeroCedulaEmergencia;
    public $numeroCelularEmergencia;

    public function __construct($id = null, $idUsuario = '',
        $nombres = '',
        $primerApellido = '',
        $segundoApellido = '',
        $idGenero = '',
        $idTipoDocumento = '',
        $numeroDocumento = '',
        $fechaExpDoc = '',
        $mpioExpDoc = '',
        $fechaNacimiento = '',
        $paisNacimiento = '',
        $mpioNacimiento = '',
        $otroLugarNacimiento = '', 
        $mpioResidencia = '',
        $idZonaResidencia = '', 
        $idTipoVivienda = '',
        $estrato = '',
        $direccionResidencia = '',
        $aniosAntigVivienda = '',
        $idEstadoCivil = '',
        $personasACargo = '',
        $tieneHijos = '',
        $numeroHijos = '',
        $correoElectronico = '',
        $telefono = '',
        $celular = '',
        $idNivelEducativo = '',
        $profesion = '',
        $ocupacionOficio = '',
        $idEmpresaLabor = '',
        $cargoOcupa = '',
        $nombreEmergencia = '',
        $numeroCedulaEmergencia = '',
        $numeroCelularEmergencia) {
        $this->id = $id;
        $this->idUsuario = $idUsuario;
        $this->nombres = $nombres;
        $this->primerApellido = $primerApellido;
        $this->segundoApellido = $segundoApellido;
        $this->idGenero = $idGenero;
        $this->idTipoDocumento = $idTipoDocumento;
        $this->numeroDocumento = $numeroDocumento;
        $this->fechaExpDoc = $fechaExpDoc;
        $this->mpioExpDoc = $mpioExpDoc;
        $this->fechaNacimiento = $fechaNacimiento;
        $this->paisNacimiento = $paisNacimiento;
        $this->mpioNacimiento = $mpioNacimiento;
        $this->otroLugarNacimiento = $otroLugarNacimiento;
        $this->mpioResidencia = $mpioResidencia;
        $this->idZonaResidencia = $idZonaResidencia;
        $this->idTipoVivienda = $idTipoVivienda;
        $this->estrato = $estrato;
        $this->direccionResidencia = $direccionResidencia;
        $this->aniosAntigVivienda = $aniosAntigVivienda;
        $this->idEstadoCivil = $idEstadoCivil;
        $this->personasACargo = $personasACargo;
        $this->tieneHijos = $tieneHijos;
        $this->numeroHijos = $numeroHijos;
        $this->correoElectronico = $correoElectronico;
        $this->telefono = $telefono;
        $this->celular = $celular;
        $this->idNivelEducativo = $idNivelEducativo;
        $this->profesion = $profesion;
        $this->ocupacionOficio = $ocupacionOficio;
        $this->idEmpresaLabor = $idEmpresaLabor;
        $this->cargoOcupa = $cargoOcupa;
        $this->nombreEmergencia = $nombreEmergencia;
        $this->numeroCedulaEmergencia = $numeroCedulaEmergencia;
        $this->numeroCelularEmergencia = $numeroCelularEmergencia;
    }

    public function guardar() {
        $db = getDB();
        if ($this->id === null) {
            $query = $db->prepare("INSERT INTO personas_naturales (id_usuario, nombres, primer_apellido, segundo_apellido, id_genero, id_tipo_documento, numero_documento, fecha_expedicion_doc, mpio_expedicion_doc, fecha_nacimiento, pais_nacimiento, mpio_nacimiento, otro_lugar_nacimiento, mpio_residencia, id_zona_residencia, id_tipo_vivienda, estrato, direccion_residencia, anios_antiguedad_vivienda, id_estado_civil, personas_a_cargo, tiene_hijos, numero_hijos, correo_electronico, telefono, celular, id_nivel_educativo, profesion, ocupacion_oficio, id_empresa_labor, cargo_ocupa, nombre_emergencia, numero_cedula_emergencia, numero_celular_emergencia) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $query->bind_param("isssiissssssssiiisiiisisssississss", $this->idUsuario, $this->nombres,
                $this->primerApellido, $this->segundoApellido, $this->idGenero, $this->idTipoDocumento,
                $this->numeroDocumento, $this->fechaExpDoc, $this->mpioExpDoc, $this->fechaNacimiento,
                $this->paisNacimiento, $this->mpioNacimiento, $this->otroLugarNacimiento, $this->mpioResidencia, $this->idZonaResidencia, $this->idTipoVivienda, $this->estrato,
                $this->direccionResidencia, $this->aniosAntigVivienda, $this->idEstadoCivil, $this->personasACargo, $this->tieneHijos, $this->numeroHijos, $this->correoElectronico, $this->telefono, $this->celular, $this->idNivelEducativo, $this->profesion, $this->ocupacionOficio, $this->idEmpresaLabor, $this->cargoOcupa, $this->nombreEmergencia,
                $this->numeroCedulaEmergencia, $this->numeroCelularEmergencia
            );
        } else {
            $query = $db->prepare("UPDATE personas_naturales SET nombres = ?, primer_apellido = ?, segundo_apellido = ?, id_genero = ?, id_tipo_documento = ?, numero_documento = ?, fecha_expedicion_doc = ?, mpio_expedicion_doc = ?, fecha_nacimiento = ?, pais_nacimiento = ?, mpio_nacimiento = ?, otro_lugar_nacimiento = ?, mpio_residencia = ?, id_zona_residencia = ?, id_tipo_vivienda = ?, estrato = ?, direccion_residencia = ?, anios_antiguedad_vivienda = ?, id_estado_civil = ?, personas_a_cargo = ?, tiene_hijos = ?, numero_hijos = ?, correo_electronico = ?, telefono = ?, celular = ?, id_nivel_educativo = ?, profesion = ?, ocupacion_oficio = ?, id_empresa_labor = ?, cargo_ocupa = ?, nombre_emergencia = ?, numero_cedula_emergencia = ?, numero_celular_emergencia = ? WHERE id = ?");
            $query->bind_param("sssiissssssssiiisiiisisssississss", $this->idUsuario, $this->nombres,
            $this->primerApellido, $this->segundoApellido, $this->idGenero, $this->idTipoDocumento,
            $this->numeroDocumento, $this->fechaExpDoc, $this->mpioExpDoc, $this->fechaNacimiento,
            $this->paisNacimiento, $this->mpioNacimiento, $this->otroLugarNacimiento, $this->mpioResidencia, $this->idZonaResidencia, $this->idTipoVivienda, $this->estrato,
            $this->direccionResidencia, $this->aniosAntigVivienda, $this->idEstadoCivil, $this->personasACargo, $this->tieneHijos, $this->numeroHijos, $this->correoElectronico, $this->telefono, $this->celular, $this->idNivelEducativo, $this->profesion, $this->ocupacionOficio, $this->idEmpresaLabor, $this->cargoOcupa, $this->nombreEmergencia,
            $this->numeroCedulaEmergencia, $this->numeroCelularEmergencia, $this->id
            );
        }
        $query->execute();
        if ($this->id === null) {
            $this->id = $query->insert_id;
        }
        $query->close();
        $db->close();
    }

    public static function obtenerPorId($idUsuario) {
        $db = getDB();
        $query = $db->prepare("SELECT id, id_usuario, nombres, primer_apellido, segundo_apellido, id_genero, id_tipo_documento, numero_documento, fecha_expedicion_doc, mpio_expedicion_doc, fecha_nacimiento, pais_nacimiento, mpio_nacimiento, otro_lugar_nacimiento, mpio_residencia, id_zona_residencia, id_tipo_vivienda, estrato, direccion_residencia, anios_antiguedad_vivienda, id_estado_civil, personas_a_cargo, tiene_hijos, numero_hijos, correo_electronico, telefono, celular, id_nivel_educativo, profesion, ocupacion_oficio, id_empresa_labor, cargo_ocupa, nombre_emergencia, numero_cedula_emergencia, numero_celular_emergencia FROM personas_naturales WHERE id_usuario = ?");
        $query->bind_param("i", $idUsuario);
        $query->execute();
        $query->bind_result($id,
        $idUsuario,
        $nombres,
        $primerApellido,
        $segundoApellido,
        $idGenero,
        $idTipoDocumento,
        $numeroDocumento,
        $fechaExpDoc,
        $mpioExpDoc,
        $fechaNacimiento,
        $paisNacimiento,
        $mpioNacimiento,
        $otroLugarNacimiento,
        $mpioResidencia,
        $idZonaResidencia,
        $idTipoVivienda,
        $estrato,
        $direccionResidencia,
        $aniosAntigVivienda,
        $idEstadoCivil,
        $personasACargo,
        $tieneHijos,
        $numeroHijos,
        $correoElectronico,
        $telefono,
        $celular,
        $idNivelEducativo,
        $profesion,
        $ocupacionOficio,
        $idEmpresaLabor,
        $cargoOcupa,
        $nombreEmergencia,
        $numeroCedulaEmergencia,
        $numeroCelularEmergencia);
        $personasNaturales = null;
        if ($query->fetch()) {
            $personasNaturales = new PersonaNatural($id,
            $idUsuario,
            $nombres,
            $primerApellido,
            $segundoApellido,
            $idGenero,
            $idTipoDocumento,
            $numeroDocumento,
            $fechaExpDoc,
            $mpioExpDoc,
            $fechaNacimiento,
            $paisNacimiento,
            $mpioNacimiento,
            $otroLugarNacimiento,
            $mpioResidencia,
            $idZonaResidencia,
            $idTipoVivienda,
            $estrato,
            $direccionResidencia,
            $aniosAntigVivienda,
            $idEstadoCivil,
            $personasACargo,
            $tieneHijos,
            $numeroHijos,
            $correoElectronico,
            $telefono,
            $celular,
            $idNivelEducativo,
            $profesion,
            $ocupacionOficio,
            $idEmpresaLabor,
            $cargoOcupa,
            $nombreEmergencia,
            $numeroCedulaEmergencia,
            $numeroCelularEmergencia);
        }
        $query->close();
        $db->close();
        return $personasNaturales;
    }

    public static function obtenerTodos() {
        $db = getDB();
        $query = "SELECT id, id_usuario, nombres, primer_apellido, segundo_apellido, id_genero, id_tipo_documento, numero_documento, fecha_expedicion_doc, mpio_expedicion_doc, fecha_nacimiento, pais_nacimiento, mpio_nacimiento, otro_lugar_nacimiento, mpio_residencia, id_zona_residencia, id_tipo_vivienda, estrato, direccion_residencia, anios_antiguedad_vivienda, id_estado_civil, personas_a_cargo, tiene_hijos, numero_hijos, correo_electronico, telefono, celular, id_nivel_educativo, profesion, ocupacion_oficio, id_empresa_labor, cargo_ocupa, nombre_emergencia, numero_cedula_emergencia, numero_celular_emergencia FROM personas_naturales";
        $result = $db->query($query);
        $personasNaturales = [];
        while ($row = $result->fetch_assoc()) {
            $personasNaturales[] = new PersonaNatural($row['id'],
            $row['id_usuario'],
            $row['nombres'],
            $row['primer_apellido'],
            $row['segundo_apellido'],
            $row['id_genero'],
            $row['id_tipo_documento'],
            $row['numero_documento'],
            $row['fecha_expedicion_doc'],
            $row['mpio_expedicion_doc'],
            $row['fecha_nacimiento'],
            $row['pais_nacimiento'],
            $row['mpio_nacimiento'],
            $row['otro_lugar_nacimiento'],
            $row['mpio_residencia'],
            $row['id_zona_residencia'],
            $row['id_tipo_vivienda'],
            $row['estrato'],
            $row['direccion_residencia'],
            $row['anios_antiguedad_vivienda'],
            $row['id_estado_civil'],
            $row['personas_a_cargo'],
            $row['tiene_hijos'],
            $row['numero_hijos'],
            $row['correo_electronico'],
            $row['telefono'],
            $row['celular'],
            $row['id_nivel_educativo'],
            $row['profesion'],
            $row['ocupacion_oficio'],
            $row['id_empresa_labor'],
            $row['cargo_ocupa'],
            $row['nombre_emergencia'],
            $row['numero_cedula_emergencia'],
            $row['numero_celular_emergencia']);
        }
        $db->close();
        return $personasNaturales;
    }

    public function eliminar() {
        $db = getDB();
        if ($this->id !== null) {
            $query = $db->prepare("DELETE FROM personas_naturales WHERE id = ?");
            $query->bind_param("i", $this->id);
            $query->execute();
            $query->close();
        }
        $db->close();
    }
}
?>