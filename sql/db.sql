-- Base de datos completa con todo lo necesario

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

-- ========================================
-- TABLA DE CLIENTES (Actualizada con tipo de usuario)
-- ========================================
CREATE TABLE IF NOT EXISTS `clientes` (
  `id_cl` int(11) NOT NULL AUTO_INCREMENT,
  `nombre_cl` varchar(50) DEFAULT NULL,
  `correo_cl` varchar(50) DEFAULT NULL,
  `contrasena_cl` varchar(255) DEFAULT NULL,
  `tipo_usuario` int(1) DEFAULT 1 COMMENT '1=Organizador, 2=Administrador',
  `fecha_registro` timestamp DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_cl`),
  UNIQUE KEY `correo_cl` (`correo_cl`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

-- Insertar organizador de ejemplo
INSERT INTO `clientes` (`nombre_cl`, `correo_cl`, `contrasena_cl`, `tipo_usuario`) VALUES
('Fernanda Moreno', 'fer.moon@gmail.com', '12345', 1);

-- Insertar los 2 administradores
INSERT INTO `clientes` (`nombre_cl`, `correo_cl`, `contrasena_cl`, `tipo_usuario`) VALUES
('Yosafat Pacheco', 'admin@test.com', 'Admin123.', 2),
('Mario Alberto', 'admin2@test.com', 'Admin321.', 2);

-- ========================================
-- TABLA DE EVENTOS
-- ========================================
CREATE TABLE IF NOT EXISTS `eventos` (
  `id_evento` int(11) NOT NULL AUTO_INCREMENT,
  `codigo_evento` varchar(8) NOT NULL UNIQUE COMMENT 'Código alfanumérico único',
  `codigo_qr` text DEFAULT NULL COMMENT 'Base64 del código QR',
  `nombre_evento` varchar(100) NOT NULL,
  `fecha_evento` date NOT NULL,
  `hora_inicio` time NOT NULL,
  `hora_fin` time NOT NULL,
  `ubicacion` text DEFAULT NULL COMMENT 'Dirección del evento',
  `latitud` decimal(10, 8) DEFAULT NULL,
  `longitud` decimal(11, 8) DEFAULT NULL,
  `id_organizador` int(11) NOT NULL,
  `estado` varchar(20) DEFAULT 'activo' COMMENT 'activo, finalizado',
  `fecha_creacion` timestamp DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_evento`),
  KEY `id_organizador` (`id_organizador`),
  FOREIGN KEY (`id_organizador`) REFERENCES `clientes`(`id_cl`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

-- ========================================
-- TABLA DE INVITADOS
-- ========================================
CREATE TABLE IF NOT EXISTS `invitados` (
  `id_invitado` int(11) NOT NULL AUTO_INCREMENT,
  `id_evento` int(11) NOT NULL,
  `correo_invitado` varchar(100) NOT NULL,
  `nombre_invitado` varchar(100) DEFAULT NULL,
  `fecha_agregado` timestamp DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_invitado`),
  KEY `id_evento` (`id_evento`),
  FOREIGN KEY (`id_evento`) REFERENCES `eventos`(`id_evento`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

-- Eventos de ejemplo (opcional)
INSERT INTO `eventos` (`codigo_evento`, `nombre_evento`, `fecha_evento`, `hora_inicio`, `hora_fin`, `ubicacion`, `latitud`, `longitud`, `id_organizador`, `estado`) VALUES
('A1B2C3D4', 'Conferencia Tech 2025', '2025-12-15', '09:00:00', '18:00:00', 'Centro de Convenciones, CDMX', 19.432608, -99.133209, 1, 'activo'),
('E5F6G7H8', 'Workshop Marketing', '2025-11-20', '10:00:00', '14:00:00', 'Hotel Marriott, Querétaro', 20.588793, -100.389888, 1, 'activo');

-- Invitados de ejemplo
INSERT INTO `invitados` (`id_evento`, `correo_invitado`, `nombre_invitado`) VALUES
(1, 'invitado1@example.com', 'Juan Pérez'),
(1, 'invitado2@example.com', 'María González'),
(2, 'invitado3@example.com', 'Carlos Ramírez');

COMMIT;