<?php
require_once __DIR__ . '/../../config/config.php';

class UsuarioComunicacion {
    public $id;
    public $idUsuario;
    public $idMedioComunicacion;
    public $creadoEl;
    public $actualizadoEl;

    public function __construct($id = null, $idUsuario = null, $idMedioComunicacion = null, $creadoEl = null, $actualizadoEl = null) {
        $this->id = $id;
        $this->idUsuario = $idUsuario;
        $this->idMedioComunicacion = $idMedioComunicacion;
        $this->creadoEl = $creadoEl;
        $this->actualizadoEl = $actualizadoEl;
    }

    public function guardar() {
        $db = getDB();
    
        try {
            $query = $db->prepare(
                "INSERT INTO usuarios_comunicacion (id_usuario, id_medio_comunicacion)
                VALUES (?, ?)
                ON DUPLICATE KEY UPDATE actualizado_el = NOW()"
            );
            $query->bind_param("ii", $this->idUsuario, $this->idMedioComunicacion);
            $query->execute();
    
            if ($query->insert_id) {
                $this->id = $query->insert_id;
            }
    
            $query->close();
        } catch (mysqli_sql_exception $e) {
            throw new Exception("Error al guardar usuario_comunicacion: " . $e->getMessage());
        }
    
        $db->close();
    }    

    public static function obtenerPorId($id) {
        $db = getDB();
        $query = $db->prepare("SELECT id, id_usuario, id_medio_comunicacion, creado_el, actualizado_el FROM usuarios_comunicacion WHERE id = ?");
        $query->bind_param("i", $id);
        $query->execute();
        $query->bind_result($id, $idUsuario, $idMedioComunicacion, $creadoEl, $actualizadoEl);
        $usuarioComunicacion = null;
        if ($query->fetch()) {
            $usuarioComunicacion = new UsuarioComunicacion($id, $idUsuario, $idMedioComunicacion, $creadoEl, $actualizadoEl);
        }
        $query->close();
        $db->close();
        return $usuarioComunicacion;
    }

    public static function obtenerPorIdUsuario($idUsuario) {
        $db = getDB();
        $query = $db->prepare(
            "SELECT
                id,
                id_usuario,
                id_medio_comunicacion,
                CONVERT_TZ(creado_el, '+00:00', '-05:00') AS creado_el,
                CONVERT_TZ(actualizado_el, '+00:00', '-05:00') AS actualizado_el
            FROM usuarios_comunicacion
            WHERE id_usuario = ?");
        $query->bind_param("i", $idUsuario);
        $query->execute();
        $query->bind_result($id, $idUsuario, $idMedioComunicacion, $creadoEl, $actualizadoEl);
        
        $usuariosComunicacion = [];

        while ($query->fetch()) {
            $usuariosComunicacion[] = new UsuarioComunicacion($id, $idUsuario, $idMedioComunicacion, $creadoEl, $actualizadoEl);
        }
        
        $query->close();
        $db->close();
        
        return $usuariosComunicacion;
    }

    public static function obtenerTodos() {
        $db = getDB();
        $query = "SELECT id, id_usuario, id_medio_comunicacion, creado_el, actualizado_el FROM usuarios_comunicacion";
        $result = $db->query($query);
        $usuariosComunicacion = [];
        while ($row = $result->fetch_assoc()) {
            $usuariosComunicacion[] = new UsuarioComunicacion($row['id'], $row['id_usuario'], $row['id_medio_comunicacion'], $row['creado_el'], $row['actualizado_el']);
        }
        $db->close();
        return $usuariosComunicacion;
    }

    public static function eliminarPorIdUsuarioYMedios($idUsuario, $idsMedios) {
        if (empty($idsMedios)) return;

        $db = getDB();
        $placeholders = implode(',', array_fill(0, count($idsMedios), '?'));
        $query = $db->prepare(
            "DELETE FROM usuarios_comunicacion WHERE id_usuario = ? AND id_medio_comunicacion IN ($placeholders)"
        );

        $types = str_repeat('i', count($idsMedios) + 1);
        $params = array_merge([$idUsuario], $idsMedios);
        $query->bind_param($types, ...$params);

        $query->execute();
        $query->close();
        $db->close();
    }

    public function eliminar() {
        $db = getDB();
        if ($this->id !== null) {
            $query = $db->prepare("DELETE FROM usuarios_comunicacion WHERE id = ?");
            $query->bind_param("i", $this->id);
            $query->execute();
            $query->close();
        }
        $db->close();
    }
}
?>