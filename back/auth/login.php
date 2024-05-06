<?php
require_once '../vendor/autoload.php';
require_once '../src/models/UsuarioModel.php'; 
require_once '../config/config.php';

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/..');
$dotenv->load();

header('Content-Type: application/json');

// Permitir solicitudes desde cualquier origen
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Credentials: true");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

$key = $_ENV['JWT_SECRET_KEY'];

// Chequear si la solicitud es POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents("php://input"), true);

    $usuario = $data['usuario'] ?? '';
    $contrasenia = $data['contrasenia'] ?? '';

    $usuarioObj = Usuario::buscarPorUsuario($usuario);

    if ($usuarioObj && password_verify($contrasenia, $usuarioObj->contrasenia)) {
        if ($usuarioObj->activo) {
            $issuedAt = time();
            $expirationTime = $issuedAt + 3600;  // token válido por 1 hora
            $payload = [
                'iss' => 'localhost',
                'iat' => $issuedAt,    
                'exp' => $expirationTime,  
                'userId' => $usuarioObj->id,
                'id_rol' => $usuarioObj->id_rol
            ];

            $jwt = JWT::encode($payload, $key, 'HS256');
            setcookie('auth_token', $jwt, $expirationTime, '/', '', false, true);  // Secure en false solo para desarrollo local

            $response = [
                'success' => true,
                'message' => 'Login exitoso.',
                'token' => $jwt
            ];
        } else {
            $response = [
                'success' => false,
                'message' => 'Cuenta inactiva. Contacte al administrador.'
            ];
        }
    } else {
        $response = [
            'success' => false,
            'message' => 'Usuario o contraseña incorrecta.'
        ];
    }
} else {
    $response = [
        'success' => false,
        'message' => 'Método no permitido.'
    ];
}

echo json_encode($response);
?>
