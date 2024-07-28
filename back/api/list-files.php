<?php
header('Content-Type: application/json');
$uploadDir = '../uploads/'; // Directorio de subida

if (is_dir($uploadDir)) {
    $files = array_diff(scandir($uploadDir), array('..', '.'));
    echo json_encode(array_values($files)); // Asegúrate de devolver un array indexado correctamente
} else {
    echo json_encode([]);
}
?>