CREATE DATABASE  IF NOT EXISTS `vivenciar` /*!40100 DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci */ /*!80016 DEFAULT ENCRYPTION='N' */;
USE `vivenciar`;
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
-- Table structure for table `atendimento`
--

DROP TABLE IF EXISTS `atendimento`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `atendimento` (
  `id` int NOT NULL AUTO_INCREMENT,
  `clinica_id` int NOT NULL COMMENT 'Referência à clínica',
  `paciente_id` int NOT NULL COMMENT 'Referência ao paciente',
  `profissional_id` int NOT NULL COMMENT 'Referência ao profissional',
  `procedimento_id` int NOT NULL COMMENT 'Referência ao procedimento',
  `competencia` char(6) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Competência AAAAMM',
  `data_atendimento` date NOT NULL COMMENT 'Data real do atendimento',
  `quantidade` int NOT NULL DEFAULT '1' COMMENT 'Quantidade de procedimentos',
  `idade_paciente` int DEFAULT NULL COMMENT 'Idade do paciente no atendimento',
  `cid_10` char(4) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Código CID-10',
  `caracter_atendimento` char(2) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Característica do atendimento',
  `numero_autorizacao` char(13) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Número da autorização',
  `origem_informacao` char(3) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 'BPA' COMMENT 'Origem da informação (BPA, PNI, etc)',
  `ine_equipe` char(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Identificação Nacional de Equipe',
  `folha_bpa` char(3) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Número da folha BPA',
  `sequencia_bpa` char(2) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Sequência na folha BPA',
  `data_registro` timestamp NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Data do registro',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=30 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Tabela de atendimentos BPA-I';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `atendimento`
--

LOCK TABLES `atendimento` WRITE;
/*!40000 ALTER TABLE `atendimento` DISABLE KEYS */;
INSERT INTO `atendimento` VALUES (2,1,1,2,11,'202505','2025-03-27',1,2,NULL,NULL,NULL,'BPA',NULL,NULL,NULL,'2025-05-07 16:54:59'),(3,1,2,1,14,'202505','2025-03-31',1,2,NULL,NULL,NULL,'BPA',NULL,NULL,NULL,'2025-05-07 17:09:48'),(4,1,3,1,10,'202505','2025-03-31',1,0,NULL,NULL,NULL,'BPA',NULL,NULL,NULL,'2025-05-07 17:10:31'),(6,1,1,1,10,'202507','2025-07-02',1,2,'',NULL,NULL,'BPA',NULL,NULL,NULL,'2025-07-03 00:23:43'),(7,1,1,1,11,'202507','2025-07-02',1,2,'',NULL,NULL,'BPA',NULL,NULL,NULL,'2025-07-03 00:23:43'),(8,1,1,1,12,'202507','2025-07-02',1,2,'',NULL,NULL,'BPA',NULL,NULL,NULL,'2025-07-03 00:23:43'),(9,1,1,1,13,'202507','2025-07-02',1,2,'',NULL,NULL,'BPA',NULL,NULL,NULL,'2025-07-03 00:23:43'),(10,1,1,2,10,'202507','2025-07-02',1,2,'',NULL,NULL,'BPA',NULL,NULL,NULL,'2025-07-03 00:23:43'),(11,1,1,2,11,'202507','2025-07-02',1,2,'',NULL,NULL,'BPA',NULL,NULL,NULL,'2025-07-03 00:23:43'),(12,1,1,2,12,'202507','2025-07-02',1,2,'',NULL,NULL,'BPA',NULL,NULL,NULL,'2025-07-03 00:23:43'),(13,1,1,2,13,'202507','2025-07-02',1,2,'',NULL,NULL,'BPA',NULL,NULL,NULL,'2025-07-03 00:23:43'),(14,1,1,3,10,'202507','2025-07-02',1,2,'',NULL,NULL,'BPA',NULL,NULL,NULL,'2025-07-03 00:23:43'),(15,1,1,3,11,'202507','2025-07-02',1,2,'',NULL,NULL,'BPA',NULL,NULL,NULL,'2025-07-03 00:23:43'),(16,1,1,3,12,'202507','2025-07-02',1,2,'',NULL,NULL,'BPA',NULL,NULL,NULL,'2025-07-03 00:23:43'),(17,1,1,3,13,'202507','2025-07-02',1,2,'',NULL,NULL,'BPA',NULL,NULL,NULL,'2025-07-03 00:23:43'),(18,1,2,1,10,'202507','2025-07-02',1,2,'',NULL,NULL,'BPA',NULL,NULL,NULL,'2025-07-03 00:23:43'),(19,1,2,1,11,'202507','2025-07-02',1,2,'',NULL,NULL,'BPA',NULL,NULL,NULL,'2025-07-03 00:23:43'),(20,1,2,1,12,'202507','2025-07-02',1,2,'',NULL,NULL,'BPA',NULL,NULL,NULL,'2025-07-03 00:23:43'),(21,1,2,1,13,'202507','2025-07-02',1,2,'',NULL,NULL,'BPA',NULL,NULL,NULL,'2025-07-03 00:23:43'),(22,1,2,2,10,'202507','2025-07-02',1,2,'',NULL,NULL,'BPA',NULL,NULL,NULL,'2025-07-03 00:23:43'),(23,1,2,2,11,'202507','2025-07-02',1,2,'',NULL,NULL,'BPA',NULL,NULL,NULL,'2025-07-03 00:23:43'),(24,1,2,2,12,'202507','2025-07-02',1,2,'',NULL,NULL,'BPA',NULL,NULL,NULL,'2025-07-03 00:23:43'),(25,1,2,2,13,'202507','2025-07-02',1,2,'',NULL,NULL,'BPA',NULL,NULL,NULL,'2025-07-03 00:23:43'),(26,1,2,3,10,'202507','2025-07-02',1,2,'',NULL,NULL,'BPA',NULL,NULL,NULL,'2025-07-03 00:23:43'),(27,1,2,3,11,'202507','2025-07-02',1,2,'',NULL,NULL,'BPA',NULL,NULL,NULL,'2025-07-03 00:23:43'),(28,1,2,3,12,'202507','2025-07-02',1,2,'',NULL,NULL,'BPA',NULL,NULL,NULL,'2025-07-03 00:23:43'),(29,1,2,3,13,'202507','2025-07-02',1,2,'',NULL,NULL,'BPA',NULL,NULL,NULL,'2025-07-03 00:23:43');
/*!40000 ALTER TABLE `atendimento` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `auditoria_execucoes`
--

DROP TABLE IF EXISTS `auditoria_execucoes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `auditoria_execucoes` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nome_usuario` varchar(50) DEFAULT NULL,
  `data_hora` datetime DEFAULT NULL,
  `tipo_evento` varchar(20) DEFAULT NULL,
  `nome_tabela` varchar(50) DEFAULT NULL,
  `detalhes` text,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `auditoria_execucoes`
--

LOCK TABLES `auditoria_execucoes` WRITE;
/*!40000 ALTER TABLE `auditoria_execucoes` DISABLE KEYS */;
/*!40000 ALTER TABLE `auditoria_execucoes` ENABLE KEYS */;
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
  `atendimento_id` int DEFAULT NULL COMMENT 'Referência ao agendamento',
  `profissional_id` int NOT NULL COMMENT 'Profissional que realizou a evolução',
  `paciente_id` int NOT NULL,
  `data_registro` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `descricao` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Descrição da evolução',
  `observacao` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `assinatura_digital` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Hash da assinatura digital',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `evolucao_paciente`
--

LOCK TABLES `evolucao_paciente` WRITE;
/*!40000 ALTER TABLE `evolucao_paciente` DISABLE KEYS */;
INSERT INTO `evolucao_paciente` VALUES (1,NULL,3,6,'2025-09-16 23:57:23','teste','teste','teste');
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
-- Table structure for table `formulario`
--

DROP TABLE IF EXISTS `formulario`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `formulario` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nome` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Nome do formulário',
  `descricao` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci COMMENT 'Descrição do formulário',
  `especialidade` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Área de atendimento (fonoaudiologia, psicologia, etc)',
  `ativo` tinyint(1) DEFAULT '1' COMMENT 'Indica se o formulário está ativo',
  `data_criacao` timestamp NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Data de criação',
  `data_atualizacao` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT 'Data da última atualização',
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_nome_area` (`nome`,`especialidade`),
  KEY `idx_area_atendimento` (`especialidade`),
  KEY `idx_ativo` (`ativo`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Templates de formulários para evoluções';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `formulario`
--

LOCK TABLES `formulario` WRITE;
/*!40000 ALTER TABLE `formulario` DISABLE KEYS */;
INSERT INTO `formulario` VALUES (7,'teste','teste 1','FISIO',1,'2025-10-07 16:27:02','2025-10-07 16:27:02'),(8,'testeeeeeeeee','tertetewsdfsdf','FONO',1,'2025-10-07 21:31:16','2025-10-07 21:31:16');
/*!40000 ALTER TABLE `formulario` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `formulario_perguntas`
--

DROP TABLE IF EXISTS `formulario_perguntas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `formulario_perguntas` (
  `id` int NOT NULL AUTO_INCREMENT,
  `formulario_id` int NOT NULL COMMENT 'Referência ao formulário template',
  `nome_unico` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Nome único para identificação futura',
  `titulo` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Título da pergunta exibido ao usuário',
  `descricao` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci COMMENT 'Descrição/explicação da pergunta',
  `tipo_input` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `opcoes` json DEFAULT NULL,
  `obrigatorio` tinyint(1) DEFAULT '0' COMMENT 'Indica se a pergunta é obrigatória',
  `multipla_escolha` tinyint(1) DEFAULT '0' COMMENT 'Permite múltipla escolha (apenas para checkbox)',
  `tamanho_maximo` int DEFAULT NULL COMMENT 'Tamanho máximo para textos',
  `placeholder` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Texto de placeholder',
  `ativo` tinyint(1) DEFAULT '1' COMMENT 'Indica se a pergunta está ativa',
  `data_criacao` timestamp NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Data de criação',
  `data_atualizacao` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT 'Data da última atualização',
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_nome_unico_formulario` (`nome_unico`,`formulario_id`),
  KEY `fk_formulario_pergunta_template` (`formulario_id`),
  CONSTRAINT `fk_formulario_pergunta_template` FOREIGN KEY (`formulario_id`) REFERENCES `formulario` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Perguntas dos formulários';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `formulario_perguntas`
--

LOCK TABLES `formulario_perguntas` WRITE;
/*!40000 ALTER TABLE `formulario_perguntas` DISABLE KEYS */;
INSERT INTO `formulario_perguntas` VALUES (3,8,'teste2','teste2','teste2','radio','[\"1\", \"2\", \"3\"]',1,0,255,'',1,'2025-10-07 23:01:33','2025-10-07 23:01:33'),(4,8,'teste3','teste3','teste3','select','[\"perna\", \"braco\", \"mao\", \"boca\"]',0,0,255,'',1,'2025-10-08 01:43:44','2025-10-08 01:43:44'),(5,8,'teste4','teste4','teste4','checkbox','[\"1\", \"2\", \"3\", \"4\", \"5\", \"6\"]',1,0,255,'',1,'2025-10-08 01:55:40','2025-10-08 01:55:40'),(6,8,'teste5','teste5','teste5','checkbox','[\"teste5\", \"teste5\", \"teste5\"]',1,0,255,'',1,'2025-10-08 01:57:40','2025-10-08 01:57:40'),(7,8,'teste6','teste6','teste6','number',NULL,0,0,255,'teste6',1,'2025-10-08 02:58:22','2025-10-08 02:58:22'),(8,8,'teste7','teste7','teste7','file',NULL,1,0,255,'teste7',1,'2025-10-08 02:58:42','2025-10-08 02:58:42');
/*!40000 ALTER TABLE `formulario_perguntas` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `formulario_resposta`
--

DROP TABLE IF EXISTS `formulario_resposta`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `formulario_resposta` (
  `id` int NOT NULL AUTO_INCREMENT,
  `formulario_id` int NOT NULL COMMENT 'Referência ao formulário respondido',
  `paciente_id` int NOT NULL COMMENT 'Referência ao paciente',
  `profissional_id` int NOT NULL COMMENT 'Referência ao profissional',
  `data_resposta` timestamp NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Data da resposta',
  `status` enum('rascunho','enviado','arquivado') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 'enviado' COMMENT 'Status da resposta',
  PRIMARY KEY (`id`),
  KEY `fk_formulario_resposta_template` (`formulario_id`),
  KEY `fk_formulario_resposta_paciente` (`paciente_id`),
  KEY `fk_formulario_resposta_profissional` (`profissional_id`),
  CONSTRAINT `fk_formulario_resposta_paciente` FOREIGN KEY (`paciente_id`) REFERENCES `paciente` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_formulario_resposta_profissional` FOREIGN KEY (`profissional_id`) REFERENCES `profissional` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_formulario_resposta_template` FOREIGN KEY (`formulario_id`) REFERENCES `formulario` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Respostas dos formulários aplicados';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `formulario_resposta`
--

LOCK TABLES `formulario_resposta` WRITE;
/*!40000 ALTER TABLE `formulario_resposta` DISABLE KEYS */;
/*!40000 ALTER TABLE `formulario_resposta` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `formulario_resposta_pergunta`
--

DROP TABLE IF EXISTS `formulario_resposta_pergunta`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `formulario_resposta_pergunta` (
  `id` int NOT NULL AUTO_INCREMENT,
  `resposta_id` int NOT NULL COMMENT 'Referência à resposta do formulário',
  `pergunta_id` int NOT NULL COMMENT 'Referência à pergunta',
  `valor_texto` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci COMMENT 'Valor para textos',
  `valor_numerico` decimal(10,2) DEFAULT NULL COMMENT 'Valor para números',
  `valor_data` date DEFAULT NULL COMMENT 'Valor para datas',
  `valor_hora` time DEFAULT NULL COMMENT 'Valor para horas',
  `valor_opcoes` json DEFAULT NULL COMMENT 'Valores para seleções múltiplas (JSON)',
  `valor_anexo` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Caminho do arquivo anexado',
  PRIMARY KEY (`id`),
  KEY `fk_resposta_pergunta_resposta` (`resposta_id`),
  KEY `fk_resposta_pergunta_pergunta` (`pergunta_id`),
  CONSTRAINT `fk_resposta_pergunta_pergunta` FOREIGN KEY (`pergunta_id`) REFERENCES `formulario_perguntas` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_resposta_pergunta_resposta` FOREIGN KEY (`resposta_id`) REFERENCES `formulario_resposta` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Respostas individuais de cada pergunta';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `formulario_resposta_pergunta`
--

LOCK TABLES `formulario_resposta_pergunta` WRITE;
/*!40000 ALTER TABLE `formulario_resposta_pergunta` DISABLE KEYS */;
/*!40000 ALTER TABLE `formulario_resposta_pergunta` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `paciente`
--

DROP TABLE IF EXISTS `paciente`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `paciente` (
  `id` int NOT NULL AUTO_INCREMENT,
  `cns` char(15) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Cartão Nacional de Saúde (opcional se tiver CPF)',
  `nome` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Nome completo do paciente',
  `data_nascimento` date NOT NULL COMMENT 'Data de nascimento',
  `sexo` char(1) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Sexo (M/F)',
  `raca_cor` char(2) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Raça/cor (01-06 conforme tabela)',
  `etnia` char(4) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Código de etnia (se raça=05)',
  `nacionalidade` char(3) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Código de nacionalidade',
  `cep` char(8) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'CEP do endereço',
  `endereco` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Nome do logradouro',
  `numero` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Número do endereço',
  `complemento` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Complemento do endereço',
  `bairro` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Bairro',
  `municipio_ibge` char(6) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Código IBGE do município',
  `telefone` char(11) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Telefone com DDD',
  `email` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'E-mail',
  `cpf` char(11) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'CPF (opcional se tiver CNS)',
  `situacao_rua` char(1) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Situação de rua (S/N)',
  `data_cadastro` timestamp NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Data do cadastro',
  `data_atualizacao` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT 'Última atualização',
  `codigo_logradouro` varchar(45) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=35 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Tabela de pacientes';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `paciente`
--

LOCK TABLES `paciente` WRITE;
/*!40000 ALTER TABLE `paciente` DISABLE KEYS */;
INSERT INTO `paciente` VALUES (1,'706007805385045','ARTHUR PIETRO SANTOS FERREIRA','2023-01-09','M','03',NULL,'10','52111581','RUA ARAMBORE','204',NULL,'AGUA FRIA','261160','81988239244',NULL,NULL,'N','2025-05-05 20:41:59','2025-05-20 21:59:39','81'),(2,'700003140310409','AURORA CHELCEA SILVA CUNHA','2023-02-10','F','03',NULL,'10','51240040','RUA RIO XINGI','76',NULL,'IBURA','261160','81988100532',NULL,NULL,'N',NULL,'2025-05-20 21:59:39','81'),(3,'706808283294225','ANTHONY WALACE SILVA BOMFIM','2024-05-12','M','03',NULL,'10','50761417','RUA PEDRO BOULITREAU','92',NULL,'SAN MARTIN','261160','81997941823',NULL,NULL,'N',NULL,'2025-05-20 21:59:39','81'),(4,'707407038246877','ANTHONY MANOEL FERREIRA DA SILVA','2023-12-08','M','03',NULL,'10','50761675','RUA Dr. FLAVIO FERREIRA DA SILVA MAROJO','10',NULL,'TORROES','261160','81999243226',NULL,NULL,'N',NULL,'2025-05-20 21:59:39','81'),(5,'705008479818757','ANTHONY KALEB GALVÃO DOS SANTOS','2023-02-12','M','03',NULL,'10','50741400','RUA INACIO DE BARROS BARRETO','24',NULL,'VARZEA','261160','81992439737',NULL,NULL,'N',NULL,'2025-05-20 21:59:39','81'),(6,'704001845149367','ANTHONY LUIZ BRITO','2023-11-13','M','03',NULL,'10','50790116','MARAGOGIPE','119',NULL,'JARDIM SAO PAULO','261160','81999738498',NULL,NULL,'N',NULL,'2025-05-20 22:00:14','8'),(7,'706709577365814','BENJAMIM DOUGLAS GONCALVES DA SILVA','2024-12-01','M','03',NULL,'10','52121155','MARCILIO DIAS','922',NULL,'CAMPINA DO BARRETO','261160','81996637227',NULL,NULL,'N',NULL,'2025-05-20 22:00:13','8'),(8,'704005863920669','BRYAN GABRIEL FRANCISCO DA SILVA','2023-09-29','M','03',NULL,'10','52081070','RUA ALTO DO EUCALIPTO','413',NULL,'VASCO DA GAMA','261160','81997412530',NULL,NULL,'N',NULL,'2025-05-20 21:59:39','81'),(9,'704602601020522','BENICIO RAPHAEL DE LIMA NASCIMENTO','2023-11-15','M','03',NULL,'10','50760110','RUA JOSE MOREIRA REIS','694',NULL,'MUSTARDINHA','261160','81997057791',NULL,NULL,'N',NULL,'2025-05-20 21:59:39','81'),(10,'706305779195073','CRISTIAN FRANCISCO DA SILVA','2023-12-28','M','03',NULL,'10','52090475','RUA SÃO BENTO','1',NULL,'MACAXEIRA','261160','81995350392',NULL,NULL,'N',NULL,'2025-05-20 21:59:39','81'),(11,'705209482922771','DANIEL MATIAS DA SILVA ALBUQUERQUE','2023-12-03','M','03',NULL,'10','50780685','RUA RAUL FREIRE DE SOUZA','767',NULL,'AREIAS','261160','81986896376',NULL,NULL,'N',NULL,'2025-05-20 21:59:39','81'),(12,'898006357038573','ENZO DAVI SOARES DE SOUSA','2024-02-04','M','03',NULL,'10','52091130','RUA ALVARES FLORENSE','140',NULL,'CORREGO DO JENIPAPO','261160','81992120202',NULL,NULL,'N',NULL,'2025-05-20 21:59:39','81'),(13,'898006347500304','HEITOR MIGUEL BATISTA DA SILVA','2023-11-17','M','03',NULL,'10','50720160','RUA PANDIA CALOGERAS','110',NULL,'PRADO','261160','81986801520',NULL,NULL,'N',NULL,'2025-05-20 21:59:39','81'),(14,'704805082508342','ISAAC NOAH BARBOS','2024-05-25','M','03',NULL,'10','50980685','RUA ELIANE FRAGOSO DO NASCIMENTO','213',NULL,'NOVA MORADA','261160','81993345570',NULL,NULL,'N',NULL,'2025-05-20 21:59:39','81'),(15,'702003888042284','JHONATA HENRIQUE SANTANA DOS SANTOS','2023-11-26','M','01',NULL,'10','52171170','RUA DO SITIO SÃO BRAS','52',NULL,'SITIO DOS PINTOS','261160','81996939717',NULL,NULL,'N',NULL,'2025-05-20 21:59:39','81'),(16,'706005895666241','JHESSYE MARIA PEREIRA BARBOSA DA SILVA','2023-09-03','F','03',NULL,'10','51150020','RUA PROFESSORA ROSILDA COSTA','15',NULL,'IMBIRIBEIRA','261160','81985887391',NULL,NULL,'N',NULL,'2025-05-20 21:59:39','81'),(17,'898006329788342','LEOAN SAMUEL DOS SANTOS','2023-02-09','M','03',NULL,'10','52091071','RUA SÃO VALERIANO','39',NULL,'CORREGO DO JENIPAPO','261160','81994220081',NULL,NULL,'N',NULL,'2025-05-20 21:59:39','81'),(18,'706803729164526','LUIZA VALENTINA WANDERLEY DUARTE','2023-11-10','F','03',NULL,'10','51010380','RUA DOUTOR HENRIQUE LINS','164',NULL,'PINA','261160','81997873628',NULL,NULL,'N',NULL,'2025-05-20 21:59:39','81'),(19,'706506304537491','LUISA BEATRIZ SILVA SOARES','2023-09-09','F','03',NULL,'10','50630670','RUA BRASABANTE','3',NULL,'CORDEIRO','261160','81987941349',NULL,NULL,'N',NULL,'2025-05-20 21:59:39','81'),(20,'700800919436886','MARIA ACACIA ALVES LINS DE OLIVEIRA','2024-12-09','F','03',NULL,'10','50680520','RUA JANIRO PONTES','90',NULL,'IPUTINGA','261160','81950680520',NULL,NULL,'N',NULL,'2025-05-20 21:59:39','81'),(21,'706808202819624','MAYA KENIA FELIX SANTOS DA SILVA','2023-09-17','F','01',NULL,'10','51330230','RUA IBIRATINGA','22',NULL,'COHAB','261160','81993928902',NULL,NULL,'N',NULL,'2025-05-20 21:59:39','81'),(22,'700508740962555','MAITE BYANCA VALENTIN PEREIRA BARROS DA SILVA','2023-09-29','F','01',NULL,'10','52090785','RUA RANUSIA ALVES RODRIGUES','175',NULL,'MACAXEIRA','261160','81997776931',NULL,NULL,'N',NULL,'2025-05-20 21:59:39','81'),(23,'705009445573655','NICOLAS EZEQUIEL DA SILVA CHAVES','2024-03-18','M','03',NULL,'10','52211001','RUA AMERICA CISNEIROS','226',NULL,'ALTO SANTA TEREZINHA','261160','81983489280',NULL,NULL,'N',NULL,'2025-05-20 21:59:39','81'),(24,'701803258284879','PEDRO EMANOEL FELICIANI','2024-01-13','M','03',NULL,'10','50970010','RUA JOSE ALVES DO NASCIMENTO','126',NULL,'VARZEA','261160','81991671751',NULL,NULL,'N',NULL,'2025-05-20 21:59:39','81'),(25,'705009262480356','PIETRO BENICIO ALBUQUERQUE MARIANO','2023-11-11','M','03',NULL,'10','50960470','RUA LUIZ AUGUSTO RABELO','189',NULL,'VARZEA','261160','81996694614',NULL,NULL,'N',NULL,'2025-05-20 21:59:39','81'),(26,'700002087515101','RUBENS JOSE DE LIRA','2024-02-03','M','03',NULL,'10','52165050','RUA PERREIRA BARRETO','500',NULL,'PASSARINHO','261160','81986731602',NULL,NULL,'N',NULL,'2025-05-20 21:59:39','81'),(27,'701006829955095','RAFAEL FELIPE DANTAS GUIMARES','2023-09-06','M','03',NULL,'10','51290501','RUA GUARACIABA','96',NULL,'COHAB','261160','81985944217',NULL,NULL,'N',NULL,'2025-05-20 21:59:39','81'),(32,'701006812322123','teste da silva teste','2025-05-21','M','01','','10','50800200','teste teste','11',NULL,'varzea','261160','81988888888',NULL,NULL,'N','2025-05-21 17:04:29','2025-05-21 21:48:38','81'),(33,'700002359416809','FELIPE teste','2025-05-12','M','01','','10','50800000','testetetetet','123',NULL,'IMBIRIBEIRA','261160','8195073899',NULL,NULL,'N','2025-05-22 23:32:51','2025-05-22 23:32:51','8'),(34,'705008479818766','teste da paz','2025-09-08','M','01','test','10','50000000','teste','55','nadad','nadad',NULL,'85959595959','teste@teste.com',NULL,'N','2025-09-14 01:36:23','2025-09-14 01:36:23','81');
/*!40000 ALTER TABLE `paciente` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `procedimento`
--

DROP TABLE IF EXISTS `procedimento`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `procedimento` (
  `id` int NOT NULL AUTO_INCREMENT,
  `codigo` char(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Código do procedimento (Tabela SUS)',
  `descricao` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Descrição do procedimento',
  `especialidade` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Especialidade relacionada',
  `ativo` tinyint(1) DEFAULT '1' COMMENT 'Indica se procedimento está ativo',
  `servico` char(3) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Código do serviço',
  `classificacao` char(3) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Código da classificação',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=20 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Tabela de procedimentos ambulatoriais';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `procedimento`
--

LOCK TABLES `procedimento` WRITE;
/*!40000 ALTER TABLE `procedimento` DISABLE KEYS */;
INSERT INTO `procedimento` VALUES (10,'301070040','ACOMPANHAMENTO NEUROPSICOLÓGICO DE PACIENTE EM REABILITAÇÃO','TERAPIA OCUPACIONAL',1,'135','011'),(11,'301070075','ATENDIMENTO / ACOMPANHAMENTO DE PACIENTE EM REABILITACAO DO DESENVOLVIMENTO NEUROPSICOMOTOR','FISIOTERAPIA',1,'135','010'),(12,'301070024','ACOMPANHAMENTO DE PACIENTE EM REABILITACAO EM COMUNICACAO ALTERNATIVA','FISIOTERAPIA',1,'135','010'),(13,'301070048','CONSULTA DE PROFISSIONAIS DE NIVEL SUPERIOR NA ATENÇÃO ESPECIALIZADA (EXCETO MÉDICO)','FISIOTERAPIA',1,'135','010'),(14,'301070067','ATENDIMENTO / ACOMPANHAMENTO EM REABILITAÇÃO NAS MULTIPLAS DEFICIÊNCIAS','FISIOTERAPIA',1,'135','010'),(17,'301070113','TERAPIA FONOAUDIOLÓGICA INDIVIDUAL','FONOAUDIOLOGO',1,'135','010'),(19,'301070091','ATENDIMENTO EM OFICINA TERAPÊUTICA II EM GRUPO PARA PESSOAS COM DEFICIÊNCIA (POR OFICINA TERAPÊUTICA II)','PSICOLOGIA',1,'135','011');
/*!40000 ALTER TABLE `procedimento` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `profissional`
--

DROP TABLE IF EXISTS `profissional`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `profissional` (
  `id` int NOT NULL AUTO_INCREMENT,
  `cns` char(15) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'CNS do profissional',
  `nome` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Nome completo',
  `cbo` char(6) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Código CBO',
  `cpf` char(11) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'CPF',
  `especialidade` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Especialidade principal',
  `telefone` char(11) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Telefone com DDD',
  `email` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'E-mail',
  `data_cadastro` timestamp NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Data do cadastro',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Tabela de profissionais de saúde';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `profissional`
--

LOCK TABLES `profissional` WRITE;
/*!40000 ALTER TABLE `profissional` DISABLE KEYS */;
INSERT INTO `profissional` VALUES (1,'702501333549039','JULIANA GOMES DE OLIVEIRA','223905','06895245448','TERAPIA OCUPACIONAL','81997099231',NULL,'2025-05-05 20:41:59'),(2,'708706194080594','MARCELA RAQUEL DE OLIVEIRA LIMA','223605','02215040440','FISIOTERAPIA','81992923383',NULL,'2025-05-05 20:41:59'),(3,'898002394524962','JESSICA GOMES DA SILVA CORREIA','251510','07113087442','PSICOLOGIA','81985973406',NULL,'2025-05-05 20:41:59'),(4,'704807520359948','ROBERTA KARLIZE PEREIRA SILVA','223905','05251828454','FISIOTERAPIA','81996181228',NULL,'2025-05-05 20:53:00'),(5,'898005904155532','MERCIA MARIA TAVARES DE MELO','223810','53334248704','FONOAUDIOLOGIA','81999749144',NULL,'2025-05-05 20:53:47');
/*!40000 ALTER TABLE `profissional` ENABLE KEYS */;
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
  `senha_numero` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Ex: P001, C002',
  `data_geracao` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `status` enum('esperando','chamado','atendido','cancelado') COLLATE utf8mb4_unicode_ci DEFAULT 'esperando',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `totem_senha`
--

LOCK TABLES `totem_senha` WRITE;
/*!40000 ALTER TABLE `totem_senha` DISABLE KEYS */;
INSERT INTO `totem_senha` VALUES (1,1,'comum','689bf0727cd5a','2025-08-12 22:54:58','esperando'),(2,1,'comum','comum689bf0c2eb2ea','2025-08-12 22:56:18','esperando');
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
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2025-10-09  7:39:42
