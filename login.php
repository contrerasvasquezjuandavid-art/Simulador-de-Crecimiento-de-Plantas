<?php
session_start();
include_once("conexion.php");

/* ==========================================
   PROCESAR INICIO DE SESIÓN
   ========================================== */

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $correo = trim($_POST['email'] ?? '');
    $password = trim($_POST['password'] ?? '');

    /* Validar campos vacíos */
    if (empty($correo) || empty($password)) {
        header("Location: login.php?error=vacios");
        exit();
    }

    /* Buscar usuario por correo */
    $sql = "SELECT id_usuario, nombre, contrasena, rol 
            FROM usuarios 
            WHERE correo = ? 
            LIMIT 1";

    $stmt = $conexion->prepare($sql);

    if (!$stmt) {
        die("Error en la consulta SQL: " . $conexion->error);
    }

    $stmt->bind_param("s", $correo);
    $stmt->execute();

    $result = $stmt->get_result();

    /* Verificar si existe el usuario */
    if ($result->num_rows == 1) {

        $usuario = $result->fetch_assoc();

        /* ==========================================
           VERIFICAR CONTRASEÑA
           ========================================== */

        $passwordCorrecta = false;

        /* Si la contraseña está guardada con password_hash() */
        if (password_verify($password, $usuario['contrasena'])) {
            $passwordCorrecta = true;
        }

        /* También permite contraseñas guardadas directamente
           en la base de datos */
        if ($password === $usuario['contrasena']) {
            $passwordCorrecta = true;
        }

        if ($passwordCorrecta) {

            /* ==========================================
               GUARDAR DATOS EN LA SESIÓN
               ========================================== */

            $_SESSION['id_usuario'] = $usuario['id_usuario'];
            $_SESSION['nombre'] = $usuario['nombre'];

            /* IMPORTANTE:
               trim elimina espacios y strtolower
               evita problemas con ADMINISTRADOR,
               Administrador, administrador, etc. */
            $rol = strtolower(trim($usuario['rol']));

            $_SESSION['rol'] = $rol;

            /* ==========================================
               REDIRECCIÓN SEGÚN EL ROL
               ========================================== */

            if ($rol === 'administrador' || $rol === 'admin') {

                header("Location: admin.php");
                exit();

            } else {

                header("Location: index.php");
                exit();
            }

        } else {

            header("Location: login.php?error=credenciales");
            exit();
        }

    } else {

        header("Location: login.php?error=credenciales");
        exit();
    }

    $stmt->close();
    $conexion->close();
}
?>

<!DOCTYPE html>
<html lang="es">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Acceso — Simulador de Plantas</title>

    <link rel="stylesheet" href="styles.css">

    <style>

        /* ==========================================
           TARJETA GIRATORIA
           ========================================== */

        .contenedor-flip {
            perspective: 1000px;
            width: 100%;
            max-width: 450px;
            margin: 20px auto;
            min-height: 520px;
        }

        .tarjeta-3d {
            width: 100%;
            height: 100%;
            position: relative;
            transform-style: preserve-3d;
            transition: transform 0.8s cubic-bezier(
                0.175,
                0.885,
                0.32,
                1.275
            );
        }

        .tarjeta-3d.volteada {
            transform: rotateY(180deg);
        }

        .cara {
            position: absolute;
            width: 100%;
            backface-visibility: hidden;
            -webkit-backface-visibility: hidden;
            border-radius: 12px;
            box-sizing: border-box;
        }

        .cara-frente {
            z-index: 2;
            transform: rotateY(0deg);
        }

        .cara-atras {
            transform: rotateY(180deg);
        }

        /* ==========================================
           FORMULARIOS
           ========================================== */

        .form-group {
            margin-bottom: 15px;
            text-align: left;
        }

        .form-group label {
            display: block;
            font-weight: 600;
            margin-bottom: 5px;
            color: #2A7B9B;
            font-size: 0.9rem;
        }

        .form-group input {
            width: 100%;
            padding: 10px;
            border: 1px solid #cbd5e1;
            border-radius: 6px;
            outline: none;
            font-size: 0.95rem;
            box-sizing: border-box;
        }

        .form-group input:focus {
            border-color: #2A7B9B;
        }

        /* ==========================================
           BOTÓN INGRESAR
           ========================================== */

        .btn-submit {
            width: 100%;
            background: linear-gradient(
                90deg,
                #2A7B9B 0%,
                #57C785 100%
            );

            color: #ffffff;
            border: none;
            padding: 12px;
            font-weight: bold;
            border-radius: 25px;
            cursor: pointer;
            transition: all 0.3s ease;
            font-size: 1rem;
            margin-top: 10px;
        }

        .btn-submit:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 10px rgba(42, 123, 155, 0.25);
        }

        /* ==========================================
           BOTÓN CAMBIAR
           ========================================== */

        .btn-cambiar {
            background: transparent;
            border: 2px solid #2A7B9B;
            color: #2A7B9B;
            width: 100%;
            padding: 10px;
            font-weight: bold;
            border-radius: 25px;
            cursor: pointer;
            transition: all 0.3s ease;
            margin-top: 15px;
        }

        .btn-cambiar:hover {
            background-color: #2A7B9B;
            color: #ffffff;
        }

        /* ==========================================
           SEPARADOR
           ========================================== */

        .separador {
            margin: 20px 0 15px 0;
            border: none;
            border-top: 1px dashed #cbd5e1;
        }

        /* ==========================================
           MENSAJES
           ========================================== */

        .alerta {
            padding: 10px;
            border-radius: 6px;
            margin-bottom: 15px;
            font-size: 0.85rem;
            text-align: center;
        }

        .alerta-error {
            background-color: #fee2e2;
            color: #991b1b;
            border: 1px solid #f87171;
        }

        .alerta-exito {
            background-color: #dcfce7;
            color: #166534;
            border: 1px solid #4ade80;
        }

    </style>

</head>

<body>

    <main>

        <!-- ==========================================
             MENSAJES DE ERROR
             ========================================== -->

        <?php if (isset($_GET['error'])): ?>

            <div
                class="alerta alerta-error"
                style="max-width: 450px; margin: 0 auto 15px auto;"
            >

                <?php

                if ($_GET['error'] == 'credenciales') {
                    echo "Correo o contraseña incorrectos.";
                }

                if ($_GET['error'] == 'existe') {
                    echo "El correo electrónico ya está registrado.";
                }

                if ($_GET['error'] == 'vacios') {
                    echo "Todos los campos son obligatorios.";
                }

                ?>

            </div>

        <?php endif; ?>


        <!-- ==========================================
             MENSAJES DE ÉXITO
             ========================================== -->

        <?php if (isset($_GET['exito'])): ?>

            <div
                class="alerta alerta-exito"
                style="max-width: 450px; margin: 0 auto 15px auto;"
            >

                <?php

                if ($_GET['exito'] == 'registrado') {
                    echo "¡Cuenta creada exitosamente! Ahora puedes iniciar sesión.";
                }

                ?>

            </div>

        <?php endif; ?>


        <!-- ==========================================
             TARJETA 3D
             ========================================== -->

        <div class="contenedor-flip">

            <div class="tarjeta-3d" id="tarjetaAcceso">


                <!-- ==================================
                     INICIAR SESIÓN
                     ================================== -->

                <div class="tarjeta cara cara-frente">

                    <h2 style="color: #2A7B9B; margin-bottom: 5px;">
                        Bienvenido
                    </h2>

                    <p
                        style="
                        font-size: 0.9rem;
                        color: #64748b;
                        margin-bottom: 20px;
                        "
                    >
                        Inicia sesión en tu cuenta
                    </p>


                    <form action="login.php" method="POST">

                        <div class="form-group">

                            <label for="email">
                                Correo electrónico
                            </label>

                            <input
                                type="email"
                                id="email"
                                name="email"
                                placeholder="tu@correo.com"
                                required
                            >

                        </div>


                        <div class="form-group">

                            <label for="password">
                                Contraseña
                            </label>

                            <input
                                type="password"
                                id="password"
                                name="password"
                                placeholder="••••••••"
                                required
                            >

                        </div>


                        <button
                            type="submit"
                            class="btn-submit"
                        >
                            Ingresar
                        </button>

                    </form>


                    <hr class="separador">


                    <p
                        style="
                        font-size: 0.85rem;
                        color: #64748b;
                        "
                    >
                        ¿Aún no tienes una cuenta?
                    </p>


                    <button
                        type="button"
                        class="btn-cambiar"
                        id="btnGirarARegistro"
                    >
                        Crear cuenta
                    </button>

                </div>


                <!-- ==================================
                     CREAR CUENTA
                     ================================== -->

                <div class="tarjeta cara cara-atras">

                    <h2 style="color: #2A7B9B; margin-bottom: 5px;">
                        Crear Cuenta
                    </h2>

                    <p
                        style="
                        font-size: 0.9rem;
                        color: #64748b;
                        margin-bottom: 20px;
                        "
                    >
                        Regístrate para comenzar
                    </p>


                    <form action="registro.php" method="POST">

                        <div class="form-group">

                            <label for="nombre">
                                Nombre completo
                            </label>

                            <input
                                type="text"
                                id="nombre"
                                name="nombre"
                                placeholder="Juan Pérez"
                                required
                            >

                        </div>


                        <div class="form-group">

                            <label for="reg_email">
                                Correo electrónico
                            </label>

                            <input
                                type="email"
                                id="reg_email"
                                name="email"
                                placeholder="tu@correo.com"
                                required
                            >

                        </div>


                        <div class="form-group">

                            <label for="reg_password">
                                Contraseña
                            </label>

                            <input
                                type="password"
                                id="reg_password"
                                name="password"
                                placeholder="••••••••"
                                required
                            >

                        </div>


                        <button
                            type="submit"
                            class="btn-submit"
                            style="
                            background: linear-gradient(
                                90deg,
                                #318698 0%,
                                #57C785 100%
                            );
                            "
                        >
                            Registrarse
                        </button>

                    </form>


                    <hr class="separador">


                    <p
                        style="
                        font-size: 0.85rem;
                        color: #64748b;
                        "
                    >
                        ¿Ya tienes una cuenta?
                    </p>


                    <button
                        type="button"
                        class="btn-cambiar"
                        id="btnGirarALogin"
                    >
                        Volver a Iniciar Sesión
                    </button>

                </div>

            </div>

        </div>

    </main>


    <!-- ==========================================
         FOOTER
         ========================================== -->

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


    <!-- ==========================================
         JAVASCRIPT TARJETA 3D
         ========================================== -->

    <script>

        const tarjetaAcceso =
            document.getElementById('tarjetaAcceso');

        const btnGirarARegistro =
            document.getElementById('btnGirarARegistro');

        const btnGirarALogin =
            document.getElementById('btnGirarALogin');


        btnGirarARegistro.addEventListener('click', function () {

            tarjetaAcceso.classList.add('volteada');

        });


        btnGirarALogin.addEventListener('click', function () {

            tarjetaAcceso.classList.remove('volteada');

        });

    </script>

</body>

</html>
```

### Pero hay algo MUY importante en XAMPP

Tu código revisa esto:

```php
if ($rol === 'administrador' || $rol === 'admin') {
    header("Location: admin.php");
    exit();
}
```

Por lo tanto, en tu base de datos la columna **`rol`** debe tener, por ejemplo:

| id_usuario | nombre        | correo                                    | contrasena | rol             |
| ---------: | ------------- | ----------------------------------------- | ---------- | --------------- |
|          1 | Administrador | [admin@gmail.com](mailto:admin@gmail.com) | 1234       | `administrador` |
|          2 | Juan          | [juan@gmail.com](mailto:juan@gmail.com)   | 1234       | `usuario`       |

Si tu administrador tiene `usuario`, `user`, `Usuario`, `ADMINISTRADOR ` u otro valor, la redirección será diferente.

### Si quieres crear el administrador directamente en phpMyAdmin

Entra a:

**XAMPP → Apache → MySQL → phpMyAdmin → tu base de datos → SQL**

Y ejecuta:

```sql
UPDATE usuarios
SET rol = 'administrador'
WHERE correo = 'admin@gmail.com';
```

Cambia `admin@gmail.com` por el correo real del administrador.

Después verifica:

```sql
SELECT id_usuario, nombre, correo, rol
FROM usuarios;
```

Debe aparecer algo parecido a:

```text
1 | Administrador | admin@gmail.com | administrador
2 | Juan Pérez    | juan@gmail.com  | usuario
```

### Y `admin.php` también debe protegerse

Para que un usuario normal no pueda simplemente escribir:

`http://localhost/tu_proyecto/admin.php`

y entrar, **al principio de `admin.php`** debes tener:

```php
<?php
session_start();

if (!isset($_SESSION['id_usuario'])) {
    header("Location: login.php");
    exit();
}

if (
    !isset($_SESSION['rol']) ||
    ($_SESSION['rol'] !== 'administrador' &&
     $_SESSION['rol'] !== 'admin')
) {
    header("Location: index.php");
    exit();
}
?>
