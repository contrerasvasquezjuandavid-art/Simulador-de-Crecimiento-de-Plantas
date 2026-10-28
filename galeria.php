<?php

/* =========================================================
   CONEXIÓN A LA BASE DE DATOS
   ========================================================= */

include_once("conexion.php");


/* =========================================================
   CONSULTAR LAS IMÁGENES DE LA GALERÍA
   ========================================================= */

$sql = "SELECT
            id_imagen,
            titulo,
            descripcion,
            imagen,
            fecha_subida
        FROM galeria
        ORDER BY fecha_subida DESC";


$galeria = $conexion->query($sql);


/* =========================================================
   FUNCIÓN PARA MOSTRAR TEXTO DE FORMA SEGURA
   ========================================================= */

function limpiar($texto)
{
    return htmlspecialchars(
        $texto ?? '',
        ENT_QUOTES,
        'UTF-8'
    );
}

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
Galería - BioGerminación S.A.S
</title>


<!-- =====================================================
     FUENTE
     ===================================================== -->

<link
    rel="preconnect"
    href="https://fonts.googleapis.com"
>

<link
    rel="preconnect"
    href="https://fonts.gstatic.com"
    crossorigin
>

<link
    href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700;800&display=swap"
    rel="stylesheet"
>


<style>

/* =========================================================
   CONFIGURACIÓN GENERAL
   ========================================================= */

* {

    margin: 0;

    padding: 0;

    box-sizing: border-box;

}


body {

    font-family: 'Montserrat', sans-serif;

    background: #f2f7f9;

    color: #2c3e50;

    min-height: 100vh;

}


/* =========================================================
   MENÚ
   ========================================================= */

nav {

    background:
        linear-gradient(
            90deg,
            #2A7B9B,
            #318698,
            #57C785,
            #EDDD53
        );

    padding: 14px 20px;

    box-shadow:
        0 3px 10px rgba(0,0,0,0.15);

}


nav ul {

    list-style: none;

    display: flex;

    justify-content: center;

    align-items: center;

    gap: 25px;

    flex-wrap: wrap;

}


nav ul li a {

    color: white;

    text-decoration: none;

    font-weight: 600;

    padding: 8px 14px;

    border-radius: 8px;

    transition: 0.3s;

}


nav ul li a:hover {

    background: rgba(255,255,255,0.20);

}


/* =========================================================
   CONTENEDOR PRINCIPAL
   ========================================================= */

.contenedor {

    width: 90%;

    max-width: 1200px;

    margin: 40px auto;

}


/* =========================================================
   ENCABEZADO
   ========================================================= */

.encabezado {

    text-align: center;

    margin-bottom: 40px;

}


.encabezado h1 {

    color: #2A7B9B;

    font-size: 2.4rem;

    margin-bottom: 12px;

}


.encabezado p {

    color: #5f6b75;

    font-size: 1rem;

    max-width: 750px;

    margin: auto;

    line-height: 1.7;

}


/* =========================================================
   GALERÍA
   ========================================================= */

.galeria {

    display: grid;

    grid-template-columns:
        repeat(
            auto-fit,
            minmax(280px, 1fr)
        );

    gap: 25px;

}


/* =========================================================
   TARJETA
   ========================================================= */

.tarjeta {

    background: white;

    border-radius: 15px;

    overflow: hidden;

    box-shadow:
        0 5px 15px rgba(0,0,0,0.10);

    transition:
        transform 0.3s,
        box-shadow 0.3s;

}


.tarjeta:hover {

    transform: translateY(-5px);

    box-shadow:
        0 10px 25px rgba(0,0,0,0.15);

}


/* =========================================================
   IMAGEN
   ========================================================= */

.tarjeta-imagen {

    width: 100%;

    height: 230px;

    background: #e9eef0;

    overflow: hidden;

}


.tarjeta-imagen img {

    width: 100%;

    height: 100%;

    object-fit: cover;

    display: block;

    transition: transform 0.4s;

}


.tarjeta:hover
.tarjeta-imagen img {

    transform: scale(1.05);

}


/* =========================================================
   INFORMACIÓN
   ========================================================= */

.tarjeta-contenido {

    padding: 20px;

}


.tarjeta-contenido h2 {

    color: #2A7B9B;

    font-size: 1.2rem;

    margin-bottom: 10px;

}


.tarjeta-contenido p {

    color: #5f6b75;

    font-size: 0.9rem;

    line-height: 1.6;

}


.fecha {

    display: block;

    margin-top: 12px;

    font-size: 0.75rem;

    color: #8a969d;

}


/* =========================================================
   CUANDO NO HAY IMÁGENES
   ========================================================= */

.sin-imagenes {

    background: white;

    padding: 40px;

    border-radius: 15px;

    text-align: center;

    box-shadow:
        0 5px 15px rgba(0,0,0,0.08);

}


.sin-imagenes h2 {

    color: #2A7B9B;

    margin-bottom: 10px;

}


.sin-imagenes p {

    color: #64748b;

}


/* =========================================================
   PIE DE PÁGINA
   ========================================================= */

footer {

    margin-top: 50px;

    background: #2A7B9B;

    color: white;

    text-align: center;

    padding: 25px;

}


footer p {

    margin: 0;

}


/* =========================================================
   RESPONSIVE
   ========================================================= */

@media (max-width: 700px) {

    .contenedor {

        width: 94%;

    }


    .encabezado h1 {

        font-size: 1.8rem;

    }


    .galeria {

        grid-template-columns: 1fr;

    }


    nav ul {

        gap: 10px;

    }

}

</style>

</head>


<body>


<!-- =====================================================
     MENÚ
     ===================================================== -->

<nav>

<ul>

<li>
<a href="index.php">
Inicio
</a>
</li>

<li>
<a href="galeria.php">
Galería
</a>
</li>

<li>
<a href="foro.php">
Foro
</a>
</li>

<li>
<a href="Contacto.php">
Contáctanos
</a>
</li>

<li>
<a href="login.php">
Iniciar sesión
</a>
</li>

</ul>

</nav>


<!-- =====================================================
     CONTENIDO
     ===================================================== -->

<main class="contenedor">


<div class="encabezado">

<h1>
Galería de BioGerminación
</h1>

<p>
En esta sección puedes observar las imágenes
relacionadas con el proceso de crecimiento,
biogerminación y desarrollo de las plantas.
</p>

</div>


<!-- =====================================================
     GALERÍA DINÁMICA
     ===================================================== -->

<?php if ($galeria && $galeria->num_rows > 0): ?>


<div class="galeria">


<?php while ($imagen = $galeria->fetch_assoc()): ?>


<article class="tarjeta">


<!-- =================================================
     IMAGEN
     ================================================= -->

<div class="tarjeta-imagen">

<img
    src="<?php echo limpiar($imagen['imagen']); ?>"
    alt="<?php echo limpiar($imagen['titulo']); ?>"
    onerror="this.src='https://via.placeholder.com/600x400?text=Imagen+no+disponible';"
>

</div>


<!-- =================================================
     INFORMACIÓN
     ================================================= -->

<div class="tarjeta-contenido">


<h2>

<?php
echo limpiar(
    $imagen['titulo']
);
?>

</h2>


<?php if (!empty($imagen['descripcion'])): ?>

<p>

<?php
echo nl2br(
    limpiar(
        $imagen['descripcion']
    )
);
?>

</p>

<?php endif; ?>


<?php if (!empty($imagen['fecha_subida'])): ?>

<span class="fecha">

Subida:
<?php
echo limpiar(
    $imagen['fecha_subida']
);
?>

</span>

<?php endif; ?>


</div>


</article>


<?php endwhile; ?>


</div>


<?php else: ?>


<!-- =================================================
     SIN IMÁGENES
     ================================================= -->

<div class="sin-imagenes">

<h2>
No hay imágenes todavía
</h2>

<p>
Las imágenes que agregues desde el
panel de administrador aparecerán aquí
automáticamente.
</p>

</div>


<?php endif; ?>


</main>


<!-- =====================================================
     FOOTER
     ===================================================== -->

<footer>

<p>
© 2026 BioGerminación S.A.S - Monitoreo de Vida
</p>

</footer>


</body>

</html>
 
