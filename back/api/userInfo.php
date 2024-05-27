<?php

require_once '../vendor/autoload.php';
require_once '../src/models/PersonaNaturalModel.php';
require_once '../src/models/InfoFinancieraModel.php';
require_once '../src/models/InfoNucleoFamiliar.php';
require_once '../src/models/OperacionesInternacionalesModel.php';
require_once '../src/models/PersonaExpuestaPublicamenteModel.php';
require_once '../src/models/ReferenciaPersonalComercialBancariaModel.php';
require_once '../src/models/UsuarioModel.php';
require_once '../auth/verifyToken.php';
require_once '../config/config.php';

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: http://localhost:4200');  // Permite específicamente a Angular en el puerto 4200
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');
header('Access-Control-Allow-Credentials: true'); 

$key = $_ENV['JWT_SECRET_KEY'];
$token = $_COOKIE['auth_token'] ?? '';

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}

try {
    $userData = verifyJWTToken($token, $key);

    if ($userData) {
        // Obtener información de Usuario
        $usuario = Usuario::obtenerPorId($userData->userId);
        // Obtener información de PersonaNatural
        $personaNatural = PersonaNatural::obtenerPorIdUsuario($userData->userId);
        $infoFinanciera = InformacionFinanciera::obtenerPorIdUsuario($userData->userId);
        $infoNucleoFamiliar = InformacionNucleoFamiliar::obtenerPorIdUsuario($userData->userId);
        $infoOperacionesInternacionales = OperacionesInternacionales::obtenerPorIdUsuario($userData->userId);
        $infoPersonaExpuestaPublicamente = PersonaExpuestaPublicamente::obtenerPorIdUsuario($userData->userId);
        $referencias = ReferenciaPersonalComercialBancaria::obtenerPorIdUsuario($userData->userId);
        
        if ($usuario) {
            $response = [
                'success' => true,
                'data' => [
                    'usuario' => [
                        'primerNombre' => $usuario->primerNombre,
                        'segundoNombre' => $usuario->segundoNombre,
                        'primerApellido' => $usuario->primerApellido,
                        'segundoApellido' => $usuario->segundoApellido,
                        'idTipoDocumento' => $usuario->idTipoDocumento,
                        'numeroDocumento' => $usuario->numeroDocumento
                    ],
                    'personaNatural' => [
                        'idGenero' => $personaNatural->idGenero,
                        'fechaExpDoc' => $personaNatural->fechaExpDoc,
                        'mpioExpDoc' => $personaNatural->mpioExpDoc,
                        'fechaNacimiento' => $personaNatural->fechaNacimiento,
                        'paisNacimiento' => $personaNatural->paisNacimiento,
                        'mpioNacimiento' => $personaNatural->mpioNacimiento,
                        'otroLugarNacimiento' => $personaNatural->otroLugarNacimiento,
                        'mpioResidencia' => $personaNatural->mpioResidencia,
                        'idZonaResidencia' => $personaNatural->idZonaResidencia,
                        'idTipoVivienda' => $personaNatural->idTipoVivienda,
                        'estrato' => $personaNatural->estrato,
                        'direccionResidencia' => $personaNatural->direccionResidencia,
                        'aniosAntigVivienda' => $personaNatural->aniosAntigVivienda,
                        'idEstadoCivil' => $personaNatural->idEstadoCivil,
                        'personasACargo' => $personaNatural->personasACargo,
                        'tieneHijos' => $personaNatural->tieneHijos,
                        'numeroHijos' => $personaNatural->numeroHijos,
                        'correoElectronico' => $personaNatural->correoElectronico,
                        'telefono' => $personaNatural->telefono,
                        'celular' => $personaNatural->celular,
                        'idNivelEducativo' => $personaNatural->idNivelEducativo,
                        'profesion' => $personaNatural->profesion,
                        'ocupacionOficio' => $personaNatural->ocupacionOficio,
                        'idEmpresaLabor' => $personaNatural->idEmpresaLabor,
                        'cargoOcupa' => $personaNatural->cargoOcupa,
                        'nombreEmergencia' => $personaNatural->nombreEmergencia,
                        'numeroCedulaEmergencia' => $personaNatural->numeroCedulaEmergencia,
                        'numeroCelularEmergencia' => $personaNatural->numeroCelularEmergencia
                    ],
                    'informacionFinanciera' => [
                        'ingresosMensuales' => $infoFinanciera->ingresos_mensuales,
                        'otrosIngresosMensuales' => $infoFinanciera->otros_ingresos_mensuales,
                        'conceptoOtrosIngresosMens' => $infoFinanciera->concepto_otros_ingresos_mens,
                        'totalIngresosMensuales' => $infoFinanciera->total_ingresos_mensuales,
                        'egresosMensuales' => $infoFinanciera->egresos_mensuales,
                        'otrosEgresosMensuales' => $infoFinanciera->otros_egresos_mensuales,
                        'totalEgresosMensuales' => $infoFinanciera->total_egresos_mensuales,
                        'totalActivos' => $infoFinanciera->total_activos,
                        'totalPasivos' => $infoFinanciera->total_pasivos,
                        'totalPatrimonio' => $infoFinanciera->total_patrimonio,
                    ],
                    'nucleoFamiliar' => array_map(function($miembro) {
                        return [
                            'nombreCompleto' => $miembro->nombre_completo,
                            'idTipoDocumento' => $miembro->id_tipo_documento,
                            'numeroDocumento' => $miembro->numero_documento,
                            'idParentesco' => $miembro->id_parentesco,
                            'idGenero' => $miembro->id_genero,
                            'fechaNacimiento' => $miembro->fecha_nacimiento,
                            'idNivelEducativo' => $miembro->id_nivel_educativo,
                            'trabaja' => $miembro->trabaja,
                            'celular' => $miembro->celular,
                        ];
                    }, $infoNucleoFamiliar),
                    'operacionesInternacionales' => [
                        'transaccionesMonedaExtranjera' => $infoOperacionesInternacionales->transacciones_moneda_extranjera,
                        'transMonedaExtranjera' => $infoOperacionesInternacionales->trans_moneda_extranjera,
                        'otrasOperaciones' => $infoOperacionesInternacionales->otras_operaciones,
                        'cuentasMonedaExtranjera' => $infoOperacionesInternacionales->cuentas_moneda_extranjera,
                        'bancoCuentaExtranjera' => $infoOperacionesInternacionales->banco_cuenta_extranjera,
                        'cuentaMonedaExtranjera' => $infoOperacionesInternacionales->cuenta_moneda_extranjera,
                        'monedaCuenta' => $infoOperacionesInternacionales->moneda_cuenta,
                        'idPaisCuenta' => $infoOperacionesInternacionales->id_pais_cuenta,
                        'ciudadCuenta' => $infoOperacionesInternacionales->ciudad_cuenta,
                    ],
                    'personaExpuestaPublicamente' => [
                        'poderPublico' => $infoPersonaExpuestaPublicamente->poderPublico,
                        'manejaRecPublicos' => $infoPersonaExpuestaPublicamente->manejaRecPublicos,
                        'reconocimientoPublico' => $infoPersonaExpuestaPublicamente->reconocimientoPublico,
                        'funcionesPublicas' => $infoPersonaExpuestaPublicamente->funcionesPublicas,
                        'actividadPublica' => $infoPersonaExpuestaPublicamente->actividadPublica,
                        'funcionarioPublicoExtranjero' => $infoPersonaExpuestaPublicamente->funcionarioPublicoExtranjero,
                        'famFuncionarioPublico' => $infoPersonaExpuestaPublicamente->famFuncionarioPublico,
                        'socioFuncionarioPublico' => $infoPersonaExpuestaPublicamente->socioFuncionarioPublico,
                    ],
                    'referencias' => array_map(function($referencia) {
                        return [
                            'nombreRazonSocial' => $referencia->nombreRazonSocial,
                            'idTipoReferencia' => $referencia->idTipoReferencia,
                            'idMunicipio' => $referencia->idMunicipio,
                            'direccion' => $referencia->direccion,
                            'telefono' => $referencia->telefono,
                        ];
                    }, $referencias) 
                ]
            ];
        } else {
            $response = [
                'success' => false,
                'message' => 'Usuario no encontrado'
            ];
        }
    } else {
        $response = [
            'success' => false,
            'message' => 'Token no válido o expirado'
        ];
    }

    echo json_encode($response);

} catch (Exception $e) {
    http_response_code(500);
    $response = [
        'success' => false,
        'message' => 'Error del servidor: ' . $e->getMessage()
    ];
    echo json_encode($response);
}
?>
