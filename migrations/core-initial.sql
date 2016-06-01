-- phpMyAdmin SQL Dump
-- version 4.0.10deb1
-- http://www.phpmyadmin.net
--
-- Servidor: localhost
-- Tiempo de generación: 15-01-2016 a las 16:32:58
-- Versión del servidor: 5.5.43-0ubuntu0.14.04.1
-- Versión de PHP: 5.5.9-1ubuntu4.9

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Base de datos: `cvapi`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `api_client`
--

CREATE TABLE IF NOT EXISTS `api_client` (
  `client_id` varchar(45) NOT NULL,
  `client_secret` varchar(45) NOT NULL,
  `email` varchar(200) NOT NULL,
  `user_id` varchar(15) NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  `enabled` tinyint(1) NOT NULL,
  `asoc` tinyint(1) NOT NULL,
  PRIMARY KEY (`client_id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `api_client_scope`
--

CREATE TABLE IF NOT EXISTS `api_client_scope` (
  `id_scope` varchar(45) NOT NULL,
  `id_client` varchar(45) NOT NULL,
  PRIMARY KEY (`id_scope`,`id_client`),
  KEY `id_client` (`id_client`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `api_field_type`
--

CREATE TABLE IF NOT EXISTS `api_field_type` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(75) NOT NULL,
  `regex` varchar(800) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=11 ;

--
-- Volcado de datos para la tabla `api_field_type`
--

INSERT INTO `api_field_type` (`id`, `name`, `regex`) VALUES
(1, 'string', '/^.{1,1500}$/'),
(2, 'integer', '/^[0-9]+$/'),
(3, 'float', '!\\d+(?:\\.\\d+)?!'),
(4, 'email', '/[-0-9a-zA-Z.+_]+@[-0-9a-zA-Z.+_]+\\.[a-zA-Z]{2,4}/'),
(5, 'password', '/^([0-9A-Za-z@.]{4,14})$/'),
(6, 'url', '#((http|https|ftp)://(\\S*?\\.\\S*?))(\\s|\\;|\\)|\\]|\\[|\\{|\\}|,|\\"|''|:|\\<|$|\\.\\s)#ie'),
(9, 'MD5', '/^[a-f0-9]{32}$/i'),
(10, 'username', '/^[a-z0-9_-]{3,16}$/');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `api_form`
--

CREATE TABLE IF NOT EXISTS `api_form` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `endpoint` varchar(50) NOT NULL,
  `field` varchar(75) NOT NULL,
  `id_type` int(11) NOT NULL,
  `sample` varchar(350) NOT NULL,
  `internal` tinyint(1) NOT NULL,
  `required` tinyint(1) NOT NULL,
  `scopes` varchar(500) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `id_type` (`id_type`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Volcado de datos para la tabla `api_field_type`
--

INSERT INTO `api_form` (`id`, `endpoint`, `field`, `id_type`, `sample`, `internal`, `required`, `scopes`) VALUES
(42, 'login', 'username', 1, '', 0, 1, ''),
(43, 'login', 'password', 1, '', 0, 1, '');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `api_token`
--

CREATE TABLE IF NOT EXISTS `api_token` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `token` varchar(128) NOT NULL,
  `created_at` datetime NOT NULL,
  `expires` int(11) NOT NULL,
  `enabled` tinyint(1) NOT NULL,
  `client_id` varchar(45) NOT NULL,
  `updated_at` datetime NOT NULL,
  `scopes` varchar(250) NOT NULL,
  `timestamp` varchar(128) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `client_id` (`client_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `scope`
--

CREATE TABLE IF NOT EXISTS `scope` (
  `name` varchar(45) NOT NULL,
  `level` int(11) NOT NULL,
  `priority` int(11) NOT NULL,
  PRIMARY KEY (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `user`
--

CREATE TABLE IF NOT EXISTS `user` (
  `username` varchar(15) NOT NULL,
  `name` varchar(45) NOT NULL,
  `lastname` varchar(45) DEFAULT NULL,
  `email` varchar(70) NOT NULL,
  `fbid` varchar(100) DEFAULT NULL,
  `googleid` varchar(100) DEFAULT NULL,
  `avatar` varchar(120) DEFAULT NULL,
  `phone` varchar(32) NOT NULL,
  `password` varchar(40) NOT NULL,
  `enabled` tinyint(1) NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  `type` int(11) NOT NULL,
  PRIMARY KEY (`username`),
  KEY `user_ibfk_1` (`type`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `user_type`
--

CREATE TABLE IF NOT EXISTS `user_type` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(32) NOT NULL,
  `priority` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `api_user_asoc`
--

CREATE TABLE IF NOT EXISTS `api_user_asoc` (
  `client_id` varchar(45) NOT NULL,
  `username` varchar(15) NOT NULL,
  PRIMARY KEY (`client_id`,`username`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `api_client`
--
ALTER TABLE `api_client`
  ADD CONSTRAINT `api_users_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`username`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `api_client_scope`
--
ALTER TABLE `api_client_scope`
  ADD CONSTRAINT `api_client_scope_ibfk_2` FOREIGN KEY (`id_client`) REFERENCES `api_client` (`client_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `api_client_scope_ibfk_1` FOREIGN KEY (`id_scope`) REFERENCES `scope` (`name`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `api_form`
--
ALTER TABLE `api_form`
  ADD CONSTRAINT `api_form_ibfk_1` FOREIGN KEY (`id_type`) REFERENCES `api_field_type` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `api_token`
--
ALTER TABLE `api_token`
  ADD CONSTRAINT `api_tokens_ibfk_1` FOREIGN KEY (`client_id`) REFERENCES `api_client` (`client_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `user`
--
ALTER TABLE `user`
  ADD CONSTRAINT `user_ibfk_1` FOREIGN KEY (`type`) REFERENCES `user_type` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

  --
  -- Filtros para la tabla `api_user_asoc`
  --

ALTER TABLE  `api_user_asoc`
  ADD CONSTRAINT `api_user_asoc_ibfk_2` FOREIGN KEY (`client_id`) REFERENCES  `api_client` (`client_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `api_user_asoc_ibfk_1` FOREIGN KEY (`username`) REFERENCES  `user` (`username`) ON DELETE CASCADE ON UPDATE CASCADE;



/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
