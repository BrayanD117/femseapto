<?php
require_once '../config/config.php';

class Usuario {
    public $id;
    public $id_rol;
    public $usuario;
    public $contrasenia;
    public $primerApellido;
    public $segundoApellido;
    public $primerNombre;
    public $segundoNombre;
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
        $this->primerApellido = $primerApellido;
        $this->segundoApellido = $segundoApellido;
        $this->primerNombre = $primerNombre;
        $this->segundoNombre = $segundoNombre;
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
            $query = $db->prepare("INSERT INTO usuarios (id_rol, usuario, contrasenia, primer_apellido, segundo_apellido, primer_nombre, segundo_nombre, id_tipo_documento, numero_documento, id_tipo_asociado, activo) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $query->bind_param("issssssisii", $this->id_rol, $this->usuario, $this->contrasenia, $this->primerApellido, $this->segundoApellido, $this->primerNombre, $this->segundoNombre, $this->idTipoDocumento, $this->numeroDocumento, $this->id_tipo_asociado, $this->activo);
        } else {
            $query = $db->prepare("UPDATE usuarios SET id_rol = ?, usuario = ?, contrasenia = ?, primer_apellido = ?, segundo_apellido = ?, primer_nombre = ?, segundo_nombre = ?, id_tipo_documento = ?, numero_documento = ?, id_tipo_asociado = ?, activo = ?, primer_ingreso = ? WHERE id = ?");
            $query->bind_param("issssssisiiii", $this->id_rol, $this->usuario, $this->contrasenia, $this->primerApellido, $this->segundoApellido, $this->primerNombre, $this->segundoNombre, $this->idTipoDocumento, $this->numeroDocumento, $this->id_tipo_asociado, $this->activo, $this->primerIngreso, $this->id);
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
            $query = $db->prepare("SELECT activo FROM usuarios WHERE id = ?");
            $query->bind_param("i", $id);
            $query->execute();
            $query->bind_result($estadoActual);
            $query->fetch();
            $query->close();
    
            $nuevoEstado = ($estadoActual == 1) ? 0 : 1;
    
            $query = $db->prepare("UPDATE usuarios SET activo = ? WHERE id = ?");
            $query->bind_param("ii", $nuevoEstado, $id);
            $query->execute();
    
            if ($query->error) {
                throw new Exception('Error en la consulta: ' . $query->error);
            }
    
            $query->close();
        } catch (Exception $e) {
            error_log($e->getMessage());
            return false;
        } finally {
            $db->close();
        }
        return true;
    }

    public static function restablecerContrasenia($id, $nuevaContrasena) {
        $db = getDB();
        try {
            $query = $db->prepare("UPDATE usuarios SET contrasenia = ?, primer_ingreso = false WHERE id = ?");
            $query->bind_param("si", $nuevaContrasena, $id);
            $query->execute();
    
            if ($query->error) {
                throw new Exception('Error al actualizar la contraseña: ' . $query->error);
            }
    
            $query->close();
        } catch (Exception $e) {
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
        $query->bind_result($id, $id_rol, $usuario, $contrasenia, $primerApellido, $segundoApellido, $primerNombre, $segundoNombre, $idTipoDocumento, $numeroDocumento, $id_tipo_asociado, $activo, $primerIngreso, $creadoEl, $actualizadoEl);
        $user = null;
        if ($query->fetch()) {
            $user = new Usuario($id, $id_rol, $usuario, $contrasenia, $primerApellido, $segundoApellido, $primerNombre, $segundoNombre, $idTipoDocumento, $numeroDocumento, $id_tipo_asociado, $activo, $primerIngreso, $creadoEl, $actualizadoEl);
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
        $query->bind_result($id, $id_rol, $usuario, $contrasenia, $primerApellido, $segundoApellido, $primerNombre, $segundoNombre, $idTipoDocumento, $numeroDocumento, $id_tipo_asociado, $activo, $primerIngreso, $creadoEl, $actualizadoEl);
        $user = null;
        if ($query->fetch()) {
            $user = new Usuario($id, $id_rol, $usuario, $contrasenia, $primerApellido, $segundoApellido, $primerNombre, $segundoNombre, $idTipoDocumento, $numeroDocumento, $id_tipo_asociado, $activo, $primerIngreso, $creadoEl, $actualizadoEl);
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

    public static function obtenerConPaginacion($page, $size, $idRol, $search = null) {
        $db = getDB();
        $offset = ($page - 1) * $size;
    
        $query = "SELECT * FROM usuarios WHERE id_rol = ?";
    
        if (!empty($search)) {
            $query .= " AND (usuario LIKE ?
                            OR primer_apellido LIKE ?
                            OR segundo_apellido LIKE ?
                            OR primer_nombre LIKE ?
                            OR segundo_nombre LIKE ?
                            OR numero_documento LIKE ?
                            OR CONCAT(primer_apellido, ' ', segundo_apellido, ' ', primer_nombre, ' ', segundo_nombre) LIKE ?
                            OR CONCAT(primer_nombre, ' ', segundo_nombre, ' ', primer_apellido, ' ', segundo_apellido) LIKE ?
                            OR CONCAT(primer_nombre, ' ', primer_apellido) LIKE ?
                            OR CONCAT(primer_nombre, ' ', segundo_apellido) LIKE ?
                            OR CONCAT(primer_apellido, ' ', primer_nombre) LIKE ? 
                            OR CONCAT(segundo_apellido, ' ', primer_nombre, ' ', primer_apellido) LIKE ?
                            OR CONCAT(primer_apellido, ' ', segundo_apellido) LIKE ?
                            OR CONCAT(primer_nombre, ' ', segundo_nombre) LIKE ?
                            OR CONCAT(primer_apellido, ' ', primer_nombre, ' ', segundo_nombre) LIKE ?
                            OR CONCAT(primer_nombre, ' ', primer_apellido, ' ', segundo_apellido) LIKE ?
                        )";
        }
    
        $query .= " LIMIT ? OFFSET ?";
    
        $stmt = $db->prepare($query);
    
        if ($stmt === false) {
            die('Error en la preparación de la consulta: ' . $db->error);
        }
    
        if (!empty($search)) {
            $searchParam = "%" . $search . "%";
            $stmt->bind_param('issssssssssssssssii', $idRol, $searchParam, $searchParam, $searchParam, $searchParam, $searchParam, $searchParam, $searchParam, $searchParam, $searchParam,
                $searchParam, $searchParam, $searchParam, $searchParam, $searchParam, $searchParam, $searchParam, $size, $offset);
        } else {
            $stmt->bind_param('iii', $idRol, $size, $offset);
        }
    
        $stmt->execute();
    
        if ($stmt->errno) {
            die('Error en la ejecución de la consulta: ' . $stmt->error);
        }
    
        $result = $stmt->get_result();
        $usuarios = [];
    
        while ($row = $result->fetch_assoc()) {
            $usuarios[] = new Usuario($row['id'], $row['id_rol'], $row['usuario'], null, $row['primer_apellido'], $row['segundo_apellido'], $row['primer_nombre'], $row['segundo_nombre'], $row['id_tipo_documento'], $row['numero_documento'], $row['id_tipo_asociado'], $row['activo'], null, $row['creado_el'], $row['actualizado_el']);
        }
    
        $countQuery = "SELECT COUNT(*) as total FROM usuarios WHERE id_rol = ?";
    
        if (!empty($search)) {
            $countQuery .= " AND (usuario LIKE ?
                                OR primer_apellido LIKE ?
                                OR segundo_apellido LIKE ?
                                OR primer_nombre LIKE ?
                                OR segundo_nombre LIKE ?
                                OR numero_documento LIKE ?
                                OR CONCAT(primer_apellido, ' ', segundo_apellido, ' ', primer_nombre, ' ', segundo_nombre) LIKE ?
                                OR CONCAT(primer_nombre, ' ', segundo_nombre, ' ', primer_apellido, ' ', segundo_apellido) LIKE ?
                                OR CONCAT(primer_nombre, ' ', primer_apellido) LIKE ?
                                OR CONCAT(primer_nombre, ' ', segundo_apellido) LIKE ? 
                                OR CONCAT(primer_apellido, ' ', primer_nombre) LIKE ? 
                                OR CONCAT(segundo_apellido, ' ', primer_nombre, ' ', primer_apellido) LIKE ?
                                OR CONCAT(primer_apellido, ' ', segundo_apellido) LIKE ?
                                OR CONCAT(primer_nombre, ' ', segundo_nombre) LIKE ?
                                OR CONCAT(primer_apellido, ' ', primer_nombre, ' ', segundo_nombre) LIKE ?
                                OR CONCAT(primer_nombre, ' ', primer_apellido, ' ', segundo_apellido) LIKE ?
                            )";
        }
    
        $countStmt = $db->prepare($countQuery);
    
        if ($countStmt === false) {
            die('Error en la preparación de la consulta de conteo: ' . $db->error);
        }
    
        if (!empty($search)) {
            $countStmt->bind_param('issssssssssssssss', $idRol, $searchParam, $searchParam, $searchParam, $searchParam, $searchParam, $searchParam, $searchParam, $searchParam,
                $searchParam, $searchParam, $searchParam, $searchParam, $searchParam, $searchParam, $searchParam, $searchParam);
        } else {
            $countStmt->bind_param('i', $idRol);
        }
    
        $countStmt->execute();
    
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

    public static function buscarPorUsuario($usuario) {
        $db = getDB();
        $query = $db->prepare("SELECT * FROM usuarios WHERE usuario = ? AND activo = 1");
        $query->bind_param("s", $usuario);
        $query->execute();
        $query->bind_result($id, $id_rol, $usuario, $contrasenia, $primerApellido, $segundoApellido, $primerNombre, $segundoNombre, $idTipoDocumento, $numeroDocumento, $id_tipo_asociado, $activo, $primerIngreso, $creadoEl, $actualizadoEl);
        $usuarioObj = null;
        if ($query->fetch()) {
            $usuarioObj = new Usuario($id, $id_rol, $usuario, $contrasenia, $primerApellido, $segundoApellido, $primerNombre, $segundoNombre, $idTipoDocumento, $numeroDocumento, $id_tipo_asociado, $activo, $primerIngreso, $creadoEl, $actualizadoEl);
        }
        $query->close();
        $db->close();
        return $usuarioObj;
    }

    public static function obtenerFechasActualizacionPorUsuarios() {
        $db = getDB();
        $query = "
            SELECT u.id AS idUsuario, 
                u.numero_documento AS numeroDocumento,
                CONCAT(IFNULL(u.primer_nombre, ''), ' ', IFNULL(u.primer_apellido, '')) AS nombre,
                MAX(IFNULL(fechas.fecha_actualizacion, '0000-00-00')) AS fechaUltimaActualizacion
            FROM usuarios u
            LEFT JOIN (
                SELECT id_usuario, MAX(actualizado_el) AS fecha_actualizacion FROM personas_naturales GROUP BY id_usuario
                UNION
                SELECT id_usuario, MAX(actualizado_el) AS fecha_actualizacion FROM informacion_financiera GROUP BY id_usuario
                UNION
                SELECT id_usuario, MAX(actualizado_el) AS fecha_actualizacion FROM informacion_nucleo_familiar GROUP BY id_usuario
                UNION
                SELECT id_usuario, MAX(actualizado_el) AS fecha_actualizacion FROM referencias_personales_comerciales_bancarias GROUP BY id_usuario
                UNION
                SELECT id_usuario, MAX(actualizado_el) AS fecha_actualizacion FROM operaciones_internacionales GROUP BY id_usuario
                UNION
                SELECT id_usuario, MAX(actualizado_el) AS fecha_actualizacion FROM personas_expuestas_publicamente GROUP BY id_usuario
            ) AS fechas ON u.id = fechas.id_usuario
            WHERE u.id_rol != 1
            GROUP BY u.id
        ";
    
        $result = $db->query($query);
        $usuarios = [];
    
        $timezoneColombia = new DateTimeZone('America/Bogota');

        while ($row = $result->fetch_assoc()) {
            $fechaServidor = new DateTime($row['fechaUltimaActualizacion']);
            $fechaServidor->setTimezone($timezoneColombia);

            $fechaServidor->modify('+1 hour');
            
            $usuarios[] = [
                'id' => $row['idUsuario'],
                'numeroDocumento' => $row['numeroDocumento'],
                'nombre' => $row['nombre'],
                'fechaUltimaActualizacion' => $fechaServidor->format('Y-m-d H:i:s'),
            ];
        }
    
        $db->close();
        return $usuarios;
    }
}
?>