-- --------------------------------------------------------
-- Versión del servidor:         10.5.10-MariaDB - mariadb.org binary distribution
-- SO del servidor:              Win64
-- --------------------------------------------------------

-- Volcando estructura de base de datos para sti_tareas
CREATE DATABASE IF NOT EXISTS `sti_tareas`
USE `sti_tareas`;

-- Volcando estructura para tabla sti_tareas.empleado
CREATE TABLE IF NOT EXISTS `empleado` (
  `empleado_id` int(11) NOT NULL AUTO_INCREMENT,
  `empleado_nombre` varchar(100) NOT NULL,
  `empleado_estado` smallint(6) NOT NULL DEFAULT 1,
  PRIMARY KEY (`empleado_id`),
  UNIQUE KEY `empleado_nombre` (`empleado_nombre`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Volcando estructura para tabla sti_tareas.tareas
CREATE TABLE IF NOT EXISTS `tareas` (
  `tareas_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `tareas_asunto` varchar(100) NOT NULL DEFAULT '',
  `tareas_descripcion` text DEFAULT NULL,
  `tareas_estado` smallint(6) NOT NULL DEFAULT 0,
  `tareas_responsable` int(11) NOT NULL DEFAULT 0,
  `tareas_fecha_creacion` datetime NOT NULL DEFAULT current_timestamp(),
  `tareas_fecha_deseada` datetime NOT NULL,
  `tareas_fecha_cierre` datetime DEFAULT NULL,
  PRIMARY KEY (`tareas_id`),
  KEY `responsable` (`tareas_responsable`),
  KEY `fecha` (`tareas_fecha_deseada`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
