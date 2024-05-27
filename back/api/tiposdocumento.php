<?php

require_once '../src/controllers/TipoDocumentoController.php';

$controlador = new TipoDocumentoController();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $idNuevoTipoDoc = $controlador->crear($_POST);
    echo json_encode(['id' => $idNuevoTipoDoc]);
} elseif ($_SERVER['REQUEST_METHOD'] === 'PUT') {
    parse_str(file_get_contents("php://input"), $datos);
    $idTipoDocExistente = $datos['id'];
    $actualizacionExitosa = $controlador->actualizar($idTipoDocExistente, $datos);
    echo json_encode(['success' => $actualizacionExitosa]);
} elseif ($_SERVER['REQUEST_METHOD'] === 'GET') {
    if (isset($_GET['id'])) {
        $id = $_GET['id'];
        $tipoDoc = $controlador->obtenerPorId($id);
        if ($tipoDoc) {
            header('Content-Type: application/json');
            echo json_encode($tipoDoc);
        } else {
            http_response_code(404);
            echo json_encode(array("message" => "Tipo documento no encontrado."));
        }
    } else {
        $tiposDoc = $controlador->obtenerTodos();
        header('Content-Type: application/json');
        echo json_encode($tiposDoc);
    }
} else {
    http_response_code(405);
    echo json_encode(array("message" => "Método no permitido."));
}
?>