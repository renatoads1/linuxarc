-- --------------------------------------------------------
-- Servidor:                     192.168.15.50
-- Versão do servidor:           5.5.60-0+deb8u1 - (Debian)
-- OS do Servidor:               debian-linux-gnu
-- HeidiSQL Versão:              9.4.0.5125
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!50503 SET NAMES utf8mb4 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;


-- Copiando estrutura do banco de dados para 3a_bancos
CREATE DATABASE IF NOT EXISTS `3a_bancos` /*!40100 DEFAULT CHARACTER SET latin1 */;
USE `3a_bancos`;

-- Copiando estrutura para tabela 3a_bancos.bancodedados
CREATE TABLE IF NOT EXISTS `bancodedados` (
  `idbancos` int(11) NOT NULL AUTO_INCREMENT,
  `razao_social` varchar(70) DEFAULT NULL,
  `nomefantasia` varchar(70) DEFAULT NULL,
  `banco` varchar(50) DEFAULT NULL,
  `usuario_banco` varchar(50) DEFAULT NULL,
  `senha_banco` varchar(30) DEFAULT NULL,
  `porta` varchar(6) DEFAULT NULL,
  `ipservidor` varchar(80) DEFAULT NULL,
  `cnpj` varchar(14) DEFAULT NULL,
  `inscricao` varchar(14) DEFAULT NULL,
  PRIMARY KEY (`idbancos`),
  UNIQUE KEY `banco_usuario_senha_porta_ipservidor` (`banco`,`usuario_banco`,`senha_banco`,`porta`,`ipservidor`),
  UNIQUE KEY `cnpj` (`cnpj`)
) ENGINE=MyISAM AUTO_INCREMENT=19 DEFAULT CHARSET=latin1;

-- Copiando dados para a tabela 3a_bancos.bancodedados: 3 rows
/*!40000 ALTER TABLE `bancodedados` DISABLE KEYS */;
INSERT INTO `bancodedados` (`idbancos`, `razao_social`, `nomefantasia`, `banco`, `usuario_banco`, `senha_banco`, `porta`, `ipservidor`, `cnpj`, `inscricao`) VALUES
	(16, '3A EMPREENDIMENTOS IMOBILIARIOS LTDA', '3A', 'doc_3a', 'nave', 'nave', '3306', '192.168.15.50', '252525252525', '0000000000'),
	(17, 'SOCIEDADE COMERCIAL CANAA', 'POSTO JOCKEY', 'doc_canaa', 'nave', 'nave', '3306', '192.168.15.50', '2525252525', '06259111010061'),
	(18, 'AGR ADMINISTRACAO EIRELI - ME', 'AGR', 'doc_agr', 'nave', 'nave', '3306', '192.168.15.50', '0101010101010', '0101010101010');
/*!40000 ALTER TABLE `bancodedados` ENABLE KEYS */;

-- Copiando estrutura para tabela 3a_bancos.departamento
CREATE TABLE IF NOT EXISTS `departamento` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `departamento` varchar(50) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=23 DEFAULT CHARSET=latin1;

-- Copiando dados para a tabela 3a_bancos.departamento: 2 rows
/*!40000 ALTER TABLE `departamento` DISABLE KEYS */;
INSERT INTO `departamento` (`id`, `departamento`) VALUES
	(3, 'financeiro'),
	(4, 'vendas');
/*!40000 ALTER TABLE `departamento` ENABLE KEYS */;

-- Copiando estrutura para tabela 3a_bancos.rel_banco_usuario
CREATE TABLE IF NOT EXISTS `rel_banco_usuario` (
  `idbancos` int(11) NOT NULL,
  `idusuario_ub` int(11) NOT NULL,
  PRIMARY KEY (`idbancos`,`idusuario_ub`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- Copiando dados para a tabela 3a_bancos.rel_banco_usuario: 18 rows
/*!40000 ALTER TABLE `rel_banco_usuario` DISABLE KEYS */;
INSERT INTO `rel_banco_usuario` (`idbancos`, `idusuario_ub`) VALUES
	(16, 41),
	(16, 53),
	(16, 54),
	(16, 68),
	(16, 70),
	(16, 74),
	(16, 75),
	(16, 76),
	(17, 41),
	(17, 53),
	(17, 54),
	(17, 70),
	(17, 75),
	(18, 41),
	(18, 53),
	(18, 54),
	(18, 70),
	(18, 75);
/*!40000 ALTER TABLE `rel_banco_usuario` ENABLE KEYS */;

-- Copiando estrutura para tabela 3a_bancos.usuario
CREATE TABLE IF NOT EXISTS `usuario` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nome` varchar(60) NOT NULL DEFAULT '0',
  `usuario` varchar(15) NOT NULL DEFAULT '0',
  `senha` varchar(32) NOT NULL DEFAULT '0',
  `documento_i` char(1) NOT NULL DEFAULT 'N',
  `documento_a` char(1) NOT NULL DEFAULT 'N',
  `documento_e` char(1) NOT NULL DEFAULT 'N',
  `documento_c` char(1) NOT NULL DEFAULT 'N',
  `cvf_i` char(1) NOT NULL DEFAULT 'N',
  `cvf_a` char(1) NOT NULL DEFAULT 'N',
  `cvf_e` char(1) NOT NULL DEFAULT 'N',
  `cvf_c` char(1) NOT NULL DEFAULT 'N',
  `usuario_i` char(1) NOT NULL DEFAULT 'N',
  `usuario_a` char(1) NOT NULL DEFAULT 'N',
  `usuario_c` char(1) NOT NULL DEFAULT 'N',
  `usuario_e` char(1) NOT NULL DEFAULT 'N',
  `modelo_i` char(1) NOT NULL DEFAULT 'N',
  `modelo_a` char(1) NOT NULL DEFAULT 'N',
  `modelo_c` char(1) NOT NULL DEFAULT 'N',
  `modelo_e` char(1) NOT NULL DEFAULT 'N',
  `contador` char(1) NOT NULL DEFAULT 'N',
  PRIMARY KEY (`id`),
  UNIQUE KEY `usuario` (`usuario`),
  UNIQUE KEY `senha` (`senha`)
) ENGINE=MyISAM AUTO_INCREMENT=77 DEFAULT CHARSET=latin1;

-- Copiando dados para a tabela 3a_bancos.usuario: 8 rows
/*!40000 ALTER TABLE `usuario` DISABLE KEYS */;
INSERT INTO `usuario` (`id`, `nome`, `usuario`, `senha`, `documento_i`, `documento_a`, `documento_e`, `documento_c`, `cvf_i`, `cvf_a`, `cvf_e`, `cvf_c`, `usuario_i`, `usuario_a`, `usuario_c`, `usuario_e`, `modelo_i`, `modelo_a`, `modelo_c`, `modelo_e`, `contador`) VALUES
	(41, 'ESDRAS', 'esdras', '0cc175b9c0f1b6a831c399e269772661', 'S', 'S', 'S', 'S', 'S', 'S', 'S', 'S', 'S', 'S', 'S', 'S', 'S', 'S', 'S', 'S', 'N'),
	(76, 'LILIAN', 'LILIAN', 'c6a32d57283f29d6c7d6bd03825802ec', 'S', 'N', 'N', 'S', 'S', 'N', 'N', 'S', 'S', 'S', 'S', 'S', 'N', 'N', 'S', 'N', 'N'),
	(74, 'WILLIANS', 'WILLIANS', '98d90e9ae77c889bddb9ace191a042f5', 'S', 'S', 'S', 'S', 'S', 'S', 'S', 'S', 'S', 'S', 'S', 'S', 'S', 'S', 'S', 'S', 'N'),
	(53, 'AGOSTINHA', 'Agostinha', 'e10adc3949ba59abbe56e057f20f883e', 'S', 'S', 'S', 'S', 'S', 'S', 'S', 'S', 'S', 'S', 'S', 'S', 'S', 'S', 'S', 'S', 'N'),
	(54, 'PAULO', 'paulo', '81dc9bdb52d04dc20036dbd8313ed055', 'S', 'S', 'S', 'S', 'S', 'S', 'S', 'S', 'S', 'S', 'S', 'S', 'S', 'S', 'S', 'S', 'N'),
	(75, 'FRANCISCO', 'FRANCISCO', '817ede84f7b2b74d2d12389390f5870e', 'S', 'S', 'S', 'S', 'S', 'S', 'S', 'S', 'S', 'S', 'S', 'S', 'S', 'S', 'S', 'S', 'N'),
	(68, 'DESIREE', 'DESIREE', '7850af4558ceae085f5c5a199649f66d', 'S', 'S', 'N', 'S', 'S', 'S', 'S', 'S', 'S', 'S', 'S', 'S', 'S', 'S', 'S', 'S', 'N'),
	(70, 'FABIANA', 'fabiana', '40c93ccb88c55f3a924591d7c0d91b98', 'N', 'S', 'N', 'S', 'N', 'N', 'N', 'N', 'S', 'S', 'S', 'S', 'N', 'N', 'N', 'N', 'N');
/*!40000 ALTER TABLE `usuario` ENABLE KEYS */;

/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IF(@OLD_FOREIGN_KEY_CHECKS IS NULL, 1, @OLD_FOREIGN_KEY_CHECKS) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
