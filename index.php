<?php
session_start();

// Validar de forma estricta si el usuario tiene una sesión activa
$estaLogueado = isset($_SESSION['id_usuario']) && !empty($_SESSION['id_usuario']);

// Obtener el nombre y el rol almacenados en la sesión
$nombreUsuario = $_SESSION['nombre'] ?? $_SESSION['usuario'] ?? 'Invitado';
$rolUsuario = strtolower(trim($_SESSION['rol'] ?? ''));

// Verificar si es administrador
$esAdmin = ($rolUsuario === 'administrador' || $rolUsuario === 'admin');
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inicio — Simulador de Plantas</title>
    <link rel="stylesheet" href="styles.css">
</head>

<body>
    <nav>
        <ul>
            <li><a href="index.php">Inicio</a></li>
            <li><a href="QuiénesSomos.html">Quiénes Somos</a></li>
            <li><a href="antecedentes.html">Antecedentes</a></li>
            <li><a href="Fundamentos.html">Fundamentos</a></li>
            <li><a href="Prototipo.html">Prototipo</a></li>
            <li><a href="Noticias.php">Noticias</a></li>
            <li><a href="foro.php">Foro</a></li>
            <li><a href="Galería.html">Galería</a></li>
            <li><a href="Contacto.php">Contacto</a></li>
            <li><a href="Políticas.html">Políticas</a></li>

            <!-- MOSTRAR BOTÓN DE PANEL DE ADMIN SI ES ADMINISTRADOR -->
            <?php if ($estaLogueado && $esAdmin): ?>
                <li><a href="admin.php" style="background-color: #57C785; color: white;">Panel Admin</a></li>
            <?php endif; ?>

            <!-- CONDICIONAL DEL BOTÓN DE SESIÓN -->
            <?php if ($estaLogueado): ?>
                <li><a href="logout.php" style="background-color: #c62828; color: white;">Cerrar sesión</a></li>
            <?php else: ?>
                <li><a href="login.php" style="background-color: #2A7B9B; color: white;">Iniciar sesión</a></li>
            <?php endif; ?>
        </ul>
    </nav>

    <main>
        <div class="tarjeta">
            <div class="centrar">
                <img src="imagenes/img.logo.jpg" alt="Logo" class="logo">
            </div>

            <?php if ($estaLogueado): ?>
                <?php if ($esAdmin): ?>
                    <h1>Bienvenido, Administrador (<?php echo htmlspecialchars($nombreUsuario); ?>)</h1>
                <?php else: ?>
                    <h1>Bienvenido, <?php echo htmlspecialchars($nombreUsuario); ?></h1>
                <?php endif; ?>
            <?php else: ?>
                <h1>Bienvenido al Simulador</h1>
            <?php endif; ?>

            <p id="textoInicio">¡Bienvenido al proyecto <b>Simulador de Crecimiento de Plantas</b>! Aquí podrás explorar cómo influyen variables como el agua, la luz y el tipo de suelo en el desarrollo de una planta, todo desde una interfaz educativa pensada para el aula.</p>
        </div>
    </main>

    <footer>
        <div class="footer-contenido">
            <div class="footer-logos">
                <img src="imagenes/image-removebg-preview.png" alt="Institución Universitaria Pascual Bravo" class="logo-footer">
                <img src="imagenes/image-removebg-preview (1).png" alt="I.E. República de Uruguay" class="logo-footer">
            </div>
            <p>© 2026 Simulador de Crecimiento de Plantas</p>
        </div>
    </footer>
</body>

</html>