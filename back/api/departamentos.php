<?php

require_once '../src/controllers/DepartamentoController.php';

$controlador = new DepartamentoController();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $idNuevoDpto = $controlador->crear($_POST);
    echo json_encode(['id' => $idNuevoDpto]);
} elseif ($_SERVER['REQUEST_METHOD'] === 'PUT') {
    parse_str(file_get_contents("php://input"), $datos);
    $idDptoExistente = $datos['id'];
    $actualizacionExitosa = $controlador->actualizar($idDptoExistente, $datos);
    echo json_encode(['success' => $actualizacionExitosa]);
} elseif ($_SERVER['REQUEST_METHOD'] === 'GET') {
    if (isset($_GET['id'])) {
        $id = $_GET['id'];
        $dpto = $controlador->obtenerPorId($id);
        if ($dpto) {
            header('Content-Type: application/json');
            echo json_encode($dpto);
        } else {
            http_response_code(404);
            echo json_encode(array("message" => "Departamento no encontrado."));
        }
    } else {
        $departamentos = $controlador->obtenerTodos();
        header('Content-Type: application/json');
        echo json_encode($departamentos);
    }
} else {
    http_response_code(405);
    echo json_encode(array("message" => "Método no permitido."));
}
?>