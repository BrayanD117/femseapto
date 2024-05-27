<?php
require_once __DIR__ . '/../../config/config.php';

class LineaCredito {
    public $id;
    public $nombre;
    public $monto;
    public $destinacion;
    public $plazo;
    public $tasa_interes_1;
    public $tasa_interes_2;

    public function __construct($id = null, $nombre = '', $monto = 0.0, $destinacion = '', $plazo = 0, $tasa_interes_1 = 0.0, $tasa_interes_2 = null) {
        $this->id = $id;
        $this->nombre = $nombre;
        $this->monto = $monto;
        $this->destinacion = $destinacion;
        $this->plazo = $plazo;
        $this->tasa_interes_1 = $tasa_interes_1;
        $this->tasa_interes_2 = $tasa_interes_2;
    }

    public function guardar() {
        $db = getDB();
        if ($this->id === null) {
            $query = $db->prepare("INSERT INTO lineascredito (nombre, monto, destinacion, plazo, tasa_interes_1, tasa_interes_2) VALUES (?, ?, ?, ?, ?, ?)");
            $query->bind_param("sdsidd", $this->nombre, $this->monto, $this->destinacion, $this->plazo, $this->tasa_interes_1, $this->tasa_interes_2);
        } else {
            $query = $db->prepare("UPDATE lineascredito SET nombre = ?, monto = ?, destinacion = ?, plazo = ?, tasa_interes_1 = ?, tasa_interes_2 = ? WHERE id = ?");
            $query->bind_param("sdsiddi", $this->nombre, $this->monto, $this->destinacion, $this->plazo, $this->tasa_interes_1, $this->tasa_interes_2, $this->id);
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
        $query = $db->prepare("SELECT * FROM lineascredito WHERE id = ?");
        $query->bind_param("i", $id);
        $query->execute();
        $query->bind_result($id, $nombre, $monto, $destinacion, $plazo, $tasa_interes_1, $tasa_interes_2);
        $lineaCredito = null;
        if ($query->fetch()) {
            $lineaCredito = new LineaCredito($id, $nombre, $monto, $destinacion, $plazo, $tasa_interes_1, $tasa_interes_2);
        }
        $query->close();
        $db->close();
        return $lineaCredito;
    }

    public static function obtenerTodos() {
        $db = getDB();
        $query = "SELECT * FROM lineascredito";
        $result = $db->query($query);
        $lineasCredito = [];
        while ($row = $result->fetch_assoc()) {
            $lineasCredito[] = new LineaCredito($row['id'], $row['nombre'], $row['monto'], $row['destinacion'], $row['plazo'], $row['tasa_interes_1'], $row['tasa_interes_2']);
        }
        $db->close();
        return $lineasCredito;
    }

    public function eliminar() {
        $db = getDB();
        if ($this->id !== null) {
            $query = $db->prepare("DELETE FROM lineascredito WHERE id = ?");
            $query->bind_param("i", $this->id);
            $query->execute();
            $query->close();
        }
        $db->close();
    }
}
?>
