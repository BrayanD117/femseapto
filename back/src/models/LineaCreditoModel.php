<?php
require_once __DIR__ . '/../../config/config.php';

class LineaCredito {
    public $id;
    public $nombre;
    public $monto;
    public $destinacion;
    public $plazo;
    public $tasaInteres1;
    public $tasaInteres2;
    public $condiciones;

    public function __construct($id = null, $nombre = '', $monto = 0.0, $destinacion = '', $plazo = 0, $tasaInteres1 = null, $tasaInteres2 = null, $condiciones = '') {
        $this->id = $id;
        $this->nombre = $nombre;
        $this->monto = $monto;
        $this->destinacion = $destinacion;
        $this->plazo = $plazo;
        $this->tasaInteres1 = $tasaInteres1;
        $this->tasaInteres2 = $tasaInteres2;
        $this->condiciones = $condiciones;
    }

    public function guardar() {
        $db = getDB();
        if ($this->id === null) {
            $query = $db->prepare("INSERT INTO lineas_credito (nombre, monto, destinacion, plazo, tasa_interes_1, tasa_interes_2, condiciones) VALUES (?, ?, ?, ?, ?, ?, ?)");
            $query->bind_param("sdsdsss", $this->nombre, $this->monto, $this->destinacion, $this->plazo, $this->tasaInteres1, $this->tasaInteres2, $this->condiciones);
        } else {
            $query = $db->prepare("UPDATE lineas_credito SET nombre = ?, monto = ?, destinacion = ?, plazo = ?, tasa_interes_1 = ?, tasa_interes_2 = ?, condiciones = ? WHERE id = ?");
            $query->bind_param("sdsdsssi", $this->nombre, $this->monto, $this->destinacion, $this->plazo, $this->tasaInteres1, $this->tasaInteres2, $this->condiciones, $this->id);
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
        $query = $db->prepare("SELECT id, nombre, monto, destinacion, plazo, tasa_interes_1, tasa_interes_2, condiciones FROM lineas_credito WHERE id = ?");
        $query->bind_param("i", $id);
        $query->execute();
        $query->bind_result($id, $nombre, $monto, $destinacion, $plazo, $tasaInteres1, $tasaInteres2, $condiciones);
        $lineaCredito = null;
        if ($query->fetch()) {
            $lineaCredito = new LineaCredito($id, $nombre, $monto, $destinacion, $plazo, $tasaInteres1, $tasaInteres2, $condiciones);
        }
        $query->close();
        $db->close();
        return $lineaCredito;
    }

    public static function obtenerTodos() {
        $db = getDB();
        $query = "SELECT id, nombre, monto, destinacion, plazo, tasa_interes_1, tasa_interes_2, condiciones FROM lineas_credito";
        $result = $db->query($query);
        $lineasCredito = [];
        while ($row = $result->fetch_assoc()) {
            $lineasCredito[] = new LineaCredito($row['id'], $row['nombre'], $row['monto'], $row['destinacion'], $row['plazo'], $row['tasa_interes_1'], $row['tasa_interes_2'], $row['condiciones']);
        }
        $db->close();
        return $lineasCredito;
    }

    public function eliminar() {
        $db = getDB();
        if ($this->id !== null) {
            $query = $db->prepare("DELETE FROM lineas_credito WHERE id = ?");
            $query->bind_param("i", $this->id);
            $query->execute();
            $query->close();
        }
        $db->close();
    }
}
?>