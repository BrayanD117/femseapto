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

    public static function obtenerDatosCompletoUsuarios($fechaInicio, $fechaFin)
    {
        $db = getDB();

        $query = "
            SELECT 
                u.id AS idUsuario,
                u.numero_documento AS numeroDocumento,
                u.primer_nombre AS primerNombre,
                u.segundo_nombre AS segundoNombre,
                u.primer_apellido AS primerApellido,
                u.segundo_apellido AS segundoApellido,
                MAX(pn.id_genero) AS idGenero,
                MAX(pn.fecha_expedicion_doc) AS fechaExpedicionDoc,
                MAX(pn.id_dpto_exp_doc) AS idDptoExpDoc,
                MAX(pn.mpio_expedicion_doc) AS mpioExpedicionDoc,
                MAX(pn.fecha_nacimiento) AS fechaNacimiento,
                MAX(pn.pais_nacimiento) AS paisNacimiento,
                MAX(pn.id_dpto_nac) AS idDptoNac,
                MAX(pn.mpio_nacimiento) AS mpioNacimiento,
                MAX(pn.otro_lugar_nacimiento) AS otroLugarNacimiento,
                MAX(pn.id_dpto_residencia) AS idDptoResidencia,
                MAX(pn.mpio_residencia) AS mpioResidencia,
                MAX(pn.id_zona_residencia) AS idZonaResidencia,
                MAX(pn.id_tipo_vivienda) AS idTipoVivienda,
                MAX(pn.estrato) AS estrato,
                MAX(pn.direccion_residencia) AS direccionResidencia,
                MAX(pn.antiguedad_vivienda) AS antiguedadVivienda,
                MAX(pn.id_estado_civil) AS idEstadoCivil,
                MAX(pn.cabeza_familia) AS cabezaFamilia,
                MAX(pn.personas_a_cargo) AS personasACargo,
                MAX(pn.tiene_hijos) AS tieneHijos,
                MAX(pn.numero_hijos) AS numeroHijos,
                MAX(pn.correo_electronico) AS correoElectronico,
                MAX(pn.telefono) AS telefono,
                MAX(pn.celular) AS celular,
                MAX(pn.telefono_oficina) AS telefonoOficina,
                MAX(pn.id_nivel_educativo) AS idNivelEducativo,
                MAX(pn.profesion) AS profesion,
                MAX(pn.ocupacion_oficio) AS ocupacionOficio,
                MAX(pn.id_empresa_labor) AS idEmpresaLabor,
                MAX(pn.id_tipo_contrato) AS idTipoContrato,
                MAX(pn.dependencia_empresa) AS dependenciaEmpresa,
                MAX(pn.cargo_ocupa) AS cargoOcupa,
                MAX(pn.jefe_inmediato) AS jefeInmediato,
                MAX(pn.antiguedad_empresa) AS antiguedadEmpresa,
                MAX(pn.meses_antiguedad_empresa) AS mesesAntiguedadEmpresa,
                MAX(pn.mes_sale_vacaciones) AS mesSaleVacaciones,
                MAX(pn.nombre_emergencia) AS nombreEmergencia,
                MAX(pn.numero_cedula_emergencia) AS numeroCedulaEmergencia,
                MAX(pn.numero_celular_emergencia) AS numeroCelularEmergencia,
                MAX(inf.nombre_banco) AS nombreBanco,
                MAX(inf.id_tipo_cuenta_banc) AS tipoCuentaBancaria,
                MAX(inf.numero_cuenta_banc) AS numeroCuentaBancaria,
                MAX(inf.ingresos_mensuales) AS ingresosMensuales,
                MAX(inf.prima_productividad) AS primaProductividad,
                MAX(inf.otros_ingresos_mensuales) AS otrosIngresosMensuales,
                MAX(inf.concepto_otros_ingresos_mens) AS conceptoOtrosIngresosMens,
                MAX(inf.total_ingresos_mensuales) AS totalIngresosMensuales,
                MAX(inf.egresos_mensuales) AS egresosMensuales,
                MAX(inf.obligacion_financiera) AS obligacionFinanciera,
                MAX(inf.otros_egresos_mensuales) AS otrosEgresosMensuales,
                MAX(inf.total_egresos_mensuales) AS totalEgresosMensuales,
                MAX(inf.total_activos) AS totalActivos,
                MAX(inf.total_pasivos) AS totalPasivos,
                MAX(inf.total_patrimonio) AS totalPatrimonio,
                JSON_ARRAYAGG(
                    JSON_OBJECT(
                        'nombreCompleto', nf.nombre_completo,
                        'parentesco', nf.id_parentesco,
                        'numeroDocumento', nf.numero_documento,
                        'tipoDocumento', nf.id_tipo_documento,
                        'genero', nf.id_genero,
                        'fechaNacimiento', nf.fecha_nacimiento,
                        'nivelEducativo', nf.id_nivel_educativo,
                        'trabaja', nf.trabaja,
                        'celular', nf.celular
                    )
                ) AS familiares,
                JSON_ARRAYAGG(
                    JSON_OBJECT(
                        'nombreRazonSocial', rpcb.nombre_razon_social,
                        'parentesco', rpcb.parentesco,
                        'direccion', rpcb.direccion,
                        'idDpto', rpcb.id_dpto,
                        'idMpio', rpcb.id_mpio,
                        'telefono', rpcb.telefono,
                        'correoElectronico', rpcb.correo_electronico
                    )
                ) AS referencias,
                JSON_ARRAYAGG(
                    JSON_OBJECT(
                        'transaccionesMonedaExtranjera', oi.transacciones_moneda_extranjera,
                        'transMonedaExtranjera', oi.trans_moneda_extranjera,
                        'otrasOperaciones', oi.otras_operaciones,
                        'cuentasMonedaExtranjera', oi.cuentas_moneda_extranjera,
                        'bancoCuentaExtranjera', oi.banco_cuenta_extranjera,
                        'cuentaMonedaExtranjera', oi.cuenta_moneda_extranjera,
                        'monedaCuenta', oi.moneda_cuenta,
                        'paisCuenta', oi.id_pais_cuenta,
                        'ciudadCuenta', oi.ciudad_cuenta
                    )
                ) AS operacionesInternacionales,
                (SELECT MAX(actualizado_el) FROM (
                    SELECT actualizado_el FROM personas_naturales WHERE id_usuario = u.id
                    UNION ALL
                    SELECT actualizado_el FROM informacion_financiera WHERE id_usuario = u.id
                    UNION ALL
                    SELECT actualizado_el FROM informacion_nucleo_familiar WHERE id_usuario = u.id
                    UNION ALL
                    SELECT actualizado_el FROM operaciones_internacionales WHERE id_usuario = u.id
                    UNION ALL
                    SELECT actualizado_el FROM personas_expuestas_publicamente WHERE id_usuario = u.id
                    UNION ALL
                    SELECT actualizado_el FROM referencias_personales_comerciales_bancarias WHERE id_usuario = u.id
                ) AS subquery) AS ultimaActualizacion
            FROM 
                usuarios u
            LEFT JOIN 
                personas_naturales pn ON u.id = pn.id_usuario
            LEFT JOIN 
                informacion_financiera inf ON u.id = inf.id_usuario
            LEFT JOIN 
                informacion_nucleo_familiar nf ON u.id = nf.id_usuario
            LEFT JOIN 
                operaciones_internacionales oi ON u.id = oi.id_usuario
            LEFT JOIN 
                referencias_personales_comerciales_bancarias rpcb ON u.id = rpcb.id_usuario
            WHERE 
                (SELECT MAX(actualizado_el) FROM (
                    SELECT actualizado_el FROM personas_naturales WHERE id_usuario = u.id
                    UNION ALL
                    SELECT actualizado_el FROM informacion_financiera WHERE id_usuario = u.id
                    UNION ALL
                    SELECT actualizado_el FROM informacion_nucleo_familiar WHERE id_usuario = u.id
                    UNION ALL
                    SELECT actualizado_el FROM operaciones_internacionales WHERE id_usuario = u.id
                    UNION ALL
                    SELECT actualizado_el FROM personas_expuestas_publicamente WHERE id_usuario = u.id
                    UNION ALL
                    SELECT actualizado_el FROM referencias_personales_comerciales_bancarias WHERE id_usuario = u.id
                ) AS subquery) BETWEEN ? AND ?
            GROUP BY 
                u.id;
        ";

        $stmt = $db->prepare($query);
        $stmt->bind_param("ss", $fechaInicio, $fechaFin);
        $stmt->execute();
        $result = $stmt->get_result();

        $usuarios = [];
        while ($row = $result->fetch_assoc()) {
            $usuarios[] = [
                'id' => $row['idUsuario'],
                'numeroDocumento' => $row['numeroDocumento'],
                'primerNombre' => $row['primerNombre'],
                'segundoNombre' => $row['segundoNombre'],
                'primerApellido' => $row['primerApellido'],
                'segundoApellido' => $row['segundoApellido'],
                'idGenero' => $row['idGenero'],
                'fechaExpedicionDoc' => $row['fechaExpedicionDoc'],
                'idDptoExpDoc' => $row['idDptoExpDoc'],
                'mpioExpedicionDoc' => $row['mpioExpedicionDoc'],
                'fechaNacimiento' => $row['fechaNacimiento'],
                'paisNacimiento' => $row['paisNacimiento'],
                'idDptoNac' => $row['idDptoNac'],
                'mpioNacimiento' => $row['mpioNacimiento'],
                'otroLugarNacimiento' => $row['otroLugarNacimiento'],
                'idDptoResidencia' => $row['idDptoResidencia'],
                'mpioResidencia' => $row['mpioResidencia'],
                'idZonaResidencia' => $row['idZonaResidencia'],
                'idTipoVivienda' => $row['idTipoVivienda'],
                'estrato' => $row['estrato'],
                'direccionResidencia' => $row['direccionResidencia'],
                'antiguedadVivienda' => $row['antiguedadVivienda'],
                'idEstadoCivil' => $row['idEstadoCivil'],
                'cabezaFamilia' => $row['cabezaFamilia'],
                'personasACargo' => $row['personasACargo'],
                'tieneHijos' => $row['tieneHijos'],
                'numeroHijos' => $row['numeroHijos'],
                'correoElectronico' => $row['correoElectronico'],
                'telefono' => $row['telefono'],
                'celular' => $row['celular'],
                'telefonoOficina' => $row['telefonoOficina'],
                'idNivelEducativo' => $row['idNivelEducativo'],
                'profesion' => $row['profesion'],
                'ocupacionOficio' => $row['ocupacionOficio'],
                'idEmpresaLabor' => $row['idEmpresaLabor'],
                'idTipoContrato' => $row['idTipoContrato'],
                'dependenciaEmpresa' => $row['dependenciaEmpresa'],
                'cargoOcupa' => $row['cargoOcupa'],
                'jefeInmediato' => $row['jefeInmediato'],
                'antiguedadEmpresa' => $row['antiguedadEmpresa'],
                'mesesAntiguedadEmpresa' => $row['mesesAntiguedadEmpresa'],
                'mesSaleVacaciones' => $row['mesSaleVacaciones'],
                'nombreEmergencia' => $row['nombreEmergencia'],
                'numeroCedulaEmergencia' => $row['numeroCedulaEmergencia'],
                'numeroCelularEmergencia' => $row['numeroCelularEmergencia'],
                'nombreBanco' => $row['nombreBanco'],
                'tipoCuentaBancaria' => $row['tipoCuentaBancaria'],
                'numeroCuentaBancaria' => $row['numeroCuentaBancaria'],
                'ingresosMensuales' => $row['ingresosMensuales'],
                'primaProductividad' => $row['primaProductividad'],
                'otrosIngresosMensuales' => $row['otrosIngresosMensuales'],
                'conceptoOtrosIngresosMens' => $row['conceptoOtrosIngresosMens'],
                'totalIngresosMensuales' => $row['totalIngresosMensuales'],
                'egresosMensuales' => $row['egresosMensuales'],
                'obligacionFinanciera' => $row['obligacionFinanciera'],
                'otrosEgresosMensuales' => $row['otrosEgresosMensuales'],
                'totalEgresosMensuales' => $row['totalEgresosMensuales'],
                'totalActivos' => $row['totalActivos'],
                'totalPasivos' => $row['totalPasivos'],
                'totalPatrimonio' => $row['totalPatrimonio'],
                'familiares' => json_decode($row['familiares'], true),
                'referencias' => json_decode($row['referencias'], true),
                'operacionesInternacionales' => json_decode($row['operacionesInternacionales'], true),
                'ultimaActualizacion' => $row['ultimaActualizacion'],
            ];            
        }

        $stmt->close();
        $db->close();

        return $usuarios;
    }
}
?>