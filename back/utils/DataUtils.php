<?php

class DataUtils {
    public static function convertirDatos($datos) {
        foreach ($datos as $key => $value) {
            switch ($key) {
                case 'correoElectronico':
                case 'usuario':
                    $datos[$key] = is_string($value) ? strtolower($value) : $value;
                    break;
                case 'contrasenia':
                    if ($key === 'correoElectronico' || $key === 'usuario') {
                        $datos[$key] = is_string($value) ? strtolower($value) : $value;
                    } else {
                        $datos[$key] = $value; 
                    }
                    break;
                case 'antigVivienda':
                case 'antigEmpresa':
                    $datos[$key] = $value;
                    break;
        
                default:
                    if (is_string($value)) {
                        $datos[$key] = strtoupper($value);
                    }
                    break;
            }
        }        
        return $datos;
    }
}
?>