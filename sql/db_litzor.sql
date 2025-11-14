-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1:3309
-- Tiempo de generación: 14-11-2025 a las 18:52:42
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
-- Base de datos: `db_litzor`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `clientes`
--

CREATE TABLE `clientes` (
  `id_cl` int(11) NOT NULL,
  `nombre_cl` varchar(50) DEFAULT NULL,
  `correo_cl` varchar(50) DEFAULT NULL,
  `contrasena_cl` varchar(255) DEFAULT NULL,
  `tipo_usuario` int(1) DEFAULT 1 COMMENT '1=Organizador, 2=Administrador',
  `fecha_registro` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

--
-- Volcado de datos para la tabla `clientes`
--

INSERT INTO `clientes` (`id_cl`, `nombre_cl`, `correo_cl`, `contrasena_cl`, `tipo_usuario`, `fecha_registro`) VALUES
(1, 'Fernanda Moreno', 'fer.moon@gmail.com', '12345', 1, '2025-11-14 06:01:57'),
(2, 'Yosafat Pacheco', 'admin@test.com', 'Admin123.', 2, '2025-11-14 06:01:57'),
(3, 'Mario Alberto', 'admin2@test.com', 'Admin321.', 2, '2025-11-14 06:01:57');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `eventos`
--

CREATE TABLE `eventos` (
  `id_evento` int(11) NOT NULL,
  `codigo_evento` varchar(8) NOT NULL COMMENT 'C?digo alfanum?rico ?nico',
  `codigo_qr` text DEFAULT NULL COMMENT 'Base64 del c?digo QR',
  `nombre_evento` varchar(100) NOT NULL,
  `fecha_evento` date NOT NULL,
  `hora_inicio` time NOT NULL,
  `hora_fin` time NOT NULL,
  `ubicacion` text DEFAULT NULL COMMENT 'Direcci?n del evento',
  `latitud` decimal(10,8) DEFAULT NULL,
  `longitud` decimal(11,8) DEFAULT NULL,
  `id_organizador` int(11) NOT NULL,
  `estado` varchar(20) DEFAULT 'activo' COMMENT 'activo, finalizado',
  `fecha_creacion` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

--
-- Volcado de datos para la tabla `eventos`
--

INSERT INTO `eventos` (`id_evento`, `codigo_evento`, `codigo_qr`, `nombre_evento`, `fecha_evento`, `hora_inicio`, `hora_fin`, `ubicacion`, `latitud`, `longitud`, `id_organizador`, `estado`, `fecha_creacion`) VALUES
(3, 'UCYSQS6D', 'iVBORw0KGgoAAAANSUhEUgAAASwAAAEsCAYAAAB5fY51AAAAAklEQVR4AewaftIAAAW0SURBVO3BgW1jC3AEsNmF+m95cw04eMgXYo9Ecu6fABTYAJTYAJTYAJTYAJTYAJTYAJTYAJTYAJTYAJTYAJTYAJTYAJTYAJTYAJTYAJTYAJTYAJTYAJTYAJTYAJTYAJTYAJTYAJTYAJTYAJTYAJTYAJTYAJTYAJTYAJTYAJTYAJTYAJTYAJTYAJTYAJTYAJTYAJTYAJTYAJTYAJR4pcTMhJ/dXRrMTJ64u7zbzISf3V3+ug1AiQ1AiQ1AiQ1AiQ1AiQ1AiQ1AiQ1AiQ1AiVc+zN3lk8xMfsvM5BvdXT7JzORTbABKbABKbABKbABKbABKbABKbABKbABKbABKbABKvPLFZia/5e7yW2YmT9xd+NnM5LfcXb7RBqDEBqDEBqDEBqDEBqDEBqDEBqDEBqDEBqDEK/AfzUyeurvA/9UGoMQGoMQGoMQGoMQGoMQGoMQGoMQGoMQGoMQr8IOZybvNTN7p7sL32ACU2ACU2ACU2ACU2ACU2ACU2ACU2ACU2ACU2ACUeOWL3V347+4u7zYz+evuLvz/2gCU2ACU2ACU2ACU2ACU2ACU2ACU2ACU2ACUeOXDzEx4j7vLEzOTp+4uf93MhL9pA1BiA1BiA1BiA1BiA1BiA1BiA1BiA1BiA1Bi7p/wVWYmn+LuwvfYAJTYAJTYAJTYAJTYAJTYAJTYAJTYAJTYAJTYAJR4pcTM5Im7y1Mzk09xd3nq7vLXzUzebWbyKe4u32gDUGIDUGIDUGIDUGIDUGIDUGIDUGIDUGIDUOKVEneXv+7u8ltmJk/dXZ6YmTxxd2lwd3liZvLE3eWpmckTd5d3m5k8cXf56zYAJTYAJTYAJTYAJTYAJTYAJTYAJTYAJTYAJV75MDOTp+4uT8xM3m1m8o1mJk/cXZ6YmTx1d3ni7vLEzOSpu8sTM5Mn7i5P3V0+xQagxAagxAagxAagxAagxAagxAagxAagxAagxAagxNw/KTAzebe7yzvNTJ66u7zTzOTd7i7faGbybneXJ2Ym73Z3+RQbgBIbgBIbgBIbgBIbgBIbgBIbgBIbgBIbgBKvlLi7vNvM5Im7y7vNTN7p7vLUzOSdZibvdnd5Ymby1N3libvLEzOTd7u7PDEz+UYbgBIbgBIbgBIbgBIbgBIbgBIbgBIbgBIbgBJz/4SvMjN5p7vLUzOTT3F3eWpm8sTd5YmZyVN3l0+xASixASixASixASixASixASixASixASixASixASjxSomZCT+7uzx1d3mnmclTd5cnZiZP3F2empn8dTMTfrYBKLEBKLEBKLEBKLEBKLEBKLEBKLEBKLEBKPHKh7m7fJKZybvNTH7LzOSdZiYN7i5PzEyeuLt8ow1AiQ1AiQ1AiQ1AiQ1AiQ1AiQ1AiQ1AiQ1AiVe+2Mzkt9xd/rq7y2+ZmTxxd3m3mckTM5On7i5P3F2emJk8dXf5FBuAEhuAEhuAEhuAEhuAEhuAEhuAEhuAEhuAEhuAEq/AfzQzeeru8tfdXf66u8s32gCU2ACU2ACU2ACU2ACU2ACU2ACU2ACU2ACUeAV+MDN5t5nJE3eXJ2YmT91d3mlm8m53F362ASixASixASixASixASixASixASixASixASjxyhe7u3yju8unuLu828zkibtLg5nJE3eXv24DUGIDUGIDUGIDUGIDUGIDUGIDUGIDUGIDUGIDUOKVDzMz4X83M3ni7vLEzOTd7i5PzEyeuru808zkqbvLEzOTJ+4uT91dPsUGoMQGoMQGoMQGoMQGoMQGoMQGoMQGoMQGoMTcPwEosAEosQEosQEosQEosQEosQEosQEosQEosQEosQEosQEosQEosQEosQEosQEosQEosQEosQEosQEosQEosQEosQEosQEosQEosQEosQEosQEosQEosQEosQEosQEosQEosQEosQEosQEosQEosQEosQEosQEosQEo8T+sPAln21qW7QAAAABJRU5ErkJggg==', 'Alimentar Perros Callejeros', '2025-11-15', '14:00:00', '15:00:00', '433, Calle Playa Mocambo, Desarrollo San Pablo, Delegación Epigmenio González, Santiago de Querétaro, Municipio de Querétaro, Querétaro, 73130, México', 20.62360173, -100.40955842, 1, 'activo', '2025-11-14 06:42:28');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `invitados`
--

CREATE TABLE `invitados` (
  `id_invitado` int(11) NOT NULL,
  `id_evento` int(11) NOT NULL,
  `correo_invitado` varchar(100) NOT NULL,
  `nombre_invitado` varchar(100) DEFAULT NULL,
  `fecha_agregado` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

--
-- Volcado de datos para la tabla `invitados`
--

INSERT INTO `invitados` (`id_invitado`, `id_evento`, `correo_invitado`, `nombre_invitado`, `fecha_agregado`) VALUES
(7, 3, 'andradeyos2006@gmail.com', NULL, '2025-11-14 06:42:28');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `clientes`
--
ALTER TABLE `clientes`
  ADD PRIMARY KEY (`id_cl`),
  ADD UNIQUE KEY `correo_cl` (`correo_cl`);

--
-- Indices de la tabla `eventos`
--
ALTER TABLE `eventos`
  ADD PRIMARY KEY (`id_evento`),
  ADD UNIQUE KEY `codigo_evento` (`codigo_evento`),
  ADD KEY `id_organizador` (`id_organizador`);

--
-- Indices de la tabla `invitados`
--
ALTER TABLE `invitados`
  ADD PRIMARY KEY (`id_invitado`),
  ADD KEY `id_evento` (`id_evento`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `clientes`
--
ALTER TABLE `clientes`
  MODIFY `id_cl` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `eventos`
--
ALTER TABLE `eventos`
  MODIFY `id_evento` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `invitados`
--
ALTER TABLE `invitados`
  MODIFY `id_invitado` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `eventos`
--
ALTER TABLE `eventos`
  ADD CONSTRAINT `eventos_ibfk_1` FOREIGN KEY (`id_organizador`) REFERENCES `clientes` (`id_cl`) ON DELETE CASCADE;

--
-- Filtros para la tabla `invitados`
--
ALTER TABLE `invitados`
  ADD CONSTRAINT `invitados_ibfk_1` FOREIGN KEY (`id_evento`) REFERENCES `eventos` (`id_evento`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
