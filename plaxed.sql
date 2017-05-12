-- --------------------------------------------------------
-- Host:                         127.0.0.1
-- Server version:               5.7.17-log - MySQL Community Server (GPL)
-- Server OS:                    Win32
-- HeidiSQL Version:             9.4.0.5170
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!50503 SET NAMES utf8mb4 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;

-- Dumping structure for table plaxedco_nuevo.actividad
DROP TABLE IF EXISTS `actividad`;
CREATE TABLE IF NOT EXISTS `actividad` (
  `actividad_id` int(11) NOT NULL AUTO_INCREMENT,
  `usuario_id` int(11) NOT NULL,
  `titulo` varchar(60) NOT NULL,
  `descripcion` text NOT NULL,
  `fecha` datetime NOT NULL,
  `fecham` datetime NOT NULL,
  `cerrado` int(1) NOT NULL,
  PRIMARY KEY (`actividad_id`)
) ENGINE=MyISAM AUTO_INCREMENT=21 DEFAULT CHARSET=utf8;

-- Data exporting was unselected.
-- Dumping structure for table plaxedco_nuevo.actividad_comentario
DROP TABLE IF EXISTS `actividad_comentario`;
CREATE TABLE IF NOT EXISTS `actividad_comentario` (
  `actividad_comentario_id` int(15) NOT NULL AUTO_INCREMENT,
  `actividad_id` int(15) NOT NULL,
  `usuario_id` int(15) NOT NULL,
  `contenido` text NOT NULL,
  `fecha` datetime NOT NULL,
  PRIMARY KEY (`actividad_comentario_id`)
) ENGINE=MyISAM AUTO_INCREMENT=30 DEFAULT CHARSET=utf8;

-- Data exporting was unselected.
-- Dumping structure for table plaxedco_nuevo.bloqueo
DROP TABLE IF EXISTS `bloqueo`;
CREATE TABLE IF NOT EXISTS `bloqueo` (
  `bloqueo_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `usuario_origen_id` int(10) unsigned NOT NULL,
  `usuario_destino_id` int(10) unsigned NOT NULL,
  `fecha` datetime NOT NULL,
  PRIMARY KEY (`bloqueo_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Data exporting was unselected.
-- Dumping structure for table plaxedco_nuevo.conexion
DROP TABLE IF EXISTS `conexion`;
CREATE TABLE IF NOT EXISTS `conexion` (
  `conexion_id` int(11) NOT NULL AUTO_INCREMENT,
  `usuario1_id` int(11) NOT NULL,
  `usuario2_id` int(11) NOT NULL,
  `fecha` datetime NOT NULL,
  PRIMARY KEY (`conexion_id`)
) ENGINE=InnoDB AUTO_INCREMENT=1110 DEFAULT CHARSET=utf8;

-- Data exporting was unselected.
-- Dumping structure for table plaxedco_nuevo.conversacion
DROP TABLE IF EXISTS `conversacion`;
CREATE TABLE IF NOT EXISTS `conversacion` (
  `conversacion_id` int(15) NOT NULL AUTO_INCREMENT,
  `usuario_id` int(15) NOT NULL,
  PRIMARY KEY (`conversacion_id`),
  KEY `usuario_id` (`usuario_id`)
) ENGINE=InnoDB AUTO_INCREMENT=40666 DEFAULT CHARSET=utf8;

-- Data exporting was unselected.
-- Dumping structure for table plaxedco_nuevo.dominios_invalidos
DROP TABLE IF EXISTS `dominios_invalidos`;
CREATE TABLE IF NOT EXISTS `dominios_invalidos` (
  `dominio` varchar(50) CHARACTER SET utf8 COLLATE utf8_estonian_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Data exporting was unselected.
-- Dumping structure for table plaxedco_nuevo.mencion
DROP TABLE IF EXISTS `mencion`;
CREATE TABLE IF NOT EXISTS `mencion` (
  `mencion_id` int(15) NOT NULL AUTO_INCREMENT,
  `publicacion_id` int(15) NOT NULL,
  `usuario_origen_id` int(15) NOT NULL,
  `usuario_destino_id` int(15) NOT NULL,
  PRIMARY KEY (`mencion_id`)
) ENGINE=InnoDB AUTO_INCREMENT=23652 DEFAULT CHARSET=utf8;

-- Data exporting was unselected.
-- Dumping structure for table plaxedco_nuevo.notificacion
DROP TABLE IF EXISTS `notificacion`;
CREATE TABLE IF NOT EXISTS `notificacion` (
  `notificacion_id` int(15) NOT NULL AUTO_INCREMENT,
  `usuario_origen_id` int(15) NOT NULL,
  `usuario_destino_id` int(15) NOT NULL,
  `destino_id` int(15) NOT NULL,
  `publicacion_id` int(15) NOT NULL,
  `tipo` varchar(30) NOT NULL,
  `visto` int(1) NOT NULL,
  `fecha` datetime NOT NULL,
  PRIMARY KEY (`notificacion_id`)
) ENGINE=InnoDB AUTO_INCREMENT=33469 DEFAULT CHARSET=utf8;

-- Data exporting was unselected.
-- Dumping structure for table plaxedco_nuevo.permiso
DROP TABLE IF EXISTS `permiso`;
CREATE TABLE IF NOT EXISTS `permiso` (
  `permiso_id` int(15) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(30) NOT NULL,
  PRIMARY KEY (`permiso_id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;

-- Data exporting was unselected.
-- Dumping structure for table plaxedco_nuevo.publicacion
DROP TABLE IF EXISTS `publicacion`;
CREATE TABLE IF NOT EXISTS `publicacion` (
  `publicacion_id` int(15) unsigned NOT NULL AUTO_INCREMENT,
  `replax_id` int(15) unsigned NOT NULL,
  `conversacion_id` int(15) NOT NULL,
  `respuesta_id` int(15) NOT NULL,
  `usuario_id` int(15) NOT NULL,
  `contenido` varchar(250) NOT NULL,
  `adjunto` int(1) NOT NULL,
  `fecha` datetime NOT NULL,
  `puntos` int(15) NOT NULL,
  PRIMARY KEY (`publicacion_id`)
) ENGINE=InnoDB AUTO_INCREMENT=57650 DEFAULT CHARSET=utf8;

-- Data exporting was unselected.
-- Dumping structure for table plaxedco_nuevo.publicacion_adjunto
DROP TABLE IF EXISTS `publicacion_adjunto`;
CREATE TABLE IF NOT EXISTS `publicacion_adjunto` (
  `publicacion_adjunto_id` int(11) NOT NULL AUTO_INCREMENT,
  `usuario_id` int(11) NOT NULL,
  `publicacion_id` int(11) NOT NULL,
  `original` varchar(50) NOT NULL,
  `miniatura` varchar(50) NOT NULL,
  PRIMARY KEY (`publicacion_adjunto_id`)
) ENGINE=InnoDB AUTO_INCREMENT=800 DEFAULT CHARSET=utf8;

-- Data exporting was unselected.
-- Dumping structure for table plaxedco_nuevo.publicacion_voto
DROP TABLE IF EXISTS `publicacion_voto`;
CREATE TABLE IF NOT EXISTS `publicacion_voto` (
  `publicacion_voto_id` int(15) NOT NULL AUTO_INCREMENT,
  `publicacion_id` int(15) NOT NULL,
  `usuario_id` int(15) NOT NULL,
  `usuario_destino_id` int(15) NOT NULL,
  `voto` char(1) NOT NULL,
  `fecha` datetime NOT NULL,
  PRIMARY KEY (`publicacion_voto_id`)
) ENGINE=InnoDB AUTO_INCREMENT=6087 DEFAULT CHARSET=utf8;

-- Data exporting was unselected.
-- Dumping structure for table plaxedco_nuevo.solicitud
DROP TABLE IF EXISTS `solicitud`;
CREATE TABLE IF NOT EXISTS `solicitud` (
  `solicitud_id` int(15) NOT NULL AUTO_INCREMENT,
  `usuario_origen_id` int(15) NOT NULL,
  `usuario_destino_id` int(15) NOT NULL,
  `estado` int(1) NOT NULL,
  `fecha` datetime NOT NULL,
  PRIMARY KEY (`solicitud_id`)
) ENGINE=InnoDB AUTO_INCREMENT=1473 DEFAULT CHARSET=utf8;

-- Data exporting was unselected.
-- Dumping structure for table plaxedco_nuevo.solicitudes_pendientes
DROP TABLE IF EXISTS `solicitudes_pendientes`;
CREATE TABLE IF NOT EXISTS `solicitudes_pendientes` (
  `solicitudes_pendientes_id` int(11) NOT NULL AUTO_INCREMENT,
  `correo` varchar(100) NOT NULL,
  `dato1` varchar(40) NOT NULL,
  `dato2` varchar(40) NOT NULL,
  `tipo` varchar(30) NOT NULL,
  `fecha` datetime NOT NULL,
  PRIMARY KEY (`solicitudes_pendientes_id`)
) ENGINE=InnoDB AUTO_INCREMENT=597 DEFAULT CHARSET=utf8;

-- Data exporting was unselected.
-- Dumping structure for table plaxedco_nuevo.usuario
DROP TABLE IF EXISTS `usuario`;
CREATE TABLE IF NOT EXISTS `usuario` (
  `usuario_id` int(15) unsigned NOT NULL AUTO_INCREMENT,
  `usuario_activo` int(1) NOT NULL,
  `usuario_confirmado` int(1) NOT NULL,
  `avatar` int(1) NOT NULL,
  `alias` varchar(15) NOT NULL,
  `clave` varchar(40) NOT NULL,
  `nombre` varchar(30) NOT NULL,
  `correo` varchar(100) NOT NULL,
  `fecha_registro` datetime NOT NULL,
  `puntos` bigint(20) NOT NULL,
  `posts` int(15) unsigned NOT NULL,
  `conexiones` int(15) unsigned NOT NULL,
  `biografia` varchar(200) NOT NULL,
  `ubicacion` varchar(40) NOT NULL,
  `tags` varchar(50) NOT NULL,
  `fecha_nacimiento` date NOT NULL,
  `sexo` char(1) NOT NULL,
  PRIMARY KEY (`usuario_id`)
) ENGINE=InnoDB AUTO_INCREMENT=705 DEFAULT CHARSET=utf8;

-- Data exporting was unselected.
-- Dumping structure for table plaxedco_nuevo.usuario_online
DROP TABLE IF EXISTS `usuario_online`;
CREATE TABLE IF NOT EXISTS `usuario_online` (
  `usuario_id` int(10) unsigned NOT NULL,
  `Momento` datetime NOT NULL,
  `ip` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Data exporting was unselected.
-- Dumping structure for table plaxedco_nuevo.usuario_permiso
DROP TABLE IF EXISTS `usuario_permiso`;
CREATE TABLE IF NOT EXISTS `usuario_permiso` (
  `usuario_permiso_id` int(15) unsigned NOT NULL AUTO_INCREMENT,
  `usuario_id` int(15) NOT NULL,
  `permiso_id` int(15) NOT NULL,
  PRIMARY KEY (`usuario_permiso_id`),
  KEY `usuario_id` (`usuario_id`),
  KEY `permiso_id` (`permiso_id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;

-- Data exporting was unselected.
-- Dumping structure for view plaxedco_nuevo.vt_actividad
DROP VIEW IF EXISTS `vt_actividad`;
-- Creating temporary table to overcome VIEW dependency errors
CREATE TABLE `vt_actividad` (
	`actividad_id` INT(11) NOT NULL,
	`cerrado` INT(1) NOT NULL,
	`titulo` VARCHAR(60) NOT NULL COLLATE 'utf8_general_ci',
	`descripcion` TEXT NOT NULL COLLATE 'utf8_general_ci',
	`usuario_id` INT(11) NOT NULL,
	`nombre` VARCHAR(30) NOT NULL COLLATE 'utf8_general_ci',
	`alias` VARCHAR(15) NOT NULL COLLATE 'utf8_general_ci',
	`fecha` DATETIME NOT NULL,
	`fecha_es` VARCHAR(10) NULL COLLATE 'utf8_general_ci',
	`hora` VARCHAR(11) NULL COLLATE 'utf8_general_ci',
	`fecham` DATETIME NOT NULL,
	`fecham_es` VARCHAR(10) NULL COLLATE 'utf8_general_ci',
	`horam` VARCHAR(11) NULL COLLATE 'utf8_general_ci'
) ENGINE=MyISAM;

-- Dumping structure for view plaxedco_nuevo.v_notificacion
DROP VIEW IF EXISTS `v_notificacion`;
-- Creating temporary table to overcome VIEW dependency errors
CREATE TABLE `v_notificacion` (
	`notificacion_id` INT(15) NOT NULL,
	`usuario_origen_id` INT(15) NOT NULL,
	`usuario_origen_nombre` VARCHAR(30) NOT NULL COLLATE 'utf8_general_ci',
	`usuario_origen_alias` VARCHAR(15) NOT NULL COLLATE 'utf8_general_ci',
	`usuario_destino_id` INT(15) NOT NULL,
	`usuario_destino_nombre` VARCHAR(30) NOT NULL COLLATE 'utf8_general_ci',
	`usuario_destino_alias` VARCHAR(15) NOT NULL COLLATE 'utf8_general_ci',
	`publicacion_id` INT(15) NOT NULL,
	`tipo` VARCHAR(30) NOT NULL COLLATE 'utf8_general_ci',
	`destino_id` INT(15) NOT NULL,
	`fecha` DATETIME NOT NULL,
	`visto` INT(1) NOT NULL
) ENGINE=MyISAM;

-- Dumping structure for view plaxedco_nuevo.vt_actividad
DROP VIEW IF EXISTS `vt_actividad`;
-- Removing temporary table and create final VIEW structure
DROP TABLE IF EXISTS `vt_actividad`;
CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `vt_actividad` AS select `a`.`actividad_id` AS `actividad_id`,`a`.`cerrado` AS `cerrado`,`a`.`titulo` AS `titulo`,`a`.`descripcion` AS `descripcion`,`a`.`usuario_id` AS `usuario_id`,`u`.`nombre` AS `nombre`,`u`.`alias` AS `alias`,`a`.`fecha` AS `fecha`,date_format(`a`.`fecha`,'%d/%m/%Y') AS `fecha_es`,date_format(`a`.`fecha`,'%h:%i:%s %p') AS `hora`,`a`.`fecham` AS `fecham`,date_format(`a`.`fecham`,'%d/%m/%Y') AS `fecham_es`,date_format(`a`.`fecham`,'%h:%i:%s %p') AS `horam` from (`actividad` `a` join `usuario` `u` on((`u`.`usuario_id` = `a`.`usuario_id`)));

-- Dumping structure for view plaxedco_nuevo.v_notificacion
DROP VIEW IF EXISTS `v_notificacion`;
-- Removing temporary table and create final VIEW structure
DROP TABLE IF EXISTS `v_notificacion`;
CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `v_notificacion` AS select `nt`.`notificacion_id` AS `notificacion_id`,`nt`.`usuario_origen_id` AS `usuario_origen_id`,`u1`.`nombre` AS `usuario_origen_nombre`,`u1`.`alias` AS `usuario_origen_alias`,`nt`.`usuario_destino_id` AS `usuario_destino_id`,`u2`.`nombre` AS `usuario_destino_nombre`,`u2`.`alias` AS `usuario_destino_alias`,`nt`.`publicacion_id` AS `publicacion_id`,`nt`.`tipo` AS `tipo`,`nt`.`destino_id` AS `destino_id`,`nt`.`fecha` AS `fecha`,`nt`.`visto` AS `visto` from ((`notificacion` `nt` join `usuario` `u1` on((`u1`.`usuario_id` = `nt`.`usuario_origen_id`))) join `usuario` `u2` on((`u2`.`usuario_id` = `nt`.`usuario_destino_id`)));

/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IF(@OLD_FOREIGN_KEY_CHECKS IS NULL, 1, @OLD_FOREIGN_KEY_CHECKS) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
