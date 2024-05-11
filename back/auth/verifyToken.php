<?php
require_once '../vendor/autoload.php';

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

function verifyJWTToken($token, $key) {
    if (empty($token)) {
        return null;
    }
    try {
        return JWT::decode($token, new Key($key, 'HS256'));
    } catch (Exception $e) {
        return null; // Token no válido o expirado
    }
}
