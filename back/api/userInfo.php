<?php

require_once '../vendor/autoload.php';
require_once '../src/models/PersonaNaturalModel.php';
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
        $personaNatural = PersonaNatural::obtenerPorId($userData->userId);

        if ($usuario && $personaNatural) {
            $response = [
                'success' => true,
                'data' => [
                    'primerNombre' => $usuario->primerNombre,
                    'segundoNombre' => $usuario->segundoNombre,
                    'primerApellido' => $usuario->primerApellido,
                    'segundoApellido' => $usuario->segundoApellido,
                    'idTipoDocumento' => $usuario->idTipoDocumento,
                    'numeroDocumento' => $usuario->numeroDocumento,
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
