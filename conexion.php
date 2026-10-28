<?php
// Configuración de parámetros de conexión
$host     = "localhost";
$usuario  = "root";
$password = ""; // En XAMPP la contraseña por defecto de root está vacía
$database = "pagina_web";

// Crear conexión con la base de datos
$conexion = new mysqli($host, $usuario, $password, $database);

// Verificar si existe algún error de conexión
if ($conexion->connect_error) {
    die("Error de conexión a la base de datos: " . $conexion->connect_error);
}

// Establecer el conjunto de caracteres a UTF-8 para tildes y ñ
$conexion->set_charset("utf8mb4");

// Opcional: puedes incluir este archivo usando include_once("conexion.php"); en tus scripts PHP.
?>