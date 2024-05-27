<?php

require_once '../src/controllers/TipoViviendaController.php';

$controlador = new TipoViviendaController();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $idNuevoTipoVivienda = $controlador->crear($_POST);
    echo json_encode(['id' => $idNuevoTipoVivienda]);
} elseif ($_SERVER['REQUEST_METHOD'] === 'PUT') {
    parse_str(file_get_contents("php://input"), $datos);
    $idTipoViviendaExistente = $datos['id'];
    $actualizacionExitosa = $controlador->actualizar($idTipoViviendaExistente, $datos);
    echo json_encode(['success' => $actualizacionExitosa]);
} elseif ($_SERVER['REQUEST_METHOD'] === 'GET') {
    if (isset($_GET['id'])) {
        $id = $_GET['id'];
        $tipoVivienda = $controlador->obtenerPorId($id);
        if ($tipoVivienda) {
            header('Content-Type: application/json');
            echo json_encode($tipoVivienda);
        } else {
            http_response_code(404);
            echo json_encode(array("message" => "Tipo vivienda no encontrada."));
        }
    } else {
        $tiposVivienda = $controlador->obtenerTodos();
        header('Content-Type: application/json');
        echo json_encode($tiposVivienda);
    }
} else {
    http_response_code(405);
    echo json_encode(array("message" => "Método no permitido."));
}
?>