<?php
require_once __DIR__ . '/../models/LineaCreditoModel.php';

class LineaCreditoController {
    
    public function crear($datos) {
        $lineaCredito = new LineaCredito(
            null,
            $datos['nombre'],
            $datos['monto'],
            $datos['destinacion'],
            $datos['plazo'],
            $datos['tasa_interes_1'],
            $datos['tasa_interes_2'],
            $datos['condiciones']
        );

        $lineaCredito->guardar();
        
        return $lineaCredito->id;
    }

    public function actualizar($id, $datos) {
        $lineaCredito = LineaCredito::obtenerPorId($id);
        if (!$lineaCredito) {
            return false; // Si no existe, devolver false
        }

        $lineaCredito->nombre = $datos['nombre'];
        $lineaCredito->monto = $datos['monto'];
        $lineaCredito->destinacion = $datos['destinacion'];
        $lineaCredito->plazo = $datos['plazo'];
        $lineaCredito->tasa_interes_1 = $datos['tasa_interes_1'];
        $lineaCredito->tasa_interes_2 = $datos['tasa_interes_2'];
        $lineaCredito->condiciones = $datos['condiciones'];

        $lineaCredito->guardar();

        return true;
    }

    public function obtenerPorId($id) {
        $lineaCredito = LineaCredito::obtenerPorId($id);
        if ($lineaCredito) {
            return $lineaCredito;
        } else {
            http_response_code(404);
            return array("message" => "Línea de crédito no encontrada.");
        }
    }

    public function obtenerTodos() {
        $lineasCredito = LineaCredito::obtenerTodos();
        if ($lineasCredito) {
            return $lineasCredito;
        } else {
            http_response_code(404);
            return array("message" => "No se encontraron líneas de crédito.");
        }
    }
}
?>
