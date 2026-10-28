<?php
session_start();
include_once("conexion.php");

$mensajeResultado = "";

// PROCESAR FORMULARIO DE CONTACTO
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nombre  = trim($_POST['nombre'] ?? '');
    $correo  = trim($_POST['correo'] ?? '');
    $asunto  = trim($_POST['asunto'] ?? '');
    $mensaje = trim($_POST['mensaje'] ?? '');

    if (!empty($nombre) && !empty($correo) && !empty($asunto) && !empty($mensaje)) {
        // Inserta el registro con estado 'pendiente' por defecto
        $stmt = $conexion->prepare("INSERT INTO contacto (nombre, correo, asunto, mensaje, fecha_envio, estado) VALUES (?, ?, ?, ?, NOW(), 'pendiente')");
        if ($stmt) {
            $stmt->bind_param("ssss", $nombre, $correo, $asunto, $mensaje);
            if ($stmt->execute()) {
                $mensajeResultado = "<div style='background-color: #d1e7dd; color: #0f5132; padding: 12px; border-radius: 6px; margin-bottom: 20px; font-weight: bold;'>¡Gracias por contactarnos! Tu mensaje ha sido enviado correctamente.</div>";
            } else {
                $mensajeResultado = "<div style='background-color: #f8d7da; color: #842029; padding: 12px; border-radius: 6px; margin-bottom: 20px;'>Ocurrió un error al enviar el mensaje. Inténtalo de nuevo.</div>";
            }
            $stmt->close();
        }
    } else {
        $mensajeResultado = "<div style='background-color: #fff3cd; color: #664d03; padding: 12px; border-radius: 6px; margin-bottom: 20px;'>Por favor, completa todos los campos del formulario.</div>";
    }
}

$estaLogueado = isset($_SESSION['id_usuario']) && !empty($_SESSION['id_usuario']);
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contacto — Simulador de Plantas</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        .empresa-ubicacion-container {
            font-family: system-ui, -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            max-width: 1000px;
            margin: 20px auto;
            padding: 20px;
            background: #ffffff;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            box-sizing: border-box;
        }

        .empresa-grid {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
        }

        .empresa-info {
            flex: 1 1 300px;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .empresa-info h3 {
            margin-top: 0;
            color: #1a73e8;
            font-size: 1.5rem;
        }

        .empresa-info p {
            margin: 8px 0;
            color: #444;
            line-height: 1.5;
        }

        .empresa-mapa {
            flex: 2 1 400px;
            min-height: 350px;
            border-radius: 8px;
            overflow: hidden;
        }

        .empresa-mapa iframe {
            width: 100%;
            height: 100%;
            min-height: 350px;
            border: 0;
        }

        .btn-como-llegar {
            display: inline-block;
            margin-top: 15px;
            padding: 10px 20px;
            background-color: #1a73e8;
            color: white;
            text-decoration: none;
            border-radius: 6px;
            font-weight: bold;
            text-align: center;
            transition: background-color 0.2s;
        }

        .btn-como-llegar:hover {
            background-color: #1557b0;
        }

        .form-contacto input,
        .form-contacto textarea {
            width: 100%;
            padding: 12px;
            margin-bottom: 12px;
            border: 1px solid #cbd5e1;
            border-radius: 6px;
            box-sizing: border-box;
            font-family: inherit;
        }

        .form-contacto button {
            background: #2A7B9B;
            color: white;
            padding: 12px 25px;
            border: none;
            border-radius: 6px;
            font-weight: bold;
            cursor: pointer;
            transition: background 0.2s;
        }

        .form-contacto button:hover {
            background: #1f5f7a;
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
            <li><a href="noticias.php">Noticias</a></li>
            <li><a href="foro.php"> Foro</a></li>
            <li><a href="Galería.html">Galería</a></li>
            <li><a href="contacto.php">Contacto</a></li>
            <li><a href="Políticas.html">Políticas</a></li>

            <?php if ($estaLogueado): ?>
                <li><a href="logout.php" style="background-color: #c62828; color: white;">Cerrar sesión</a></li>
            <?php else: ?>
                <li><a href="login.php" style="background-color: #2A7B9B; color: white;">Iniciar sesión</a></li>
            <?php endif; ?>
        </ul>
    </nav>

    <main>
        <div class="tarjeta">
            <h1>Contacto</h1>
            <p>Si tienes dudas sobre el funcionamiento del simulador o deseas comunicarte con el equipo del proyecto, envíanos un mensaje o búscanos a través de nuestros canales:</p>

            <?php echo $mensajeResultado; ?>

            <!-- FORMULARIO DE CONTACTO (GUARDA EN BD) -->
            <div style="background: #f8fafc; padding: 25px; border-radius: 10px; border: 1px solid #cbd5e1; margin-top: 20px;">
                <h3> Envíanos un mensaje</h3>
                <form action="contacto.php" method="POST" class="form-contacto">
                    <input type="text" name="nombre" placeholder="Tu nombre completo" required>
                    <input type="email" name="correo" placeholder="Tu correo electrónico" required>
                    <input type="text" name="asunto" placeholder="Asunto de la consulta" required>
                    <textarea name="mensaje" rows="5" placeholder="Escribe tu mensaje aquí..." required></textarea>
                    <button type="submit">Enviar Mensaje</button>
                </form>
            </div>

            <!-- INFORMACIÓN DEL PROYECTO -->
            <div style="margin-top: 30px;">
                <p><b>Docente encargado:</b> Cindy Bedoya</p>
                <p><b>Integrantes del proyecto:</b></p>
                <ul>
                    <li>Juan David Contreras</li>
                    <li>Stiven Zapata Flórez</li>
                    <li>Moises Jesus Salgado </li>
                </ul>

                <p><b>Correo electrónico:</b> simuladorplantas@proyecto.edu.co</p>
                <p><b>Teléfono:</b> +57 310 987 6543</p>
                <p><b>Horario de atención:</b> Lunes a Viernes, 8:00 a.m. – 5:00 p.m.</p>
            </div>

            <!-- SECCIÓN UBICACIÓN Y MAPA -->
            <div class="empresa-ubicacion-container">
                <div class="empresa-grid">
                    <div class="empresa-info">
                        <h3>Nuestra Ubicación</h3>
                        <p><strong>Dirección:</strong><br> Av. Principal 123, Oficina 402, Ciudad</p>
                        <p><strong>Teléfono:</strong><br> +52 55 1234 5678</p>
                        <p><strong>Horario de Atención:</strong><br> Lunes a Viernes: 9:00 AM - 6:00 PM</p>
                        <a href="https://maps.app.goo.gl/wCND2yCf4XfoWF189" target="_blank" rel="noopener noreferrer" class="btn-como-llegar">
                            Cómo llegar con Google Maps
                        </a>
                    </div>

                    <div class="empresa-mapa">
                        <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3610.1847583199587!2d55.27423570475761!3d25.196991075394696!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3e5f43348a67e24b%3A0xff45e502e1ceb7e2!2sBurj%20Khalifa!5e0!3m2!1ses!2sco!4v1790103165828!5m2!1ses!2sco" allowfullscreen="" loading="lazy" referrerpolicy="strict-origin-when-cross-origin"></iframe>
                    </div>
                </div>
            </div>

            <div class="centrar" style="margin-top: 20px;">
                <img src="https://images.unsplash.com/photo-1423666639041-f56000c27a9a?auto=format&fit=crop&w=800&q=80" alt="Contacto y comunicación" class="imagen-seccion">
            </div>

            <div class="centrar" style="margin-top: 20px;">
                <a href="index.php" class="btn-inicio">← Volver al inicio</a>
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