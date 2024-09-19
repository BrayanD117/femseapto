<?php

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Credentials: true');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

setcookie('auth_token', '', time() - 3600, '/', '', false, true);

$response = [
    'success' => true,
    'message' => 'Sesión cerrada exitosamente.'
];

echo json_encode($response);
?>