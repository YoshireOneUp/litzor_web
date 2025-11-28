-- ============================================
-- BASE DE DATOS UNIFICADA LITZOR
-- Versión: 2.0
-- App Web: Organizadores y Administradores
-- App Móvil: Solo Invitados (sin registro)
-- ============================================

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";

START TRANSACTION;

SET time_zone = "+00:00";

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */
;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */
;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */
;
/*!40101 SET NAMES utf8mb4 */
;

-- ============================================
-- CREAR BASE DE DATOS
-- ============================================

DROP DATABASE IF EXISTS `db_litzor`;

CREATE DATABASE IF NOT EXISTS `db_litzor` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

USE `db_litzor`;

-- ============================================
-- TABLA: clientes
-- USO: Usuarios de la app WEB (Organizadores y Administradores)
-- IMPORTANTE: Los invitados móviles NO están aquí
-- ============================================

DROP TABLE IF EXISTS `clientes`;

CREATE TABLE `clientes` (
    `id_cl` int(11) NOT NULL AUTO_INCREMENT,
    `nombre_cl` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
    `correo_cl` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
    `contrasena_cl` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
    `tipo_usuario` int(1) NOT NULL DEFAULT 1 COMMENT '1=Organizador (Web), 2=Administrador (Web)',
    `fecha_registro` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id_cl`),
    UNIQUE KEY `correo_cl` (`correo_cl`),
    KEY `idx_tipo_usuario` (`tipo_usuario`)
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;

-- ============================================
-- DATOS DE EJEMPLO: clientes
-- ============================================

INSERT INTO
    `clientes` (
        `id_cl`,
        `nombre_cl`,
        `correo_cl`,
        `contrasena_cl`,
        `tipo_usuario`,
        `fecha_registro`
    )
VALUES (
        1,
        'Fernanda Moreno',
        'fer.moon@gmail.com',
        '12345',
        1,
        '2025-11-14 06:01:57'
    ),
    (
        2,
        'Yosafat Pacheco',
        'admin@test.com',
        'Admin123.',
        2,
        '2025-11-14 06:01:57'
    ),
    (
        3,
        'Mario Alberto',
        'admin2@test.com',
        'Admin321.',
        2,
        '2025-11-14 06:01:57'
    );

-- ============================================
-- TABLA: eventos
-- USO: Eventos creados por organizadores (Web)
--      Visualizados por invitados (Móvil)
-- ============================================

DROP TABLE IF EXISTS `eventos`;

CREATE TABLE `eventos` (
    `id_evento` int(11) NOT NULL AUTO_INCREMENT,
    `codigo_evento` varchar(8) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Código alfanumérico único',
    `codigo_qr` longtext COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Base64 del código QR',
    `nombre_evento` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
    `fecha_evento` date NOT NULL,
    `hora_inicio` time NOT NULL,
    `hora_fin` time NOT NULL,
    `ubicacion` text COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Dirección del evento',
    `latitud` decimal(10, 8) DEFAULT NULL,
    `longitud` decimal(11, 8) DEFAULT NULL,
    `id_organizador` int(11) NOT NULL,
    `estado` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT 'activo' COMMENT 'activo, finalizado',
    `fecha_creacion` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id_evento`),
    UNIQUE KEY `codigo_evento` (`codigo_evento`),
    KEY `id_organizador` (`id_organizador`),
    KEY `idx_fecha_evento` (`fecha_evento`),
    KEY `idx_estado` (`estado`),
    CONSTRAINT `fk_eventos_organizador` FOREIGN KEY (`id_organizador`) REFERENCES `clientes` (`id_cl`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;

-- ============================================
-- TABLA: invitados
-- USO: Lista de correos invitados a eventos
--      App MÓVIL usa esta tabla para "login"
-- IMPORTANTE: Los invitados NO están en tabla 'clientes'
-- ============================================

DROP TABLE IF EXISTS `invitados`;

CREATE TABLE `invitados` (
    `id_invitado` int(11) NOT NULL AUTO_INCREMENT,
    `id_evento` int(11) NOT NULL,
    `correo_invitado` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
    `nombre_invitado` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
    `fecha_agregado` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id_invitado`),
    KEY `id_evento` (`id_evento`),
    KEY `idx_correo_invitado` (`correo_invitado`),
    CONSTRAINT `fk_invitados_evento` FOREIGN KEY (`id_evento`) REFERENCES `eventos` (`id_evento`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;

-- ============================================
-- DATOS DE EJEMPLO: invitados
-- ============================================

INSERT INTO
    `invitados` (
        `id_invitado`,
        `id_evento`,
        `correo_invitado`,
        `nombre_invitado`,
        `fecha_agregado`
    )
VALUES (
        1,
        1,
        'andradeyos2006@gmail.com',
        NULL,
        '2025-11-14 06:42:28'
    ),
    (
        2,
        1,
        'maria.lopez@example.com',
        NULL,
        '2025-11-27 20:00:00'
    ),
    (
        3,
        1,
        'juan.perez@example.com',
        NULL,
        '2025-11-27 20:00:00'
    ),
    (
        4,
        2,
        'andradeyos2006@gmail.com',
        NULL,
        '2025-11-27 20:00:00'
    ),
    (
        5,
        2,
        'maria.lopez@example.com',
        NULL,
        '2025-11-27 20:00:00'
    ),
    (
        6,
        2,
        'carlos.ruiz@example.com',
        NULL,
        '2025-11-27 20:00:00'
    ),
    (
        7,
        2,
        'ana.garcia@example.com',
        NULL,
        '2025-11-27 20:00:00'
    ),
    (
        8,
        3,
        'andradeyos2006@gmail.com',
        NULL,
        '2025-11-27 20:00:00'
    ),
    (
        9,
        3,
        'pedro.martinez@example.com',
        NULL,
        '2025-11-27 20:00:00'
    );

-- ============================================
-- ÍNDICES Y OPTIMIZACIONES
-- ============================================

-- Optimizar búsquedas por correo de invitado (app móvil)
ALTER TABLE `invitados`
ADD INDEX `idx_correo_evento` (
    `correo_invitado`,
    `id_evento`
);

-- Optimizar búsquedas de eventos activos
ALTER TABLE `eventos`
ADD INDEX `idx_estado_fecha` (`estado`, `fecha_evento`);

-- ============================================
-- AUTO_INCREMENT
-- ============================================

ALTER TABLE `clientes`
MODIFY `id_cl` int(11) NOT NULL AUTO_INCREMENT,
AUTO_INCREMENT = 4;

ALTER TABLE `eventos`
MODIFY `id_evento` int(11) NOT NULL AUTO_INCREMENT,
AUTO_INCREMENT = 4;

ALTER TABLE `invitados`
MODIFY `id_invitado` int(11) NOT NULL AUTO_INCREMENT,
AUTO_INCREMENT = 10;

-- ============================================
-- TRIGGERS AUTOMÁTICOS
-- ============================================

-- Actualizar eventos pasados a 'finalizado' automáticamente
DELIMITER $$

DROP EVENT IF EXISTS `actualizar_eventos_finalizados` $$

CREATE EVENT `actualizar_eventos_finalizados`
ON SCHEDULE EVERY 1 DAY
STARTS CURRENT_TIMESTAMP
DO
BEGIN
    UPDATE eventos 
    SET estado = 'finalizado' 
    WHERE fecha_evento < CURDATE() AND estado = 'activo';
END $$

DELIMITER;

-- ============================================
-- COMENTARIOS Y DOCUMENTACIÓN
-- ============================================

/*
ARQUITECTURA DE LA BASE DE DATOS:

┌─────────────────────────────────────────────────────────┐
│                    APP WEB                              │
│  - Organizadores (tipo_usuario = 1) en 'clientes'      │
│  - Administradores (tipo_usuario = 2) en 'clientes'    │
│  - Crean eventos y agregan invitados                   │
└─────────────────────────────────────────────────────────┘
│
│ Crea eventos y
│ agrega correos
▼
┌─────────────────────────────────────────────────────────┐
│                 TABLA: eventos                          │
│  - Eventos creados por organizadores                    │
│  - Accesibles desde web y móvil                        │
└─────────────────────────────────────────────────────────┘
│
│ Relaciona con
▼
┌─────────────────────────────────────────────────────────┐
│               TABLA: invitados                          │
│  - Lista de correos invitados                           │
│  - NO son usuarios registrados                          │
│  - Solo correos para acceso móvil                       │
└─────────────────────────────────────────────────────────┘
│
│ App móvil consulta
▼
┌─────────────────────────────────────────────────────────┐
│                   APP MÓVIL                             │
│  - Solo requiere correo (sin registro)                  │
│  - Si correo está en 'invitados', ve eventos           │
│  - NO tiene acceso a tabla 'clientes'                   │
└─────────────────────────────────────────────────────────┘

FLUJO DE AUTENTICACIÓN:

APP WEB:
1. Usuario se registra en 'clientes' con contraseña
2. Hace login con correo + contraseña
3. Accede según tipo_usuario (1=Organizador, 2=Admin)

APP MÓVIL:
1. Usuario ingresa solo su correo
2. API verifica si correo existe en 'invitados'
3. Si existe, muestra eventos relacionados
4. NO requiere contraseña ni registro

TABLAS Y SUS USOS:
- clientes: Solo usuarios WEB (organizadores y admins)
- eventos: Compartido entre WEB (crear) y MÓVIL (ver)
- invitados: Solo para APP MÓVIL (lista de acceso)

IMPORTANTE:
- Los invitados NUNCA se registran en 'clientes'
- Los invitados solo existen como correos en 'invitados'
- La app móvil NO tiene sistema de registro
- La app móvil solo verifica correos contra 'invitados'
*/

-- ============================================
-- VERIFICACIÓN DE INTEGRIDAD
-- ============================================

-- Verificar que no hay usuarios duplicados
SELECT correo_cl, COUNT(*) as duplicados
FROM clientes
GROUP BY
    correo_cl
HAVING
    COUNT(*) > 1;

-- Verificar eventos sin organizador
SELECT e.*
FROM eventos e
    LEFT JOIN clientes c ON e.id_organizador = c.id_cl
WHERE
    c.id_cl IS NULL;

-- Verificar invitados sin evento
SELECT i.*
FROM invitados i
    LEFT JOIN eventos e ON i.id_evento = e.id_evento
WHERE
    e.id_evento IS NULL;

-- ============================================
-- CONSULTAS DE PRUEBA
-- ============================================

-- Ver todos los organizadores
SELECT * FROM clientes WHERE tipo_usuario = 1;

-- Ver todos los administradores
SELECT * FROM clientes WHERE tipo_usuario = 2;

-- Ver eventos activos con organizador
SELECT e.nombre_evento, e.codigo_evento, e.fecha_evento, c.nombre_cl as organizador
FROM eventos e
    JOIN clientes c ON e.id_organizador = c.id_cl
WHERE
    e.estado = 'activo'
ORDER BY e.fecha_evento;

-- Ver invitados por evento
SELECT e.nombre_evento, i.correo_invitado, i.fecha_agregado
FROM eventos e
    JOIN invitados i ON e.id_evento = i.id_evento
ORDER BY e.nombre_evento, i.fecha_agregado;

-- Ver cuántos eventos tiene cada invitado
SELECT
    correo_invitado,
    COUNT(*) as total_eventos
FROM invitados
GROUP BY
    correo_invitado
ORDER BY total_eventos DESC;

-- ============================================
-- FIN DEL SCRIPT
-- ============================================

COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */
;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */
;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */
;

-- Mensaje de confirmación
SELECT 'Base de datos db_litzor creada exitosamente!' as Mensaje;