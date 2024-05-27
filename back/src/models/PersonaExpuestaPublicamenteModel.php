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

    public function __construct($id = null, $idUsuario = '',
        $poderPublico = '',
        $manejaRecPublicos = '',
        $reconocimientoPublico = '',
        $funcionesPublicas = '',
        $actividadPublica = '',
        $funcionarioPublicoExtranjero = '',
        $famFuncionarioPublico = '',
        $socioFuncionarioPublico = '') {
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

    public static function obtenerPorIdUsuario($idUsuario) {
        $db = getDB();
        $query = $db->prepare("SELECT id, id_usuario, poder_publico, maneja_rec_public, reconoc_public, funciones_publicas, actividad_publica, funcion_publico_extranjero, fam_funcion_publico, socio_funcion_publico FROM personas_expuestas_publicamente WHERE id_usuario = ?");
        $query->bind_param("i", $idUsuario);
        $query->execute();
        $query->bind_result($id, $idUsuario, $poderPublico, $manejaRecPublicos, $reconocimientoPublico, $funcionesPublicas, $actividadPublica, $funcionarioPublicoExtranjero, $famFuncionarioPublico, $socioFuncionarioPublico);
        $persExpuestasPubl = null;
        if ($query->fetch()) {
            $persExpuestasPubl = new PersonaExpuestaPublicamente($id, $idUsuario, $poderPublico, $manejaRecPublicos, $reconocimientoPublico, $funcionesPublicas, $actividadPublica, $funcionarioPublicoExtranjero, $famFuncionarioPublico, $socioFuncionarioPublico);
        }
        $query->close();
        $db->close();
        return $persExpuestasPubl;
    }

    public static function obtenerTodos() {
        $db = getDB();
        $query = "SELECT id, id_usuario, poder_publico, maneja_rec_public, reconoc_public, funciones_publicas, actividad_publica, funcion_publico_extranjero, fam_funcion_publico, socio_funcion_publico FROM personas_expuestas_publicamente";
        $result = $db->query($query);
        $persExpuestasPubl = [];
        while ($row = $result->fetch_assoc()) {
            $persExpuestasPubl[] = new PersonaExpuestaPublicamente($row['id'], $row['abreviatura'], $row['nombre']);
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