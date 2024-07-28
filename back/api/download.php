<?php
if (isset($_GET['file'])) {
    $file = basename($_GET['file']);
    $uploadDir = '../uploads/'; // Directorio de subida
    $filePath = $uploadDir . $file;

    if (file_exists($filePath)) {
        header('Content-Description: File Transfer');
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="' . $file . '"');
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        header('Content-Length: ' . filesize($filePath));
        readfile($filePath);
        exit;
    } else {
        echo 'Archivo no encontrado';
    }
} else {
    echo 'No se ha especificado un archivo para descargar';
}
?>