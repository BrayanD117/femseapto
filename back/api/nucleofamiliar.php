<?php

require_once __DIR__ . '/../src/controllers/NucleoFamiliarController.php';

$controlador = new NucleoFamiliarController();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $idNuevaInfo = $controlador->crear($_POST);
    echo json_encode(['id' => $idNuevaInfo]);
} elseif ($_SERVER['REQUEST_METHOD'] === 'PUT') {
    parse_str(file_get_contents("php://input"), $datos);
    $idExistente = $datos['id'];
    $actualizacionExitosa = $controlador->actualizar($idExistente, $datos);
    echo json_encode(['success' => $actualizacionExitosa]);
} elseif ($_SERVER['REQUEST_METHOD'] === 'GET') {
    if (isset($_GET['id'])) {
        $id = $_GET['id'];
        $infoFamiliar = $controlador->obtenerPorId($id);
        if ($infoFamiliar) {
            header('Content-Type: application/json');
            echo json_encode($infoFamiliar);
        } else {
            http_response_code(404);
            echo json_encode(array("message" => "Información del núcleo familiar no encontrada."));
        }
    } else {
        $infoFamiliarArray = $controlador->obtenerTodos();
        header('Content-Type: application/json');
        echo json_encode($infoFamiliarArray);
    }
} else {
    http_response_code(405);
    echo json_encode(array("message" => "Método no permitido."));
}
?>
