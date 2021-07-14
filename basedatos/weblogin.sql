-- phpMyAdmin SQL Dump
-- version 4.8.5
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 14-07-2021 a las 05:23:38
-- Versión del servidor: 10.1.40-MariaDB
-- Versión de PHP: 7.1.29

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `weblogin`
--

DELIMITER $$
--
-- Procedimientos
--
CREATE DEFINER=`root`@`localhost` PROCEDURE `pa_admMantenimientoUsuario` (`_xFlag` CHAR(2), `_xPersona` CHAR(8), `_xCadena` VARCHAR(15), `_xTexto` VARCHAR(40))  BEGIN

	IF _xFlag='1'  THEN
		BEGIN
			SET @Cant=0;
			SELECT COUNT(*) INTO @Cant FROM mae_persona WHERE persona = _xPersona ;
			IF  @Cant=0 THEN
				BEGIN
					SELECT '0' AS result;
				END;
			ELSE
				BEGIN
				
					UPDATE mae_persona 
                    SET 
						pass = MD5(_xTexto), 
                        cambioclave = '1'
					WHERE persona = _xPersona;
					
					SELECT '1' AS result;
				END;
			END IF;
		END;	
	END IF;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `pa_confClaveMantenimiento` (`_xFlag` CHAR(1), `_xRol` CHAR(4), `_xUsuario` VARCHAR(15))  BEGIN
	IF _xFlag = '1' THEN 
		SELECT cambioclave FROM mae_persona 
        WHERE persona = _xUsuario;	
	END IF;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `pa_consultarValorLdap` (`_xFlag` CHAR(1), `_xParametro` CHAR(10))  BEGIN
	if _xFlag='1' then
		set @valor='0';
		SELECT valor into @valor FROM mae_parametro WHERE parametro = _xParametro AND valor = '1';
		select @valor as valor;
	end if;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `pa_loginConsultarMisDatosPersonales` (IN `_xFlag` CHAR(1), IN `_xPerfil` CHAR(6), IN `_xPersona` CHAR(8))  BEGIN
    IF _xFlag = '1' THEN
		SELECT
			p.accesodatosper AS adp,
			p.accesocambiopwd AS pwd
		FROM men_rol p
		WHERE
			p.rol = _xPerfil;
	END IF;	
    
    
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `pa_loginValidarUsuario` (`_xFlag` CHAR(2), `_xUsuario` VARCHAR(20), `_xClave` VARCHAR(40), `_xTipousuario` CHAR(1))  BEGIN

	IF _xFlag='1' THEN
	
		SET @xExiste='';
		SELECT COUNT(*) INTO @xExiste FROM mae_persona WHERE usuario = _xUsuario AND pass = MD5(_xClave);
		
		IF @xExiste > 0 THEN
			SELECT '1' AS result;
		ELSE
			UPDATE mae_persona 
            SET 
				pass = MD5(_xClave)
			WHERE usuario = _xUsuario;	
            
			SELECT '1' AS result;
		END IF;	
	END IF;
    
    IF _xFlag = '2' THEN -- validar user

		##SELECT
			##count(*)			
		##FROM		
		##(
			SELECT 
				persona,
                numerodocumento AS nrodoc, 
                nombrecompleto 
			FROM mae_persona AS m 
			WHERE 
				usuario = _xUsuario AND 
                ##m.
                pass = MD5(_xClave) AND 
                ##m.
                activo = '1';
		##) AS p, per_trabajador AS q
		##WHERE p.persona=q.persona;
		
	END IF;
    
    IF _xFlag = '3' THEN

		##IF _tipousuario = '2' THEN -- administrativo
			SELECT
				##_tipousuario AS tipousuario,
				p.persona,
				nrodoc,
				nombrecompleto,
				nrodoc AS codigo,
				q.trabajador AS auxiliar
			FROM (
				SELECT 
					persona, 
                    numerodocumento AS nrodoc, 
                    nombrecompleto 
				FROM mae_persona AS m
				WHERE m.usuario = _xUsuario
			) AS p, per_trabajador AS q
			WHERE p.persona = q.persona;
		##END IF;
	END IF;
    
    
    IF _xFlag = '4' THEN
		SELECT 
			pr.rol,
            r.nombre AS nomrol
		FROM men_personarol pr
		INNER JOIN men_rol r ON r.rol=pr.rol
		WHERE pr.persona = _xUsuario
		ORDER BY r.nombre limit 1;
    END IF;

END$$

DELIMITER ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `mae_parametro`
--

CREATE TABLE `mae_parametro` (
  `idparametro` int(11) NOT NULL,
  `parametro` char(10) COLLATE utf8_spanish2_ci DEFAULT NULL,
  `descripcion` varchar(100) COLLATE utf8_spanish2_ci DEFAULT NULL,
  `valor` varchar(100) COLLATE utf8_spanish2_ci DEFAULT NULL,
  `activo` tinyint(4) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish2_ci;

--
-- Volcado de datos para la tabla `mae_parametro`
--

INSERT INTO `mae_parametro` (`idparametro`, `parametro`, `descripcion`, `valor`, `activo`) VALUES
(1, 'LDAPLGN', 'Activar login con LDAP', '0', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `mae_persona`
--

CREATE TABLE `mae_persona` (
  `persona` char(8) COLLATE utf8_spanish2_ci NOT NULL,
  `apellidopaterno` varchar(100) COLLATE utf8_spanish2_ci DEFAULT NULL,
  `apellidomaterno` varchar(100) COLLATE utf8_spanish2_ci DEFAULT NULL,
  `primernombre` varchar(100) COLLATE utf8_spanish2_ci DEFAULT NULL,
  `segundonombre` varchar(100) COLLATE utf8_spanish2_ci DEFAULT NULL,
  `nombrecompleto` varchar(100) COLLATE utf8_spanish2_ci DEFAULT NULL,
  `direccion` varchar(300) COLLATE utf8_spanish2_ci DEFAULT NULL,
  `email` varchar(100) COLLATE utf8_spanish2_ci DEFAULT NULL,
  `celular` char(50) COLLATE utf8_spanish2_ci DEFAULT NULL,
  `numerodocumento` char(15) COLLATE utf8_spanish2_ci DEFAULT NULL,
  `usuario` varchar(30) COLLATE utf8_spanish2_ci DEFAULT NULL,
  `pass` char(40) COLLATE utf8_spanish2_ci DEFAULT NULL,
  `activo` tinyint(4) DEFAULT '1',
  `cambioclave` char(1) COLLATE utf8_spanish2_ci DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish2_ci;

--
-- Volcado de datos para la tabla `mae_persona`
--

INSERT INTO `mae_persona` (`persona`, `apellidopaterno`, `apellidomaterno`, `primernombre`, `segundonombre`, `nombrecompleto`, `direccion`, `email`, `celular`, `numerodocumento`, `usuario`, `pass`, `activo`, `cambioclave`) VALUES
('00000001', 'PANTA', 'GARCIA', 'IRVIN', 'POOL', 'IRVIN POOL PANTA GARCIA', NULL, 'irvinpanta96@gmal.com', '928412415', '10101010', '10101010', 'a66abb5684c45962d887564f08346e8d', 1, '1');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `men_personarol`
--

CREATE TABLE `men_personarol` (
  `rol` char(4) COLLATE utf8_spanish2_ci DEFAULT NULL,
  `persona` char(8) COLLATE utf8_spanish2_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish2_ci;

--
-- Volcado de datos para la tabla `men_personarol`
--

INSERT INTO `men_personarol` (`rol`, `persona`) VALUES
('0001', '00000001');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `men_rol`
--

CREATE TABLE `men_rol` (
  `rol` char(4) COLLATE utf8_spanish2_ci NOT NULL,
  `nombre` varchar(100) COLLATE utf8_spanish2_ci DEFAULT NULL,
  `activo` tinyint(4) DEFAULT '1',
  `accesodatosper` char(1) COLLATE utf8_spanish2_ci DEFAULT '0',
  `accesocambiopwd` char(1) COLLATE utf8_spanish2_ci DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish2_ci;

--
-- Volcado de datos para la tabla `men_rol`
--

INSERT INTO `men_rol` (`rol`, `nombre`, `activo`, `accesodatosper`, `accesocambiopwd`) VALUES
('0001', 'Administrador', 1, '1', '1');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `per_trabajador`
--

CREATE TABLE `per_trabajador` (
  `trabajador` char(6) COLLATE utf8_spanish2_ci NOT NULL,
  `persona` char(8) COLLATE utf8_spanish2_ci DEFAULT NULL,
  `activo` tinyint(4) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish2_ci;

--
-- Volcado de datos para la tabla `per_trabajador`
--

INSERT INTO `per_trabajador` (`trabajador`, `persona`, `activo`) VALUES
('000001', '00000001', 1);

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `mae_parametro`
--
ALTER TABLE `mae_parametro`
  ADD PRIMARY KEY (`idparametro`);

--
-- Indices de la tabla `mae_persona`
--
ALTER TABLE `mae_persona`
  ADD PRIMARY KEY (`persona`),
  ADD UNIQUE KEY `numerodocumento` (`numerodocumento`);

--
-- Indices de la tabla `men_personarol`
--
ALTER TABLE `men_personarol`
  ADD KEY `personarol_rol` (`rol`),
  ADD KEY `personarol_persona` (`persona`);

--
-- Indices de la tabla `men_rol`
--
ALTER TABLE `men_rol`
  ADD PRIMARY KEY (`rol`);

--
-- Indices de la tabla `per_trabajador`
--
ALTER TABLE `per_trabajador`
  ADD PRIMARY KEY (`trabajador`),
  ADD KEY `per_trabajador_mae_persona` (`persona`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `mae_parametro`
--
ALTER TABLE `mae_parametro`
  MODIFY `idparametro` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `men_personarol`
--
ALTER TABLE `men_personarol`
  ADD CONSTRAINT `personarol_persona` FOREIGN KEY (`persona`) REFERENCES `mae_persona` (`persona`),
  ADD CONSTRAINT `personarol_rol` FOREIGN KEY (`rol`) REFERENCES `men_rol` (`rol`);

--
-- Filtros para la tabla `per_trabajador`
--
ALTER TABLE `per_trabajador`
  ADD CONSTRAINT `per_trabajador_mae_persona` FOREIGN KEY (`persona`) REFERENCES `mae_persona` (`persona`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
