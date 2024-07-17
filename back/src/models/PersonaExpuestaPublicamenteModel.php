<?php
require_once '../config/config.php';

class PersonaExpuestaPublicamente {
    public $id;
    public $idUsuario;
    public $poderPublico;
    public $manejaRecPublicos;
    public $reconocimientoPublico;
    public $funcionesPublicas;
    public $actividadPublica;
    public $funcionarioPublicoExtranjero;
    public $famFuncionarioPublico;
    public $socioFuncionarioPublico;
    public $creadoEl;
    public $actualizadoEl;

    public function __construct($id = null, $idUsuario = '',
        $poderPublico = '',
        $manejaRecPublicos = '',
        $reconocimientoPublico = '',
        $funcionesPublicas = '',
        $actividadPublica = '',
        $funcionarioPublicoExtranjero = '',
        $famFuncionarioPublico = '',
        $socioFuncionarioPublico = '',
        $creadoEl = '',
        $actualizadoEl = '') {
        $this->id = $id;
        $this->idUsuario = $idUsuario;
        $this->poderPublico = $poderPublico;
        $this->manejaRecPublicos = $manejaRecPublicos;
        $this->reconocimientoPublico = $reconocimientoPublico;
        $this->funcionesPublicas = $funcionesPublicas;
        $this->actividadPublica = $actividadPublica;
        $this->funcionarioPublicoExtranjero = $funcionarioPublicoExtranjero;
        $this->famFuncionarioPublico = $famFuncionarioPublico;
        $this->socioFuncionarioPublico = $socioFuncionarioPublico;
        $this->creadoEl = $creadoEl;
        $this->actualizadoEl = $actualizadoEl;
    }

    public function guardar() {
        $db = getDB();
        if ($this->id === null) {
            $query = $db->prepare("INSERT INTO personas_expuestas_publicamente (id_usuario, poder_publico, maneja_rec_public, reconoc_public, funciones_publicas, actividad_publica, funcion_publico_extranjero, fam_funcion_publico, socio_funcion_publico) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $query->bind_param("issssssss", $this->idUsuario, $this->poderPublico, $this->manejaRecPublicos, $this->reconocimientoPublico, $this->funcionesPublicas, $this->actividadPublica, $this->funcionarioPublicoExtranjero, $this->famFuncionarioPublico, $this->socioFuncionarioPublico);
        } else {
            $query = $db->prepare("UPDATE personas_expuestas_publicamente SET poder_publico = ?, maneja_rec_public = ?, reconoc_public = ?, funciones_publicas = ?, actividad_publica = ?, funcion_publico_extranjero = ?, fam_funcion_publico = ?, socio_funcion_publico = ? WHERE id = ?");
            $query->bind_param("ssssssssi", $this->poderPublico, $this->manejaRecPublicos, $this->reconocimientoPublico, $this->funcionesPublicas, $this->actividadPublica, $this->funcionarioPublicoExtranjero, $this->famFuncionarioPublico, $this->socioFuncionarioPublico, $this->id);
        }
        $query->execute();
        if ($this->id === null) {
            $this->id = $query->insert_id;
        }
        $query->close();
        $db->close();
    }

    public static function validarPersonaPublica($id) {
        $db = getDB();
        $query = $db->prepare("SELECT validarPersonaPublicaUsuario(?) AS isValid");
        $query->bind_param("i", $id);
        $query->execute();
        $query->bind_result($isValid);
        $query->fetch();
        $query->close();
        $db->close();
        return (bool)$isValid;
    }

    public static function obtenerPorId($id) {
        $db = getDB();
        $query = $db->prepare("SELECT * FROM personas_expuestas_publicamente WHERE id = ?");
        $query->bind_param("i", $id);
        $query->execute();
        $query->bind_result($id, $idUsuario, $poderPublico, $manejaRecPublicos, $reconocimientoPublico, $funcionesPublicas, $actividadPublica, $funcionarioPublicoExtranjero, $famFuncionarioPublico, $socioFuncionarioPublico, $creadoEl, $actualizadoEl);
        $persExpuestasPubl = null;
        if ($query->fetch()) {
            $persExpuestasPubl = new PersonaExpuestaPublicamente($id, $idUsuario, $poderPublico, $manejaRecPublicos, $reconocimientoPublico, $funcionesPublicas, $actividadPublica, $funcionarioPublicoExtranjero, $famFuncionarioPublico, $socioFuncionarioPublico, $creadoEl, $actualizadoEl);
        }
        $query->close();
        $db->close();
        return $persExpuestasPubl;
    }

    public static function obtenerPorIdUsuario($idUsuario) {
        $db = getDB();
        $query = $db->prepare("SELECT * FROM personas_expuestas_publicamente WHERE id_usuario = ?");
        $query->bind_param("i", $idUsuario);
        $query->execute();
        $query->bind_result($id, $idUsuario, $poderPublico, $manejaRecPublicos, $reconocimientoPublico, $funcionesPublicas, $actividadPublica, $funcionarioPublicoExtranjero, $famFuncionarioPublico, $socioFuncionarioPublico, $creadoEl, $actualizadoEl);
        $persExpuestasPubl = null;
        if ($query->fetch()) {
            $persExpuestasPubl = new PersonaExpuestaPublicamente($id, $idUsuario, $poderPublico, $manejaRecPublicos, $reconocimientoPublico, $funcionesPublicas, $actividadPublica, $funcionarioPublicoExtranjero, $famFuncionarioPublico, $socioFuncionarioPublico, $creadoEl, $actualizadoEl);
        }
        $query->close();
        $db->close();
        return $persExpuestasPubl;
    }

    public static function obtenerTodos() {
        $db = getDB();
        $query = "SELECT * FROM personas_expuestas_publicamente";
        $result = $db->query($query);
        $persExpuestasPubl = [];
        while ($row = $result->fetch_assoc()) {
            $persExpuestasPubl[] = new PersonaExpuestaPublicamente($row['id'], $row['id_usuario'], $row['poder_publico'], $row['maneja_rec_public'], $row['reconoc_public'], $row['funciones_publicas'], $row['actividad_publica'], $row['funcion_publico_extranjero'], $row['fam_funcion_publico'], $row['socio_funcion_publico'], $row['creado_el'], $row['actualizado_el']);
        }
        $db->close();
        return $persExpuestasPubl;
    }

    public function eliminar() {
        $db = getDB();
        if ($this->id !== null) {
            $query = $db->prepare("DELETE FROM personas_expuestas_publicamente WHERE id = ?");
            $query->bind_param("i", $this->id);
            $query->execute();
            $query->close();
        }
        $db->close();
    }
}
?>