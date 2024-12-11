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
    public $perfilActualizadoEl;

    public function __construct($id = null, $id_rol = null, $usuario = '', $contrasenia = '',
    $primerApellido = '', $segundoApellido = null, $primerNombre = '', $segundoNombre = null, $idTipoDocumento = '', $numeroDocumento = '', $id_tipo_asociado = '', $activo = null, $primerIngreso = null, $creadoEl = '', $actualizadoEl = '', $perfilActualizadoEl = '') {
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
        $this->perfilActualizadoEl = $perfilActualizadoEl;
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
        $query = $db->prepare(
            "SELECT
                id,
                id_rol,
                usuario,
                contrasenia,
                primer_apellido,
                segundo_apellido,
                primer_nombre,
                segundo_nombre,
                id_tipo_documento,
                numero_documento,
                id_tipo_asociado,
                activo,
                primer_ingreso,
                DATE_FORMAT(CONVERT_TZ(creado_el, '+00:00', '-05:00'), '%d/%m/%Y %H:%i:%s') AS creado_el,
                DATE_FORMAT(CONVERT_TZ(actualizado_el, '+00:00', '-05:00'), '%d/%m/%Y %H:%i:%s') AS actualizado_el
            FROM usuarios
            WHERE id = ?");
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
        $query = $db->prepare(
            "SELECT
                id,
                id_rol,
                usuario,
                contrasenia,
                primer_apellido,
                segundo_apellido,
                primer_nombre,
                segundo_nombre,
                id_tipo_documento,
                numero_documento,
                id_tipo_asociado,
                activo,
                primer_ingreso,
                DATE_FORMAT(CONVERT_TZ(creado_el, '+00:00', '-05:00'), '%d/%m/%Y %H:%i:%s') AS creado_el,
                DATE_FORMAT(CONVERT_TZ(actualizado_el, '+00:00', '-05:00'), '%d/%m/%Y %H:%i:%s') AS actualizado_el
            FROM usuarios WHERE numero_documento = ?");
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
        $query = "SELECT
                    id,
                    id_rol,
                    usuario,
                    contrasenia,
                    primer_apellido,
                    segundo_apellido,
                    primer_nombre,
                    segundo_nombre,
                    id_tipo_documento,
                    numero_documento,
                    id_tipo_asociado,
                    activo,
                    primer_ingreso,
                    DATE_FORMAT(CONVERT_TZ(creado_el, '+00:00', '-05:00'), '%d/%m/%Y %H:%i:%s') AS creado_el,
                    DATE_FORMAT(CONVERT_TZ(actualizado_el, '+00:00', '-05:00'), '%d/%m/%Y %H:%i:%s') AS actualizado_el
                FROM usuarios";
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
    
        $query = "SELECT
                    id,
                    id_rol,
                    usuario,
                    contrasenia,
                    primer_apellido,
                    segundo_apellido,
                    primer_nombre,
                    segundo_nombre,
                    id_tipo_documento,
                    numero_documento,
                    id_tipo_asociado,
                    activo,
                    primer_ingreso,
                    DATE_FORMAT(CONVERT_TZ(creado_el, '+00:00', '-05:00'), '%d/%m/%Y %H:%i:%s') AS creado_el,
                    DATE_FORMAT(CONVERT_TZ(actualizado_el, '+00:00', '-05:00'), '%d/%m/%Y %H:%i:%s') AS actualizado_el
                FROM usuarios
                WHERE id_rol = ?";
    
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
        $query = $db->prepare(
            "SELECT
                id,
                id_rol,
                usuario,
                contrasenia,
                primer_apellido,
                segundo_apellido,
                primer_nombre,
                segundo_nombre,
                id_tipo_documento,
                numero_documento,
                id_tipo_asociado,
                activo,
                primer_ingreso,
                DATE_FORMAT(CONVERT_TZ(creado_el, '+00:00', '-05:00'), '%d/%m/%Y %H:%i:%s') AS creado_el,
                DATE_FORMAT(CONVERT_TZ(actualizado_el, '+00:00', '-05:00'), '%d/%m/%Y %H:%i:%s') AS actualizado_el
            FROM usuarios
            WHERE usuario = ?
            AND activo = 1");
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
            SELECT 
                u.id AS idUsuario, 
                u.numero_documento AS numeroDocumento,
                CONCAT(IFNULL(u.primer_nombre, ''), ' ', IFNULL(u.primer_apellido, '')) AS nombre,
                IFNULL(u.perfil_actualizado_el, '0000-00-00 00:00:00') AS fechaUltimaActualizacion
            FROM usuarios u
            WHERE u.id_rol NOT IN (1, 3)
            ORDER BY u.perfil_actualizado_el DESC
        ";
        
        $result = $db->query($query);
        $usuarios = [];
    
        while ($row = $result->fetch_assoc()) {
            $fecha = $row['fechaUltimaActualizacion'] !== '0000-00-00 00:00:00'
                ? DateTime::createFromFormat('Y-m-d H:i:s', $row['fechaUltimaActualizacion'])->format('d/m/Y H:i:s')
                : 'No ha actualizado';
    
            $usuarios[] = [
                'id' => $row['idUsuario'],
                'numeroDocumento' => $row['numeroDocumento'],
                'nombre' => $row['nombre'],
                'fechaUltimaActualizacion' => $fecha,
            ];
        }
        
        $db->close();
        return $usuarios;
    }        

    public static function actualizarPerfilActualizadoEl($idUsuario) {
        $db = getDB();
        try {
            $query = $db->prepare("UPDATE usuarios SET perfil_actualizado_el = ? WHERE id = ?");
            $fechaActual = (new DateTime('now', new DateTimeZone('America/Bogota')))->format('Y-m-d H:i:s');
            $query->bind_param("si", $fechaActual, $idUsuario);
            $query->execute();
    
            if ($query->error) {
                throw new Exception('Error al actualizar perfilActualizadoEl: ' . $query->error);
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

    public static function obtenerDatosCompletoUsuarios($fechaInicio, $fechaFin)
    {
        $db = getDB();

        $db->query("SET lc_time_names = 'es_ES';");

        $query = "
            SELECT 
                u.id AS idUsuario,
                u.numero_documento AS numeroDocumento,
                u.primer_nombre AS primerNombre,
                u.segundo_nombre AS segundoNombre,
                u.primer_apellido AS primerApellido,
                u.segundo_apellido AS segundoApellido,
                DATE_FORMAT(MAX(u.perfil_actualizado_el), '%d del mes de %M del año %Y') AS fechaActualizacion,
                MAX(g.nombre) AS generoNombre,
                MAX(pn.id_genero) AS idGenero,
                DATE_FORMAT(MAX(pn.fecha_expedicion_doc), '%d/%m/%Y') AS fechaExpedicionDoc,
                MAX(deExp.nombre) AS nombreDptoExpDoc,
                MAX(muExp.nombre) AS nombreMpioExpDoc,
                DATE_FORMAT(MAX(pn.fecha_nacimiento), '%d/%m/%Y') AS fechaNacimiento,
                MAX(paNac.nombre) AS nombrePaisNacimiento,
                MAX(deNac.nombre) AS nombreDptoNac,
                MAX(muNac.nombre) AS nombreMpioNac,
                MAX(pn.otro_lugar_nacimiento) AS otroLugarNacimiento,
                MAX(deRes.nombre) AS nombreDptoResidencia,
                MAX(muRes.nombre) AS nombreMpioResidencia,
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
                MAX(ne.nombre) AS nombreNivelEducativo,
                MAX(pn.profesion) AS profesion,
                MAX(pn.ocupacion_oficio) AS ocupacionOficio,
                MAX(e.nombre) AS nombreEmpresaLabor,
                MAX(e.nit) AS nitEmpresa,
                MAX(e.direccion) AS direccionEmpresa,
                MAX(muEmp.nombre) AS municipioEmpresa,
                MAX(e.telefono) AS telefonoEmpresa,
                MAX(e.fax) AS faxEmpresa,
                MAX(e.actividad_economica) AS actividadEconomicaEmpresa,
                MAX(e.ciiu) AS ciiuEmpresa,
                MAX(te.nombre) AS tipoEmpresa,
                MAX(pn.id_tipo_contrato) AS idTipoContrato,
                MAX(tc.nombre) AS tipoContratoNombre,
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
                MAX(zg.nombre) AS zonaGeografica,
                MAX(tv.nombre) AS tipoVivienda,
                MAX(ec.nombre) AS estadoCivil,
                MAX(par.nombre) AS parentescoNombre,
                MAX(td.abreviatura) AS tipoDocumentoFamiliar,
                MAX(nef.nombre) AS nivelEducativoFamiliar,
                MAX(gf.nombre) AS generoFamiliar,
                MAX(tme.transacciones_moneda_extranjera) AS transaccionesMonedaExtranjera,
                MAX(tme.trans_moneda_extranjera) AS monedaTransaccion,
                MAX(tme.otras_operaciones) AS otrasOperaciones,
                MAX(tme.cuentas_moneda_extranjera) AS cuentaExtranjera,
                MAX(tme.banco_cuenta_extranjera) AS bancoExtranjera,
                MAX(tme.cuenta_moneda_extranjera) AS numeroCuentaExtranjera,
                MAX(tme.moneda_cuenta) AS monedaCuenta,
                MAX(p.nombre) AS paisCuenta,
                MAX(tme.ciudad_cuenta) AS ciudadCuenta,
                MAX(pep.poder_publico) AS poderPublico,
                MAX(pep.maneja_rec_public) AS manejaRecursosPublicos,
                MAX(pep.reconoc_public) AS reconocimientoPublico,
                MAX(pep.funciones_publicas) AS funcionesPublicas,
                MAX(pep.actividad_publica) AS actividadPublica,
                MAX(pep.funcion_publico_extranjero) AS funcionPublicoExtranjero,
                MAX(pep.fam_funcion_publico) AS familiarFuncionPublico,
                MAX(pep.socio_funcion_publico) AS socioFuncionPublico,
                (SELECT JSON_ARRAYAGG(
                    JSON_OBJECT(
                        'nombreCompleto', sub_nf.nombre_completo,
                        'parentesco', par.nombre,
                        'numeroDocumento', sub_nf.numero_documento,
                        'tipoDocumento', td.abreviatura,
                        'genero', gf.nombre,
                        'fechaNacimiento', DATE_FORMAT(sub_nf.fecha_nacimiento, '%d/%m/%Y'),
                        'nivelEducativo', nef.nombre,
                        'trabaja', sub_nf.trabaja,
                        'celular', sub_nf.celular
                    )
                )
                FROM (
                    SELECT DISTINCT nf.* 
                    FROM informacion_nucleo_familiar nf
                    WHERE nf.id_usuario = u.id
                ) AS sub_nf
                LEFT JOIN parentescos par ON sub_nf.id_parentesco = par.id
                LEFT JOIN tipos_documento td ON sub_nf.id_tipo_documento = td.id
                LEFT JOIN niveles_educativos nef ON sub_nf.id_nivel_educativo = nef.id
                LEFT JOIN generos gf ON sub_nf.id_genero = gf.id) AS familiares,
                (SELECT JSON_ARRAYAGG(
                    JSON_OBJECT(
                        'nombreRazonSocial', sub_rpcb.nombre_razon_social,
                        'abreviatura', tr.abreviatura,
                        'direccion', sub_rpcb.direccion,
                        'ciudad', m.nombre,
                        'telefono', sub_rpcb.telefono
                    )
                )
                FROM (
                    SELECT DISTINCT rpcb.* 
                    FROM referencias_personales_comerciales_bancarias rpcb
                    WHERE rpcb.id_usuario = u.id
                    LIMIT 2
                ) AS sub_rpcb
                LEFT JOIN tipos_referencia tr ON sub_rpcb.id_tipo_referencia = tr.id
                LEFT JOIN municipios m ON sub_rpcb.id_mpio = m.id) AS referencias,
                (SELECT JSON_ARRAYAGG(
                    JSON_OBJECT(
                        'idMedioComunicacion', sub_usu_com.id_medio_comunicacion
                    )
                )
                FROM (
                    SELECT DISTINCT id_medio_comunicacion
                    FROM usuarios_comunicacion
                    WHERE id_usuario = u.id
                ) AS sub_usu_com) AS mediosComunicacion
            FROM 
                usuarios u
            LEFT JOIN 
                personas_naturales pn ON u.id = pn.id_usuario
            LEFT JOIN 
                generos g ON pn.id_genero = g.id
            LEFT JOIN 
                departamentos deExp ON pn.id_dpto_exp_doc = deExp.id
            LEFT JOIN 
                municipios muExp ON pn.mpio_expedicion_doc = muExp.id
            LEFT JOIN 
                paises paNac ON pn.pais_nacimiento = paNac.id
            LEFT JOIN 
                departamentos deNac ON pn.id_dpto_nac = deNac.id
            LEFT JOIN 
                municipios muNac ON pn.mpio_nacimiento = muNac.id
            LEFT JOIN 
                departamentos deRes ON pn.id_dpto_residencia = deRes.id
            LEFT JOIN 
                municipios muRes ON pn.mpio_residencia = muRes.id
            LEFT JOIN 
                empresas e ON pn.id_empresa_labor = e.id
            LEFT JOIN 
                tipos_empresa te ON e.id_tipo_empresa = te.id
            LEFT JOIN 
                municipios muEmp ON e.id_municipio = muEmp.id
            LEFT JOIN 
                tipos_contrato tc ON pn.id_tipo_contrato = tc.id
            LEFT JOIN 
                niveles_educativos ne ON pn.id_nivel_educativo = ne.id
            LEFT JOIN 
                informacion_financiera inf ON u.id = inf.id_usuario
            LEFT JOIN 
                informacion_nucleo_familiar nf ON u.id = nf.id_usuario
            LEFT JOIN 
                tipos_documento td ON nf.id_tipo_documento = td.id
            LEFT JOIN 
                niveles_educativos nef ON nf.id_nivel_educativo = nef.id
            LEFT JOIN 
                generos gf ON nf.id_genero = gf.id
            LEFT JOIN 
                referencias_personales_comerciales_bancarias rpcb ON u.id = rpcb.id_usuario
            LEFT JOIN 
                zonas_geograficas zg ON pn.id_zona_residencia = zg.id
            LEFT JOIN 
                tipos_vivienda tv ON pn.id_tipo_vivienda = tv.id
            LEFT JOIN 
                estados_civiles ec ON pn.id_estado_civil = ec.id
            LEFT JOIN 
                parentescos par ON nf.id_parentesco = par.id
            LEFT JOIN 
                operaciones_internacionales tme ON tme.id_usuario = u.id
            LEFT JOIN 
                paises p ON tme.id_pais_cuenta = p.id
            LEFT JOIN 
                personas_expuestas_publicamente pep ON pep.id_usuario = u.id
            WHERE 
                (SELECT MAX(actualizado_el) FROM (
                    SELECT CONVERT_TZ(actualizado_el, '+00:00', '-05:00') AS actualizado_el FROM personas_naturales WHERE id_usuario = u.id
                    UNION ALL
                    SELECT CONVERT_TZ(actualizado_el, '+00:00', '-05:00') AS actualizado_el FROM informacion_financiera WHERE id_usuario = u.id
                    UNION ALL
                    SELECT CONVERT_TZ(actualizado_el, '+00:00', '-05:00') AS actualizado_el FROM informacion_nucleo_familiar WHERE id_usuario = u.id
                    UNION ALL
                    SELECT CONVERT_TZ(actualizado_el, '+00:00', '-05:00') AS actualizado_el FROM referencias_personales_comerciales_bancarias WHERE id_usuario = u.id
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
                'fechaActualizacion' => $row['fechaActualizacion'],
                'generoNombre' => $row['generoNombre'],
                'fechaExpedicionDoc' => $row['fechaExpedicionDoc'],
                'nombreDptoExpDoc' => $row['nombreDptoExpDoc'],
                'nombreMpioExpDoc' => $row['nombreMpioExpDoc'],
                'fechaNacimiento' => $row['fechaNacimiento'],
                'nombrePaisNacimiento' => $row['nombrePaisNacimiento'],
                'nombreDptoNac' => $row['nombreDptoNac'],
                'nombreMpioNac' => $row['nombreMpioNac'],
                'otroLugarNacimiento' => $row['otroLugarNacimiento'],
                'nombreDptoResidencia' => $row['nombreDptoResidencia'],
                'nombreMpioResidencia' => $row['nombreMpioResidencia'],
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
                'nombreNivelEducativo' => $row['nombreNivelEducativo'],
                'profesion' => $row['profesion'],
                'ocupacionOficio' => $row['ocupacionOficio'],
                'nombreEmpresaLabor' => $row['nombreEmpresaLabor'],
                'nitEmpresa' => $row['nitEmpresa'],
                'direccionEmpresa' => $row['direccionEmpresa'],
                'municipioEmpresa' => $row['municipioEmpresa'],
                'telefonoEmpresa' => $row['telefonoEmpresa'],
                'faxEmpresa' => $row['faxEmpresa'],
                'actividadEconomicaEmpresa' => $row['actividadEconomicaEmpresa'],
                'ciiuEmpresa' => $row['ciiuEmpresa'],
                'tipoEmpresa' => $row['tipoEmpresa'],
                'idTipoContrato' => $row['idTipoContrato'],
                'tipoContratoNombre' => $row['tipoContratoNombre'],
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
                'parentescoNombre' => $row['parentescoNombre'],
                'zonaGeografica' => $row['zonaGeografica'],
                'tipoVivienda' => $row['tipoVivienda'],
                'estadoCivil' => $row['estadoCivil'],
                'transaccionesMonedaExtranjera' => $row['transaccionesMonedaExtranjera'],
                'monedaTransaccion' => $row['monedaTransaccion'],
                'otrasOperaciones' => $row['otrasOperaciones'],
                'cuentaExtranjera' => $row['cuentaExtranjera'],
                'bancoExtranjera' => $row['bancoExtranjera'],
                'numeroCuentaExtranjera' => $row['numeroCuentaExtranjera'],
                'monedaCuenta' => $row['monedaCuenta'],
                'paisCuenta' => $row['paisCuenta'],
                'ciudadCuenta' => $row['ciudadCuenta'],
                'poderPublico' => $row['poderPublico'],
                'manejaRecursosPublicos' => $row['manejaRecursosPublicos'],
                'reconocimientoPublico' => $row['reconocimientoPublico'],
                'funcionesPublicas' => $row['funcionesPublicas'],
                'actividadesPublicas' => $row['actividadPublica'],
                'funcionPublicoExtranjero' => $row['funcionPublicoExtranjero'],
                'familiarFuncionPublico' => $row['familiarFuncionPublico'],
                'socioFuncionPublico' => $row['socioFuncionPublico'],
                'familiares' => json_decode($row['familiares'], true),
                'referencias' => json_decode($row['referencias'], true),
                'mediosComunicacion' => json_decode($row['mediosComunicacion'], true)  
            ];
        }

        $stmt->close();
        $db->close();

        return $usuarios;
    }

    public static function obtenerDatosCompletosPorNumeroDocumento($numeroDocumento)
    {
        $db = getDB();

        $db->query("SET lc_time_names = 'es_ES';");

        $query = "
            SELECT 
                u.id AS idUsuario,
                u.numero_documento AS numeroDocumento,
                u.primer_nombre AS primerNombre,
                u.segundo_nombre AS segundoNombre,
                u.primer_apellido AS primerApellido,
                u.segundo_apellido AS segundoApellido,
                DATE_FORMAT(MAX(u.perfil_actualizado_el), '%d del mes de %M del año %Y') AS fechaActualizacion,
                MAX(g.nombre) AS generoNombre,
                MAX(pn.id_genero) AS idGenero,
                DATE_FORMAT(MAX(pn.fecha_expedicion_doc), '%d/%m/%Y') AS fechaExpedicionDoc,
                MAX(deExp.nombre) AS nombreDptoExpDoc,
                MAX(muExp.nombre) AS nombreMpioExpDoc,
                DATE_FORMAT(MAX(pn.fecha_nacimiento), '%d/%m/%Y') AS fechaNacimiento,
                MAX(paNac.nombre) AS nombrePaisNacimiento,
                MAX(deNac.nombre) AS nombreDptoNac,
                MAX(muNac.nombre) AS nombreMpioNac,
                MAX(pn.otro_lugar_nacimiento) AS otroLugarNacimiento,
                MAX(deRes.nombre) AS nombreDptoResidencia,
                MAX(muRes.nombre) AS nombreMpioResidencia,
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
                MAX(ne.nombre) AS nombreNivelEducativo,
                MAX(pn.profesion) AS profesion,
                MAX(pn.ocupacion_oficio) AS ocupacionOficio,
                MAX(e.nombre) AS nombreEmpresaLabor,
                MAX(e.nit) AS nitEmpresa,
                MAX(e.direccion) AS direccionEmpresa,
                MAX(muEmp.nombre) AS municipioEmpresa,
                MAX(e.telefono) AS telefonoEmpresa,
                MAX(e.fax) AS faxEmpresa,
                MAX(e.actividad_economica) AS actividadEconomicaEmpresa,
                MAX(e.ciiu) AS ciiuEmpresa,
                MAX(te.nombre) AS tipoEmpresa,
                MAX(pn.id_tipo_contrato) AS idTipoContrato,
                MAX(tc.nombre) AS tipoContratoNombre,
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
                MAX(zg.nombre) AS zonaGeografica,
                MAX(tv.nombre) AS tipoVivienda,
                MAX(ec.nombre) AS estadoCivil,
                MAX(par.nombre) AS parentescoNombre,
                MAX(td.abreviatura) AS tipoDocumentoFamiliar,
                MAX(nef.nombre) AS nivelEducativoFamiliar,
                MAX(gf.nombre) AS generoFamiliar,
                MAX(tme.transacciones_moneda_extranjera) AS transaccionesMonedaExtranjera,
                MAX(tme.trans_moneda_extranjera) AS monedaTransaccion,
                MAX(tme.otras_operaciones) AS otrasOperaciones,
                MAX(tme.cuentas_moneda_extranjera) AS cuentaExtranjera,
                MAX(tme.banco_cuenta_extranjera) AS bancoExtranjera,
                MAX(tme.cuenta_moneda_extranjera) AS numeroCuentaExtranjera,
                MAX(tme.moneda_cuenta) AS monedaCuenta,
                MAX(p.nombre) AS paisCuenta,
                MAX(tme.ciudad_cuenta) AS ciudadCuenta,
                MAX(pep.poder_publico) AS poderPublico,
                MAX(pep.maneja_rec_public) AS manejaRecursosPublicos,
                MAX(pep.reconoc_public) AS reconocimientoPublico,
                MAX(pep.funciones_publicas) AS funcionesPublicas,
                MAX(pep.actividad_publica) AS actividadPublica,
                MAX(pep.funcion_publico_extranjero) AS funcionPublicoExtranjero,
                MAX(pep.fam_funcion_publico) AS familiarFuncionPublico,
                MAX(pep.socio_funcion_publico) AS socioFuncionPublico,
                (SELECT JSON_ARRAYAGG(
                    JSON_OBJECT(
                        'nombreCompleto', sub_nf.nombre_completo,
                        'parentesco', par.nombre,
                        'numeroDocumento', sub_nf.numero_documento,
                        'tipoDocumento', td.abreviatura,
                        'genero', gf.nombre,
                        'fechaNacimiento', DATE_FORMAT(sub_nf.fecha_nacimiento, '%d/%m/%Y'),
                        'nivelEducativo', nef.nombre,
                        'trabaja', sub_nf.trabaja,
                        'celular', sub_nf.celular
                    )
                )
                FROM (
                    SELECT DISTINCT nf.* 
                    FROM informacion_nucleo_familiar nf
                    WHERE nf.id_usuario = u.id
                ) AS sub_nf
                LEFT JOIN parentescos par ON sub_nf.id_parentesco = par.id
                LEFT JOIN tipos_documento td ON sub_nf.id_tipo_documento = td.id
                LEFT JOIN niveles_educativos nef ON sub_nf.id_nivel_educativo = nef.id
                LEFT JOIN generos gf ON sub_nf.id_genero = gf.id) AS familiares,
                (SELECT JSON_ARRAYAGG(
                    JSON_OBJECT(
                        'nombreRazonSocial', sub_rpcb.nombre_razon_social,
                        'abreviatura', tr.abreviatura,
                        'direccion', sub_rpcb.direccion,
                        'ciudad', m.nombre,
                        'telefono', sub_rpcb.telefono
                    )
                )
                FROM (
                    SELECT DISTINCT rpcb.* 
                    FROM referencias_personales_comerciales_bancarias rpcb
                    WHERE rpcb.id_usuario = u.id
                    LIMIT 2
                ) AS sub_rpcb
                LEFT JOIN tipos_referencia tr ON sub_rpcb.id_tipo_referencia = tr.id
                LEFT JOIN municipios m ON sub_rpcb.id_mpio = m.id) AS referencias,
                (SELECT JSON_ARRAYAGG(
                    JSON_OBJECT(
                        'idMedioComunicacion', sub_usu_com.id_medio_comunicacion
                    )
                )
                FROM (
                    SELECT DISTINCT id_medio_comunicacion
                    FROM usuarios_comunicacion
                    WHERE id_usuario = u.id
                ) AS sub_usu_com) AS mediosComunicacion

            FROM 
                usuarios u
            LEFT JOIN 
                personas_naturales pn ON u.id = pn.id_usuario
            LEFT JOIN 
                generos g ON pn.id_genero = g.id
            LEFT JOIN 
                departamentos deExp ON pn.id_dpto_exp_doc = deExp.id
            LEFT JOIN 
                municipios muExp ON pn.mpio_expedicion_doc = muExp.id
            LEFT JOIN 
                paises paNac ON pn.pais_nacimiento = paNac.id
            LEFT JOIN 
                departamentos deNac ON pn.id_dpto_nac = deNac.id
            LEFT JOIN 
                municipios muNac ON pn.mpio_nacimiento = muNac.id
            LEFT JOIN 
                departamentos deRes ON pn.id_dpto_residencia = deRes.id
            LEFT JOIN 
                municipios muRes ON pn.mpio_residencia = muRes.id
            LEFT JOIN 
                empresas e ON pn.id_empresa_labor = e.id
            LEFT JOIN 
                tipos_empresa te ON e.id_tipo_empresa = te.id
            LEFT JOIN 
                municipios muEmp ON e.id_municipio = muEmp.id
            LEFT JOIN 
                tipos_contrato tc ON pn.id_tipo_contrato = tc.id
            LEFT JOIN 
                niveles_educativos ne ON pn.id_nivel_educativo = ne.id
            LEFT JOIN 
                informacion_financiera inf ON u.id = inf.id_usuario
            LEFT JOIN 
                informacion_nucleo_familiar nf ON u.id = nf.id_usuario
            LEFT JOIN 
                tipos_documento td ON nf.id_tipo_documento = td.id
            LEFT JOIN 
                niveles_educativos nef ON nf.id_nivel_educativo = nef.id
            LEFT JOIN 
                generos gf ON nf.id_genero = gf.id
            LEFT JOIN 
                referencias_personales_comerciales_bancarias rpcb ON u.id = rpcb.id_usuario
            LEFT JOIN 
                zonas_geograficas zg ON pn.id_zona_residencia = zg.id
            LEFT JOIN 
                tipos_vivienda tv ON pn.id_tipo_vivienda = tv.id
            LEFT JOIN 
                estados_civiles ec ON pn.id_estado_civil = ec.id
            LEFT JOIN 
                parentescos par ON nf.id_parentesco = par.id
            LEFT JOIN 
                operaciones_internacionales tme ON tme.id_usuario = u.id
            LEFT JOIN 
                paises p ON tme.id_pais_cuenta = p.id
            LEFT JOIN 
                personas_expuestas_publicamente pep ON pep.id_usuario = u.id
            WHERE 
                u.numero_documento = ?
            GROUP BY 
                u.id;
        ";

        $stmt = $db->prepare($query);
        $stmt->bind_param("s", $numeroDocumento);
        $stmt->execute();
        $result = $stmt->get_result();

        $usuario = [];
        if ($row = $result->fetch_assoc()) {
            $usuario = [
                'id' => $row['idUsuario'],
                'numeroDocumento' => $row['numeroDocumento'],
                'primerNombre' => $row['primerNombre'],
                'segundoNombre' => $row['segundoNombre'],
                'primerApellido' => $row['primerApellido'],
                'segundoApellido' => $row['segundoApellido'],
                'fechaActualizacion' => $row['fechaActualizacion'],
                'generoNombre' => $row['generoNombre'],
                'fechaExpedicionDoc' => $row['fechaExpedicionDoc'],
                'nombreDptoExpDoc' => $row['nombreDptoExpDoc'],
                'nombreMpioExpDoc' => $row['nombreMpioExpDoc'],
                'fechaNacimiento' => $row['fechaNacimiento'],
                'nombrePaisNacimiento' => $row['nombrePaisNacimiento'],
                'nombreDptoNac' => $row['nombreDptoNac'],
                'nombreMpioNac' => $row['nombreMpioNac'],
                'otroLugarNacimiento' => $row['otroLugarNacimiento'],
                'nombreDptoResidencia' => $row['nombreDptoResidencia'],
                'nombreMpioResidencia' => $row['nombreMpioResidencia'],
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
                'nombreNivelEducativo' => $row['nombreNivelEducativo'],
                'profesion' => $row['profesion'],
                'ocupacionOficio' => $row['ocupacionOficio'],
                'nombreEmpresaLabor' => $row['nombreEmpresaLabor'],
                'nitEmpresa' => $row['nitEmpresa'],
                'direccionEmpresa' => $row['direccionEmpresa'],
                'municipioEmpresa' => $row['municipioEmpresa'],
                'telefonoEmpresa' => $row['telefonoEmpresa'],
                'faxEmpresa' => $row['faxEmpresa'],
                'actividadEconomicaEmpresa' => $row['actividadEconomicaEmpresa'],
                'ciiuEmpresa' => $row['ciiuEmpresa'],
                'tipoEmpresa' => $row['tipoEmpresa'],
                'idTipoContrato' => $row['idTipoContrato'],
                'tipoContratoNombre' => $row['tipoContratoNombre'],
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
                'parentescoNombre' => $row['parentescoNombre'],
                'zonaGeografica' => $row['zonaGeografica'],
                'tipoVivienda' => $row['tipoVivienda'],
                'estadoCivil' => $row['estadoCivil'],
                'transaccionesMonedaExtranjera' => $row['transaccionesMonedaExtranjera'],
                'monedaTransaccion' => $row['monedaTransaccion'],
                'otrasOperaciones' => $row['otrasOperaciones'],
                'cuentaExtranjera' => $row['cuentaExtranjera'],
                'bancoExtranjera' => $row['bancoExtranjera'],
                'numeroCuentaExtranjera' => $row['numeroCuentaExtranjera'],
                'monedaCuenta' => $row['monedaCuenta'],
                'paisCuenta' => $row['paisCuenta'],
                'ciudadCuenta' => $row['ciudadCuenta'],
                'poderPublico' => $row['poderPublico'],
                'manejaRecursosPublicos' => $row['manejaRecursosPublicos'],
                'reconocimientoPublico' => $row['reconocimientoPublico'],
                'funcionesPublicas' => $row['funcionesPublicas'],
                'actividadesPublicas' => $row['actividadPublica'],
                'funcionPublicoExtranjero' => $row['funcionPublicoExtranjero'],
                'familiarFuncionPublico' => $row['familiarFuncionPublico'],
                'socioFuncionPublico' => $row['socioFuncionPublico'],
                'familiares' => json_decode($row['familiares'], true),
                'referencias' => json_decode($row['referencias'], true),
                'mediosComunicacion' => json_decode($row['mediosComunicacion'], true) 
            ];
        }

        $stmt->close();
        $db->close();

        return $usuario;
    }
}
?>