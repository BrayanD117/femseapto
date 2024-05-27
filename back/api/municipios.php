<?php

require_once '../src/controllers/MunicipioController.php';

$controlador = new MunicipioController();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $idNuevoMpio = $controlador->crear($_POST);
    echo json_encode(['id' => $idNuevoMpio]);
} elseif ($_SERVER['REQUEST_METHOD'] === 'PUT') {
    parse_str(file_get_contents("php://input"), $datos);
    $idMpioExistente = $datos['id'];
    $actualizacionExitosa = $controlador->actualizar($idMpioExistente, $datos);
    echo json_encode(['success' => $actualizacionExitosa]);
} elseif ($_SERVER['REQUEST_METHOD'] === 'GET') {
    if (isset($_GET['id'])) {
        $id = $_GET['id'];
        $mpio = $controlador->obtenerPorId($id);
        if ($mpio) {
            header('Content-Type: application/json');
            echo json_encode($mpio);
        } else {
            http_response_code(404);
            echo json_encode(array("message" => "Municipio no encontrado."));
        }
    } elseif (isset($_GET['idDpto'])) {
        $idDpto = $_GET['idDpto'];
        $mpios = $controlador->obtenerPorIdDpto($idDpto);
        if ($mpios) {
            header('Content-Type: application/json');
            echo json_encode($mpios);
        } else {
            http_response_code(404);
            echo json_encode(array("message" => "Municipios no encontrados."));
        }
    } else {
        $mpios = $controlador->obtenerTodos();
        header('Content-Type: application/json');
        echo json_encode($mpios);
    }
} else {
    http_response_code(405);
    echo json_encode(array("message" => "Método no permitido."));
}
?>