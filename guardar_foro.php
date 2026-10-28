<?php
session_start();

if (!isset($_SESSION['usuario'])) {
    echo "<script>
            alert('Debes iniciar sesión para publicar.');
            window.location.href = 'index.php';
          </script>";
    exit();
}

$titulo = trim($_POST['titulo']);
$texto = trim($_POST['texto']);
$autor = $_SESSION['usuario'];
$fecha = date('d/m/Y H:i');

$archivo = 'foro.json';
$posts = file_exists($archivo) ? json_decode(file_get_contents($archivo), true) : [];

$nuevoPost = [
    'titulo' => $titulo,
    'texto' => $texto,
    'autor' => $autor,
    'fecha' => $fecha
];

array_unshift($posts, $nuevoPost);
file_put_contents($archivo, json_encode($posts, JSON_PRETTY_PRINT));

header("Location: foro.php");
exit();
?>