<?php

session_start();

include_once("conexion.php");


/* =========================================================
   PROTECCIÓN DEL PANEL
   ========================================================= */

if (!isset($_SESSION['id_usuario'])) {
    header("Location: login.php");
    exit();
}

$rol = strtolower(trim($_SESSION['rol'] ?? ''));

if ($rol !== 'administrador' && $rol !== 'admin') {
    header("Location: index.php");
    exit();
}


/* =========================================================
   DATOS DEL ADMINISTRADOR
   ========================================================= */

$idAdmin = intval($_SESSION['id_usuario']);
$nombreAdmin = $_SESSION['nombre'] ?? 'Administrador';


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


/* =========================================================
   VARIABLES
   ========================================================= */

$mensaje = '';
$error = '';

$editarNoticia = null;
$editarGaleria = null;
$editarForo = null;
$editarContenido = null;


/* =========================================================
   PROCESAR ACCIONES
   ========================================================= */

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $accion = $_POST['accion'] ?? '';


    /* =====================================================
       USUARIOS - CAMBIAR ROL
       ===================================================== */

    if ($accion === 'cambiar_rol') {

        $id = intval($_POST['id'] ?? 0);
        $nuevoRol = $_POST['rol'] ?? 'usuario';

        if (
            $nuevoRol !== 'usuario' &&
            $nuevoRol !== 'administrador'
        ) {
            $nuevoRol = 'usuario';
        }

        if ($id === $idAdmin) {

            $error = "No puedes cambiar tu propio rol.";

        } else {

            $stmt = $conexion->prepare(
                "UPDATE usuarios
                 SET rol = ?
                 WHERE id_usuario = ?"
            );

            $stmt->bind_param(
                "si",
                $nuevoRol,
                $id
            );

            if ($stmt->execute()) {
                $mensaje = "Rol actualizado correctamente.";
            } else {
                $error = "No se pudo actualizar el rol.";
            }

            $stmt->close();
        }
    }


    /* =====================================================
       USUARIOS - ELIMINAR
       ===================================================== */

    elseif ($accion === 'eliminar_usuario') {

        $id = intval($_POST['id'] ?? 0);

        if ($id === $idAdmin) {

            $error = "No puedes eliminar tu propia cuenta.";

        } else {

            $stmt = $conexion->prepare(
                "DELETE FROM usuarios
                 WHERE id_usuario = ?"
            );

            $stmt->bind_param("i", $id);

            if ($stmt->execute()) {
                $mensaje = "Usuario eliminado correctamente.";
            } else {
                $error = "No se pudo eliminar el usuario.";
            }

            $stmt->close();
        }
    }


    /* =====================================================
       NOTICIAS - AGREGAR
       ===================================================== */

    elseif ($accion === 'agregar_noticia') {

        $titulo = trim($_POST['titulo'] ?? '');
        $contenido = trim($_POST['contenido'] ?? '');
        $imagen = trim($_POST['imagen'] ?? '');

        if ($titulo === '' || $contenido === '') {

            $error = "El título y el contenido son obligatorios.";

        } else {

            $stmt = $conexion->prepare(
                "INSERT INTO noticias
                (titulo, contenido, imagen, id_administrador)
                VALUES (?, ?, ?, ?)"
            );

            $stmt->bind_param(
                "sssi",
                $titulo,
                $contenido,
                $imagen,
                $idAdmin
            );

            if ($stmt->execute()) {
                $mensaje = "Noticia agregada correctamente.";
            } else {
                $error = "No se pudo agregar la noticia.";
            }

            $stmt->close();
        }
    }


    /* =====================================================
       NOTICIAS - ACTUALIZAR
       ===================================================== */

    elseif ($accion === 'editar_noticia') {

        $id = intval($_POST['id'] ?? 0);

        $titulo = trim($_POST['titulo'] ?? '');
        $contenido = trim($_POST['contenido'] ?? '');
        $imagen = trim($_POST['imagen'] ?? '');

        $stmt = $conexion->prepare(
            "UPDATE noticias
             SET titulo = ?,
                 contenido = ?,
                 imagen = ?
             WHERE id_noticia = ?"
        );

        $stmt->bind_param(
            "sssi",
            $titulo,
            $contenido,
            $imagen,
            $id
        );

        if ($stmt->execute()) {
            $mensaje = "Noticia actualizada correctamente.";
        } else {
            $error = "No se pudo actualizar la noticia.";
        }

        $stmt->close();
    }


    /* =====================================================
       NOTICIAS - ELIMINAR
       ===================================================== */

    elseif ($accion === 'eliminar_noticia') {

        $id = intval($_POST['id'] ?? 0);

        $stmt = $conexion->prepare(
            "DELETE FROM noticias
             WHERE id_noticia = ?"
        );

        $stmt->bind_param("i", $id);

        if ($stmt->execute()) {
            $mensaje = "Noticia eliminada correctamente.";
        } else {
            $error = "No se pudo eliminar la noticia.";
        }

        $stmt->close();
    }


    /* =====================================================
       GALERÍA - AGREGAR
       ===================================================== */

    elseif ($accion === 'agregar_galeria') {

        $titulo = trim($_POST['titulo'] ?? '');
        $descripcion = trim($_POST['descripcion'] ?? '');
        $imagen = trim($_POST['imagen'] ?? '');

        if ($titulo === '' || $imagen === '') {

            $error = "El título y la imagen son obligatorios.";

        } else {

            $stmt = $conexion->prepare(
                "INSERT INTO galeria
                (titulo, descripcion, imagen, id_administrador)
                VALUES (?, ?, ?, ?)"
            );

            $stmt->bind_param(
                "sssi",
                $titulo,
                $descripcion,
                $imagen,
                $idAdmin
            );

            if ($stmt->execute()) {
                $mensaje = "Imagen agregada correctamente.";
            } else {
                $error = "No se pudo agregar la imagen.";
            }

            $stmt->close();
        }
    }


    /* =====================================================
       GALERÍA - ACTUALIZAR
       ===================================================== */

    elseif ($accion === 'editar_galeria') {

        $id = intval($_POST['id'] ?? 0);

        $titulo = trim($_POST['titulo'] ?? '');
        $descripcion = trim($_POST['descripcion'] ?? '');
        $imagen = trim($_POST['imagen'] ?? '');

        $stmt = $conexion->prepare(
            "UPDATE galeria
             SET titulo = ?,
                 descripcion = ?,
                 imagen = ?
             WHERE id_imagen = ?"
        );

        $stmt->bind_param(
            "sssi",
            $titulo,
            $descripcion,
            $imagen,
            $id
        );

        if ($stmt->execute()) {
            $mensaje = "Imagen actualizada correctamente.";
        } else {
            $error = "No se pudo actualizar la imagen.";
        }

        $stmt->close();
    }


    /* =====================================================
       GALERÍA - ELIMINAR
       ===================================================== */

    elseif ($accion === 'eliminar_galeria') {

        $id = intval($_POST['id'] ?? 0);

        $stmt = $conexion->prepare(
            "DELETE FROM galeria
             WHERE id_imagen = ?"
        );

        $stmt->bind_param("i", $id);

        if ($stmt->execute()) {
            $mensaje = "Imagen eliminada correctamente.";
        } else {
            $error = "No se pudo eliminar la imagen.";
        }

        $stmt->close();
    }


    /* =====================================================
       FORO - ACTUALIZAR
       ===================================================== */

    elseif ($accion === 'editar_foro') {

        $id = intval($_POST['id'] ?? 0);

        $titulo = trim($_POST['titulo'] ?? '');
        $contenido = trim($_POST['contenido'] ?? '');

        $stmt = $conexion->prepare(
            "UPDATE foro
             SET titulo = ?,
                 contenido = ?
             WHERE id_publicacion = ?"
        );

        $stmt->bind_param(
            "ssi",
            $titulo,
            $contenido,
            $id
        );

        if ($stmt->execute()) {
            $mensaje = "Publicación actualizada correctamente.";
        } else {
            $error = "No se pudo actualizar la publicación.";
        }

        $stmt->close();
    }


    /* =====================================================
       FORO - ELIMINAR
       ===================================================== */

    elseif ($accion === 'eliminar_foro') {

        $id = intval($_POST['id'] ?? 0);

        $stmt = $conexion->prepare(
            "DELETE FROM foro
             WHERE id_publicacion = ?"
        );

        $stmt->bind_param("i", $id);

        if ($stmt->execute()) {
            $mensaje = "Publicación eliminada correctamente.";
        } else {
            $error = "No se pudo eliminar la publicación.";
        }

        $stmt->close();
    }


    /* =====================================================
       COMENTARIOS - ELIMINAR
       ===================================================== */

    elseif ($accion === 'eliminar_comentario') {

        $id = intval($_POST['id'] ?? 0);

        $stmt = $conexion->prepare(
            "DELETE FROM comentarios
             WHERE id_comentario = ?"
        );

        $stmt->bind_param("i", $id);

        if ($stmt->execute()) {
            $mensaje = "Comentario eliminado correctamente.";
        } else {
            $error = "No se pudo eliminar el comentario.";
        }

        $stmt->close();
    }


    /* =====================================================
       CONTACTO - MARCAR COMO LEÍDO
       ===================================================== */

    elseif ($accion === 'leer_contacto') {

        $id = intval($_POST['id'] ?? 0);

        $stmt = $conexion->prepare(
            "UPDATE contacto
             SET estado = 'leido'
             WHERE id_contacto = ?"
        );

        $stmt->bind_param("i", $id);

        if ($stmt->execute()) {
            $mensaje = "Mensaje marcado como leído.";
        } else {
            $error = "No se pudo actualizar el mensaje.";
        }

        $stmt->close();
    }


    /* =====================================================
       CONTACTO - ELIMINAR
       ===================================================== */

    elseif ($accion === 'eliminar_contacto') {

        $id = intval($_POST['id'] ?? 0);

        $stmt = $conexion->prepare(
            "DELETE FROM contacto
             WHERE id_contacto = ?"
        );

        $stmt->bind_param("i", $id);

        if ($stmt->execute()) {
            $mensaje = "Mensaje eliminado correctamente.";
        } else {
            $error = "No se pudo eliminar el mensaje.";
        }

        $stmt->close();
    }


    /* =====================================================
       CONTENIDO - AGREGAR
       ===================================================== */

    elseif ($accion === 'agregar_contenido') {

        $seccion = trim($_POST['seccion'] ?? '');
        $titulo = trim($_POST['titulo'] ?? '');
        $texto = trim($_POST['texto'] ?? '');

        if ($seccion === '') {

            $error = "La sección es obligatoria.";

        } else {

            $stmt = $conexion->prepare(
                "INSERT INTO contenido
                (seccion, titulo, texto)
                VALUES (?, ?, ?)"
            );

            $stmt->bind_param(
                "sss",
                $seccion,
                $titulo,
                $texto
            );

            if ($stmt->execute()) {
                $mensaje = "Contenido agregado correctamente.";
            } else {
                $error = "No se pudo agregar el contenido.";
            }

            $stmt->close();
        }
    }


    /* =====================================================
       CONTENIDO - ACTUALIZAR
       ===================================================== */

    elseif ($accion === 'editar_contenido') {

        $id = intval($_POST['id'] ?? 0);

        $seccion = trim($_POST['seccion'] ?? '');
        $titulo = trim($_POST['titulo'] ?? '');
        $texto = trim($_POST['texto'] ?? '');

        $stmt = $conexion->prepare(
            "UPDATE contenido
             SET seccion = ?,
                 titulo = ?,
                 texto = ?
             WHERE id_contenido = ?"
        );

        $stmt->bind_param(
            "sssi",
            $seccion,
            $titulo,
            $texto,
            $id
        );

        if ($stmt->execute()) {
            $mensaje = "Contenido actualizado correctamente.";
        } else {
            $error = "No se pudo actualizar el contenido.";
        }

        $stmt->close();
    }


    /* =====================================================
       CONTENIDO - ELIMINAR
       ===================================================== */

    elseif ($accion === 'eliminar_contenido') {

        $id = intval($_POST['id'] ?? 0);

        $stmt = $conexion->prepare(
            "DELETE FROM contenido
             WHERE id_contenido = ?"
        );

        $stmt->bind_param("i", $id);

        if ($stmt->execute()) {
            $mensaje = "Contenido eliminado correctamente.";
        } else {
            $error = "No se pudo eliminar el contenido.";
        }

        $stmt->close();
    }

}


/* =========================================================
   CONSULTAS
   ========================================================= */


/* USUARIOS */

$usuarios = $conexion->query(
    "SELECT *
     FROM usuarios
     ORDER BY id_usuario DESC"
);


/* NOTICIAS */

$noticias = $conexion->query(
    "SELECT *
     FROM noticias
     ORDER BY fecha_publicacion DESC"
);


/* GALERÍA */

$galeria = $conexion->query(
    "SELECT *
     FROM galeria
     ORDER BY fecha_subida DESC"
);


/* FORO */

$foro = $conexion->query(
    "SELECT
        f.id_publicacion,
        f.id_usuario,
        f.titulo,
        f.contenido,
        f.fecha_publicacion,
        u.nombre
     FROM foro f
     INNER JOIN usuarios u
     ON f.id_usuario = u.id_usuario
     ORDER BY f.fecha_publicacion DESC"
);


/* COMENTARIOS */

$comentarios = $conexion->query(
    "SELECT
        c.id_comentario,
        c.comentario,
        c.fecha_comentario,
        u.nombre,
        f.titulo
     FROM comentarios c
     INNER JOIN usuarios u
     ON c.id_usuario = u.id_usuario
     INNER JOIN foro f
     ON c.id_publicacion = f.id_publicacion
     ORDER BY c.fecha_comentario DESC"
);


/* CONTACTO */

$contactos = $conexion->query(
    "SELECT *
     FROM contacto
     ORDER BY fecha_envio DESC"
);


/* CONTENIDO */

$contenidos = $conexion->query(
    "SELECT *
     FROM contenido
     ORDER BY id_contenido DESC"
);


/* =========================================================
   CONTADORES
   ========================================================= */

$totalUsuarios = $conexion
    ->query("SELECT COUNT(*) AS total FROM usuarios")
    ->fetch_assoc()['total'];

$totalNoticias = $conexion
    ->query("SELECT COUNT(*) AS total FROM noticias")
    ->fetch_assoc()['total'];

$totalGaleria = $conexion
    ->query("SELECT COUNT(*) AS total FROM galeria")
    ->fetch_assoc()['total'];

$totalForo = $conexion
    ->query("SELECT COUNT(*) AS total FROM foro")
    ->fetch_assoc()['total'];

$totalComentarios = $conexion
    ->query("SELECT COUNT(*) AS total FROM comentarios")
    ->fetch_assoc()['total'];

$totalContactos = $conexion
    ->query("SELECT COUNT(*) AS total FROM contacto")
    ->fetch_assoc()['total'];

$totalPendientes = $conexion
    ->query(
        "SELECT COUNT(*) AS total
         FROM contacto
         WHERE estado = 'pendiente'"
    )
    ->fetch_assoc()['total'];

$totalContenido = $conexion
    ->query("SELECT COUNT(*) AS total FROM contenido")
    ->fetch_assoc()['total'];

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
Panel de Administrador
</title>

<link
    rel="stylesheet"
    href="styles.css"
>


<style>

/* =========================================================
   GENERAL
   ========================================================= */

body {
    background: #f2f7f9;
}

.admin-contenedor {

    width: 95%;

    max-width: 1450px;

    margin: 30px auto;

}


/* =========================================================
   ENCABEZADO
   ========================================================= */

.admin-header {

    background:
        linear-gradient(
            90deg,
            #2A7B9B,
            #57C785
        );

    color: white;

    padding: 25px;

    border-radius: 15px;

    margin-bottom: 20px;

    box-shadow:
        0 5px 15px rgba(0,0,0,.10);

}

.admin-header h1 {

    margin: 0 0 8px 0;

}

.admin-header p {

    margin: 5px 0;

}


/* =========================================================
   MENSAJES
   ========================================================= */

.mensaje {

    padding: 12px 15px;

    border-radius: 8px;

    margin-bottom: 20px;

    font-weight: 600;

}

.exito {

    background: #dcfce7;

    color: #166534;

    border: 1px solid #4ade80;

}

.error {

    background: #fee2e2;

    color: #991b1b;

    border: 1px solid #f87171;

}


/* =========================================================
   ESTADÍSTICAS
   ========================================================= */

.estadisticas {

    display: grid;

    grid-template-columns:
        repeat(
            auto-fit,
            minmax(150px, 1fr)
        );

    gap: 15px;

    margin-bottom: 25px;

}

.estadistica {

    background: white;

    border-radius: 12px;

    padding: 18px;

    text-align: center;

    box-shadow:
        0 3px 10px rgba(0,0,0,.08);

}

.estadistica .numero {

    display: block;

    font-size: 2rem;

    font-weight: bold;

    color: #2A7B9B;

}

.estadistica .nombre {

    color: #64748b;

}


/* =========================================================
   SECCIONES
   ========================================================= */

.admin-seccion {

    background: white;

    padding: 20px;

    border-radius: 12px;

    margin-bottom: 25px;

    box-shadow:
        0 3px 10px rgba(0,0,0,.08);

    overflow-x: auto;

}

.admin-seccion h2 {

    color: #2A7B9B;

    border-bottom:
        2px solid #e2e8f0;

    padding-bottom: 10px;

}


/* =========================================================
   TABLAS
   ========================================================= */

.tabla {

    width: 100%;

    border-collapse: collapse;

    margin-top: 15px;

    min-width: 750px;

}

.tabla th {

    background: #2A7B9B;

    color: white;

    padding: 10px;

    text-align: left;

}

.tabla td {

    padding: 10px;

    border-bottom:
        1px solid #e2e8f0;

    vertical-align: top;

}

.tabla tr:hover {

    background: #f8fafc;

}


/* =========================================================
   BOTONES
   ========================================================= */

.btn {

    border: none;

    padding: 8px 12px;

    border-radius: 6px;

    cursor: pointer;

    font-weight: bold;

    margin: 2px;

}

.btn-agregar {

    background: #2A7B9B;

    color: white;

}

.btn-editar {

    background: #2563eb;

    color: white;

}

.btn-eliminar {

    background: #dc2626;

    color: white;

}

.btn-leer {

    background: #16a34a;

    color: white;

}


/* =========================================================
   FORMULARIOS
   ========================================================= */

.form-admin {

    background: #f8fafc;

    padding: 15px;

    border-radius: 10px;

    margin-top: 15px;

    margin-bottom: 15px;

}

.form-admin label {

    display: block;

    font-weight: bold;

    margin-top: 8px;

    color: #334155;

}

.form-admin input,
.form-admin textarea,
.form-admin select {

    width: 100%;

    padding: 10px;

    margin:
        5px 0 10px 0;

    border:
        1px solid #cbd5e1;

    border-radius: 6px;

    box-sizing: border-box;

}

.form-admin textarea {

    min-height: 120px;

    resize: vertical;

}


/* =========================================================
   BADGES
   ========================================================= */

.badge {

    display: inline-block;

    padding:
        4px 8px;

    border-radius: 12px;

    font-size: .75rem;

    font-weight: bold;

}

.badge-admin {

    background: #dbeafe;

    color: #1e40af;

}

.badge-user {

    background: #dcfce7;

    color: #166534;

}

.badge-pendiente {

    background: #fef3c7;

    color: #92400e;

}

.badge-leido {

    background: #dcfce7;

    color: #166534;

}


/* =========================================================
   IMÁGENES
   ========================================================= */

.miniatura {

    width: 100px;

    height: 70px;

    object-fit: cover;

    border-radius: 6px;

}


/* =========================================================
   TEXTO
   ========================================================= */

.texto {

    max-width: 350px;

    white-space: pre-wrap;

    word-break: break-word;

}


/* =========================================================
   DETALLES
   ========================================================= */

details {

    margin-bottom: 8px;

}

details summary {

    list-style: none;

    display: inline-block;

}

details summary::-webkit-details-marker {

    display: none;

}


/* =========================================================
   RESPONSIVE
   ========================================================= */

@media (max-width: 700px) {

    .admin-contenedor {

        width: 92%;

    }

    .admin-header {

        padding: 18px;

    }

    .admin-seccion {

        padding: 15px;

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
<a href="foro.php">
Foro
</a>
</li>

<li>
<a href="Noticias.php">
Noticias
</a>
</li>

<li>
<a href="logout.php">
Cerrar sesión
</a>
</li>

</ul>

</nav>


<div class="admin-contenedor">


<!-- =====================================================
     ENCABEZADO
     ===================================================== -->

<div class="admin-header">

<h1> Panel de Administrador
</h1>

<p>

Bienvenido,
<strong>
<?php echo limpiar($nombreAdmin); ?>
</strong>

</p>

<p>
Desde aquí puedes administrar todo el contenido
del sitio web y los usuarios.
</p>

</div>


<!-- =====================================================
     MENSAJES
     ===================================================== -->

<?php if ($mensaje !== ''): ?>

<div class="mensaje exito">

<?php echo limpiar($mensaje); ?>

</div>

<?php endif; ?>


<?php if ($error !== ''): ?>

<div class="mensaje error">

<?php echo limpiar($error); ?>

</div>

<?php endif; ?>


<!-- =====================================================
     ESTADÍSTICAS
     ===================================================== -->

<div class="estadisticas">


<div class="estadistica">

<span class="numero">
<?php echo $totalUsuarios; ?>
</span>

<span class="nombre">
Usuarios
</span>

</div>


<div class="estadistica">

<span class="numero">
<?php echo $totalNoticias; ?>
</span>

<span class="nombre">
 Noticias
</span>

</div>


<div class="estadistica">

<span class="numero">
<?php echo $totalGaleria; ?>
</span>

<span class="nombre">
 Galería
</span>

</div>


<div class="estadistica">

<span class="numero">
<?php echo $totalForo; ?>
</span>

<span class="nombre">
 Foro
</span>

</div>


<div class="estadistica">

<span class="numero">
<?php echo $totalComentarios; ?>
</span>

<span class="nombre">
 Comentarios
</span>

</div>


<div class="estadistica">

<span class="numero">
<?php echo $totalContactos; ?>
</span>

<span class="nombre">
 Contactos
</span>

</div>


<div class="estadistica">

<span class="numero">
<?php echo $totalPendientes; ?>
</span>

<span class="nombre">
Pendientes
</span>

</div>


<div class="estadistica">

<span class="numero">
<?php echo $totalContenido; ?>
</span>

<span class="nombre">
 Contenido
</span>

</div>


</div>


<!-- =====================================================
     1. USUARIOS
     ===================================================== -->

<section class="admin-seccion">

<h2>
 Administración de Usuarios
</h2>


<table class="tabla">

<tr>

<th>ID</th>
<th>Nombre</th>
<th>Correo</th>
<th>Rol</th>
<th>Fecha</th>
<th>Acciones</th>

</tr>


<?php while ($u = $usuarios->fetch_assoc()): ?>

<tr>

<td>
<?php echo $u['id_usuario']; ?>
</td>

<td>
<?php echo limpiar($u['nombre']); ?>
</td>

<td>
<?php echo limpiar($u['correo']); ?>
</td>

<td>

<span class="badge
<?php
echo $u['rol'] === 'administrador'
    ? ' badge-admin'
    : ' badge-user';
?>
">

<?php echo limpiar($u['rol']); ?>

</span>

</td>

<td>
<?php echo limpiar($u['fecha_registro']); ?>
</td>

<td>


<?php if ($u['id_usuario'] != $idAdmin): ?>


<form method="POST">

<input
type="hidden"
name="accion"
value="cambiar_rol"
>

<input
type="hidden"
name="id"
value="<?php echo $u['id_usuario']; ?>"
>

<select name="rol">

<option
value="usuario"
<?php
echo $u['rol'] === 'usuario'
    ? 'selected'
    : '';
?>
>
Usuario
</option>

<option
value="administrador"
<?php
echo $u['rol'] === 'administrador'
    ? 'selected'
    : '';
?>
>
Administrador
</option>

</select>

<button
type="submit"
class="btn btn-editar"
>
Guardar rol
</button>

</form>


<form
method="POST"
onsubmit="return confirm('¿Eliminar este usuario?');"
>

<input
type="hidden"
name="accion"
value="eliminar_usuario"
>

<input
type="hidden"
name="id"
value="<?php echo $u['id_usuario']; ?>"
>

<button
type="submit"
class="btn btn-eliminar"
>
Eliminar
</button>

</form>


<?php else: ?>

<strong>
Administrador actual
</strong>

<?php endif; ?>


</td>

</tr>

<?php endwhile; ?>

</table>

</section>


<!-- =====================================================
     2. NOTICIAS
     ===================================================== -->

<section class="admin-seccion">

<h2>
 Administración de Noticias
</h2>


<form
method="POST"
class="form-admin"
>

<h3>
Agregar noticia
</h3>

<input
type="hidden"
name="accion"
value="agregar_noticia"
>

<label>
Título
</label>

<input
type="text"
name="titulo"
required
>

<label>
Contenido
</label>

<textarea
name="contenido"
required
></textarea>

<label>
URL de imagen
</label>

<input
type="url"
name="imagen"
placeholder="https://..."
>

<button
type="submit"
class="btn btn-agregar"
>
Agregar noticia
</button>

</form>


<table class="tabla">

<tr>

<th>ID</th>
<th>Título</th>
<th>Contenido</th>
<th>Imagen</th>
<th>Fecha</th>
<th>Acciones</th>

</tr>


<?php while ($n = $noticias->fetch_assoc()): ?>

<tr>

<td>
<?php echo $n['id_noticia']; ?>
</td>

<td>
<?php echo limpiar($n['titulo']); ?>
</td>

<td class="texto">
<?php echo limpiar($n['contenido']); ?>
</td>

<td>

<?php if (!empty($n['imagen'])): ?>

<img
src="<?php echo limpiar($n['imagen']); ?>"
class="miniatura"
alt="Noticia"
>

<?php endif; ?>

</td>

<td>
<?php echo limpiar($n['fecha_publicacion']); ?>
</td>

<td>


<details>

<summary class="btn btn-editar">
Editar
</summary>

<form
method="POST"
class="form-admin"
>

<input
type="hidden"
name="accion"
value="editar_noticia"
>

<input
type="hidden"
name="id"
value="<?php echo $n['id_noticia']; ?>"
>

<label>
Título
</label>

<input
type="text"
name="titulo"
value="<?php echo limpiar($n['titulo']); ?>"
required
>

<label>
Contenido
</label>

<textarea
name="contenido"
required
><?php echo limpiar($n['contenido']); ?></textarea>

<label>
URL de imagen
</label>

<input
type="url"
name="imagen"
value="<?php echo limpiar($n['imagen']); ?>"
>

<button
type="submit"
class="btn btn-editar"
>
Guardar cambios
</button>

</form>

</details>


<form
method="POST"
onsubmit="return confirm('¿Eliminar esta noticia?');"
>

<input
type="hidden"
name="accion"
value="eliminar_noticia"
>

<input
type="hidden"
name="id"
value="<?php echo $n['id_noticia']; ?>"
>

<button
type="submit"
class="btn btn-eliminar"
>
Eliminar
</button>

</form>


</td>

</tr>

<?php endwhile; ?>

</table>

</section>


<!-- =====================================================
     3. GALERÍA
     ===================================================== -->

<section class="admin-seccion">

<h2>
 Administración de Galería
</h2>


<form
method="POST"
class="form-admin"
>

<h3>
Agregar imagen
</h3>

<input
type="hidden"
name="accion"
value="agregar_galeria"
>

<label>
Título
</label>

<input
type="text"
name="titulo"
required
>

<label>
Descripción
</label>

<textarea
name="descripcion"
></textarea>

<label>
URL de imagen
</label>

<input
type="url"
name="imagen"
placeholder="https://..."
required
>

<button
type="submit"
class="btn btn-agregar"
>
Agregar imagen
</button>

</form>


<table class="tabla">

<tr>

<th>ID</th>
<th>Imagen</th>
<th>Título</th>
<th>Descripción</th>
<th>Fecha</th>
<th>Acciones</th>

</tr>


<?php while ($g = $galeria->fetch_assoc()): ?>

<tr>

<td>
<?php echo $g['id_imagen']; ?>
</td>

<td>

<img
src="<?php echo limpiar($g['imagen']); ?>"
class="miniatura"
alt="Imagen"
>

</td>

<td>
<?php echo limpiar($g['titulo']); ?>
</td>

<td>
<?php echo limpiar($g['descripcion']); ?>
</td>

<td>
<?php echo limpiar($g['fecha_subida']); ?>
</td>

<td>


<details>

<summary class="btn btn-editar">
Editar
</summary>

<form
method="POST"
class="form-admin"
>

<input
type="hidden"
name="accion"
value="editar_galeria"
>

<input
type="hidden"
name="id"
value="<?php echo $g['id_imagen']; ?>"
>

<label>
Título
</label>

<input
type="text"
name="titulo"
value="<?php echo limpiar($g['titulo']); ?>"
required
>

<label>
Descripción
</label>

<textarea
name="descripcion"
><?php echo limpiar($g['descripcion']); ?></textarea>

<label>
URL de imagen
</label>

<input
type="url"
name="imagen"
value="<?php echo limpiar($g['imagen']); ?>"
required
>

<button
type="submit"
class="btn btn-editar"
>
Guardar cambios
</button>

</form>

</details>


<form
method="POST"
onsubmit="return confirm('¿Eliminar esta imagen?');"
>

<input
type="hidden"
name="accion"
value="eliminar_galeria"
>

<input
type="hidden"
name="id"
value="<?php echo $g['id_imagen']; ?>"
>

<button
type="submit"
class="btn btn-eliminar"
>
Eliminar
</button>

</form>


</td>

</tr>

<?php endwhile; ?>

</table>

</section>


<!-- =====================================================
     4. FORO
     ===================================================== -->

<section class="admin-seccion">

<h2>
Administración del Foro
</h2>


<table class="tabla">

<tr>

<th>ID</th>
<th>Usuario</th>
<th>Título</th>
<th>Contenido</th>
<th>Fecha</th>
<th>Acciones</th>

</tr>


<?php while ($f = $foro->fetch_assoc()): ?>

<tr>

<td>
<?php echo $f['id_publicacion']; ?>
</td>

<td>
<?php echo limpiar($f['nombre']); ?>
</td>

<td>
<?php echo limpiar($f['titulo']); ?>
</td>

<td class="texto">
<?php echo limpiar($f['contenido']); ?>
</td>

<td>
<?php echo limpiar($f['fecha_publicacion']); ?>
</td>

<td>


<details>

<summary class="btn btn-editar">
Editar
</summary>

<form
method="POST"
class="form-admin"
>

<input
type="hidden"
name="accion"
value="editar_foro"
>

<input
type="hidden"
name="id"
value="<?php echo $f['id_publicacion']; ?>"
>

<label>
Título
</label>

<input
type="text"
name="titulo"
value="<?php echo limpiar($f['titulo']); ?>"
required
>

<label>
Contenido
</label>

<textarea
name="contenido"
required
><?php echo limpiar($f['contenido']); ?></textarea>

<button
type="submit"
class="btn btn-editar"
>
Guardar
</button>

</form>

</details>


<form
method="POST"
onsubmit="
return confirm(
'¿Eliminar esta publicación? También se eliminarán sus comentarios.'
);
"
>

<input
type="hidden"
name="accion"
value="eliminar_foro"
>

<input
type="hidden"
name="id"
value="<?php echo $f['id_publicacion']; ?>"
>

<button
type="submit"
class="btn btn-eliminar"
>
Eliminar
</button>

</form>


</td>

</tr>

<?php endwhile; ?>

</table>

</section>


<!-- =====================================================
     5. COMENTARIOS
     ===================================================== -->

<section class="admin-seccion">

<h2>
 Moderación de Comentarios
</h2>


<table class="tabla">

<tr>

<th>ID</th>
<th>Usuario</th>
<th>Publicación</th>
<th>Comentario</th>
<th>Fecha</th>
<th>Acción</th>

</tr>


<?php if ($comentarios && $comentarios->num_rows > 0): ?>


<?php while ($c = $comentarios->fetch_assoc()): ?>

<tr>

<td>
<?php echo $c['id_comentario']; ?>
</td>

<td>
<?php echo limpiar($c['nombre']); ?>
</td>

<td>
<?php echo limpiar($c['titulo']); ?>
</td>

<td class="texto">
<?php echo limpiar($c['comentario']); ?>
</td>

<td>
<?php echo limpiar($c['fecha_comentario']); ?>
</td>

<td>

<form
method="POST"
onsubmit="return confirm('¿Eliminar este comentario?');"
>

<input
type="hidden"
name="accion"
value="eliminar_comentario"
>

<input
type="hidden"
name="id"
value="<?php echo $c['id_comentario']; ?>"
>

<button
type="submit"
class="btn btn-eliminar"
>
Eliminar
</button>

</form>

</td>

</tr>

<?php endwhile; ?>


<?php else: ?>

<tr>

<td colspan="6">

No hay comentarios registrados.

</td>

</tr>

<?php endif; ?>

</table>

</section>


<!-- =====================================================
     6. CONTACTO
     ===================================================== -->

<section class="admin-seccion">

<h2>
Mensajes de Contacto
</h2>


<table class="tabla">

<tr>

<th>ID</th>
<th>Nombre</th>
<th>Correo</th>
<th>Asunto</th>
<th>Mensaje</th>
<th>Fecha</th>
<th>Estado</th>
<th>Acciones</th>

</tr>


<?php while ($c = $contactos->fetch_assoc()): ?>

<tr>

<td>
<?php echo $c['id_contacto']; ?>
</td>

<td>
<?php echo limpiar($c['nombre']); ?>
</td>

<td>
<?php echo limpiar($c['correo']); ?>
</td>

<td>
<?php echo limpiar($c['asunto']); ?>
</td>

<td class="texto">
<?php echo limpiar($c['mensaje']); ?>
</td>

<td>
<?php echo limpiar($c['fecha_envio']); ?>
</td>

<td>


<?php if ($c['estado'] === 'pendiente'): ?>

<span class="badge badge-pendiente">
Pendiente
</span>

<?php else: ?>

<span class="badge badge-leido">
Leído
</span>

<?php endif; ?>


</td>

<td>


<?php if ($c['estado'] === 'pendiente'): ?>

<form
method="POST"
>

<input
type="hidden"
name="accion"
value="leer_contacto"
>

<input
type="hidden"
name="id"
value="<?php echo $c['id_contacto']; ?>"
>

<button
type="submit"
class="btn btn-leer"
>
Marcar leído
</button>

</form>

<?php endif; ?>


<form
method="POST"
onsubmit="return confirm('¿Eliminar este mensaje?');"
>

<input
type="hidden"
name="accion"
value="eliminar_contacto"
>

<input
type="hidden"
name="id"
value="<?php echo $c['id_contacto']; ?>"
>

<button
type="submit"
class="btn btn-eliminar"
>
Eliminar
</button>

</form>


</td>

</tr>

<?php endwhile; ?>

</table>

</section>


<!-- =====================================================
     7. CONTENIDO
     ===================================================== -->

<section class="admin-seccion">

<h2>
 Administración del Contenido
</h2>


<form
method="POST"
class="form-admin"
>

<h3>
Agregar contenido
</h3>

<input
type="hidden"
name="accion"
value="agregar_contenido"
>

<label>
Sección
</label>

<input
type="text"
name="seccion"
placeholder="Ejemplo: Inicio"
required
>

<label>
Título
</label>

<input
type="text"
name="titulo"
>

<label>
Texto
</label>

<textarea
name="texto"
></textarea>

<button
type="submit"
class="btn btn-agregar"
>
Agregar contenido
</button>

</form>


<table class="tabla">

<tr>

<th>ID</th>
<th>Sección</th>
<th>Título</th>
<th>Texto</th>
<th>Actualización</th>
<th>Acciones</th>

</tr>


<?php while ($c = $contenidos->fetch_assoc()): ?>

<tr>

<td>
<?php echo $c['id_contenido']; ?>
</td>

<td>
<?php echo limpiar($c['seccion']); ?>
</td>

<td>
<?php echo limpiar($c['titulo']); ?>
</td>

<td class="texto">
<?php echo limpiar($c['texto']); ?>
</td>

<td>
<?php echo limpiar($c['fecha_actualizacion']); ?>
</td>

<td>


<details>

<summary class="btn btn-editar">
Editar
</summary>

<form
method="POST"
class="form-admin"
>

<input
type="hidden"
name="accion"
value="editar_contenido"
>

<input
type="hidden"
name="id"
value="<?php echo $c['id_contenido']; ?>"
>

<label>
Sección
</label>

<input
type="text"
name="seccion"
value="<?php echo limpiar($c['seccion']); ?>"
required
>

<label>
Título
</label>

<input
type="text"
name="titulo"
value="<?php echo limpiar($c['titulo']); ?>"
>

<label>
Texto
</label>

<textarea
name="texto"
><?php echo limpiar($c['texto']); ?></textarea>

<button
type="submit"
class="btn btn-editar"
>
Guardar cambios
</button>

</form>

</details>


<form
method="POST"
onsubmit="return confirm('¿Eliminar este contenido?');"
>

<input
type="hidden"
name="accion"
value="eliminar_contenido"
>

<input
type="hidden"
name="id"
value="<?php echo $c['id_contenido']; ?>"
>

<button
type="submit"
class="btn btn-eliminar"
>
Eliminar
</button>

</form>


</td>

</tr>

<?php endwhile; ?>

</table>

</section>


</div>


<!-- =====================================================
     FOOTER
     ===================================================== -->

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
 
