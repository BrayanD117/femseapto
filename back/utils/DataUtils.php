<?php

class DataUtils {
    public static function convertirDatos($datos) {
        foreach ($datos as $key => $value) {
            if ($key === 'correoElectronico' || $key === 'usuario') {
                $datos[$key] = is_string($value) ? strtolower($value) : $value;
            } if ($key === 'antigVivienda' || $key === 'antigEmpresa') {
                $datos[$key] = $value;
            } elseif (is_string($value)) {
                $datos[$key] = strtoupper($value);
            }
        }
        return $datos;
    }
}
?>