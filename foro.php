<?php
session_start();
include_once("conexion.php");

// 1. VERIFICAR SI EL USUARIO ESTÁ LOGUEADO
$estaLogueado = isset($_SESSION['id_usuario']) && !empty($_SESSION['id_usuario']);
$idUsuarioSesion = $estaLogueado ? $_SESSION['id_usuario'] : null;

// 2. ACCIÓN: CREAR NUEVA PUBLICACIÓN
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['accion']) && $_POST['accion'] === 'crear') {
    if (!$estaLogueado) {
        header("Location: login.php");
        exit();
    }

    $titulo    = trim($_POST['titulo'] ?? '');
    $contenido = trim($_POST['texto'] ?? '');

    if (!empty($titulo) && !empty($contenido)) {
        $stmt = $conexion->prepare("INSERT INTO foro (id_usuario, titulo, contenido) VALUES (?, ?, ?)");
        if ($stmt) {
            $stmt->bind_param("iss", $idUsuarioSesion, $titulo, $contenido);
            $stmt->execute();
            $stmt->close();
        }
    }
    header("Location: foro.php");
    exit();
}

// 3. ACCIÓN: EDITAR PUBLICACIÓN
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['accion']) && $_POST['accion'] === 'editar') {
    if (!$estaLogueado) {
        header("Location: login.php");
        exit();
    }

    $idPublicacion = intval($_POST['id_publicacion'] ?? 0);
    $titulo        = trim($_POST['titulo'] ?? '');
    $contenido     = trim($_POST['texto'] ?? '');

    if ($idPublicacion > 0 && !empty($titulo) && !empty($contenido)) {
        // Asegurar que el usuario solo edite su propia publicación
        $stmt = $conexion->prepare("UPDATE foro SET titulo = ?, contenido = ? WHERE id_publicacion = ? AND id_usuario = ?");
        if ($stmt) {
            $stmt->bind_param("ssii", $titulo, $contenido, $idPublicacion, $idUsuarioSesion);
            $stmt->execute();
            $stmt->close();
        }
    }
    header("Location: foro.php");
    exit();
}

// 4. ACCIÓN: ELIMINAR PUBLICACIÓN
if (isset($_GET['eliminar']) && $estaLogueado) {
    $idPublicacionEliminar = intval($_GET['eliminar']);

    if ($idPublicacionEliminar > 0) {
        // Asegurar que el usuario solo elimine su propia publicación
        $stmt = $conexion->prepare("DELETE FROM foro WHERE id_publicacion = ? AND id_usuario = ?");
        if ($stmt) {
            $stmt->bind_param("ii", $idPublicacionEliminar, $idUsuarioSesion);
            $stmt->execute();
            $stmt->close();
        }
    }
    header("Location: foro.php");
    exit();
}

// 5. CONSULTAR PUBLICACIONES
$sql = "SELECT f.id_publicacion, f.id_usuario, f.titulo, f.contenido, f.fecha_publicacion, u.nombre AS autor 
        FROM foro f 
        INNER JOIN usuarios u ON f.id_usuario = u.id_usuario 
        ORDER BY f.fecha_publicacion DESC";

$resultado = $conexion->query($sql);

// Variable para controlar si el usuario seleccionó "Editar" en una publicación
$idEditar = isset($_GET['editar']) ? intval($_GET['editar']) : 0;
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Foro — Simulador de Plantas</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        .form-foro {
            background: #f8fafc;
            padding: 20px;
            border-radius: 8px;
            border: 1px solid #cbd5e1;
            margin-bottom: 25px;
        }

        .form-foro input[type="text"],
        .form-foro textarea {
            width: 100%;
            padding: 10px;
            margin-bottom: 12px;
            border: 1px solid #cbd5e1;
            border-radius: 6px;
            font-size: 0.95rem;
            box-sizing: border-box;
        }

        .form-foro textarea {
            min-height: 100px;
            resize: vertical;
        }

        .btn-publicar {
            background: #2A7B9B;
            color: #fff;
            border: none;
            padding: 10px 20px;
            border-radius: 6px;
            cursor: pointer;
            font-weight: bold;
        }

        .btn-cancelar {
            background: #64748b;
            color: #fff;
            border: none;
            padding: 10px 20px;
            border-radius: 6px;
            text-decoration: none;
            display: inline-block;
            font-size: 0.9rem;
            margin-left: 8px;
        }

        .post {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            padding: 18px;
            margin-bottom: 15px;
            position: relative;
        }

        .post h2 {
            margin-top: 0;
            color: #2A7B9B;
            font-size: 1.2rem;
        }

        .post-acciones {
            margin-top: 12px;
            display: flex;
            gap: 10px;
        }

        .btn-accion-editar {
            background-color: #f59e0b;
            color: white;
            padding: 5px 12px;
            border-radius: 4px;
            text-decoration: none;
            font-size: 0.85rem;
            font-weight: bold;
        }

        .btn-accion-eliminar {
            background-color: #ef4444;
            color: white;
            padding: 5px 12px;
            border-radius: 4px;
            text-decoration: none;
            font-size: 0.85rem;
            font-weight: bold;
        }

        .mensaje-login {
            background-color: #e0f2fe;
            border-left: 4px solid #0284c7;
            padding: 15px;
            border-radius: 6px;
            color: #0369a1;
            margin-bottom: 25px;
        }
    </style>
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
            <li><a href="foro.php"> Foro</a></li>
            <li><a href="Galería.html">Galería</a></li>
            <li><a href="Contacto.php">Contacto</a></li>
            <li><a href="Políticas.html">Políticas</a></li>

            <!-- MENÚ DINÁMICO -->
            <?php if ($estaLogueado): ?>
                <li><a href="logout.php" style="background-color: #c62828; color: white;">Cerrar sesión</a></li>
            <?php else: ?>
                <li><a href="login.php" style="background-color: #2A7B9B; color: white;">Iniciar sesión</a></li>
            <?php endif; ?>
        </ul>
    </nav>

    <main>
        <div class="tarjeta">
            <h1> Foro de la comunidad</h1>
            <p>Comparte dudas, experimentos y consejos sobre el crecimiento de las plantas.</p>

            <!-- SECCIÓN SI ESTÁ LOGUEADO: NUEVA PUBLICACIÓN -->
            <?php if ($estaLogueado && $idEditar === 0): ?>
                <div class="form-foro">
                    <h3>Crear nueva publicación</h3>
                    <form action="foro.php" method="POST">
                        <input type="hidden" name="accion" value="crear">
                        <input type="text" name="titulo" placeholder="Título de tu publicación" required>
                        <textarea name="texto" placeholder="Escribe tu pregunta o aporte..." required></textarea>
                        <button type="submit" class="btn-publicar">Publicar </button>
                    </form>
                </div>
            <?php elseif (!$estaLogueado): ?>
                <!-- SECCIÓN SI NO ESTÁ LOGUEADO -->
                <div class="mensaje-login">
                     Estás en modo lectura. Para crear, editar o eliminar publicaciones, <a href="login.php" style="color: #0284c7; font-weight: bold; text-decoration: underline;">inicia sesión aquí</a>.
                </div>
            <?php endif; ?>

            <!-- LISTADO DE PUBLICACIONES DESDE LA BASE DE DATOS -->
            <div id="publicaciones">
                <?php if ($resultado && $resultado->num_rows > 0): ?>
                    <?php while ($p = $resultado->fetch_assoc()): ?>
                        
                        <!-- MODO EDICIÓN EN LA MISMA HOJA -->
                        <?php if ($estaLogueado && $idEditar === intval($p['id_publicacion']) && $p['id_usuario'] == $idUsuarioSesion): ?>
                            <div class="form-foro" style="border-color: #f59e0b; background-color: #fffbeb;">
                                <h3> Editando publicación</h3>
                                <form action="foro.php" method="POST">
                                    <input type="hidden" name="accion" value="editar">
                                    <input type="hidden" name="id_publicacion" value="<?php echo $p['id_publicacion']; ?>">
                                    <input type="text" name="titulo" value="<?php echo htmlspecialchars($p['titulo']); ?>" required>
                                    <textarea name="texto" required><?php echo htmlspecialchars($p['contenido']); ?></textarea>
                                    <button type="submit" class="btn-publicar" style="background-color: #f59e0b;">Guardar Cambios</button>
                                    <a href="foro.php" class="btn-cancelar">Cancelar</a>
                                </form>
                            </div>
                        <?php else: ?>
                            <!-- MODO LECTURA REGULAR -->
                            <article class="post">
                                <h2><?php echo htmlspecialchars($p['titulo']); ?></h2>
                                <small> <b><?php echo htmlspecialchars($p['autor']); ?></b>   <?php echo date("d/m/Y H:i", strtotime($p['fecha_publicacion'])); ?></small>
                                <p style="margin-top: 10px;"><?php echo nl2br(htmlspecialchars($p['contenido'])); ?></p>

                                <!-- BOTONES DE EDITAR Y ELIMINAR (SOLO PARA EL AUTOR DE LA PUBLICACIÓN) -->
                                <?php if ($estaLogueado && $p['id_usuario'] == $idUsuarioSesion): ?>
                                    <div class="post-acciones">
                                        <a href="foro.php?editar=<?php echo $p['id_publicacion']; ?>" class="btn-accion-editar">✏️ Editar</a>
                                        <a href="foro.php?eliminar=<?php echo $p['id_publicacion']; ?>" class="btn-accion-eliminar" onclick="return confirm('¿Estás seguro de que deseas eliminar esta publicación?');">🗑️ Eliminar</a>
                                    </div>
                                <?php endif; ?>
                            </article>
                        <?php endif; ?>

                    <?php endwhile; ?>
                <?php else: ?>
                    <p class="vacio">Todavía no hay publicaciones. ¡Sé el primero en aportar!</p>
                <?php endif; ?>
            </div>
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