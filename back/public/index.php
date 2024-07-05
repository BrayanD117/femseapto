<?php
require_once '../vendor/autoload.php';
require_once '../routes/api.php';

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: http://localhost:4200');  // Permite específicamente a Angular en el puerto 4200
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');
header('Access-Control-Allow-Credentials: true');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}

$router = new Router();
$router->handleRequest();
?>