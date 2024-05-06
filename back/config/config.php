<?php
// config.php - Configuración de conexión a la base de datos para XAMPP

// Variables de configuración de la base de datos
define('DB_SERVER', 'localhost');
define('DB_USERNAME', 'root');
define('DB_PASSWORD', '');
define('DB_DATABASE', 'femseaptodb');

// Función para obtener la conexión
function getDB() {
    $dbConnection = mysqli_connect(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_DATABASE);
    if (mysqli_connect_errno()) {
        die("Failed to connect to MySQL: " . mysqli_connect_error());
    }
    return $dbConnection;
}
