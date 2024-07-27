<?php

require_once '../src/controllers/RolController.php';

$controlador = new RolController();

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    if (isset($_GET['id'])) {
        $id = $_GET['id'];
        $rol = $controlador->obtenerPorId($id);
        if ($rol) {
            header('Content-Type: application/json');
            echo json_encode($rol);
        } else {
            http_response_code(404);
            echo json_encode(array("message" => "Rol no encontrado."));
        }
    } else {
        $roles = $controlador->obtenerTodos();
        header('Content-Type: application/json');
        echo json_encode($roles);
    }
} else {
    http_response_code(405);
    echo json_encode(array("message" => "Método no permitido."));
}
?>