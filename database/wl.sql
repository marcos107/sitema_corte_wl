-- MariaDB dump 10.19  Distrib 10.4.34-MariaDB, for debian-linux-gnu (x86_64)
--
-- Host: localhost    Database: wl
-- ------------------------------------------------------
-- Server version	10.4.34-MariaDB-1:10.4.34+maria~ubu2004

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `alteracoes`
--

DROP TABLE IF EXISTS `alteracoes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `alteracoes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `usuario_id` int(11) DEFAULT NULL,
  `item` varchar(255) DEFAULT NULL COMMENT 'Nome da tabela em que a alteração ocorreu',
  `id_item` varchar(255) DEFAULT NULL COMMENT 'ID do item modificado na tabela referida por "item"',
  `data_add` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `usuario_id` (`usuario_id`),
  CONSTRAINT `alteracoes_ibfk_1` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `alteracoes`
--


--
-- Table structure for table `alteracoes_detalhes`
--

DROP TABLE IF EXISTS `alteracoes_detalhes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `alteracoes_detalhes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `alteracao_id` int(11) DEFAULT NULL,
  `campo` varchar(255) DEFAULT NULL COMMENT 'Nome do campo alterado',
  `valor_antes` text DEFAULT NULL COMMENT 'Valor anterior do campo',
  `valor_depois` text DEFAULT NULL COMMENT 'Valor após a alteração',
  PRIMARY KEY (`id`),
  KEY `alteracao_id` (`alteracao_id`),
  CONSTRAINT `alteracoes_detalhes_ibfk_1` FOREIGN KEY (`alteracao_id`) REFERENCES `alteracoes` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `alteracoes_detalhes`
--


--
-- Table structure for table `corte`
--

DROP TABLE IF EXISTS `corte`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `corte` (
  `id` int(255) NOT NULL AUTO_INCREMENT,
  `usuario_id_ini` int(11) DEFAULT NULL COMMENT 'Id do usuario que começou',
  `usuario_id_fim` int(11) DEFAULT NULL COMMENT 'Id do usuario que finalizou',
  `data_add` datetime DEFAULT NULL,
  `status` varchar(255) DEFAULT NULL,
  `ip` varchar(255) DEFAULT NULL,
  `data_end` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `usuario_id_ini` (`usuario_id_ini`),
  KEY `usuario_id_fim` (`usuario_id_fim`),
  CONSTRAINT `corte_ibfk_1` FOREIGN KEY (`usuario_id_ini`) REFERENCES `usuarios` (`id`),
  CONSTRAINT `corte_ibfk_2` FOREIGN KEY (`usuario_id_fim`) REFERENCES `usuarios` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `corte`
--


--
-- Table structure for table `desenhos`
--

DROP TABLE IF EXISTS `desenhos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `desenhos` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `corte_id` int(11) DEFAULT NULL,
  `usuario_id_desenhista` int(11) DEFAULT NULL COMMENT 'Id do usuario que fez o desenho',
  `prioridade_id` int(11) DEFAULT NULL,
  `finalidade_id` int(11) DEFAULT NULL,
  `empreendimentos_id` int(11) DEFAULT NULL,
  `empresa_id` int(11) DEFAULT NULL,
  `processos_id` int(11) DEFAULT NULL,
  `nome` varchar(255) DEFAULT NULL COMMENT 'Nome do arquivo',
  `diretorio` varchar(1000) DEFAULT NULL,
  `status` varchar(255) DEFAULT NULL,
  `ordem` int(11) DEFAULT NULL,
  `data_add` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `corte_id` (`corte_id`),
  KEY `usuario_id_desenhista` (`usuario_id_desenhista`),
  KEY `finalidade_id` (`finalidade_id`),
  KEY `empreendimentos_id` (`empreendimentos_id`),
  KEY `empresa_id` (`empresa_id`),
  KEY `processos_id` (`processos_id`),
  CONSTRAINT `desenhos_ibfk_2` FOREIGN KEY (`usuario_id_desenhista`) REFERENCES `usuarios` (`id`),
  CONSTRAINT `desenhos_ibfk_3` FOREIGN KEY (`finalidade_id`) REFERENCES `finalidade` (`id`),
  CONSTRAINT `desenhos_ibfk_4` FOREIGN KEY (`empreendimentos_id`) REFERENCES `empreendimentos` (`id`),
  CONSTRAINT `desenhos_ibfk_5` FOREIGN KEY (`empresa_id`) REFERENCES `empresa` (`id`),
  CONSTRAINT `desenhos_ibfk_6` FOREIGN KEY (`processos_id`) REFERENCES `processos` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `desenhos`
--


--
-- Table structure for table `desenhos_subpasta`
--

DROP TABLE IF EXISTS `desenhos_subpasta`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `desenhos_subpasta` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `desenho_id` int(11) DEFAULT NULL,
  `tag_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `desenho_id` (`desenho_id`),
  KEY `tag_id` (`tag_id`),
  CONSTRAINT `desenhos_subpasta_ibfk_1` FOREIGN KEY (`desenho_id`) REFERENCES `desenhos` (`id`),
  CONSTRAINT `desenhos_subpasta_ibfk_2` FOREIGN KEY (`tag_id`) REFERENCES `subpasta` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `desenhos_subpasta`
--


--
-- Table structure for table `desenhos_temp`
--

DROP TABLE IF EXISTS `desenhos_temp`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `desenhos_temp` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `usuario_id` int(11) DEFAULT NULL,
  `diretorio` varchar(1000) DEFAULT NULL,
  `status` varchar(255) DEFAULT NULL,
  `data_add` datetime DEFAULT NULL,
  `data_end` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `usuario_id` (`usuario_id`),
  CONSTRAINT `desenhos_temp_ibfk_2` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `desenhos_temp`
--


--
-- Table structure for table `empreendimentos`
--

DROP TABLE IF EXISTS `empreendimentos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `empreendimentos` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `empresa_id` int(11) DEFAULT NULL,
  `usuario_id` int(11) DEFAULT NULL,
  `nome` varchar(255) DEFAULT NULL,
  `status` varchar(255) DEFAULT NULL,
  `data_add` datetime DEFAULT NULL,
  `escala` varchar(15) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `empresa_id` (`empresa_id`),
  KEY `usuario_id` (`usuario_id`),
  CONSTRAINT `empreendimentos_ibfk_1` FOREIGN KEY (`empresa_id`) REFERENCES `empresa` (`id`),
  CONSTRAINT `empreendimentos_ibfk_2` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `empreendimentos`
--


--
-- Table structure for table `empresa`
--

DROP TABLE IF EXISTS `empresa`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `empresa` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `usuario_id` int(11) DEFAULT NULL,
  `nome` varchar(255) DEFAULT NULL,
  `status` varchar(255) DEFAULT NULL,
  `data_add` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `usuario_id` (`usuario_id`),
  CONSTRAINT `empresa_ibfk_1` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `empresa`
--


--
-- Table structure for table `filtros`
--

DROP TABLE IF EXISTS `filtros`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `filtros` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `usuario_id` int(11) DEFAULT NULL,
  `nome` varchar(255) DEFAULT NULL,
  `status` varchar(255) DEFAULT NULL,
  `data_add` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `usuario_id` (`usuario_id`),
  CONSTRAINT `filtros_ibfk_1` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `filtros`
--


--
-- Table structure for table `finalidade`
--

DROP TABLE IF EXISTS `finalidade`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `finalidade` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `usuario_id` int(11) DEFAULT NULL,
  `nome` varchar(255) DEFAULT NULL,
  `status` varchar(255) DEFAULT NULL,
  `data_add` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `usuario_id` (`usuario_id`),
  CONSTRAINT `finalidade_ibfk_1` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `finalidade`
--


--
-- Table structure for table `lixo_desenhos`
--

DROP TABLE IF EXISTS `lixo_desenhos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `lixo_desenhos` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `desenho_id` int(11) DEFAULT NULL,
  `usuario_id` int(11) DEFAULT NULL,
  `diretorio` varchar(1000) DEFAULT NULL,
  `data_add` datetime DEFAULT NULL,
  `nome` varchar(255) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `desenho_id` (`desenho_id`),
  KEY `usuario_id` (`usuario_id`),
  CONSTRAINT `lixo_desenhos_ibfk_1` FOREIGN KEY (`desenho_id`) REFERENCES `desenhos` (`id`),
  CONSTRAINT `lixo_desenhos_ibfk_2` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `lixo_desenhos`
--


--
-- Table structure for table `log_requisicoes`
--

DROP TABLE IF EXISTS `log_requisicoes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `log_requisicoes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `usuario_id` int(11) DEFAULT NULL,
  `acao` varchar(255) DEFAULT NULL,
  `metodo` varchar(10) DEFAULT NULL COMMENT 'Método HTTP usado: GET, POST, PUT, DELETE',
  `status_execucao` varchar(50) DEFAULT NULL COMMENT 'Status da execução: sucesso, erro, pendente, etc.',
  `mensagem` text DEFAULT NULL COMMENT 'Mensagem opcional retornada pela execução ou erro detalhado',
  `ip_origem` varchar(100) DEFAULT NULL COMMENT 'IP do usuário que fez a requisição',
  `data_add` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='Log de requisições e ações feitas por usuários no sistema.';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `log_requisicoes`
--


--
-- Table structure for table `nivel`
--

DROP TABLE IF EXISTS `nivel`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `nivel` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `usuario_id` int(11) DEFAULT NULL,
  `nome` varchar(100) DEFAULT NULL,
  `status` varchar(255) DEFAULT NULL,
  `relatorio` tinyint(1) DEFAULT NULL,
  `data_add` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `usuario_id` (`usuario_id`),
  CONSTRAINT `nivel_ibfk_1` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `nivel`
--


--
-- Table structure for table `nivel_permissoes`
--

DROP TABLE IF EXISTS `nivel_permissoes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `nivel_permissoes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `usuario_id` int(11) DEFAULT NULL,
  `nivel_id` int(11) DEFAULT NULL,
  `permissao` varchar(255) DEFAULT NULL,
  `status` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `usuario_id` (`usuario_id`),
  KEY `nivel_id` (`nivel_id`),
  CONSTRAINT `nivel_permissoes_ibfk_1` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`),
  CONSTRAINT `nivel_permissoes_ibfk_2` FOREIGN KEY (`nivel_id`) REFERENCES `nivel` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='faço como para quando tiver alteração apago o item';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `nivel_permissoes`
--


--
-- Table structure for table `nivel_processos`
--

DROP TABLE IF EXISTS `nivel_processos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `nivel_processos` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nivel_id` int(11) DEFAULT NULL,
  `processo_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `nivel_id` (`nivel_id`),
  KEY `processo_id` (`processo_id`),
  CONSTRAINT `nivel_processos_ibfk_1` FOREIGN KEY (`nivel_id`) REFERENCES `nivel` (`id`),
  CONSTRAINT `nivel_processos_ibfk_2` FOREIGN KEY (`processo_id`) REFERENCES `processos` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='faço como para quando tiver alteração apago o item';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `nivel_processos`
--


--
-- Table structure for table `ordem`
--

DROP TABLE IF EXISTS `ordem`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ordem` (
  `id` int(255) NOT NULL AUTO_INCREMENT,
  `desenho_id` int(255) DEFAULT NULL,
  `projeto_id` int(255) DEFAULT NULL,
  `prioridade_id` int(255) NOT NULL,
  `ordem` int(255) NOT NULL,
  `status` varchar(255) NOT NULL,
  `data_add` datetime NOT NULL,
  `processos_id` int(255) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `ordem_ibfk_1` (`desenho_id`),
  KEY `ordem_ibfk_2` (`projeto_id`),
  KEY `ordem_ibfk_3` (`prioridade_id`),
  CONSTRAINT `ordem_ibfk_1` FOREIGN KEY (`desenho_id`) REFERENCES `desenhos` (`id`),
  CONSTRAINT `ordem_ibfk_2` FOREIGN KEY (`projeto_id`) REFERENCES `projeto` (`id`),
  CONSTRAINT `ordem_ibfk_3` FOREIGN KEY (`prioridade_id`) REFERENCES `prioridade` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ordem`
--


--
-- Table structure for table `prioridade`
--

DROP TABLE IF EXISTS `prioridade`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `prioridade` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `usuario_id` int(11) DEFAULT NULL,
  `nome` varchar(255) DEFAULT NULL,
  `status` varchar(255) DEFAULT NULL,
  `cor` varchar(20) DEFAULT NULL,
  `ordem` int(11) DEFAULT NULL,
  `data_add` datetime DEFAULT NULL,
  `periodo` int(4) DEFAULT NULL COMMENT 'periodod que ficara ativo',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `prioridade`
--


--
-- Table structure for table `processos`
--

DROP TABLE IF EXISTS `processos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `processos` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `usuario_id` int(11) DEFAULT NULL,
  `filtros_id` int(11) DEFAULT NULL,
  `nome` varchar(255) DEFAULT NULL,
  `status` varchar(255) DEFAULT NULL,
  `diretorio` varchar(255) DEFAULT NULL,
  `data_add` datetime DEFAULT NULL,
  `input` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `processos`
--


--
-- Table structure for table `processos_filtro`
--

DROP TABLE IF EXISTS `processos_filtro`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `processos_filtro` (
  `id` int(255) NOT NULL AUTO_INCREMENT,
  `usuario_id` int(255) NOT NULL,
  `processos_id` int(255) NOT NULL,
  `filtros_id` int(255) DEFAULT NULL,
  `data_add` datetime NOT NULL,
  `status` varchar(255) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_processos_filtro_filtros_id` (`filtros_id`),
  KEY `fk_processos_filtro_processos_id` (`processos_id`),
  KEY `fk_processos_filtro_usuario_id` (`usuario_id`),
  CONSTRAINT `fk_processos_filtro_filtros_id` FOREIGN KEY (`filtros_id`) REFERENCES `filtros` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `fk_processos_filtro_processos_id` FOREIGN KEY (`processos_id`) REFERENCES `processos` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_processos_filtro_usuario_id` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `processos_filtro`
--


--
-- Table structure for table `projeto`
--

DROP TABLE IF EXISTS `projeto`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `projeto` (
  `id` int(255) NOT NULL AUTO_INCREMENT,
  `usuario_id` int(255) NOT NULL,
  `diretorio` varchar(255) NOT NULL,
  `descricao` varchar(255) NOT NULL,
  `status` varchar(255) NOT NULL,
  `data_add` datetime NOT NULL,
  `input` varchar(255) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_projeto_usuario` (`usuario_id`),
  CONSTRAINT `fk_projeto_usuario` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `projeto`
--


--
-- Table structure for table `projeto_desenho`
--

DROP TABLE IF EXISTS `projeto_desenho`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `projeto_desenho` (
  `id` int(255) NOT NULL AUTO_INCREMENT,
  `usuario_id` int(255) NOT NULL,
  `desenho_id` int(255) NOT NULL,
  `projeto_id` int(255) NOT NULL,
  `data_add` datetime NOT NULL,
  `marcador` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `projeto_desenho`
--


--
-- Table structure for table `recolocar_desenho`
--

DROP TABLE IF EXISTS `recolocar_desenho`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `recolocar_desenho` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `usuario_id_pedido` int(255) DEFAULT NULL,
  `usuario_id_confirmado` int(255) DEFAULT NULL,
  `recolocar_desenho_id_anterior` int(255) DEFAULT NULL,
  `desenhos_id` int(255) DEFAULT NULL,
  `status` varchar(255) DEFAULT NULL,
  `quantidade` int(11) DEFAULT NULL,
  `data_add` datetime DEFAULT NULL,
  `data_end` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `recolocar_desenho_ibfk_1` (`usuario_id_pedido`),
  KEY `recolocar_desenho_ibfk_2` (`usuario_id_confirmado`),
  KEY `recolocar_desenho_ibfk_3` (`recolocar_desenho_id_anterior`),
  KEY `recolocar_desenho_ibfk_4` (`desenhos_id`),
  CONSTRAINT `recolocar_desenho_ibfk_1` FOREIGN KEY (`usuario_id_pedido`) REFERENCES `usuarios` (`id`),
  CONSTRAINT `recolocar_desenho_ibfk_2` FOREIGN KEY (`usuario_id_confirmado`) REFERENCES `usuarios` (`id`),
  CONSTRAINT `recolocar_desenho_ibfk_3` FOREIGN KEY (`recolocar_desenho_id_anterior`) REFERENCES `recolocar_desenho` (`id`),
  CONSTRAINT `recolocar_desenho_ibfk_4` FOREIGN KEY (`desenhos_id`) REFERENCES `desenhos` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `recolocar_desenho`
--


--
-- Table structure for table `subpasta`
--

DROP TABLE IF EXISTS `subpasta`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `subpasta` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `usuario_id` int(11) DEFAULT NULL,
  `empreendimentos_id` int(11) DEFAULT NULL,
  `finalidade_id` int(11) DEFAULT NULL,
  `nome` varchar(100) DEFAULT NULL,
  `status` varchar(255) DEFAULT NULL,
  `data_add` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `subpasta`
--


--
-- Table structure for table `usuarios`
--

DROP TABLE IF EXISTS `usuarios`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `usuarios` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `usuario_id` int(11) DEFAULT NULL,
  `nivel_id` int(11) DEFAULT NULL,
  `nome` varchar(255) DEFAULT NULL,
  `senha` varchar(255) DEFAULT NULL,
  `status` varchar(255) DEFAULT NULL,
  `whatsapp` varchar(255) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `data_add` datetime DEFAULT NULL,
  `acesso_remoto` int(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `usuarios`
--


--
-- Table structure for table `violacao`
--

DROP TABLE IF EXISTS `violacao`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `violacao` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `usuario_id` int(11) DEFAULT NULL,
  `causa` varchar(255) DEFAULT NULL,
  `data_add` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `violacao`
--

/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-08-03 20:55:21
