-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 26-03-2026 a las 19:00:19
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
-- Base de datos: `security_hub`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `eventos`
--

CREATE TABLE `eventos` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `titulo` varchar(100) DEFAULT NULL,
  `descripcion` text DEFAULT NULL,
  `fecha` date DEFAULT NULL,
  `hora` time DEFAULT NULL,
  `tipo` enum('feriado','partido','efemeride','personal') NOT NULL DEFAULT 'personal',
  `color` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `eventos`
--

INSERT INTO `eventos` (`id`, `user_id`, `titulo`, `descripcion`, `fecha`, `hora`, `tipo`, `color`) VALUES
(1, NULL, 'Año Nuevo', 'Feriado nacional', '2026-01-01', NULL, 'feriado', 'event-blue'),
(2, NULL, 'Carnaval', 'Feriado nacional', '2026-02-16', NULL, 'feriado', 'event-blue'),
(3, NULL, 'Carnaval', 'Feriado nacional', '2026-02-17', NULL, 'feriado', 'event-blue'),
(4, NULL, 'Día de la Memoria', 'Feriado nacional', '2026-03-24', NULL, 'feriado', 'event-blue'),
(5, NULL, 'Día del Veterano y de los Caídos en Malvinas', 'Feriado nacional', '2026-04-02', NULL, 'feriado', 'event-blue'),
(6, NULL, 'Día del Trabajador', 'Feriado nacional', '2026-05-01', NULL, 'feriado', 'event-blue'),
(7, NULL, 'Día de la Revolución de Mayo', 'Feriado nacional', '2026-05-25', NULL, 'feriado', 'event-blue'),
(8, NULL, 'Paso a la Inmortalidad de Güemes', 'Feriado nacional', '2026-06-17', NULL, 'feriado', 'event-blue'),
(9, NULL, 'Paso a la Inmortalidad de Belgrano', 'Feriado nacional', '2026-06-20', NULL, 'feriado', 'event-blue'),
(10, NULL, 'Día de la Independencia', 'Feriado nacional', '2026-07-09', NULL, 'feriado', 'event-blue'),
(11, NULL, 'Paso a la Inmortalidad de San Martín', 'Feriado trasladable', '2026-08-17', NULL, 'feriado', 'event-blue'),
(12, NULL, 'Día del Respeto a la Diversidad Cultural', 'Feriado trasladable', '2026-10-12', NULL, 'feriado', 'event-blue'),
(13, NULL, 'Día de la Soberanía Nacional', 'Feriado trasladable', '2026-11-20', NULL, 'feriado', 'event-blue'),
(14, NULL, 'Inmaculada Concepción de María', 'Feriado nacional', '2026-12-08', NULL, 'feriado', 'event-blue'),
(15, NULL, 'Navidad', 'Feriado nacional', '2026-12-25', NULL, 'feriado', 'event-blue'),
(16, NULL, 'Inicio de clases', 'Conmemoración o recuerdo de un hecho histórico notable.', '2026-03-09', NULL, 'efemeride', 'event-purple'),
(17, NULL, 'Día del Estudiante', 'Conmemoración o recuerdo de un hecho histórico notable.', '2026-09-21', NULL, 'efemeride', 'event-purple'),
(18, NULL, 'Fin de clases', 'Conmemoración o recuerdo de un hecho histórico notable.', '2026-12-10', NULL, 'efemeride', 'event-purple'),
(19, NULL, 'Argentina vs Argelia', 'Partido de la selección', '2026-06-16', '22:00:00', 'partido', 'event-pink'),
(20, NULL, 'Argentina vs Austria', 'Partido de la selección', '2026-06-22', '14:00:00', 'partido', 'event-pink'),
(21, NULL, 'Argentina vs Jordania', 'Partido de la selección', '2026-06-27', '23:00:00', 'partido', 'event-pink'),
(22, 1, '1', '1', '2026-03-03', '12:00:00', 'personal', 'event-green'),
(23, 1, '2', '2', '2026-03-03', '12:00:00', 'personal', 'event-green'),
(24, 1, '3', '3', '2026-03-03', '12:00:00', 'personal', 'event-green'),
(45, 3, 'a', 'a', '2026-03-03', '12:00:00', 'personal', 'event-green');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `password_logs`
--

CREATE TABLE `password_logs` (
  `id` int(11) NOT NULL,
  `password_hash` varchar(255) DEFAULT NULL,
  `fecha_creacion` timestamp NOT NULL DEFAULT current_timestamp(),
  `fecha_evento_calendario` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `users`
--

INSERT INTO `users` (`id`, `username`, `email`, `password_hash`, `created_at`) VALUES
(1, 'tomasiezzi16@gmail.com', 'tomasiezzi16@gmail.com', '$2y$10$8ud2wIhb4Grf89pCPrP2.ulyjHZP5kdGfMUPSaTUnvOAV2TS/K5tq', '2026-03-26 14:55:42'),
(3, 'hola', 'holaweb@gmail.com', '$2y$10$O5uk3pRemddi9H9u5WtRdOjMUUAZu0Z19XbWuqkNiW3MJL6EtbEPO', '2026-03-26 15:01:10');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `eventos`
--
ALTER TABLE `eventos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indices de la tabla `password_logs`
--
ALTER TABLE `password_logs`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `eventos`
--
ALTER TABLE `eventos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=46;

--
-- AUTO_INCREMENT de la tabla `password_logs`
--
ALTER TABLE `password_logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `eventos`
--
ALTER TABLE `eventos`
  ADD CONSTRAINT `eventos_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
