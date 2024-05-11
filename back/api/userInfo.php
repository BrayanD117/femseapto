<?php

require_once '../vendor/autoload.php';
require_once '../src/models/PersonaNaturalModel.php';
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
    // Detiene la ejecución del script después de enviar los encabezados
    exit(0);
}

$userData = verifyJWTToken($token, $key);

if ($userData) {
    $personaNatural = PersonaNatural::obtenerPorId($userData->userId);
    if ($personaNatural) {
        $response = [
            'success' => true,
            'data' => [
                'nombres' => $personaNatural->nombres,
                'primerApellido' => $personaNatural->primerApellido,
                'segundoApellido' => $personaNatural->segundoApellido,
                'correoElectronico' => $personaNatural->correoElectronico,
                'telefono' => $personaNatural->telefono,
                'celular' => $personaNatural->celular,
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