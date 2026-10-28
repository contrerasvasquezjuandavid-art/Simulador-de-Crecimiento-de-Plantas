-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 27-10-2026 a las 22:33:59
-- Versión del servidor: 10.4.32-MariaDB
-- Versión de PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `pagina_web`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `comentarios`
--

CREATE TABLE `comentarios` (
  `id_comentario` int(11) NOT NULL,
  `id_publicacion` int(11) NOT NULL,
  `id_usuario` int(11) NOT NULL,
  `comentario` text NOT NULL,
  `fecha_comentario` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `contacto`
--

CREATE TABLE `contacto` (
  `id_contacto` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `correo` varchar(150) NOT NULL,
  `asunto` varchar(200) NOT NULL,
  `mensaje` text NOT NULL,
  `fecha_envio` timestamp NOT NULL DEFAULT current_timestamp(),
  `estado` enum('pendiente','leido') DEFAULT 'pendiente'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `contacto`
--

INSERT INTO `contacto` (`id_contacto`, `nombre`, `correo`, `asunto`, `mensaje`, `fecha_envio`, `estado`) VALUES
(1, 'Zapata', 'zapataflorezstiven@ieru.edu.co', 'kpo', 'poop', '2026-09-24 21:53:09', 'pendiente'),
(2, 'Contreras Vasquez', 'contrerasvasquezjuandavid@ieru.edu.co', 'hola', 'hola', '2026-10-27 21:26:43', 'pendiente');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `contenido`
--

CREATE TABLE `contenido` (
  `id_contenido` int(11) NOT NULL,
  `seccion` varchar(100) NOT NULL,
  `titulo` varchar(200) DEFAULT NULL,
  `texto` text DEFAULT NULL,
  `fecha_actualizacion` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `foro`
--

CREATE TABLE `foro` (
  `id_publicacion` int(11) NOT NULL,
  `id_usuario` int(11) NOT NULL,
  `titulo` varchar(200) NOT NULL,
  `contenido` text NOT NULL,
  `fecha_publicacion` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `foro`
--

INSERT INTO `foro` (`id_publicacion`, `id_usuario`, `titulo`, `contenido`, `fecha_publicacion`) VALUES
(3, 1, 'Avances en el Módulo de Clima y Nutrientes', 'Los avances en el Módulo de Clima y Nutrientes (aplicados a plataformas de agricultura de precisión como Agricolus) se centran en la integración de modelos predictivos de inteligencia artificial (IA), sensores IoT y datos meteorológicos en tiempo real para maximizar el rendimiento de los cultivos frente al cambio climático', '2026-10-27 21:27:03');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `galeria`
--

CREATE TABLE `galeria` (
  `id_imagen` int(11) NOT NULL,
  `titulo` varchar(200) NOT NULL,
  `descripcion` text DEFAULT NULL,
  `imagen` varchar(500) NOT NULL,
  `fecha_subida` timestamp NOT NULL DEFAULT current_timestamp(),
  `id_administrador` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `galeria`
--

INSERT INTO `galeria` (`id_imagen`, `titulo`, `descripcion`, `imagen`, `fecha_subida`, `id_administrador`) VALUES
(1, 'Invernadero', 'Vista de un entorno utilizado para el cultivo y desarrollo de plantas.', 'https://images.unsplash.com/photo-1416879595882-3373a0480b5b?auto=format&fit=crop&w=1200&q=80', '2026-09-29 21:22:45', NULL),
(2, 'Cuidado de cultivos', 'Proceso de cuidado y seguimiento del crecimiento de diferentes plantas.', 'https://images.unsplash.com/photo-1530836369250-ef72a3f5cda8?auto=format&fit=crop&w=1200&q=80', '2026-09-29 21:22:45', NULL),
(3, 'Crecimiento de una planta', 'Representación del crecimiento y desarrollo de una planta.', 'https://images.unsplash.com/photo-1463936575829-25148e1db1b8?auto=format&fit=crop&w=1200&q=80', '2026-09-29 21:22:45', NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `noticias`
--

CREATE TABLE `noticias` (
  `id_noticia` int(11) NOT NULL,
  `titulo` varchar(200) NOT NULL,
  `contenido` text NOT NULL,
  `imagen` varchar(255) DEFAULT NULL,
  `fecha_publicacion` timestamp NOT NULL DEFAULT current_timestamp(),
  `id_administrador` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `noticias`
--

INSERT INTO `noticias` (`id_noticia`, `titulo`, `contenido`, `imagen`, `fecha_publicacion`, `id_administrador`) VALUES
(2, 'Avances en el Módulo de Clima y Nutrientes', 'Nos complace anunciar el inicio de la fase de pruebas para la integración de parámetros meteorológicos en tiempo real.\r\n\r\nEsta actualización permitirá a los usuarios simular cómo variaciones en la radiación solar, niveles de humedad y balance de NPK (nitrógeno, fósforo y potasio) afectan directamente la tasa fotosintética y el crecimiento radicular de distintas especies.', 'https://images.unsplash.com/photo-1530836369250-ef72a3f5cda8?q=80&w=800&auto=format&fit=crop', '2026-09-25 02:50:43', NULL),
(6, 'Avances en el Módulo de Clima y Nutrientes', 'Nos complace anunciar el inicio de la fase de pruebas para la integración de parámetros meteorológicos en tiempo real. Esta actualización permitirá a los usuarios simular cómo las variaciones en la radiación solar, los niveles de humedad y el balance de NPK (nitrógeno, fósforo y potasio) afectan directamente la tasa fotosintética y el crecimiento radicular de distintas especies.', 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcRkxkH4Ni86_96gynRsJLWTEOHrnfRhYzL5-XPq3uKmPQ&s=10', '2026-10-27 21:05:05', 1),
(7, 'Módulo de Control de Plagas y Enfermedades', 'Incorporamos un sistema interactivo para simular la aparición de patógenos comunes como la roya y el mildeo. Los usuarios podrán aplicar tratamientos biológicos o químicos en el simulador y analizar su impacto en el rendimiento final del cultivo.', 'https://www.ecoavant.com/uploads/s1/28/20/68/imagen-de-que-es-la-naturaleza.webp', '2026-10-27 21:12:08', 1),
(8, 'Simulación de Cultivos Hidropónicos y Aeropónicos', 'Extendemos las capacidades del sistema más allá del suelo tradicional. Ahora es posible configurar sistemas de cultivo en agua y aire, ajustando la conductividad eléctrica (EC) y los niveles de pH de la solución nutritiva.', 'https://img.magnific.com/foto-gratis/pequeno-tronco-rio-selva-tropical_1150-13681.jpg?semt=ais_hybrid&w=740&q=80', '2026-10-27 21:12:42', 1),
(9, 'Herramientas de Exportación y Análisis Estadístico', 'Se ha habilitado la descarga de datos experimentales en formatos CSV y Excel. Los usuarios ahora pueden generar gráficos de dispersión, diagramas de barras y análisis de regresión sobre el desarrollo de sus plantas directamente desde el panel.', 'https://www.ecoavant.com/uploads/s1/28/20/90/imagen-que-es-el-medio-ambiente.webp', '2026-10-27 21:13:23', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios`
--

CREATE TABLE `usuarios` (
  `id_usuario` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `correo` varchar(150) NOT NULL,
  `contrasena` varchar(255) NOT NULL,
  `rol` enum('usuario','administrador') DEFAULT 'usuario',
  `fecha_registro` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `usuarios`
--

INSERT INTO `usuarios` (`id_usuario`, `nombre`, `correo`, `contrasena`, `rol`, `fecha_registro`) VALUES
(1, 'Administrador', 'elprofe@gmail.com', 'Admin123', 'administrador', '2026-09-24 21:30:24'),
(3, 'estiven', 'zapataflorezstiven@ieru.edu.co', '123456', 'usuario', '2026-09-24 21:33:17');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `comentarios`
--
ALTER TABLE `comentarios`
  ADD PRIMARY KEY (`id_comentario`),
  ADD KEY `id_publicacion` (`id_publicacion`),
  ADD KEY `id_usuario` (`id_usuario`);

--
-- Indices de la tabla `contacto`
--
ALTER TABLE `contacto`
  ADD PRIMARY KEY (`id_contacto`);

--
-- Indices de la tabla `contenido`
--
ALTER TABLE `contenido`
  ADD PRIMARY KEY (`id_contenido`);

--
-- Indices de la tabla `foro`
--
ALTER TABLE `foro`
  ADD PRIMARY KEY (`id_publicacion`),
  ADD KEY `id_usuario` (`id_usuario`);

--
-- Indices de la tabla `galeria`
--
ALTER TABLE `galeria`
  ADD PRIMARY KEY (`id_imagen`),
  ADD KEY `id_administrador` (`id_administrador`);

--
-- Indices de la tabla `noticias`
--
ALTER TABLE `noticias`
  ADD PRIMARY KEY (`id_noticia`),
  ADD KEY `id_administrador` (`id_administrador`);

--
-- Indices de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id_usuario`),
  ADD UNIQUE KEY `correo` (`correo`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `comentarios`
--
ALTER TABLE `comentarios`
  MODIFY `id_comentario` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `contacto`
--
ALTER TABLE `contacto`
  MODIFY `id_contacto` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `contenido`
--
ALTER TABLE `contenido`
  MODIFY `id_contenido` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `foro`
--
ALTER TABLE `foro`
  MODIFY `id_publicacion` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `galeria`
--
ALTER TABLE `galeria`
  MODIFY `id_imagen` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `noticias`
--
ALTER TABLE `noticias`
  MODIFY `id_noticia` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id_usuario` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `comentarios`
--
ALTER TABLE `comentarios`
  ADD CONSTRAINT `fk_comentarios_foro` FOREIGN KEY (`id_publicacion`) REFERENCES `foro` (`id_publicacion`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_comentarios_usuario` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id_usuario`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `foro`
--
ALTER TABLE `foro`
  ADD CONSTRAINT `fk_foro_usuario` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id_usuario`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `noticias`
--
ALTER TABLE `noticias`
  ADD CONSTRAINT `fk_noticias_admin` FOREIGN KEY (`id_administrador`) REFERENCES `usuarios` (`id_usuario`) ON DELETE SET NULL ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
