<?php
session_start();
include_once("conexion.php");

/* =========================================================
   1. VERIFICAR SESIÓN Y ROL
   ========================================================= */

$estaLogueado = isset($_SESSION['id_usuario']) && !empty($_SESSION['id_usuario']);

$esAdmin = $estaLogueado &&
           isset($_SESSION['rol']) &&
           $_SESSION['rol'] === 'administrador';

$idAdmin = $estaLogueado ? $_SESSION['id_usuario'] : null;


/* =========================================================
   2. CREAR NUEVA NOTICIA
   ========================================================= */

if (
    $_SERVER["REQUEST_METHOD"] === "POST" &&
    isset($_POST['accion']) &&
    $_POST['accion'] === 'crear' &&
    $esAdmin
) {

    $titulo = trim($_POST['titulo'] ?? '');
    $contenido = trim($_POST['contenido'] ?? '');
    $imagen = trim($_POST['imagen'] ?? '');

    if (!empty($titulo) && !empty($contenido)) {

        $stmt = $conexion->prepare("
            INSERT INTO noticias
            (titulo, contenido, imagen, id_administrador)
            VALUES (?, ?, ?, ?)
        ");

        if ($stmt) {

            $stmt->bind_param(
                "sssi",
                $titulo,
                $contenido,
                $imagen,
                $idAdmin
            );

            $stmt->execute();
            $stmt->close();
        }
    }

    header("Location: noticias.php");
    exit();
}


/* =========================================================
   3. EDITAR NOTICIA
   ========================================================= */

if (
    $_SERVER["REQUEST_METHOD"] === "POST" &&
    isset($_POST['accion']) &&
    $_POST['accion'] === 'editar' &&
    $esAdmin
) {

    $idNoticia = intval($_POST['id_noticia'] ?? 0);
    $titulo = trim($_POST['titulo'] ?? '');
    $contenido = trim($_POST['contenido'] ?? '');
    $imagen = trim($_POST['imagen'] ?? '');

    if (
        $idNoticia > 0 &&
        !empty($titulo) &&
        !empty($contenido)
    ) {

        $stmt = $conexion->prepare("
            UPDATE noticias
            SET titulo = ?,
                contenido = ?,
                imagen = ?
            WHERE id_noticia = ?
        ");

        if ($stmt) {

            $stmt->bind_param(
                "sssi",
                $titulo,
                $contenido,
                $imagen,
                $idNoticia
            );

            $stmt->execute();
            $stmt->close();
        }
    }

    header("Location: noticias.php");
    exit();
}


/* =========================================================
   4. ELIMINAR NOTICIA
   ========================================================= */

if (isset($_GET['eliminar']) && $esAdmin) {

    $idEliminar = intval($_GET['eliminar']);

    if ($idEliminar > 0) {

        $stmt = $conexion->prepare("
            DELETE FROM noticias
            WHERE id_noticia = ?
        ");

        if ($stmt) {

            $stmt->bind_param(
                "i",
                $idEliminar
            );

            $stmt->execute();
            $stmt->close();
        }
    }

    header("Location: noticias.php");
    exit();
}


/* =========================================================
   5. CONSULTAR NOTICIAS
   ========================================================= */

$sql = "
    SELECT
        id_noticia,
        titulo,
        contenido,
        imagen,
        fecha_publicacion
    FROM noticias
    ORDER BY fecha_publicacion DESC
";

$resultado = $conexion->query($sql);


/* =========================================================
   6. DETECTAR SI SE ESTÁ EDITANDO
   ========================================================= */

$idEditar = isset($_GET['editar'])
    ? intval($_GET['editar'])
    : 0;

?>

<!DOCTYPE html>

<html lang="es">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>
        Noticias — Simulador de Plantas
    </title>

    <link
        rel="stylesheet"
        href="styles.css"
    >

</head>


<body>


<!-- =========================================================
     MENÚ DE NAVEGACIÓN
     ========================================================= -->

<nav>

    <ul>

        <li>
            <a href="index.php">
                Inicio
            </a>
        </li>

        <li>
            <a href="QuiénesSomos.html">
                Quiénes Somos
            </a>
        </li>

        <li>
            <a href="antecedentes.html">
                Antecedentes
            </a>
        </li>

        <li>
            <a href="Fundamentos.html">
                Fundamentos
            </a>
        </li>

        <li>
            <a href="Prototipo.html">
                Prototipo
            </a>
        </li>

        <li>
            <a href="noticias.php">
                Noticias
            </a>
        </li>

        <li>
            <a href="foro.php">
                Foro
            </a>
        </li>

        <li>
            <a href="galeria.php">
                Galería
            </a>
        </li>

        <li>
            <a href="Contacto.php">
                Contacto
            </a>
        </li>

        <li>
            <a href="Políticas.html">
                Políticas
            </a>
        </li>


        <?php if ($estaLogueado): ?>

            <li>
                <a
                    href="logout.php"
                    class="nav-logout"
                >
                    Cerrar sesión
                </a>
            </li>

        <?php else: ?>

            <li>
                <a
                    href="login.php"
                    class="nav-login"
                >
                    Iniciar sesión
                </a>
            </li>

        <?php endif; ?>

    </ul>

</nav>


<!-- =========================================================
     CONTENIDO PRINCIPAL
     ========================================================= -->

<main>

    <div class="tarjeta">


        <!-- ENCABEZADO -->

        <div class="noticias-header">

            <span class="noticias-icono">
                
            </span>

            <h1>
                Noticias
            </h1>

            <p>
                Conoce las novedades, avances y actualizaciones
                del Simulador de Crecimiento de Plantas.
            </p>

        </div>



        <!-- =================================================
             FORMULARIO PARA CREAR NOTICIA
             ================================================= -->

        <?php if ($esAdmin && $idEditar === 0): ?>

            <section class="form-noticia">

                <div class="form-noticia-header">

                    <div>
 

                        <h2>
                            Publicar nueva noticia
                        </h2>

                    </div>

                    <p>
                        Comparte una nueva actualización
                        del proyecto.
                    </p>

                </div>


                <form
                    action="noticias.php"
                    method="POST"
                >

                    <input
                        type="hidden"
                        name="accion"
                        value="crear"
                    >


                    <div class="campo-formulario">

                        <label for="titulo">
                            Título de la noticia
                        </label>

                        <input
                            type="text"
                            id="titulo"
                            name="titulo"
                            placeholder="Ej: Nuevos avances del simulador"
                            required
                        >

                    </div>


                    <div class="campo-formulario">

                        <label for="contenido">
                            Contenido
                        </label>

                        <textarea
                            id="contenido"
                            name="contenido"
                            placeholder="Escribe aquí el contenido de la noticia..."
                            required
                        ></textarea>

                    </div>


                    <div class="campo-formulario">

                        <label for="imagen">
                            Imagen
                        </label>

                        <input
                            type="text"
                            id="imagen"
                            name="imagen"
                            placeholder="Pega aquí la URL de la imagen"
                        >

                        <small>
                            Puedes utilizar una URL de una imagen
                            alojada en Internet.
                        </small>

                    </div>


                    <button
                        type="submit"
                        class="btn-publicar"
                    >
                        Publicar noticia
                    </button>

                </form>

            </section>

        <?php endif; ?>



        <!-- =================================================
             LISTADO DE NOTICIAS
             ================================================= -->

        <section class="lista-noticias">


            <?php if ($resultado && $resultado->num_rows > 0): ?>


                <?php while ($n = $resultado->fetch_assoc()): ?>


                    <!-- =================================================
                         FORMULARIO DE EDICIÓN
                         ================================================= -->

                    <?php if (
                        $esAdmin &&
                        $idEditar === intval($n['id_noticia'])
                    ): ?>


                        <section class="form-editar">

                            <div class="form-editar-header">

                                

                                <div>

                                    <h2>
                                        Editando noticia
                                    </h2>

                                    <p>
                                        Modifica la información
                                        de esta publicación.
                                    </p>

                                </div>

                            </div>


                            <form
                                action="noticias.php"
                                method="POST"
                            >

                                <input
                                    type="hidden"
                                    name="accion"
                                    value="editar"
                                >

                                <input
                                    type="hidden"
                                    name="id_noticia"
                                    value="<?php echo $n['id_noticia']; ?>"
                                >


                                <div class="campo-formulario">

                                    <label>
                                        Título
                                    </label>

                                    <input
                                        type="text"
                                        name="titulo"
                                        value="<?php echo htmlspecialchars($n['titulo']); ?>"
                                        required
                                    >

                                </div>


                                <div class="campo-formulario">

                                    <label>
                                        Contenido
                                    </label>

                                    <textarea
                                        name="contenido"
                                        required
                                    ><?php echo htmlspecialchars($n['contenido']); ?></textarea>

                                </div>


                                <div class="campo-formulario">

                                    <label>
                                        Imagen
                                    </label>

                                    <input
                                        type="text"
                                        name="imagen"
                                        value="<?php echo htmlspecialchars($n['imagen']); ?>"
                                        placeholder="URL de la imagen"
                                    >

                                </div>


                                <div class="acciones-formulario">

                                    <button
                                        type="submit"
                                        class="btn-guardar"
                                    >
                                         Guardar cambios
                                    </button>


                                    <a
                                        href="noticias.php"
                                        class="btn-cancelar"
                                    >
                                        Cancelar
                                    </a>

                                </div>

                            </form>

                        </section>


                    <?php else: ?>


                        <!-- =================================================
                             TARJETA DE NOTICIA
                             ================================================= -->

                        <article class="noticia-card">


                            <!-- IMAGEN -->

                            <?php if (!empty($n['imagen'])): ?>

                                <div class="noticia-imagen-container">

                                    <img
                                        src="<?php echo htmlspecialchars($n['imagen']); ?>"
                                        alt="<?php echo htmlspecialchars($n['titulo']); ?>"
                                        class="noticia-imagen"
                                    >

                                </div>

                            <?php else: ?>

                                <div class="noticia-imagen-vacia">


                                    <p>
                                        Simulador de Crecimiento de Plantas
                                    </p>

                                </div>

                            <?php endif; ?>


                            <!-- INFORMACIÓN DE LA NOTICIA -->

                            <div class="noticia-info">


                                <div class="noticia-meta">

                                    <span class="noticia-categoria">
                                        Simulador de Plantas
                                    </span>


                                    <span class="noticia-fecha">

                                        

                                        <?php

                                        echo date(
                                            'd/m/Y',
                                            strtotime(
                                                $n['fecha_publicacion']
                                            )
                                        );

                                        ?>

                                    </span>

                                </div>


                                <h2 class="noticia-titulo">

                                    <?php
                                    echo htmlspecialchars(
                                        $n['titulo']
                                    );
                                    ?>

                                </h2>


                                <div class="noticia-separador"></div>


                                <div class="noticia-texto">

                                    <?php

                                    echo nl2br(
                                        htmlspecialchars(
                                            $n['contenido']
                                        )
                                    );

                                    ?>

                                </div>


                                <!-- =================================================
                                     CONTROLES DEL ADMINISTRADOR
                                     ================================================= -->

                                <?php if ($esAdmin): ?>

                                    <div class="noticia-admin">

                                        <span class="admin-label">
                                            Administración
                                        </span>


                                        <div class="admin-botones">

                                            <a
                                                href="noticias.php?editar=<?php echo $n['id_noticia']; ?>"
                                                class="btn-editar"
                                            >
                                                Editar
                                            </a>


                                            <a
                                                href="noticias.php?eliminar=<?php echo $n['id_noticia']; ?>"
                                                class="btn-eliminar"
                                                onclick="return confirm('¿Deseas eliminar esta noticia?');"
                                            >
                                         Eliminar
                                            </a>

                                        </div>

                                    </div>

                                <?php endif; ?>


                            </div>

                        </article>


                    <?php endif; ?>


                <?php endwhile; ?>


            <?php else: ?>


                <!-- =================================================
                     SIN NOTICIAS
                     ================================================= -->

                <div class="sin-noticias">

                    <div class="sin-noticias-icono">
                        
                    </div>

                    <h2>
                        No hay noticias publicadas
                    </h2>

                    <p>
                        En este momento no hay noticias disponibles.
                        Pronto encontrarás aquí las novedades
                        del proyecto.
                    </p>

                </div>


            <?php endif; ?>


        </section>



        <!-- VOLVER -->

        <div class="centrar">

            <a
                href="index.php"
                class="btn-inicio"
            >
                ← Volver al inicio
            </a>

        </div>


    </div>

</main>



<!-- =========================================================
     FOOTER
     ========================================================= -->

<footer>

    <div class="footer-contenido">


        <div class="footer-logos">

            <img
                src="imagenes/image-removebg-preview.png"
                alt="Institución Universitaria Pascual Bravo"
                class="logo-footer"
            >

            <img
                src="imagenes/image-removebg-preview (1).png"
                alt="I.E. República de Uruguay"
                class="logo-footer"
            >

        </div>


        <p>
            © 2026 Simulador de Crecimiento de Plantas
        </p>


    </div>

</footer>


</body>

</html>