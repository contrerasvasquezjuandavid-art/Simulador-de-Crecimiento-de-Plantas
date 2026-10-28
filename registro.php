<?php
session_start();
include_once("conexion.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nombre   = trim($_POST['nombre']);
    $correo   = trim($_POST['email']);
    $password = trim($_POST['password']);

    if (empty($nombre) || empty($correo) || empty($password)) {
        header("Location: index.php?error=vacios");
        exit();
    }

    // Verificar si el correo ya está registrado
    $checkQuery = $conexion->prepare("SELECT id_usuario FROM usuarios WHERE correo = ?");
    $checkQuery->bind_param("s", $correo);
    $checkQuery->execute();
    $result = $checkQuery->get_result();

    if ($result->num_rows > 0) {
        header("Location: index.php?error=existe");
        exit();
    }

    // Insertar nuevo usuario
    $stmt = $conexion->prepare("INSERT INTO usuarios (nombre, correo, contrasena, rol) VALUES (?, ?, ?, 'usuario')");
    $stmt->bind_param("sss", $nombre, $correo, $password);

    if ($stmt->execute()) {
        header("Location: index.php?exito=registrado");
    } else {
        echo "Error al registrar: " . $conexion->error;
    }

    $stmt->close();
    $conexion->close();
}
?>