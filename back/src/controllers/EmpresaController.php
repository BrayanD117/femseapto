<?php

require_once __DIR__ . '/../models/EmpresaModel.php';

class EmpresaController {
    
    public function crear($datos) {
        $empresa = new Empresa(
            $datos['id'],
            $datos['nit'],
            $datos['nombre'],
            $datos['id_tipo_empresa'],
            $datos['id_tipo_vinculacion'],
            $datos['id_municipio'],
            $datos['direccion'],
            $datos['telefono'],
            $datos['fax'],
            $datos['actividad_economica'],
            $datos['ciiu']
        );

        $empresa->guardar();
        
        return $empresa->id;
    }

    public function actualizar($id, $datos) {

        $empresa = Empresa::obtenerPorId($id);
        if (!$empresa) {
            return false;
        }

        $empresa->id = $datos['id'];
        $empresa->nit = $datos['nit'];
        $empresa->nombre = $datos['nombre'];
        $empresa->id_tipo_empresa = $datos['id_tipo_empresa'];
        $empresa->id_tipo_vinculacion =  $datos['id_tipo_vinculacion'];
        $empresa->id_municipio = $datos['id_municipio'];
        $empresa->direccion = $datos['direccion'];
        $empresa->telefono = $datos['telefono'];
        $empresa->fax = $datos['fax'];
        $empresa->actividad_economica = $datos['actividad_economica'];
        $empresa->ciiu = $datos['ciiu'];

        $empresa->guardar();

        return true;
    }

    public function obtenerPorId($id) {
        $empresa = Empresa::obtenerPorId($id);
        if ($empresa) {
            return $empresa;
        } else {
            http_response_code(404);
            return array("message" => "Empresa no encontrada.");
        }
    }

    public function obtenerTodos() {
        $empresas = Empresa::obtenerTodos();
        if ($empresas) {
            return $empresas;
        } else {
            http_response_code(404);
            return array("message" => "No se encontraron empresas.");
        }
    }
}
?>