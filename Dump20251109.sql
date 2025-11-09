
DROP TABLE IF EXISTS `atendimento`;
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
INSERT INTO `atendimento` VALUES (2,1,1,2,11,'202505','2025-03-27',1,2,NULL,NULL,NULL,'BPA',NULL,NULL,NULL,'2025-05-07 16:54:59'),(3,1,2,1,14,'202505','2025-03-31',1,2,NULL,NULL,NULL,'BPA',NULL,NULL,NULL,'2025-05-07 17:09:48'),(4,1,3,1,10,'202505','2025-03-31',1,0,NULL,NULL,NULL,'BPA',NULL,NULL,NULL,'2025-05-07 17:10:31'),(6,1,1,1,10,'202507','2025-07-02',1,2,'',NULL,NULL,'BPA',NULL,NULL,NULL,'2025-07-03 00:23:43'),(7,1,1,1,11,'202507','2025-07-02',1,2,'',NULL,NULL,'BPA',NULL,NULL,NULL,'2025-07-03 00:23:43'),(8,1,1,1,12,'202507','2025-07-02',1,2,'',NULL,NULL,'BPA',NULL,NULL,NULL,'2025-07-03 00:23:43'),(9,1,1,1,13,'202507','2025-07-02',1,2,'',NULL,NULL,'BPA',NULL,NULL,NULL,'2025-07-03 00:23:43'),(10,1,1,2,10,'202507','2025-07-02',1,2,'',NULL,NULL,'BPA',NULL,NULL,NULL,'2025-07-03 00:23:43'),(11,1,1,2,11,'202507','2025-07-02',1,2,'',NULL,NULL,'BPA',NULL,NULL,NULL,'2025-07-03 00:23:43'),(12,1,1,2,12,'202507','2025-07-02',1,2,'',NULL,NULL,'BPA',NULL,NULL,NULL,'2025-07-03 00:23:43'),(13,1,1,2,13,'202507','2025-07-02',1,2,'',NULL,NULL,'BPA',NULL,NULL,NULL,'2025-07-03 00:23:43'),(14,1,1,3,10,'202507','2025-07-02',1,2,'',NULL,NULL,'BPA',NULL,NULL,NULL,'2025-07-03 00:23:43'),(15,1,1,3,11,'202507','2025-07-02',1,2,'',NULL,NULL,'BPA',NULL,NULL,NULL,'2025-07-03 00:23:43'),(16,1,1,3,12,'202507','2025-07-02',1,2,'',NULL,NULL,'BPA',NULL,NULL,NULL,'2025-07-03 00:23:43'),(17,1,1,3,13,'202507','2025-07-02',1,2,'',NULL,NULL,'BPA',NULL,NULL,NULL,'2025-07-03 00:23:43'),(18,1,2,1,10,'202507','2025-07-02',1,2,'',NULL,NULL,'BPA',NULL,NULL,NULL,'2025-07-03 00:23:43'),(19,1,2,1,11,'202507','2025-07-02',1,2,'',NULL,NULL,'BPA',NULL,NULL,NULL,'2025-07-03 00:23:43'),(20,1,2,1,12,'202507','2025-07-02',1,2,'',NULL,NULL,'BPA',NULL,NULL,NULL,'2025-07-03 00:23:43'),(21,1,2,1,13,'202507','2025-07-02',1,2,'',NULL,NULL,'BPA',NULL,NULL,NULL,'2025-07-03 00:23:43'),(22,1,2,2,10,'202507','2025-07-02',1,2,'',NULL,NULL,'BPA',NULL,NULL,NULL,'2025-07-03 00:23:43'),(23,1,2,2,11,'202507','2025-07-02',1,2,'',NULL,NULL,'BPA',NULL,NULL,NULL,'2025-07-03 00:23:43'),(24,1,2,2,12,'202507','2025-07-02',1,2,'',NULL,NULL,'BPA',NULL,NULL,NULL,'2025-07-03 00:23:43'),(25,1,2,2,13,'202507','2025-07-02',1,2,'',NULL,NULL,'BPA',NULL,NULL,NULL,'2025-07-03 00:23:43'),(26,1,2,3,10,'202507','2025-07-02',1,2,'',NULL,NULL,'BPA',NULL,NULL,NULL,'2025-07-03 00:23:43'),(27,1,2,3,11,'202507','2025-07-02',1,2,'',NULL,NULL,'BPA',NULL,NULL,NULL,'2025-07-03 00:23:43'),(28,1,2,3,12,'202507','2025-07-02',1,2,'',NULL,NULL,'BPA',NULL,NULL,NULL,'2025-07-03 00:23:43'),(29,1,2,3,13,'202507','2025-07-02',1,2,'',NULL,NULL,'BPA',NULL,NULL,NULL,'2025-07-03 00:23:43');


DROP TABLE IF EXISTS `clinica`;
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
INSERT INTO `clinica` VALUES (1,'4019601','VIVENCIAR ESPACO TERAPEUTICO SMS','46445526000186','RUA GENERAL JOAQUIM INACIO, Nº187 ILHA DO LEITE RECIFE','81995073899','ESPAÇOVIVENCIAR.PE@GMAIL.COM','261160','M','1.0.0','SECRETARIA DE SAUDE DO RECIFE');



DROP TABLE IF EXISTS `evolucao_arquivos`;
CREATE TABLE `evolucao_arquivos` (
  `id` int NOT NULL AUTO_INCREMENT,
  `evolucao_id` int NOT NULL,
  `campo` varchar(100) NOT NULL,
  `nome_salvo` varchar(255) NOT NULL,
  `nome_original` varchar(255) DEFAULT NULL,
  `mime` varchar(100) DEFAULT NULL,
  `tamanho` int DEFAULT NULL,
  `caminho_relativo` varchar(255) DEFAULT NULL,
  `criado_em` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `evolucao_id` (`evolucao_id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
INSERT INTO `evolucao_arquivos` VALUES (1,1,'novo_teste','form11_pac5_evo1_novo_teste_08f270e43e88.jpg','159c0f7037c7c6e682579398bb84868f6ca63186_hq.jpg','image/jpeg',47145,'anexo/11/1/form11_pac5_evo1_novo_teste_08f270e43e88.jpg','2025-11-09 16:12:28'),(2,1,'143easdase3','form11_pac5_evo1_143easdase3_8ef5d196f0a4.webp','475883163_18060199753945041_4632143025216350835_n.webp','image/webp',242040,'anexo/11/1/form11_pac5_evo1_143easdase3_8ef5d196f0a4.webp','2025-11-09 16:12:28'),(3,2,'adicione_fotos_aqui','form10_pac5_evo2_adicione_fotos_aqui_411d6a06bc75.png','vivenciar_logov2.png','image/png',446802,'anexo/10/2/form10_pac5_evo2_adicione_fotos_aqui_411d6a06bc75.png','2025-11-09 16:12:53'),(4,3,'novo_teste','form11_pac6_evo3_novo_teste_dc4e484ba950.xlsx','evolucao_5.xlsx','application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',6595,'anexo/11/3/form11_pac6_evo3_novo_teste_dc4e484ba950.xlsx','2025-11-09 16:16:32'),(5,3,'143easdase3','form11_pac6_evo3_143easdase3_6d6e46ed4d7a.jpg','159c0f7037c7c6e682579398bb84868f6ca63186_hq.jpg','image/jpeg',47145,'anexo/11/3/form11_pac6_evo3_143easdase3_6d6e46ed4d7a.jpg','2025-11-09 16:16:32');


DROP TABLE IF EXISTS `evolucao_clinica`;
CREATE TABLE `evolucao_clinica` (
  `id` int NOT NULL AUTO_INCREMENT,
  `formulario_id` int NOT NULL,
  `paciente_id` int NOT NULL,
  `atendimento_id` int DEFAULT NULL,
  `data_referencia` date DEFAULT NULL,
  `data_hora` datetime DEFAULT CURRENT_TIMESTAMP,
  `dados` json NOT NULL,
  `observacoes` text,
  `criado_por` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_formulario` (`formulario_id`),
  KEY `idx_paciente` (`paciente_id`),
  KEY `idx_atendimento` (`atendimento_id`),
  KEY `idx_data_referencia` (`data_referencia`),
  KEY `idx_data_hora` (`data_hora`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
INSERT INTO `evolucao_clinica` VALUES (1,11,5,NULL,NULL,'2025-11-09 16:12:28','{\"novo\": \"teste\", \"wfsddasdasd\": \"teste\"}','testetet','eliabe paz'),(2,10,5,NULL,NULL,'2025-11-09 16:12:53','{\"teste\": [\"sim\"], \"asdasd\": {\"1\": \"1\", \"2\": \"4\", \"3\": \"3\"}, \"teste1\": \"acha que sim\", \"teste_2\": \"nao\", \"testetet\": \"asdfdsvsdfv\", \"teste_2_justificativa\": \"aaaaas\"}','asdsfddfas','eliabe paz'),(3,11,6,NULL,NULL,'2025-11-09 16:16:32','{\"novo\": \"asadgac\", \"wfsddasdasd\": \"dasdasdasd\"}','asdrasdasd','eliabe paz');


DROP TABLE IF EXISTS `formulario`;
CREATE TABLE `formulario` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nome` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Nome do formulário',
  `descricao` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci COMMENT 'Descrição do formulário',
  `especialidade` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Área de atendimento (fonoaudiologia, psicologia, etc)',
  `s_n_anexo` char(1) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'N',
  `ativo` tinyint(1) DEFAULT '1' COMMENT 'Indica se o formulário está ativo',
  `data_criacao` timestamp NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Data de criação',
  `data_atualizacao` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT 'Data da última atualização',
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_nome_area` (`nome`,`especialidade`),
  KEY `idx_area_atendimento` (`especialidade`),
  KEY `idx_ativo` (`ativo`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Templates de formulários para evoluções';
INSERT INTO `formulario` VALUES (7,'teste','teste 1','FISIO','N',1,'2025-10-07 16:27:02','2025-10-07 16:27:02'),(8,'testeeeeeeeee','tertetewsdfsdf','FONO','N',1,'2025-10-07 21:31:16','2025-10-07 21:31:16'),(9,'teste5','opa','FONO','N',1,'2025-10-09 20:09:05','2025-10-09 20:09:05'),(10,'evolução ao vivo','teste','FONO','N',1,'2025-10-22 13:43:15','2025-10-22 13:43:15'),(11,'09-11_teste','0911','TEOC','S',1,'2025-11-09 17:05:30','2025-11-09 17:05:30');


DROP TABLE IF EXISTS `formulario_perguntas`;
CREATE TABLE `formulario_perguntas` (
  `id` int NOT NULL AUTO_INCREMENT,
  `formulario_id` int NOT NULL COMMENT 'Referência ao formulário template',
  `nome_unico` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Nome único para identificação futura',
  `titulo` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Título da pergunta exibido ao usuário',
  `descricao` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci COMMENT 'Descrição/explicação da pergunta',
  `tipo_input` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
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
) ENGINE=InnoDB AUTO_INCREMENT=48 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Perguntas dos formulários';
INSERT INTO `formulario_perguntas` VALUES (3,8,'teste2','teste2','teste2','radio','[\"1\", \"2\", \"3\"]',1,0,255,'',1,'2025-10-07 23:01:33','2025-10-07 23:01:33'),(4,8,'teste3','teste3','teste3','select','[\"perna\", \"braco\", \"mao\", \"boca\"]',0,0,255,'',1,'2025-10-08 01:43:44','2025-10-08 01:43:44'),(5,8,'teste4','teste4','teste4','checkbox','[\"1\", \"2\", \"3\", \"4\", \"5\", \"6\"]',1,0,255,'',1,'2025-10-08 01:55:40','2025-10-08 01:55:40'),(6,8,'teste5','teste5','teste5','checkbox','[\"teste5\", \"teste5\", \"teste5\"]',1,0,255,'',1,'2025-10-08 01:57:40','2025-10-08 01:57:40'),(7,8,'teste6','teste6','teste6','number',NULL,0,0,255,'teste6',1,'2025-10-08 02:58:22','2025-10-08 02:58:22'),(8,8,'teste7','teste7','teste7','file',NULL,1,0,255,'teste7',1,'2025-10-08 02:58:42','2025-10-08 02:58:42'),(9,9,'opa','teste5','apo','texto',NULL,1,0,255,'eita',1,'2025-10-09 20:09:29','2025-10-09 20:09:29'),(10,7,'h_d','H.D','','texto',NULL,1,0,100,'',1,'2025-10-14 15:40:13','2025-10-14 15:40:13'),(11,7,'medica____es','Medicações','','texto',NULL,1,0,255,'',1,'2025-10-14 15:40:30','2025-10-14 15:40:30'),(12,7,'prematuridade','Prematuridade','','radio','[\"SIM\", \"NÃO\"]',1,0,255,'',1,'2025-10-14 15:41:07','2025-10-14 15:41:07'),(13,7,'tipo_do_parto','Tipo do Parto','','radio','[\"vaginal\", \"cesáreo\"]',1,0,255,'',1,'2025-10-14 15:42:37','2025-10-14 15:42:37'),(14,7,'ig','IG','','texto',NULL,1,0,50,'',1,'2025-10-14 15:42:58','2025-10-14 15:42:58'),(15,7,'pn','PN','','texto',NULL,1,0,55,'',1,'2025-10-14 15:43:10','2025-10-14 15:43:10'),(16,7,'apgar_1','APGAR 1','','texto',NULL,1,0,55,'',1,'2025-10-14 15:43:24','2025-10-14 15:43:24'),(17,7,'apgar_5','APGAR 5','','texto',NULL,1,0,55,'',1,'2025-10-14 15:43:35','2025-10-14 15:43:35'),(18,7,'intecorrencias','Intecorrencias','','radio','[\"gestação\", \"parto\"]',1,0,255,'',1,'2025-10-14 15:44:42','2025-10-14 15:44:42'),(19,7,'descri____o_das_intercorr__ncias','Descrição das Intercorrências','Prencher apenas se houveram intecorrencias','texto',NULL,0,0,255,'Prencher apenas se houveram intecorrencias',1,'2025-10-14 15:45:36','2025-10-14 15:45:36'),(20,7,'internamento_em_uti_neonatal_','Internamento em UTI neonatal:','','radio','[\"SIM\", \"NÃO\"]',1,0,255,'',1,'2025-10-14 15:46:51','2025-10-14 15:46:51'),(21,7,'internamento_em_uti_neonatal','Internamento em UTI neonatal','','texto',NULL,0,0,255,'',1,'2025-10-14 15:47:18','2025-10-14 15:47:18'),(23,7,'aspectos_relacionados____alimenta____o','Aspectos relacionados à alimentação','','texto',NULL,0,0,255,'',1,'2025-10-14 15:48:31','2025-10-14 15:48:31'),(24,7,'brincar','Brincar','','texto',NULL,0,0,255,'',1,'2025-10-14 15:48:54','2025-10-14 15:48:54'),(25,7,'queixa_principal','Queixa principal','','texto',NULL,1,0,255,'',1,'2025-10-14 15:49:02','2025-10-14 15:49:02'),(26,7,'teste','teste','','tabela','{\"linhas\": [\"moro\", \"sucção\", \"gag\", \"liberação de vias aéreas\", \"plantar\", \"flexão palmar\", \"rtca\", \"marcha\"], \"colunas\": [\"sim\", \"não\"]}',1,0,255,'',1,'2025-10-14 16:20:29','2025-10-14 16:20:29'),(27,8,'teste','teste','teste','sim_nao_justificativa','{\"condicao\": \"sim\", \"placeholder\": \"teste\"}',1,0,255,'',1,'2025-10-14 23:35:38','2025-10-14 23:35:38'),(28,7,'teste_ao_vivo','teste ao vivo','teste ao vivo','textarea',NULL,0,0,255,'teste ao vivo',1,'2025-10-22 13:36:00','2025-10-22 13:36:00'),(30,7,'sdf','ttttdrsdfsdf','sdf','sim_nao_justificativa','{\"condicao\": \"sim\", \"placeholder\": \"sdfsdf\"}',1,0,255,'',1,'2025-10-22 13:39:11','2025-10-22 13:39:11'),(31,10,'teste1','teste1','teste','radio','[\"sim\", \"não\", \"acha que sim\", \"acha que não\"]',1,0,255,'',1,'2025-10-22 13:43:49','2025-10-22 13:43:49'),(32,10,'teste_2','teste 2','teste','sim_nao_justificativa','{\"condicao\": \"nao\", \"placeholder\": \"Justifique caso não\"}',1,0,255,'',1,'2025-10-22 13:44:52','2025-10-22 13:44:52'),(33,10,'adicione_fotos_aqui','adicione fotos aqui','','file',NULL,1,0,255,'',1,'2025-10-22 13:45:17','2025-10-22 13:45:17'),(35,10,'teste','teste','','checkbox','[\"sim\", \"não\"]',1,1,255,'',1,'2025-10-22 13:50:46','2025-10-22 13:50:46'),(36,10,'asdasd','asdasdasd','asdas','tabela','{\"linhas\": [\"1\", \"2\", \"3\"], \"colunas\": [\"1\", \"2\", \"3\", \"4\", \"5\"]}',1,0,255,'',1,'2025-10-22 14:16:56','2025-10-22 14:16:56'),(37,7,'teste_11','teste 11','teste','tabela','{\"linhas\": [\"pergunta 1\", \"pergunta 2\", \"pergunta 3\"], \"colunas\": [\"1\", \"2\", \"3\", \"4\", \"5\"]}',1,0,255,'',1,'2025-10-22 18:00:04','2025-10-22 18:00:04'),(39,10,'testetet','testetet','','texto',NULL,1,0,255,'',1,'2025-11-08 20:18:57','2025-11-08 20:18:57'),(44,11,'novo','novo','','texto',NULL,1,0,255,'',1,'2025-11-09 18:32:59','2025-11-09 18:32:59'),(45,11,'novo_teste','novo_teste','','file',NULL,1,0,255,'',1,'2025-11-09 18:33:08','2025-11-09 18:33:08'),(46,11,'wfsddasdasd','wfsddasdASD','','texto',NULL,1,0,255,'',1,'2025-11-09 18:33:32','2025-11-09 18:33:32'),(47,11,'143easdase3','143EASDASE3','','file',NULL,1,0,255,'',1,'2025-11-09 18:33:39','2025-11-09 18:33:39');


DROP TABLE IF EXISTS `paciente`;
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
INSERT INTO `paciente` VALUES (1,'706007805385045','ARTHUR LIRA','2023-01-09','M','03',NULL,'10','52111581','RUA ARAMBORE','204','123','AGUA FRIA','261160','81988239244',NULL,NULL,'N','2025-05-05 20:41:59','2025-10-29 21:40:01','81'),(2,'700003140310409','AURORA CHELCEA SILVA CUNHA','2023-02-10','F','03',NULL,'10','51240040','RUA RIO XINGI','76',NULL,'IBURA','261160','81988100532',NULL,NULL,'N',NULL,'2025-05-20 21:59:39','81'),(3,'706808283294225','ANTHONY WALACE SILVA BOMFIM','2024-05-12','M','03',NULL,'10','50761417','RUA PEDRO BOULITREAU','92',NULL,'SAN MARTIN','261160','81997941823',NULL,NULL,'N',NULL,'2025-05-20 21:59:39','81'),(4,'707407038246877','ANTHONY MANOEL FERREIRA DA SILVA','2023-12-08','M','03',NULL,'10','50761675','RUA Dr. FLAVIO FERREIRA DA SILVA MAROJO','10','121','TORROES','261160','81999243226',NULL,NULL,'N',NULL,'2025-10-25 15:10:49','81'),(5,'705008479818757','ANTHONY KALEB GALVÃO DOS SANTOS','2023-02-12','M','03',NULL,'10','50741400','RUA INACIO DE BARROS BARRETO','24',NULL,'VARZEA','261160','81992439737',NULL,NULL,'N',NULL,'2025-05-20 21:59:39','81'),(6,'704001845149367','ANTHONY LUIZ BRITO','2023-11-13','M','03',NULL,'10','50790116','MARAGOGIPE','119',NULL,'JARDIM SAO PAULO','261160','81999738498',NULL,NULL,'N',NULL,'2025-05-20 22:00:14','8'),(7,'706709577365814','BENJAMIM DOUGLAS GONCALVES DA SILVA','2024-12-01','M','03',NULL,'10','52121155','MARCILIO DIAS','922','teste','CAMPINA DO BARRETO','261160','81996637227',NULL,NULL,'N',NULL,'2025-10-25 15:09:14','8'),(8,'704005863920669','BRYAN GABRIEL FRANCISCO DA SILVA','2023-09-29','M','03',NULL,'10','52081070','RUA ALTO DO EUCALIPTO','413',NULL,'VASCO DA GAMA','261160','81997412530',NULL,NULL,'N',NULL,'2025-05-20 21:59:39','81'),(9,'704602601020522','BENICIO RAPHAEL DE LIMA NASCIMENTO','2023-11-15','M','03',NULL,'10','50760110','RUA JOSE MOREIRA REIS','694',NULL,'MUSTARDINHA','261160','81997057791',NULL,NULL,'N',NULL,'2025-05-20 21:59:39','81'),(10,'706305779195073','CRISTIAN FRANCISCO DA SILVA','2023-12-28','M','03',NULL,'10','52090475','RUA SÃO BENTO','1',NULL,'MACAXEIRA','261160','81995350392',NULL,NULL,'N',NULL,'2025-05-20 21:59:39','81'),(11,'705209482922771','DANIEL MATIAS DA SILVA ALBUQUERQUE','2023-12-03','M','03',NULL,'10','50780685','RUA RAUL FREIRE DE SOUZA','767',NULL,'AREIAS','261160','81986896376',NULL,NULL,'N',NULL,'2025-05-20 21:59:39','81'),(12,'898006357038573','ENZO DAVI SOARES DE SOUSA','2024-02-04','M','03',NULL,'10','52091130','RUA ALVARES FLORENSE','140',NULL,'CORREGO DO JENIPAPO','261160','81992120202',NULL,NULL,'N',NULL,'2025-05-20 21:59:39','81'),(13,'898006347500304','HEITOR MIGUEL BATISTA DA SILVA','2023-11-17','M','03',NULL,'10','50720160','RUA PANDIA CALOGERAS','110',NULL,'PRADO','261160','81986801520',NULL,NULL,'N',NULL,'2025-05-20 21:59:39','81'),(14,'704805082508342','ISAAC NOAH BARBOS','2024-05-25','M','03',NULL,'10','50980685','RUA ELIANE FRAGOSO DO NASCIMENTO','213',NULL,'NOVA MORADA','261160','81993345570',NULL,NULL,'N',NULL,'2025-05-20 21:59:39','81'),(15,'702003888042284','JHONATA HENRIQUE SANTANA DOS SANTOS','2023-11-26','M','01',NULL,'10','52171170','RUA DO SITIO SÃO BRAS','52',NULL,'SITIO DOS PINTOS','261160','81996939717',NULL,NULL,'N',NULL,'2025-05-20 21:59:39','81'),(16,'706005895666241','JHESSYE MARIA PEREIRA BARBOSA DA SILVA','2023-09-03','F','03',NULL,'10','51150020','RUA PROFESSORA ROSILDA COSTA','15',NULL,'IMBIRIBEIRA','261160','81985887391',NULL,NULL,'N',NULL,'2025-05-20 21:59:39','81'),(17,'898006329788342','LEOAN SAMUEL DOS SANTOS','2023-02-09','M','03',NULL,'10','52091071','RUA SÃO VALERIANO','39',NULL,'CORREGO DO JENIPAPO','261160','81994220081',NULL,NULL,'N',NULL,'2025-05-20 21:59:39','81'),(18,'706803729164526','LUIZA VALENTINA WANDERLEY DUARTE','2023-11-10','F','03',NULL,'10','51010380','RUA DOUTOR HENRIQUE LINS','164',NULL,'PINA','261160','81997873628',NULL,NULL,'N',NULL,'2025-05-20 21:59:39','81'),(19,'706506304537491','LUISA BEATRIZ SILVA SOARES','2023-09-09','F','03',NULL,'10','50630670','RUA BRASABANTE','3',NULL,'CORDEIRO','261160','81987941349',NULL,NULL,'N',NULL,'2025-05-20 21:59:39','81'),(20,'700800919436886','MARIA ACACIA ALVES LINS DE OLIVEIRA','2024-12-09','F','03',NULL,'10','50680520','RUA JANIRO PONTES','90',NULL,'IPUTINGA','261160','81950680520',NULL,NULL,'N',NULL,'2025-05-20 21:59:39','81'),(21,'706808202819624','MAYA KENIA FELIX SANTOS DA SILVA','2023-09-17','F','01',NULL,'10','51330230','RUA IBIRATINGA','22',NULL,'COHAB','261160','81993928902',NULL,NULL,'N',NULL,'2025-05-20 21:59:39','81'),(22,'700508740962555','MAITE BYANCA VALENTIN PEREIRA BARROS DA SILVA','2023-09-29','F','01',NULL,'10','52090785','RUA RANUSIA ALVES RODRIGUES','175',NULL,'MACAXEIRA','261160','81997776931',NULL,NULL,'N',NULL,'2025-05-20 21:59:39','81'),(23,'705009445573655','NICOLAS EZEQUIEL DA SILVA CHAVES','2024-03-18','M','03',NULL,'10','52211001','RUA AMERICA CISNEIROS','226',NULL,'ALTO SANTA TEREZINHA','261160','81983489280',NULL,NULL,'N',NULL,'2025-05-20 21:59:39','81'),(24,'701803258284879','PEDRO EMANOEL FELICIANI','2024-01-13','M','03',NULL,'10','50970010','RUA JOSE ALVES DO NASCIMENTO','126',NULL,'VARZEA','261160','81991671751',NULL,NULL,'N',NULL,'2025-05-20 21:59:39','81'),(25,'705009262480356','PIETRO BENICIO ALBUQUERQUE MARIANO','2023-11-11','M','03',NULL,'10','50960470','RUA LUIZ AUGUSTO RABELO','189',NULL,'VARZEA','261160','81996694614',NULL,NULL,'N',NULL,'2025-05-20 21:59:39','81'),(26,'700002087515101','RUBENS JOSE DE LIRA','2024-02-03','M','03',NULL,'10','52165050','RUA PERREIRA BARRETO','500',NULL,'PASSARINHO','261160','81986731602',NULL,NULL,'N',NULL,'2025-05-20 21:59:39','81'),(27,'701006829955095','RAFAEL FELIPE DANTAS GUIMARES','2023-09-06','M','03',NULL,'10','51290501','RUA GUARACIABA','96',NULL,'COHAB','261160','81985944217',NULL,NULL,'N',NULL,'2025-05-20 21:59:39','81'),(32,'701006812322123','teste da silva teste','2025-05-21','M','01','','10','50800200','teste teste','11',NULL,'varzea','261160','81988888888',NULL,NULL,'N','2025-05-21 17:04:29','2025-05-21 21:48:38','81'),(33,'700002359416809','FELIPE teste','2025-05-12','M','01','','10','50800000','testetetetet','123',NULL,'IMBIRIBEIRA','261160','8195073899',NULL,NULL,'N','2025-05-22 23:32:51','2025-05-22 23:32:51','8'),(34,'705008479818766','teste da paz','2025-09-08','M','01','test','10','50000000','teste','55','nadad','nadad',NULL,'85959595959','teste@teste.com',NULL,'N','2025-09-14 01:36:23','2025-09-14 01:36:23','81');

DROP TABLE IF EXISTS `procedimento`;
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
INSERT INTO `procedimento` VALUES (10,'301070040','ACOMPANHAMENTO NEUROPSICOLÓGICO DE PACIENTE EM REABILITAÇÃO','TERAPIA OCUPACIONAL',1,'135','011'),(11,'301070075','ATENDIMENTO / ACOMPANHAMENTO DE PACIENTE EM REABILITACAO DO DESENVOLVIMENTO NEUROPSICOMOTOR','FISIOTERAPIA',1,'135','010'),(12,'301070024','ACOMPANHAMENTO DE PACIENTE EM REABILITACAO EM COMUNICACAO ALTERNATIVA','FISIOTERAPIA',1,'135','010'),(13,'301070048','CONSULTA DE PROFISSIONAIS DE NIVEL SUPERIOR NA ATENÇÃO ESPECIALIZADA (EXCETO MÉDICO)','FISIOTERAPIA',1,'135','010'),(14,'301070067','ATENDIMENTO / ACOMPANHAMENTO EM REABILITAÇÃO NAS MULTIPLAS DEFICIÊNCIAS','FISIOTERAPIA',1,'135','010'),(17,'301070113','TERAPIA FONOAUDIOLÓGICA INDIVIDUAL','FONOAUDIOLOGO',1,'135','010'),(19,'301070091','ATENDIMENTO EM OFICINA TERAPÊUTICA II EM GRUPO PARA PESSOAS COM DEFICIÊNCIA (POR OFICINA TERAPÊUTICA II)','PSICOLOGIA',1,'135','011');



DROP TABLE IF EXISTS `profissional`;

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
INSERT INTO `profissional` VALUES (1,'702501333549039','JULIANA GOMES DE OLIVEIRA','223905','06895245448','TERAPIA OCUPACIONAL','81997099231',NULL,'2025-05-05 20:41:59'),(2,'708706194080594','MARCELA RAQUEL DE OLIVEIRA LIMA','223605','02215040440','FISIOTERAPIA','81992923383',NULL,'2025-05-05 20:41:59'),(3,'898002394524962','JESSICA GOMES DA SILVA CORREIA','251510','07113087442','PSICOLOGIA','81985973406',NULL,'2025-05-05 20:41:59'),(4,'704807520359948','ROBERTA KARLIZE PEREIRA SILVA','223905','05251828454','FISIOTERAPIA','81996181228',NULL,'2025-05-05 20:53:00'),(5,'898005904155532','MERCIA MARIA TAVARES DE MELO','223810','53334248704','FONOAUDIOLOGIA','81999749144',NULL,'2025-05-05 20:53:47');



DROP TABLE IF EXISTS `usuarios`;
CREATE TABLE `usuarios` (
  `id` int NOT NULL AUTO_INCREMENT,
  `cpf` varchar(45) NOT NULL,
  `login` varchar(60) NOT NULL,
  `senha` varchar(300) NOT NULL,
  `nm_usuario` varchar(45) NOT NULL,
  `tipo` varchar(45) NOT NULL DEFAULT 'user',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
INSERT INTO `usuarios` VALUES (1,'11123344488','hants','$2y$10$kYIML1hSPAA8/vobCtX3JO0ax3XybwNo/pSiT3/s7O.svTXg21ygG','eliabe paz','admin'),(2,'12345678911','user','$2y$10$NS706aAYeNqG6.bB1TBVh.RSbM5PHFj.Gfl5qj.GMidNeWU7qNHrO','user','user'),(10,'12345678910','vivenciar','$2y$10$aNeDZra0K2jvf..3Ey9Q1OH6Ajb/7YwUB7mc5HQurr.tiI5ol01Ke','vivenciar','user'),(11,'11133399977','teste123','$2y$10$AxFRxuvHlhTVduVchIFoe.qYUCWzJOdKr3EITmKi/x1fyjjhEZMBW','teste','user');
