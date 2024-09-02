<?php

class DataUtils {
    public static function convertirDatos($datos) {
        foreach ($datos as $key => $value) {
            if ($key === 'correoElectronico') {
                // Convertir el correo electrónico a minúsculas
                $datos[$key] = is_string($value) ? strtolower($value) : $value;
            } elseif (is_string($value)) {
                // Convertir otros valores a mayúsculas
                $datos[$key] = strtoupper($value);
            }
        }
        return $datos;
    }
}
?>