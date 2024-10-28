<?php

require_once '../vendor/autoload.php';
require_once '../src/models/UsuarioModel.php';
require_once '../src/models/PersonaNaturalModel.php';
require_once '../src/models/InfoFinancieraModel.php';
require_once '../src/models/InfoNucleoFamiliarModel.php';
require_once '../src/models/ReferenciaPersonalComercialBancariaModel.php';
require_once '../src/models/OperacionesInternacionalesModel.php';
require_once '../src/models/PersonaExpuestaPublicamenteModel.php';
require_once '../auth/verifyToken.php';
require_once '../config/config.php';
require_once '../config/cors.php';

function getReporteUsuarios() {
    $key = $_ENV['JWT_SECRET_KEY'];
    $token = $_COOKIE['auth_token'] ?? '';

    try {
        $userData = verifyJWTToken($token, $key);

        if ($userData) {
            $usuarios = Usuario::obtenerFechasActualizacionPorUsuarios();

            $response = [
                'success' => true,
                'data' => $usuarios,
            ];
        } else {
            $response = [
                'success' => false,
                'message' => 'Token no válido o expirado',
            ];
        }

        echo json_encode($response);
    } catch (Exception $e) {
        http_response_code(500);
        $response = [
            'success' => false,
            'message' => 'Error del servidor: ' . $e->getMessage(),
        ];
        echo json_encode($response);
    }
}