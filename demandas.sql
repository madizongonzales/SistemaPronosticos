-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 04-12-2023 a las 00:42:02
-- Versión del servidor: 10.4.28-MariaDB
-- Versión de PHP: 8.2.4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `bd_pronostico`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `demandas`
--

CREATE TABLE `demandas` (
  `id_demandas` int(11) NOT NULL,
  `demanda` int(11) NOT NULL,
  `id_anio` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `demandas`
--

INSERT INTO `demandas` (`id_demandas`, `demanda`, `id_anio`) VALUES
(1, 1250, 1),
(2, 1160, 1),
(3, 980, 1),
(4, 850, 1),
(5, 930, 1),
(6, 1215, 1),
(7, 1350, 1),
(8, 1486, 1),
(9, 1321, 1),
(10, 1246, 1),
(11, 1132, 1),
(12, 854, 1);

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `demandas`
--
ALTER TABLE `demandas`
  ADD PRIMARY KEY (`id_demandas`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `demandas`
--
ALTER TABLE `demandas`
  MODIFY `id_demandas` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
