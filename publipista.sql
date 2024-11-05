-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 15-10-2024 a las 20:45:19
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
-- Estructura de tabla para la tabla `categorias_pistas`
--

CREATE TABLE `categorias_pistas` (
  `id` int(11) NOT NULL,
  `nombre_categoria` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

--
-- Volcado de datos para la tabla `categorias_pistas`
--

INSERT INTO `categorias_pistas` (`id`, `nombre_categoria`) VALUES
(1, 'Normal'),
(2, 'Premium'),
(3, 'Avanzado'),
(4, 'Básico'),
(5, 'Intermedio'),
(6, 'Profesional'),
(7, 'VIP'),
(8, 'Amateur'),
(9, 'Familiar'),
(10, 'Competitivo');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `pistas`
--

CREATE TABLE `pistas` (
  `id` int(11) NOT NULL,
  `nombre` varchar(50) DEFAULT NULL,
  `tipo` enum('tenis','padel','futbol_7','futbol_11','baloncesto') DEFAULT NULL,
  `ubicacion` varchar(100) DEFAULT NULL,
  `categoria_id` int(11) DEFAULT NULL,
  `precio_base` decimal(10,2) NOT NULL DEFAULT 10.00
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

--
-- Volcado de datos para la tabla `pistas`
--

INSERT INTO `pistas` (`id`, `nombre`, `tipo`, `ubicacion`, `categoria_id`, `precio_base`) VALUES
(1, 'Pista de tenis 1', 'tenis', 'Calle Sol, 10', 1, 15.00),
(2, 'Pista de padel 1', 'padel', 'Avenida Luna, 5', 2, 12.00),
(3, 'Pista de futbol 7', 'futbol_7', 'Plaza Estadio, s/n', 3, 25.00),
(4, 'Pista de baloncesto 1', 'baloncesto', 'Calle Estrella, 15', 4, 20.00),
(5, 'Pista de tenis 2', 'tenis', 'Calle Sol, 12', 5, 18.00),
(6, 'Pista de padel 2', 'padel', 'Avenida Luna, 7', 6, 14.00),
(7, 'Pista de futbol 11', 'futbol_11', 'Plaza Mayor, 20', 7, 35.00),
(8, 'Pista de baloncesto 2', 'baloncesto', 'Calle Estadio, 22', 8, 22.00),
(9, 'Pista de futbol 7', 'futbol_7', 'Calle Campos, 1', 9, 25.00),
(10, 'Pista de tenis 3', 'tenis', 'Calle Sol, 14', 10, 17.00);

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
  `estado` enum('activa','modificada','cancelada') DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

--
-- Volcado de datos para la tabla `reservas`
--

INSERT INTO `reservas` (`id`, `usuario_id`, `pista_id`, `fecha_reserva`, `hora_inicio`, `hora_fin`, `fecha_creacion`, `estado`) VALUES
(1, 1, 1, '2024-10-01', '10:00:00', '11:00:00', '2024-09-30 16:58:20', 'activa'),
(2, 2, 2, '2024-10-02', '12:00:00', '14:00:00', '2024-09-30 16:58:20', 'activa'),
(3, 3, 3, '2024-10-03', '15:00:00', '16:00:00', '2024-09-30 16:58:20', 'activa'),
(4, 4, 4, '2024-10-04', '17:00:00', '18:00:00', '2024-09-30 16:58:20', 'cancelada'),
(5, 5, 5, '2024-10-05', '09:00:00', '10:00:00', '2024-09-30 16:58:20', 'activa'),
(6, 6, 6, '2024-10-06', '10:00:00', '11:00:00', '2024-09-30 16:58:20', 'modificada'),
(7, 7, 7, '2024-10-07', '11:00:00', '12:00:00', '2024-09-30 16:58:20', 'activa'),
(8, 8, 8, '2024-10-08', '12:00:00', '13:00:00', '2024-09-30 16:58:20', 'activa'),
(9, 9, 9, '2024-10-09', '14:00:00', '15:00:00', '2024-09-30 16:58:20', 'cancelada'),
(10, 10, 10, '2024-10-10', '16:00:00', '17:00:00', '2024-09-30 16:58:20', 'activa');

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
(1, 'Juan', 'Pérez García', 'juan.perez@gmail.com', '600123456', 'contraseña123', '2024-09-30 16:58:20'),
(2, 'María', 'López Martínez', 'maria.lopez@yahoo.com', '600654321', 'mipass456', '2024-09-30 16:58:20'),
(3, 'Carlos', 'Sánchez Fernández', 'carlos.sanchez@outlook.com', '600987654', 'segura789', '2024-09-30 16:58:20'),
(4, 'Ana', 'Martínez Ruiz', 'ana.martinez@gmail.com', '600345678', 'clave321', '2024-09-30 16:58:20'),
(5, 'Pedro', 'Gómez Navarro', 'pedro.gomez@hotmail.com', '600456789', 'password999', '2024-09-30 16:58:20'),
(6, 'Lucía', 'Torres García', 'lucia.torres@gmail.com', '600234567', 'clave101', '2024-09-30 16:58:20'),
(7, 'Javier', 'Muñoz Delgado', 'javier.munoz@live.com', '600765432', 'password202', '2024-09-30 16:58:20'),
(8, 'Elena', 'Rodríguez Pérez', 'elena.rodriguez@gmail.com', '600876543', 'pass303', '2024-09-30 16:58:20'),
(9, 'Miguel', 'Romero Vázquez', 'miguel.romero@outlook.com', '600112233', 'password404', '2024-09-30 16:58:20'),
(10, 'Sara', 'Hernández Gómez', 'sara.hernandez@gmail.com', '600221144', 'clave505', '2024-09-30 16:58:20');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `categorias_pistas`
--
ALTER TABLE `categorias_pistas`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `pistas`
--
ALTER TABLE `pistas`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_categoria_pista` (`categoria_id`);

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
-- AUTO_INCREMENT de la tabla `categorias_pistas`
--
ALTER TABLE `categorias_pistas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT de la tabla `pistas`
--
ALTER TABLE `pistas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT de la tabla `reservas`
--
ALTER TABLE `reservas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `pistas`
--
ALTER TABLE `pistas`
  ADD CONSTRAINT `fk_categoria_pista` FOREIGN KEY (`categoria_id`) REFERENCES `categorias_pistas` (`id`);

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
