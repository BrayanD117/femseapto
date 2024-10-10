<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_FILES['file'])) {
        $file = $_FILES['file'];
        $uploadDir = '../uploads/information'; // Directorio de subida

        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        $uploadFile = $uploadDir . basename($file['name']);

        if (move_uploaded_file($file['tmp_name'], $uploadFile)) {
            echo json_encode(['status' => 'success', 'message' => 'Archivo subido correctamente']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Error al subir el archivo']);
        }
    } else {
        echo json_encode(['status' => 'error', 'message' => 'No se ha enviado ningún archivo']);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Método no permitido']);
}
?>