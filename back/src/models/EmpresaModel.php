<?php
require_once __DIR__ . '/../../config/config.php';

class Empresa {
    public $id;
    public $nit;
    public $nombre;
    public $idTipoEmpresa;
    public $idTipoVinculacion;
    public $idMunicipio;
    public $direccion;
    public $telefono;
    public $fax;
    public $actividadEconomica;
    public $ciiu;

    public function __construct($id = null, $nit = '', $nombre = '', $idTipoEmpresa = null, $idTipoVinculacion = null, $idMunicipio = '', $direccion = '', $telefono = '', $fax = '', $actividadEconomica = '', $ciiu = '') {
        $this->id = $id;
        $this->nit = $nit;
        $this->nombre = $nombre;
        $this->idTipoEmpresa = $idTipoEmpresa;
        $this->idTipoVinculacion = $idTipoVinculacion;
        $this->idMunicipio = $idMunicipio;
        $this->direccion = $direccion;
        $this->telefono = $telefono;
        $this->fax = $fax;
        $this->actividadEconomica = $actividadEconomica;
        $this->ciiu = $ciiu;
    }

    public function guardar() {
        $db = getDB();
        if ($this->id === null) {
            $query = $db->prepare("INSERT INTO empresas (nit, nombre, id_tipo_empresa, id_tipo_vinculacion, id_municipio, direccion, telefono, fax, actividad_economica, ciiu) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $query->bind_param("ssiissssss", $this->nit, $this->nombre, $this->idTipoEmpresa, $this->idTipoVinculacion, $this->idMunicipio, $this->direccion, $this->telefono, $this->fax, $this->actividadEconomica, $this->ciiu);
        } else {
            $query = $db->prepare("UPDATE empresas SET nit = ?, nombre = ?, id_tipo_empresa = ?, id_tipo_vinculacion = ?, id_municipio = ?, direccion = ?, telefono = ?, fax = ?, actividad_economica = ?, ciiu = ? WHERE id = ?");
            $query->bind_param("ssiissssssi", $this->nit, $this->nombre, $this->idTipoEmpresa, $this->idTipoVinculacion, $this->idMunicipio, $this->direccion, $this->telefono, $this->fax, $this->actividadEconomica, $this->ciiu, $this->id);
        }
        $query->execute();
        if ($this->id === null) {
            $this->id = $query->insert_id;
        }
        $query->close();
        $db->close();
    }

    public static function obtenerPorId($id) {
        $db = getDB();
        $query = $db->prepare("SELECT * FROM empresas WHERE id = ?");
        $query->bind_param("i", $id);
        $query->execute();
        $query->bind_result($id, $nit, $nombre, $idTipoEmpresa, $idTipoVinculacion, $idMunicipio, $direccion, $telefono, $fax, $actividadEconomica, $ciiu);
        $empresa = null;
        if ($query->fetch()) {
            $empresa = new Empresa($id, $nit, $nombre, $idTipoEmpresa, $idTipoVinculacion, $idMunicipio, $direccion, $telefono, $fax, $actividadEconomica, $ciiu);
        }
        $query->close();
        $db->close();
        return $empresa;
    }

    public static function obtenerTodos() {
        $db = getDB();
        $query = "SELECT * FROM empresas";
        $result = $db->query($query);
        $empresas = [];
        while ($row = $result->fetch_assoc()) {
            $empresas[] = new Empresa($row['id'], $row['nit'], $row['nombre'], $row['id_tipo_empresa'], $row['id_tipo_vinculacion'], $row['id_municipio'], $row['direccion'], $row['telefono'], $row['fax'], $row['actividad_economica'], $row['ciiu']);
        }
        $db->close();
        return $empresas;
    }

    public function eliminar() {
        $db = getDB();
        if ($this->id !== null) {
            $query = $db->prepare("DELETE FROM empresas WHERE id = ?");
            $query->bind_param("i", $this->id);
            $query->execute();
            $query->close();
        }
        $db->close();
    }
}
?>