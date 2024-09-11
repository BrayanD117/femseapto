<?php
require_once '../config/config.php';

class Usuario {
    public $id;
    public $id_rol;
    public $usuario;
    public $contrasenia;
    public $primerNombre;
    public $segundoNombre;
    public $primerApellido;
    public $segundoApellido;
    public $idTipoDocumento;
    public $numeroDocumento;
    public $id_tipo_asociado;
    public $activo;
    public $primerIngreso;
    public $creadoEl;
    public $actualizadoEl;

    public function __construct($id = null, $id_rol = null, $usuario = '', $contrasenia = '',
    $primerApellido = '', $segundoApellido = null, $primerNombre = '', $segundoNombre = null, $idTipoDocumento = '', $numeroDocumento = '', $id_tipo_asociado = '', $activo = null, $primerIngreso = null, $creadoEl = '', $actualizadoEl = '') {
        $this->id = $id;
        $this->id_rol = $id_rol;
        $this->usuario = $usuario;
        $this->contrasenia = $contrasenia;
        $this->primerNombre = $primerNombre;
        $this->segundoNombre = $segundoNombre;
        $this->primerApellido = $primerApellido;
        $this->segundoApellido = $segundoApellido;
        $this->idTipoDocumento = $idTipoDocumento;
        $this->numeroDocumento = $numeroDocumento;
        $this->id_tipo_asociado = $id_tipo_asociado;
        $this->activo = $activo;
        $this->primerIngreso = $primerIngreso;
        $this->creadoEl = $creadoEl;
        $this->actualizadoEl = $actualizadoEl;
    }

    public function guardar() {
        $db = getDB();
        if ($this->id === null) {
            $query = $db->prepare("INSERT INTO usuarios (id_rol, usuario, contrasenia, primer_nombre, segundo_nombre, primer_apellido, segundo_apellido, id_tipo_documento, numero_documento, id_tipo_asociado, activo, primer_ingreso) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $query->bind_param("issssssisiii", $this->id_rol, $this->usuario, $this->contrasenia, $this->primerNombre, $this->segundoNombre, $this->primerApellido, $this->segundoApellido, $this->idTipoDocumento, $this->numeroDocumento, $this->id_tipo_asociado, $this->activo, $this->primerIngreso);
        } else {
            $query = $db->prepare("UPDATE usuarios SET id_rol = ?, usuario = ?, contrasenia = ?, primer_nombre = ?, segundo_nombre = ?, primer_apellido = ?, segundo_apellido = ?, id_tipo_documento = ?, numero_documento = ?, id_tipo_asociado = ?, activo = ?, primer_ingreso = ? WHERE id = ?");
            $query->bind_param("issssssisiiii", $this->id_rol, $this->usuario, $this->contrasenia, $this->primerNombre, $this->segundoNombre, $this->primerApellido, $this->segundoApellido, $this->idTipoDocumento, $this->numeroDocumento, $this->id_tipo_asociado, $this->activo, $this->primerIngreso, $this->id);
        }
        $query->execute();
        if ($query->error) {
            die('Error en la consulta: ' . $query->error);
        }
        if ($this->id === null) {
            $this->id = $query->insert_id;
        }
        $query->close();
        $db->close();
    }    

    public static function cambiarEstadoActivo($id) {
        $db = getDB();
        try {
            // Obtener el estado actual del usuario
            $query = $db->prepare("SELECT activo FROM usuarios WHERE id = ?");
            $query->bind_param("i", $id);
            $query->execute();
            $query->bind_result($estadoActual);
            $query->fetch();
            $query->close();
    
            // Cambiar el estado activo
            $nuevoEstado = ($estadoActual == 1) ? 0 : 1;
    
            // Actualizar el estado del usuario
            $query = $db->prepare("UPDATE usuarios SET activo = ? WHERE id = ?");
            $query->bind_param("ii", $nuevoEstado, $id);
            $query->execute();
    
            if ($query->error) {
                throw new Exception('Error en la consulta: ' . $query->error);
            }
    
            $query->close();
        } catch (Exception $e) {
            // Manejo de errores
            error_log($e->getMessage());
            return false;
        } finally {
            $db->close();
        }
        return true;
    }
    

    public static function obtenerPorId($id) {
        $db = getDB();
        $query = $db->prepare("SELECT * FROM usuarios WHERE id = ?");
        $query->bind_param("i", $id);
        $query->execute();
        $query->bind_result($id, $id_rol, $usuario, $contrasenia, $primerNombre, $segundoNombre, $primerApellido, $segundoApellido, $idTipoDocumento, $numeroDocumento, $id_tipo_asociado, $activo, $primerIngreso, $creadoEl, $actualizadoEl);
        $user = null;
        if ($query->fetch()) {
            $user = new Usuario($id, $id_rol, $usuario, $contrasenia, $primerNombre, $segundoNombre, $primerApellido, $segundoApellido, $idTipoDocumento, $numeroDocumento, $id_tipo_asociado, $activo, $primerIngreso, $creadoEl, $actualizadoEl);
        }
        $query->close();
        $db->close();
        return $user;
    }

    public static function obtenerPorNumeroDocumento($numDocumento) {
        $db = getDB();
        $query = $db->prepare("SELECT * FROM usuarios WHERE numero_documento = ?");
        $query->bind_param("s", $numDocumento);
        $query->execute();
        $query->bind_result($id, $id_rol, $usuario, $contrasenia, $primerNombre, $segundoNombre, $primerApellido, $segundoApellido, $idTipoDocumento, $numeroDocumento, $id_tipo_asociado, $activo, $primerIngreso, $creadoEl, $actualizadoEl);
        $user = null;
        if ($query->fetch()) {
            $user = new Usuario($id, $id_rol, $usuario, $contrasenia, $primerNombre, $segundoNombre, $primerApellido, $segundoApellido, $idTipoDocumento, $numeroDocumento, $id_tipo_asociado, $activo, $primerIngreso, $creadoEl, $actualizadoEl);
        }
        $query->close();
        $db->close();
        return $user;
    }

    public static function existePorNumeroDocumentoYUsuario($numeroDocumento, $usuario) {
        $db = getDB();
        $query = $db->prepare("SELECT COUNT(*) FROM usuarios WHERE numero_documento = ? OR usuario = ?");
        $query->bind_param("ss", $numeroDocumento, $usuario);
        $query->execute();
        $query->bind_result($count);
        $query->fetch();
        $query->close();
        $db->close();
        return $count > 0;
    }
    
    public static function obtenerTodos() {
        $db = getDB();
        $query = "SELECT * FROM usuarios";
        $result = $db->query($query);
        $users = [];
        while ($row = $result->fetch_assoc()) {
            $users[] = new Usuario($row['id'], $row['id_rol'], $row['usuario'], $row['contrasenia'], $row['primer_apellido'], $row['segundo_apellido'], $row['primer_nombre'], $row['segundo_nombre'], $row['id_tipo_documento'], $row['numero_documento'], $row['id_tipo_asociado'], $row['activo'], null, $row['creado_el'], $row['actualizado_el']);
        }
        $db->close();
        return $users;
    }

    public static function obtenerConPaginacion($page, $size, $idRol) {
        $db = getDB();
        $offset = ($page - 1) * $size;
        
        // Consulta principal con el parámetro id_rol
        $query = "SELECT * FROM usuarios WHERE id_rol = ? LIMIT ? OFFSET ?";
        $stmt = $db->prepare($query);
        
        // Verifica si la preparación de la consulta fue exitosa
        if ($stmt === false) {
            die('Error en la preparación de la consulta: ' . $db->error);
        }
        
        // Asignación de parámetros
        $stmt->bind_param('iii', $idRol, $size, $offset);
        $stmt->execute();
        
        // Verifica si la ejecución de la consulta fue exitosa
        if ($stmt->errno) {
            die('Error en la ejecución de la consulta: ' . $stmt->error);
        }
        
        $result = $stmt->get_result();
        $usuarios = [];
        
        while ($row = $result->fetch_assoc()) {
            $usuarios[] = new Usuario($row['id'], $row['id_rol'], $row['usuario'], null, $row['primer_apellido'], $row['segundo_apellido'], $row['primer_nombre'], $row['segundo_nombre'], $row['id_tipo_documento'], $row['numero_documento'], $row['id_tipo_asociado'], $row['activo'], null, $row['creado_el'], $row['actualizado_el']);
        }
        
        // Consulta para contar los registros con el parámetro id_rol
        $countQuery = "SELECT COUNT(*) as total FROM usuarios WHERE id_rol = ?";
        $countStmt = $db->prepare($countQuery);
        
        // Verifica si la preparación de la consulta de conteo fue exitosa
        if ($countStmt === false) {
            die('Error en la preparación de la consulta de conteo: ' . $db->error);
        }
        
        $countStmt->bind_param('i', $idRol);
        $countStmt->execute();
        
        // Verifica si la ejecución de la consulta de conteo fue exitosa
        if ($countStmt->errno) {
            die('Error en la ejecución de la consulta de conteo: ' . $countStmt->error);
        }
        
        $countResult = $countStmt->get_result();
        $total = $countResult->fetch_assoc()['total'];
        
        $db->close();
        return [
            'data' => $usuarios,
            'total' => $total
        ];
    }

    public function eliminar() {
        $db = getDB();
        if ($this->id !== null) {
            $query = $db->prepare("DELETE FROM usuarios WHERE id = ?");
            $query->bind_param("i", $this->id);
            $query->execute();
            $query->close();
        }
        $db->close();
    }

    // User Login
    public static function buscarPorUsuario($usuario) {
        $db = getDB();
        $query = $db->prepare("SELECT * FROM usuarios WHERE usuario = ? AND activo = 1");
        $query->bind_param("s", $usuario);
        $query->execute();
        $query->bind_result($id, $id_rol, $usuario, $contrasenia, $primerNombre, $segundoNombre, $primerApellido, $segundoApellido, $idTipoDocumento, $numeroDocumento, $id_tipo_asociado, $activo, $primerIngreso, $creadoEl, $actualizadoEl);
        $usuarioObj = null;
        if ($query->fetch()) {
            $usuarioObj = new Usuario($id, $id_rol, $usuario, $contrasenia, $primerNombre, $segundoNombre, $primerApellido, $segundoApellido, $idTipoDocumento, $numeroDocumento, $id_tipo_asociado, $activo, $primerIngreso, $creadoEl, $actualizadoEl);
        }
        $query->close();
        $db->close();
        return $usuarioObj;
    }
}
?>