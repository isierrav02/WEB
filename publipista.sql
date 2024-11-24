-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 24-11-2024 a las 21:40:54
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
-- Base de datos: `publipista`
--
CREATE DATABASE IF NOT EXISTS `publipista` DEFAULT CHARACTER SET utf8 COLLATE utf8_spanish_ci;
USE `publipista`;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `fotos_pistas`
--

CREATE TABLE `fotos_pistas` (
  `id` int(11) NOT NULL,
  `url` varchar(255) NOT NULL,
  `descripcion` varchar(255) DEFAULT NULL,
  `pista_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

--
-- Volcado de datos para la tabla `fotos_pistas`
--

INSERT INTO `fotos_pistas` (`id`, `url`, `descripcion`, `pista_id`) VALUES
(1, 'img/pista-tenis1.jpg', 'Pista de tenis 1', 1),
(2, 'img/pista-padel1.jpg', 'Pista de pádel 1', 2),
(3, 'img/pista-futbol7.jpg', 'Pista de fútbol 7', 3),
(4, 'img/cancha-baloncesto1.jpg', 'Cancha de baloncesto 1', 4),
(5, 'img/pista-tenis2.jpg', 'Pista de tenis 2', 5),
(6, 'img/pista-padel2.jpg', 'Pista de pádel 2', 6),
(7, 'img/campo-futbol11.jpg', 'Campo de fútbol 11', 7),
(8, 'img/cancha-baloncesto2.jpg', 'Cancha de baloncesto 2', 8);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `pistas`
--

CREATE TABLE `pistas` (
  `id` int(11) NOT NULL,
  `nombre` varchar(50) DEFAULT NULL,
  `tipo` enum('tenis','padel','futbol_7','futbol_11','baloncesto') DEFAULT NULL,
  `ubicacion` varchar(100) DEFAULT NULL,
  `precio_base` decimal(10,2) NOT NULL DEFAULT 10.00
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

--
-- Volcado de datos para la tabla `pistas`
--

INSERT INTO `pistas` (`id`, `nombre`, `tipo`, `ubicacion`, `precio_base`) VALUES
(1, 'Pista de tenis 1', 'tenis', 'Calle Sol, 10', 15.00),
(2, 'Pista de padel 1', 'padel', 'Avenida Luna, 5', 12.00),
(3, 'Pista de futbol 7 ', 'futbol_7', 'Plaza Estadio, s/n', 25.00),
(4, 'Cancha de baloncesto 1', 'baloncesto', 'Calle Estrella, 15', 20.00),
(5, 'Pista de tenis 2', 'tenis', 'Calle Sol, 12', 15.00),
(6, 'Pista de padel 2', 'padel', 'Avenida Luna, 7', 12.00),
(7, 'Pista de futbol 11', 'futbol_11', 'Plaza Mayor, 20', 35.00),
(8, 'Cancha de baloncesto 2', 'baloncesto', 'Calle Estadio, 22', 20.00);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `reservas`
--

CREATE TABLE `reservas` (
  `id` int(11) NOT NULL,
  `usuario_id` int(11) DEFAULT NULL,
  `pista_id` int(11) DEFAULT NULL,
  `fecha_reserva` date DEFAULT NULL,
  `hora_inicio` time DEFAULT NULL,
  `hora_fin` time DEFAULT NULL,
  `fecha_creacion` timestamp NOT NULL DEFAULT current_timestamp(),
  `precio_total` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios`
--

CREATE TABLE `usuarios` (
  `id` int(11) NOT NULL,
  `nombre` varchar(100) DEFAULT NULL,
  `apellidos` varchar(100) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `telefono` varchar(15) DEFAULT NULL,
  `contrasena` varchar(255) DEFAULT NULL,
  `fecha_registro` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

--
-- Volcado de datos para la tabla `usuarios`
--

INSERT INTO `usuarios` (`id`, `nombre`, `apellidos`, `email`, `telefono`, `contrasena`, `fecha_registro`) VALUES
(1, 'Ismael', 'Sierra Vega', 'isierrav02@educarex.es', '654102515', '$2y$10$8.hqcmOEg.wjayA1K0HJKeW/KZwJEIv3q1u.oKjSL85IXx0FI1eTe', '2024-10-25 16:47:10'),
(13, 'Ana', 'Vega García', 'i301003@hotmail.com', '654686800', '$2y$10$gepSBNbFRHHhZVkEejuMk.5/EUSosHseJvb6O7mt.ChGH7HRuL7uy', '2024-11-23 14:10:21'),
(14, 'Fernando', 'Pavón Valle', 'pavonvallefernando@gmail.com', '677386570', '$2y$10$QwrQizotxQ2y5qqUbllhq.zo6/jTSBShtCLDiGcsG7.c8aX1ccZSm', '2024-11-24 20:32:04');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `fotos_pistas`
--
ALTER TABLE `fotos_pistas`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_pista` (`pista_id`);

--
-- Indices de la tabla `pistas`
--
ALTER TABLE `pistas`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `reservas`
--
ALTER TABLE `reservas`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unica_reserva` (`usuario_id`,`pista_id`,`fecha_reserva`),
  ADD KEY `pista_id` (`pista_id`);

--
-- Indices de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `fotos_pistas`
--
ALTER TABLE `fotos_pistas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT de la tabla `pistas`
--
ALTER TABLE `pistas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT de la tabla `reservas`
--
ALTER TABLE `reservas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=31;

--
-- AUTO_INCREMENT de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `fotos_pistas`
--
ALTER TABLE `fotos_pistas`
  ADD CONSTRAINT `fk_pista` FOREIGN KEY (`pista_id`) REFERENCES `pistas` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `reservas`
--
ALTER TABLE `reservas`
  ADD CONSTRAINT `reservas_ibfk_1` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`),
  ADD CONSTRAINT `reservas_ibfk_2` FOREIGN KEY (`pista_id`) REFERENCES `pistas` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
