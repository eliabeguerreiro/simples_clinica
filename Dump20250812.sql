-- MySQL dump 10.13  Distrib 8.0.40, for Win64 (x86_64)
--
-- Host: 127.0.0.1    Database: vivenciar
-- ------------------------------------------------------
-- Server version	8.0.40

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!50503 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `agendamento`
--

DROP TABLE IF EXISTS `agendamento`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `agendamento` (
  `id` int NOT NULL AUTO_INCREMENT,
  `fila_triagem_id` int NOT NULL COMMENT 'Referência à triagem',
  `paciente_id` int NOT NULL,
  `profissional_id` int NOT NULL,
  `clinica_id` int NOT NULL,
  `data_agendamento` date NOT NULL COMMENT 'Data escolhida para o atendimento',
  `hora_agendamento` time NOT NULL COMMENT 'Horário específico',
  `data_cadastro` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `status` enum('agendado','em_atendimento','atendido','faltou','cancelado') COLLATE utf8mb4_unicode_ci DEFAULT 'agendado',
  `prioridade` enum('normal','urgente','prioritario') COLLATE utf8mb4_unicode_ci DEFAULT 'normal',
  `agendamentocol` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `agendamento`
--

LOCK TABLES `agendamento` WRITE;
/*!40000 ALTER TABLE `agendamento` DISABLE KEYS */;
/*!40000 ALTER TABLE `agendamento` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `clinica`
--

DROP TABLE IF EXISTS `clinica`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `clinica` (
  `id` int NOT NULL AUTO_INCREMENT,
  `cnes` char(7) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Código CNES do estabelecimento',
  `nome` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Nome da clínica',
  `cnpj` char(14) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'CNPJ da clínica',
  `endereco` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Endereço completo',
  `telefone` char(11) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Telefone com DDD',
  `email` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'E-mail de contato',
  `municipio_ibge` char(6) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Código IBGE do município',
  `tipo_orgao_destino` char(1) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 'E' COMMENT 'E-Estadual, M-Municipal',
  `versao_sistema` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT '1.0.0' COMMENT 'Versão do sistema usado no BPA',
  `secretaria_saude` varchar(40) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 'SECRETARIA ESTADUAL DE SAUDE' COMMENT 'Nome da secretaria',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Tabela de clínicas/estabelecimentos de saúde';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `clinica`
--

LOCK TABLES `clinica` WRITE;
/*!40000 ALTER TABLE `clinica` DISABLE KEYS */;
INSERT INTO `clinica` VALUES (1,'4019601','VIVENCIAR ESPACO TERAPEUTICO SMS','46445526000186','RUA GENERAL JOAQUIM INACIO, Nº187 ILHA DO LEITE RECIFE','81995073899','ESPAÇOVIVENCIAR.PE@GMAIL.COM','261160','M','1.0.0','SECRETARIA DE SAUDE DO RECIFE');
/*!40000 ALTER TABLE `clinica` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `evolucao_paciente`
--

DROP TABLE IF EXISTS `evolucao_paciente`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `evolucao_paciente` (
  `id` int NOT NULL AUTO_INCREMENT,
  `agendamento_id` int NOT NULL COMMENT 'Referência ao agendamento',
  `profissional_id` int NOT NULL COMMENT 'Profissional que realizou a evolução',
  `paciente_id` int NOT NULL,
  `data_registro` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `descricao` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Descrição da evolução',
  `observacao` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `assinatura_digital` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Hash da assinatura digital',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `evolucao_paciente`
--

LOCK TABLES `evolucao_paciente` WRITE;
/*!40000 ALTER TABLE `evolucao_paciente` DISABLE KEYS */;
/*!40000 ALTER TABLE `evolucao_paciente` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `fila_triagem`
--

DROP TABLE IF EXISTS `fila_triagem`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `fila_triagem` (
  `id` int NOT NULL AUTO_INCREMENT,
  `paciente_id` int NOT NULL,
  `totem_senha_id` int NOT NULL,
  `clinica_id` int NOT NULL,
  `tipo_atendimento` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Ex: clínico geral, psicólogo, etc',
  `data_registro` datetime DEFAULT CURRENT_TIMESTAMP,
  `status` enum('triagem','agendado','cancelado') COLLATE utf8mb4_unicode_ci DEFAULT 'triagem',
  `observacao` text COLLATE utf8mb4_unicode_ci,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `fila_triagem`
--

LOCK TABLES `fila_triagem` WRITE;
/*!40000 ALTER TABLE `fila_triagem` DISABLE KEYS */;
/*!40000 ALTER TABLE `fila_triagem` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `totem_senha`
--

DROP TABLE IF EXISTS `totem_senha`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `totem_senha` (
  `id` int NOT NULL AUTO_INCREMENT,
  `clinica_id` int NOT NULL COMMENT 'Clínica onde a senha foi gerada',
  `tipo_senha` enum('comum','preferencial') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'comum',
  `senha_numero` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Ex: P001, C002',
  `data_geracao` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `status` enum('esperando','chamado','atendido','cancelado') COLLATE utf8mb4_unicode_ci DEFAULT 'esperando',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `totem_senha`
--

LOCK TABLES `totem_senha` WRITE;
/*!40000 ALTER TABLE `totem_senha` DISABLE KEYS */;
/*!40000 ALTER TABLE `totem_senha` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `usuarios`
--

DROP TABLE IF EXISTS `usuarios`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `usuarios` (
  `id` int NOT NULL AUTO_INCREMENT,
  `cpf` varchar(45) NOT NULL,
  `login` varchar(60) NOT NULL,
  `senha` varchar(300) NOT NULL,
  `nm_usuario` varchar(45) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `usuarios`
--

LOCK TABLES `usuarios` WRITE;
/*!40000 ALTER TABLE `usuarios` DISABLE KEYS */;
INSERT INTO `usuarios` VALUES (1,'12297095414','hants','$2y$10$NS706aAYeNqG6.bB1TBVh.RSbM5PHFj.Gfl5qj.GMidNeWU7qNHrO','eliabe paz'),(10,'12345678910','vivenciar','$2y$10$NS706aAYeNqG6.bB1TBVh.RSbM5PHFj.Gfl5qj.GMidNeWU7qNHrO','vivenciar');
/*!40000 ALTER TABLE `usuarios` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping routines for database 'vivenciar'
--
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2025-08-12 21:45:05
