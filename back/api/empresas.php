<?php

require_once '../src/controllers/EmpresaController.php';

$controlador = new EmpresaController();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $idNuevo = $controlador->crear($_POST);
    echo json_encode(['id' => $idNuevo]);
} elseif ($_SERVER['REQUEST_METHOD'] === 'PUT') {
    parse_str(file_get_contents("php://input"), $datos);
    $idExistente = $datos['id'];
    $actualizacionExitosa = $controlador->actualizar($idExistente, $datos);
    echo json_encode(['success' => $actualizacionExitosa]);
} elseif ($_SERVER['REQUEST_METHOD'] === 'GET') {
    if (isset($_GET['id'])) {
        $id = $_GET['id'];
        $empresa = $controlador->obtenerPorId($id);
        if ($empresa) {
            header('Content-Type: application/json');
            echo json_encode($empresa);
        } else {
            http_response_code(404);
            echo json_encode(array("message" => "Empresa no encontrada."));
        }
    } else {
        $empresas = $controlador->obtenerTodos();
        header('Content-Type: application/json');
        echo json_encode($empresas);
    }
} else {
    http_response_code(405);
    echo json_encode(array("message" => "Método no permitido."));
}
?>