CREATE DATABASE  IF NOT EXISTS `ciesytem` /*!40100 DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci */ /*!80016 DEFAULT ENCRYPTION='N' */;
USE `ciesytem`;
-- MySQL dump 10.13  Distrib 8.0.40, for Win64 (x86_64)
--
-- Host: localhost    Database: ciesytem
-- ------------------------------------------------------
-- Server version	8.0.45-0ubuntu0.22.04.1

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
-- Table structure for table `carteirinhas_estudantis`
--

DROP TABLE IF EXISTS `carteirinhas_estudantis`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `carteirinhas_estudantis` (
  `id` int NOT NULL AUTO_INCREMENT,
  `inscricao_id` int NOT NULL,
  `codigo_cie` varchar(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `certificado_digit` text COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Certificado de Atributo ICP-Brasil em JSON',
  `qr_code_content` text COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Conteúdo do QR Code em Base64',
  `data_emissao` datetime NOT NULL,
  `data_validade` date NOT NULL,
  `status` enum('emitida','cancelada','bloqueada','vencida') COLLATE utf8mb4_unicode_ci DEFAULT 'emitida',
  `registrado_nacional` tinyint(1) DEFAULT '0' COMMENT 'Registrado no banco nacional',
  `data_registro_nacional` datetime DEFAULT NULL,
  `criado_em` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `atualizado_em` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `codigo_cie` (`codigo_cie`),
  KEY `idx_codigo_cie` (`codigo_cie`),
  KEY `idx_inscricao_id` (`inscricao_id`),
  KEY `idx_status` (`status`),
  CONSTRAINT `carteirinhas_estudantis_ibfk_1` FOREIGN KEY (`inscricao_id`) REFERENCES `inscricoes` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `carteirinhas_estudantis`
--

LOCK TABLES `carteirinhas_estudantis` WRITE;
/*!40000 ALTER TABLE `carteirinhas_estudantis` DISABLE KEYS */;
INSERT INTO `carteirinhas_estudantis` VALUES (10,77,'c5a80fb9-53cc-4bb2-bbb0-164ddbe5c968','{\n    \"certificado\": {\n        \"versao\": \"1.0\",\n        \"tipo\": \"Certificado de Atributo ICP-Brasil\",\n        \"norma\": \"Portaria ITI nº 78\\/2018\",\n        \"doc_icp\": \"DOC-ICP-16\",\n        \"conteudo\": {\n            \"cie\": {\n                \"codigo\": \"c5a80fb9-53cc-4bb2-bbb0-164ddbe5c968\",\n                \"dataEmissao\": \"2026-03-23 20:23:16\",\n                \"dataValidade\": \"2027-03-31\",\n                \"entidadeEmissora\": \"UNE\\/UBES\\/ANPG\",\n                \"status\": \"emitida\"\n            },\n            \"estudante\": {\n                \"nome\": \"eliabe teste publico\",\n                \"nomeSocial\": null,\n                \"dataNascimento\": \"2026-03-23\",\n                \"cpf\": \"12345678912\",\n                \"documento\": {\n                    \"tipo\": \"RG\",\n                    \"numero\": \"1234567\",\n                    \"orgao\": \"ssds\"\n                }\n            },\n            \"escolaridade\": {\n                \"instituicao\": \"UFPE\",\n                \"instituicaoId\": 3,\n                \"curso\": \"testepublico\",\n                \"nivel\": \"testepublico\",\n                \"matricula\": \"testepublico\",\n                \"situacao\": \"Matriculado\"\n            },\n            \"validacao\": {\n                \"url\": \"https:\\/\\/meuidestudantil.com.br\\/validar-cie.php?codigo=c5a80fb9-53cc-4bb2-bbb0-164ddbe5c968\",\n                \"metodo\": \"Certificado Digital ICP-Brasil\"\n            }\n        },\n        \"metadados\": {\n            \"data_geracao\": \"2026-03-23T20:23:16-03:00\",\n            \"emissor\": \"CIE - Carteira de Identificação Estudantil\",\n            \"entidades\": {\n                \"UNE\": \"União Nacional dos Estudantes\",\n                \"UBES\": \"União Brasileira dos Estudantes Secundaristas\",\n                \"ANPG\": \"Associação Nacional de Pós-Graduandos\"\n            }\n        }\n    },\n    \"assinatura_digital\": {\n        \"assinatura\": \"doxbK1\\/ER4g78BoQeGULt2A0TAs+XZ\\/NvbF2xGL2B+bcG+7ZWE6sjecjOPaI4UxzbGd6MmH+8cuLlrptLLP4q8Qnj64cbe8vRYOcoDeBqiZvDyM\\/+4YI71\\/vs2I5Yltidz2VWjo3bHZI2DVozmvJ0QfvRG80cIIP4gLRwHZE0Og5yjABYqadNvKr+MNlMWXniGX17Em5MaIRlbxFIGo5MclzubIILRKvEaihPbS0PfhAsvK0ZM3+KtyivENS13MxKcl2ce6oRZZK23M9VMw\\/YZ2tND2kyjHamN\\/Hc\\/dQhBaWomXEhWbLu42cM9n6epYFilkWNktRQgS3LDjttAVyCw==\",\n        \"algoritmo\": \"SHA256withRSA\",\n        \"certificado\": \"LS0tLS1CRUdJTiBDRVJUSUZJQ0FURS0tLS0tCk1JSUlBVENDQmVtZ0F3SUJBZ0lJVXN6M1RFek5NV0V3RFFZSktvWklodmNOQVFFTEJRQXdkakVMTUFrR0ExVUUKQmhNQ1FsSXhFekFSQmdOVkJBb1RDa2xEVUMxQ2NtRnphV3d4TmpBMEJnTlZCQXNUTFZObFkzSmxkR0Z5YVdFZwpaR0VnVW1WalpXbDBZU0JHWldSbGNtRnNJR1J2SUVKeVlYTnBiQ0F0SUZKR1FqRWFNQmdHQTFVRUF4TVJRVU1nClUwRkdSVmRGUWlCU1JrSWdkalV3SGhjTk1qWXdNekV3TVRNMU56QTBXaGNOTWpjd016RXdNVE0xTnpBMFdqQ0MKQVJReEN6QUpCZ05WQkFZVEFrSlNNUk13RVFZRFZRUUtFd3BKUTFBdFFuSmhjMmxzTVFzd0NRWURWUVFJRXdKUQpRakVVTUJJR0ExVUVCeE1MU2s5QlR5QlFSVk5UVDBFeE5qQTBCZ05WQkFzVExWTmxZM0psZEdGeWFXRWdaR0VnClVtVmpaV2wwWVNCR1pXUmxjbUZzSUdSdklFSnlZWE5wYkNBdElGSkdRakVXTUJRR0ExVUVDeE1OVWtaQ0lHVXQKUTA1UVNpQkJNVEVYTUJVR0ExVUVDeE1PTWpNNE56a3dORFl3TURBeE1qZ3hHVEFYQmdOVkJBc1RFSFpwWkdWdgpZMjl1Wm1WeVpXNWphV0V4U1RCSEJnTlZCQU1UUUVGVFUwOURTVUZEUVU4Z1RrRkRTVTlPUVV3Z1JFVWdSVk5VClZVUkJUbFJGVXlCRVJTQkZSRlZEUVVOQlR5QkJJRVE2TmpVek56QXpPVFF3TURBeE1UVXdnZ0VpTUEwR0NTcUcKU0liM0RRRUJBUVVBQTRJQkR3QXdnZ0VLQW9JQkFRQ3g5cEU2UlVVb2VISmZEUkR5Q1J2UzVFblpHa0VOZnhpQwpGYVRXOVRYL3l2eUxSR1hwRUtBdUFFQTNPa3NSUE1TZU02YnN1d0pLSzNNVnQrOXRTT2QwN29hMXZEMVVzVWZQClI4QU44Q2FxbmtCTGlhZjg3SWowQ2doeEpITTJBY21HM2U1dUNwMTM3cEVvTXZjWEtSQVMwdWE2UElzZm1QaGEKVUxqemxtK2R2aFVNTHB5cjNQaFFDRTFMK0FVTmdiSDR0YnZEcEdGb24zeFFEZ3pOMnZGS21qR3dJcVc1SXk3NQpJd0NCejlVcDBDV3Q4S3pxWE5yc1lMQzRnM2w3UUkwWHZwK0hWTTlZTlZlYmI4dTZLcGYzalp0OFMzS3d4Yk13CnZsdUhKU0ZZYUdaY0RoSFo5Y3UrMjdLbURLQ2FTQXp4VCswR1R2THFyYUVuM04wcXpoVWhBZ01CQUFHamdnTHgKTUlJQzdUQWZCZ05WSFNNRUdEQVdnQlFwWGt2VlJreTcvaGFuWThFZHhDYnkzZGp6QlRBT0JnTlZIUThCQWY4RQpCQU1DQmVBd2FRWURWUjBnQkdJd1lEQmVCZ1pnVEFFQ0FUTXdWREJTQmdnckJnRUZCUWNDQVJaR2FIUjBjRG92CkwzSmxjRzl6YVhSdmNtbHZMbUZqYzJGbVpYZGxZaTVqYjIwdVluSXZZV010YzJGbVpYZGxZbkptWWk5a2NHTXQKWVdOellXWmxkMlZpY21aaUxuQmtaakNCcmdZRFZSMGZCSUdtTUlHak1FK2dUYUJMaGtsb2RIUndPaTh2Y21WdwpiM05wZEc5eWFXOHVZV056WVdabGQyVmlMbU52YlM1aWNpOWhZeTF6WVdabGQyVmljbVppTDJ4amNpMWhZeTF6CllXWmxkMlZpY21aaWRqVXVZM0pzTUZDZ1RxQk1oa3BvZEhSd09pOHZjbVZ3YjNOcGRHOXlhVzh5TG1GamMyRm0KWlhkbFlpNWpiMjB1WW5JdllXTXRjMkZtWlhkbFluSm1ZaTlzWTNJdFlXTXRjMkZtWlhkbFluSm1ZblkxTG1OeQpiRENCdHdZSUt3WUJCUVVIQVFFRWdhb3dnYWN3VVFZSUt3WUJCUVVITUFLR1JXaDBkSEE2THk5eVpYQnZjMmwwCmIzSnBieTVoWTNOaFptVjNaV0l1WTI5dExtSnlMMkZqTFhOaFptVjNaV0p5Wm1JdllXTXRjMkZtWlhkbFluSm0KWW5ZMUxuQTNZakJTQmdnckJnRUZCUWN3QW9aR2FIUjBjRG92TDNKbGNHOXphWFJ2Y21sdk1pNWhZM05oWm1WMwpaV0l1WTI5dExtSnlMMkZqTFhOaFptVjNaV0p5Wm1JdllXTXRjMkZtWlhkbFluSm1ZblkxTG5BM1lqQ0J1UVlEClZSMFJCSUd4TUlHdWdSaFFUMHhQVTBWRVZVTkJUVUZKVTBCSFRVRkpUQzVEVDAyZ0pBWUZZRXdCQXdLZ0d4TVoKUlV4SlJVeFRUMDRnUWtGVVNWTlVRU0JFUVNCVFNVeFdRYUFaQmdWZ1RBRURBNkFRRXc0Mk5UTTNNRE01TkRBdwpNREV4TmFBNEJnVmdUQUVEQktBdkV5MHhOVEEwTVRrNU1EQTRNRFkwTVRFMk5ETXdNREF3TURBd01EQXdNREF3Ck1EQXdNREF3TURBd01EQXdNRENnRndZRllFd0JBd2VnRGhNTU1EQXdNREF3TURBd01EQXdNQjBHQTFVZEpRUVcKTUJRR0NDc0dBUVVGQndNQ0JnZ3JCZ0VGQlFjREJEQUpCZ05WSFJNRUFqQUFNQTBHQ1NxR1NJYjNEUUVCQ3dVQQpBNElDQVFDQmU5VzBia1BhaUozSHRGM0kvY3dzOVB6YVlWeDI5SFUwOUxUNG5VZVprZWFjUEZCSTFSMGl1dFpjCmtlZjN1M0l5ZGpWZCtIWFRhWnZjSVhyMU5DRTNpY25hb3VZRnBvanMyVGlWSFdsZHV5UjRFb21pMnZVQ21OdXYKWXdHekdGVWlGRzZIeXc1dmt4dFZNSVk3QjYzZDhtNHBJbWxMK1dJQkR2U050UTh0dXdjb0NuOE9Zd0dlR2JwSwo5MHBHSEpteDZvQ1JwUnVaU2NOZ3FvcFZkYVlBT2E3RDdnY21pUzZ2K3NNSnJ2Z1ZPTnFRZGRETUlXZEJzYkVtCjZrU2lScXIvU25TNGNCODFCZ015Y3I3RWtVMExseXZSRCs2WDFNN3QxRHNyMWR5SndtTy9Ta1E2MCtqekkyL3QKK0RIK3l4b29Da2w1alo4VWMwTEpjUWgycTYzZXFMRjNrclBvNUxzZW8rVzFqNDNMMldYbzBKQVMyY2NXUGFFUQp0Q01wY2dVb0hXWm1nRVpmd1VzRnlnZEg4OTVFQzdmL2crYkhtdU5oVGFKSkJBWWR1UU9Id3BSWDFYTzR3M2dECmp4RlFFbDNVQU9NN3h1SStPd1lxYkIrWnJvOVo2ckFvbGkxQTRzaHJ2K2syc01EU1hHbE1iSEFWY3AzcUlEWDcKYnBnalBHRUdPR0lxSHczR0xVVW9rdHllZmlBNDFVZ3ovMFZUZkhmQnUxa29iUG93S1BTK2NIdmovbXdrMFdKeAoxalFsS0NoSi9KWkI1bXZ6eFdZRVB6Y2RjcWtPb2FHcHRUeHEySnNBYjFTbnp1emhhaU0wY1BhakdJaTFOeHNOClkwbGVpd1lvYjU1T20vZjNnTVd3bVhCLzNiZWU2NW4reUJhSlJSNkdab3lkWmhpOFdRPT0KLS0tLS1FTkQgQ0VSVElGSUNBVEUtLS0tLQo=\",\n        \"data_assinatura\": \"2026-03-23T20:23:16-03:00\",\n        \"hash_conteudo\": \"JwFUerQeEYQgxhgQhLAMMmHEYzzKI\\/xbdaYEs6Mp2gQ=\",\n        \"modo_teste\": false\n    }\n}','eyJ2ZXJzYW8iOiIxLjAiLCJ0aXBvIjoiQ0lFIiwiY29kaWdvIjoiYzVhODBmYjktNTNjYy00YmIyLWJiYjAtMTY0ZGRiZTVjOTY4IiwidXJsVmFsaWRhY2FvIjoiaHR0cHM6XC9cL21ldWlkZXN0dWRhbnRpbC5jb20uYnJcL3ZhbGlkYXItY2llLnBocD9jb2RpZ289YzVhODBmYjktNTNjYy00YmIyLWJiYjAtMTY0ZGRiZTVjOTY4IiwidGltZXN0YW1wIjoxNzc0MzA4MTk2fQ==','2026-03-23 20:23:16','2027-03-31','emitida',1,'2026-03-23 20:23:18','2026-03-23 23:23:16','2026-03-23 23:23:18'),(11,78,'72afc3d8-cdeb-498c-99d9-297d4a4fa7c1','{\n    \"certificado\": {\n        \"versao\": \"1.0\",\n        \"tipo\": \"Certificado de Atributo ICP-Brasil\",\n        \"norma\": \"Portaria ITI nº 78\\/2018\",\n        \"doc_icp\": \"DOC-ICP-16\",\n        \"conteudo\": {\n            \"cie\": {\n                \"codigo\": \"72afc3d8-cdeb-498c-99d9-297d4a4fa7c1\",\n                \"dataEmissao\": \"2026-03-24 06:18:16\",\n                \"dataValidade\": \"2027-03-31\",\n                \"entidadeEmissora\": \"UNE\\/UBES\\/ANPG\",\n                \"status\": \"emitida\"\n            },\n            \"estudante\": {\n                \"nome\": \"teste uber cedo\",\n                \"nomeSocial\": null,\n                \"dataNascimento\": \"1999-03-20\",\n                \"cpf\": \"12232112222\",\n                \"documento\": {\n                    \"tipo\": \"RG\",\n                    \"numero\": \"4146797\",\n                    \"orgao\": \"ssds\"\n                }\n            },\n            \"escolaridade\": {\n                \"instituicao\": \"teste 1\",\n                \"instituicaoId\": 1,\n                \"curso\": \"uber\",\n                \"nivel\": \"uber\",\n                \"matricula\": \"uber20conto\",\n                \"situacao\": \"Matriculado\"\n            },\n            \"validacao\": {\n                \"url\": \"https:\\/\\/meuidestudantil.com.br\\/validar-cie.php?codigo=72afc3d8-cdeb-498c-99d9-297d4a4fa7c1\",\n                \"metodo\": \"Certificado Digital ICP-Brasil\"\n            }\n        },\n        \"metadados\": {\n            \"data_geracao\": \"2026-03-24T06:18:16-03:00\",\n            \"emissor\": \"CIE - Carteira de Identificação Estudantil\",\n            \"entidades\": {\n                \"UNE\": \"União Nacional dos Estudantes\",\n                \"UBES\": \"União Brasileira dos Estudantes Secundaristas\",\n                \"ANPG\": \"Associação Nacional de Pós-Graduandos\"\n            }\n        }\n    },\n    \"assinatura_digital\": {\n        \"assinatura\": \"C9hWygNH92IFmhBgpfa2d5ZBYF7x5JSkHM7FaWpkzYk66xXWiVo8i1F2eUArsBiDtzl64R1Lq8ageoB0H3E0w46pNTreEwWF22kCE3je+U1VaCOhYNITFhPet6\\/zwi7fuZUSu2XAhwa8cMYoh3Y2JFsmgTLcEUXweTAjKcrS2vDNj4Rzao88iaYzWPL2vHM\\/yf7N433G8sKAaT1dXE9o72iRvo8LEhELGLoJUqlFrPeolwGQmwwSfJTWD+uKgMa93Iuc+6c0qLRDTbxlQjKxC9NiYHb\\/YNrusdtc9R7SpmpPdY54czSHpwFJ89gBgemvOxKCMfK7EUnGdYGMTcwOJA==\",\n        \"algoritmo\": \"SHA256withRSA\",\n        \"certificado\": \"LS0tLS1CRUdJTiBDRVJUSUZJQ0FURS0tLS0tCk1JSUlBVENDQmVtZ0F3SUJBZ0lJVXN6M1RFek5NV0V3RFFZSktvWklodmNOQVFFTEJRQXdkakVMTUFrR0ExVUUKQmhNQ1FsSXhFekFSQmdOVkJBb1RDa2xEVUMxQ2NtRnphV3d4TmpBMEJnTlZCQXNUTFZObFkzSmxkR0Z5YVdFZwpaR0VnVW1WalpXbDBZU0JHWldSbGNtRnNJR1J2SUVKeVlYTnBiQ0F0SUZKR1FqRWFNQmdHQTFVRUF4TVJRVU1nClUwRkdSVmRGUWlCU1JrSWdkalV3SGhjTk1qWXdNekV3TVRNMU56QTBXaGNOTWpjd016RXdNVE0xTnpBMFdqQ0MKQVJReEN6QUpCZ05WQkFZVEFrSlNNUk13RVFZRFZRUUtFd3BKUTFBdFFuSmhjMmxzTVFzd0NRWURWUVFJRXdKUQpRakVVTUJJR0ExVUVCeE1MU2s5QlR5QlFSVk5UVDBFeE5qQTBCZ05WQkFzVExWTmxZM0psZEdGeWFXRWdaR0VnClVtVmpaV2wwWVNCR1pXUmxjbUZzSUdSdklFSnlZWE5wYkNBdElGSkdRakVXTUJRR0ExVUVDeE1OVWtaQ0lHVXQKUTA1UVNpQkJNVEVYTUJVR0ExVUVDeE1PTWpNNE56a3dORFl3TURBeE1qZ3hHVEFYQmdOVkJBc1RFSFpwWkdWdgpZMjl1Wm1WeVpXNWphV0V4U1RCSEJnTlZCQU1UUUVGVFUwOURTVUZEUVU4Z1RrRkRTVTlPUVV3Z1JFVWdSVk5VClZVUkJUbFJGVXlCRVJTQkZSRlZEUVVOQlR5QkJJRVE2TmpVek56QXpPVFF3TURBeE1UVXdnZ0VpTUEwR0NTcUcKU0liM0RRRUJBUVVBQTRJQkR3QXdnZ0VLQW9JQkFRQ3g5cEU2UlVVb2VISmZEUkR5Q1J2UzVFblpHa0VOZnhpQwpGYVRXOVRYL3l2eUxSR1hwRUtBdUFFQTNPa3NSUE1TZU02YnN1d0pLSzNNVnQrOXRTT2QwN29hMXZEMVVzVWZQClI4QU44Q2FxbmtCTGlhZjg3SWowQ2doeEpITTJBY21HM2U1dUNwMTM3cEVvTXZjWEtSQVMwdWE2UElzZm1QaGEKVUxqemxtK2R2aFVNTHB5cjNQaFFDRTFMK0FVTmdiSDR0YnZEcEdGb24zeFFEZ3pOMnZGS21qR3dJcVc1SXk3NQpJd0NCejlVcDBDV3Q4S3pxWE5yc1lMQzRnM2w3UUkwWHZwK0hWTTlZTlZlYmI4dTZLcGYzalp0OFMzS3d4Yk13CnZsdUhKU0ZZYUdaY0RoSFo5Y3UrMjdLbURLQ2FTQXp4VCswR1R2THFyYUVuM04wcXpoVWhBZ01CQUFHamdnTHgKTUlJQzdUQWZCZ05WSFNNRUdEQVdnQlFwWGt2VlJreTcvaGFuWThFZHhDYnkzZGp6QlRBT0JnTlZIUThCQWY4RQpCQU1DQmVBd2FRWURWUjBnQkdJd1lEQmVCZ1pnVEFFQ0FUTXdWREJTQmdnckJnRUZCUWNDQVJaR2FIUjBjRG92CkwzSmxjRzl6YVhSdmNtbHZMbUZqYzJGbVpYZGxZaTVqYjIwdVluSXZZV010YzJGbVpYZGxZbkptWWk5a2NHTXQKWVdOellXWmxkMlZpY21aaUxuQmtaakNCcmdZRFZSMGZCSUdtTUlHak1FK2dUYUJMaGtsb2RIUndPaTh2Y21WdwpiM05wZEc5eWFXOHVZV056WVdabGQyVmlMbU52YlM1aWNpOWhZeTF6WVdabGQyVmljbVppTDJ4amNpMWhZeTF6CllXWmxkMlZpY21aaWRqVXVZM0pzTUZDZ1RxQk1oa3BvZEhSd09pOHZjbVZ3YjNOcGRHOXlhVzh5TG1GamMyRm0KWlhkbFlpNWpiMjB1WW5JdllXTXRjMkZtWlhkbFluSm1ZaTlzWTNJdFlXTXRjMkZtWlhkbFluSm1ZblkxTG1OeQpiRENCdHdZSUt3WUJCUVVIQVFFRWdhb3dnYWN3VVFZSUt3WUJCUVVITUFLR1JXaDBkSEE2THk5eVpYQnZjMmwwCmIzSnBieTVoWTNOaFptVjNaV0l1WTI5dExtSnlMMkZqTFhOaFptVjNaV0p5Wm1JdllXTXRjMkZtWlhkbFluSm0KWW5ZMUxuQTNZakJTQmdnckJnRUZCUWN3QW9aR2FIUjBjRG92TDNKbGNHOXphWFJ2Y21sdk1pNWhZM05oWm1WMwpaV0l1WTI5dExtSnlMMkZqTFhOaFptVjNaV0p5Wm1JdllXTXRjMkZtWlhkbFluSm1ZblkxTG5BM1lqQ0J1UVlEClZSMFJCSUd4TUlHdWdSaFFUMHhQVTBWRVZVTkJUVUZKVTBCSFRVRkpUQzVEVDAyZ0pBWUZZRXdCQXdLZ0d4TVoKUlV4SlJVeFRUMDRnUWtGVVNWTlVRU0JFUVNCVFNVeFdRYUFaQmdWZ1RBRURBNkFRRXc0Mk5UTTNNRE01TkRBdwpNREV4TmFBNEJnVmdUQUVEQktBdkV5MHhOVEEwTVRrNU1EQTRNRFkwTVRFMk5ETXdNREF3TURBd01EQXdNREF3Ck1EQXdNREF3TURBd01EQXdNRENnRndZRllFd0JBd2VnRGhNTU1EQXdNREF3TURBd01EQXdNQjBHQTFVZEpRUVcKTUJRR0NDc0dBUVVGQndNQ0JnZ3JCZ0VGQlFjREJEQUpCZ05WSFJNRUFqQUFNQTBHQ1NxR1NJYjNEUUVCQ3dVQQpBNElDQVFDQmU5VzBia1BhaUozSHRGM0kvY3dzOVB6YVlWeDI5SFUwOUxUNG5VZVprZWFjUEZCSTFSMGl1dFpjCmtlZjN1M0l5ZGpWZCtIWFRhWnZjSVhyMU5DRTNpY25hb3VZRnBvanMyVGlWSFdsZHV5UjRFb21pMnZVQ21OdXYKWXdHekdGVWlGRzZIeXc1dmt4dFZNSVk3QjYzZDhtNHBJbWxMK1dJQkR2U050UTh0dXdjb0NuOE9Zd0dlR2JwSwo5MHBHSEpteDZvQ1JwUnVaU2NOZ3FvcFZkYVlBT2E3RDdnY21pUzZ2K3NNSnJ2Z1ZPTnFRZGRETUlXZEJzYkVtCjZrU2lScXIvU25TNGNCODFCZ015Y3I3RWtVMExseXZSRCs2WDFNN3QxRHNyMWR5SndtTy9Ta1E2MCtqekkyL3QKK0RIK3l4b29Da2w1alo4VWMwTEpjUWgycTYzZXFMRjNrclBvNUxzZW8rVzFqNDNMMldYbzBKQVMyY2NXUGFFUQp0Q01wY2dVb0hXWm1nRVpmd1VzRnlnZEg4OTVFQzdmL2crYkhtdU5oVGFKSkJBWWR1UU9Id3BSWDFYTzR3M2dECmp4RlFFbDNVQU9NN3h1SStPd1lxYkIrWnJvOVo2ckFvbGkxQTRzaHJ2K2syc01EU1hHbE1iSEFWY3AzcUlEWDcKYnBnalBHRUdPR0lxSHczR0xVVW9rdHllZmlBNDFVZ3ovMFZUZkhmQnUxa29iUG93S1BTK2NIdmovbXdrMFdKeAoxalFsS0NoSi9KWkI1bXZ6eFdZRVB6Y2RjcWtPb2FHcHRUeHEySnNBYjFTbnp1emhhaU0wY1BhakdJaTFOeHNOClkwbGVpd1lvYjU1T20vZjNnTVd3bVhCLzNiZWU2NW4reUJhSlJSNkdab3lkWmhpOFdRPT0KLS0tLS1FTkQgQ0VSVElGSUNBVEUtLS0tLQo=\",\n        \"data_assinatura\": \"2026-03-24T06:18:16-03:00\",\n        \"hash_conteudo\": \"qOx985H4TPVFFdRLE+uVN\\/xr2ywswV0CJBkrjNYup78=\",\n        \"modo_teste\": false\n    }\n}','eyJ2ZXJzYW8iOiIxLjAiLCJ0aXBvIjoiQ0lFIiwiY29kaWdvIjoiNzJhZmMzZDgtY2RlYi00OThjLTk5ZDktMjk3ZDRhNGZhN2MxIiwidXJsVmFsaWRhY2FvIjoiaHR0cHM6XC9cL21ldWlkZXN0dWRhbnRpbC5jb20uYnJcL3ZhbGlkYXItY2llLnBocD9jb2RpZ289NzJhZmMzZDgtY2RlYi00OThjLTk5ZDktMjk3ZDRhNGZhN2MxIiwidGltZXN0YW1wIjoxNzc0MzQzODk2fQ==','2026-03-24 06:18:16','2027-03-31','emitida',1,'2026-03-24 06:18:18','2026-03-24 09:18:16','2026-03-24 09:18:18');
/*!40000 ALTER TABLE `carteirinhas_estudantis` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `documentos_anexados`
--

DROP TABLE IF EXISTS `documentos_anexados`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `documentos_anexados` (
  `id` int NOT NULL AUTO_INCREMENT,
  `entidade_tipo` enum('estudante','inscricao') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `entidade_id` int NOT NULL,
  `tipo` enum('rg_frente','rg_verso','cnh_frente','cnh_verso','cpf_frente','cpf_verso','matricula','pagamento','selfie_documento','foto_3x4') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `caminho_arquivo` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `descricao` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `validado` enum('pendente','validado','invalido') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 'pendente',
  `observacoes_validacao` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `criado_em` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `atualizado_em` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_entidade` (`entidade_tipo`,`entidade_id`),
  KEY `idx_tipo` (`tipo`)
) ENGINE=InnoDB AUTO_INCREMENT=130 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `documentos_anexados`
--

LOCK TABLES `documentos_anexados` WRITE;
/*!40000 ALTER TABLE `documentos_anexados` DISABLE KEYS */;
INSERT INTO `documentos_anexados` VALUES (111,'inscricao',76,'foto_3x4','uploads/documentos/doc_foto_3x4_69c1767ced968.jpg','WhatsApp Image 2025-03-30 at 17.24.20.jpeg','validado','Validado pelo administrador.','2026-03-23 17:00:47','2026-03-23 17:21:13'),(112,'inscricao',76,'matricula','uploads/documentos/doc_matricula_69c171bff3530.png','file_2025-04-15_19.02.13.png','validado','Validado pelo administrador.','2026-03-23 17:00:47','2026-03-23 17:01:33'),(113,'inscricao',76,'rg_frente','uploads/documentos/doc_rg_frente_69c171c0003e7.jpg','1ebdme.jpg','validado','Validado pelo administrador.','2026-03-23 17:00:48','2026-03-23 17:01:35'),(114,'inscricao',76,'rg_verso','uploads/documentos/doc_rg_verso_69c171c001068.jpg','1ebdme.jpg','validado','Validado pelo administrador.','2026-03-23 17:00:48','2026-03-23 17:01:34'),(115,'inscricao',76,'pagamento','uploads/documentos/doc_pagamento_69c171d14ff29.jpeg','WhatsApp Image 2025-04-22 at 14.07.05.jpeg','validado','Validado pelo administrador.','2026-03-23 17:01:05','2026-03-23 17:01:32'),(116,'inscricao',77,'foto_3x4','uploads/documentos/doc_foto_3x4_69c1ac9966550.jpg','159c0f7037c7c6e682579398bb84868f6ca63186_hq.jpg','validado','Validado pelo administrador.','2026-03-23 21:11:53','2026-03-23 21:43:29'),(117,'inscricao',77,'matricula','uploads/documentos/doc_matricula_69c1b41e8e39d.jpg','channels4_profile.jpg','validado','Validado pelo administrador.','2026-03-23 21:11:53','2026-03-23 21:44:23'),(118,'inscricao',77,'rg_frente','uploads/documentos/doc_rg_frente_69c1ac9968736.jpg','1ebdm.jpg','validado','Validado pelo administrador.','2026-03-23 21:11:53','2026-03-23 21:43:37'),(119,'inscricao',77,'rg_verso','uploads/documentos/doc_rg_verso_69c1ac9969e55.jpg','1ebdme.jpg','validado','Validado pelo administrador.','2026-03-23 21:11:53','2026-03-23 21:43:36'),(120,'inscricao',77,'pagamento','uploads/documentos/doc_pagamento_69c1acc0c25de.png','file_2025-04-15_19.02.13.png','validado','Validado pelo administrador.','2026-03-23 21:12:32','2026-03-23 21:43:34'),(121,'inscricao',78,'foto_3x4','uploads/documentos/doc_foto_3x4_69c255ded231f.png','vivenciar_logov2.png','validado',NULL,'2026-03-24 09:14:06','2026-03-24 09:14:06'),(122,'inscricao',78,'rg_frente','uploads/documentos/doc_rg_frente_69c255ded5470.jpg','channels4_profile.jpg','validado',NULL,'2026-03-24 09:14:06','2026-03-24 09:14:06'),(123,'inscricao',78,'rg_verso','uploads/documentos/doc_rg_verso_69c255ded7789.jpg','159c0f7037c7c6e682579398bb84868f6ca63186_hq.jpg','validado',NULL,'2026-03-24 09:14:06','2026-03-24 09:14:06'),(124,'inscricao',78,'matricula','uploads/documentos/doc_matricula_69c255ded9caf.jpg','1ebdm.jpg','validado',NULL,'2026-03-24 09:14:06','2026-03-24 09:14:06'),(125,'inscricao',78,'pagamento','uploads/documentos/doc_pagamento_69c2564a42a09.jpg','channels4_profile.jpg','validado','Validado pelo administrador.','2026-03-24 09:15:54','2026-03-24 09:18:02'),(126,'inscricao',78,'pagamento','uploads/documentos/doc_pagamento_69c256aaa49c3.jpg','1ebdme.jpg','validado','Validado pelo administrador.','2026-03-24 09:17:30','2026-03-24 09:17:57'),(127,'inscricao',79,'matricula','uploads/documentos/doc_matricula_69d13c1825908.png','IMG_2819.png','pendente',NULL,'2026-04-04 16:28:08','2026-04-04 16:28:08'),(128,'inscricao',79,'cnh_frente','uploads/documentos/doc_cnh_frente_69d13c18273a4.png','IMG_2820.png','pendente',NULL,'2026-04-04 16:28:08','2026-04-04 16:28:08'),(129,'inscricao',79,'cnh_verso','uploads/documentos/doc_cnh_verso_69d13c18297b1.png','IMG_2865.png','pendente',NULL,'2026-04-04 16:28:08','2026-04-04 16:28:08');
/*!40000 ALTER TABLE `documentos_anexados` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `estudantes`
--

DROP TABLE IF EXISTS `estudantes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `estudantes` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nome` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `data_nascimento` date NOT NULL,
  `cpf` varchar(14) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `documento_tipo` enum('RG','CNH','PASSAPORTE') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `documento_numero` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `documento_orgao` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `instituicao_id` int NOT NULL,
  `campus` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `curso` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `nivel` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `matricula` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `situacao_academica` enum('Matriculado','Trancado','Formado','Cancelado') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 'Matriculado',
  `status_validacao` enum('pendente','dados_aprovados') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 'pendente',
  `email` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `telefone` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `criado_em` datetime DEFAULT CURRENT_TIMESTAMP,
  `atualizado_em` datetime DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `cpf` (`cpf`)
) ENGINE=InnoDB AUTO_INCREMENT=23 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `estudantes`
--

LOCK TABLES `estudantes` WRITE;
/*!40000 ALTER TABLE `estudantes` DISABLE KEYS */;
INSERT INTO `estudantes` VALUES (20,'eliabe teste publico','2026-03-23','12345678912','RG','1234567','ssds',3,'testepublico','testepublico','testepublico','testepublico','Matriculado','dados_aprovados','testepublico@gmail.com','81988696633','2026-03-23 18:11:53','2026-03-23 18:44:23'),(21,'teste uber cedo','1999-03-20','12232112222','RG','4146797','ssds',1,'ube','uber','uber','uber20conto','Matriculado','dados_aprovados','uber@uber','(84) 955668899','2026-03-24 06:14:06',NULL),(22,'Luan Santana Cosme Damião','1986-04-16','12343210998','CNH','12233444','Detran',1,'Monteiro','Engenharia','Superior','0000001','Matriculado','pendente','leandrolevy.edu01@gmail.com','83999561503','2026-04-04 13:28:08',NULL);
/*!40000 ALTER TABLE `estudantes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `inscricoes`
--

DROP TABLE IF EXISTS `inscricoes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `inscricoes` (
  `id` int NOT NULL AUTO_INCREMENT,
  `estudante_id` int NOT NULL,
  `codigo_inscricao` varchar(36) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `data_emissao` date DEFAULT (curdate()),
  `data_validade` date NOT NULL,
  `situacao` enum('aguardando_validacao','dados_aprovados','pagamento_pendente','documentos_anexados','pago','cie_emitida','cie_emitida_aguardando_entrega','cie_entregue_na_instituicao') COLLATE utf8mb4_unicode_ci DEFAULT 'aguardando_validacao',
  `carteirinha_atual_id` int DEFAULT NULL,
  `assinatura_digital` text COLLATE utf8mb4_unicode_ci,
  `dados_assinatura` json DEFAULT NULL,
  `certificado_thumbprint` varchar(64) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `data_emissao_cie` datetime DEFAULT NULL,
  `criado_em` datetime DEFAULT CURRENT_TIMESTAMP,
  `pagamento_confirmado` tinyint(1) NOT NULL DEFAULT '0',
  `origem` enum('estudante','administrador') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'estudante',
  `matricula_validada` tinyint(1) NOT NULL DEFAULT '0',
  `foto_documento_id` int DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `cie_codigo` (`codigo_inscricao`),
  KEY `estudante_id` (`estudante_id`),
  KEY `fk_inscricao_foto_documento` (`foto_documento_id`),
  KEY `idx_inscricao_carteirinha` (`carteirinha_atual_id`),
  CONSTRAINT `fk_inscricao_foto_documento` FOREIGN KEY (`foto_documento_id`) REFERENCES `documentos_anexados` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `inscricoes_ibfk_1` FOREIGN KEY (`estudante_id`) REFERENCES `estudantes` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=80 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `inscricoes`
--

LOCK TABLES `inscricoes` WRITE;
/*!40000 ALTER TABLE `inscricoes` DISABLE KEYS */;
INSERT INTO `inscricoes` VALUES (77,20,'2ebf3df0-fe55-41ba-a7c8-6825ca43ff1a','2026-03-23','2027-03-31','cie_emitida_aguardando_entrega',NULL,NULL,NULL,NULL,NULL,'2026-03-23 18:11:53',1,'estudante',1,NULL),(78,21,'72fff922-8784-4e9e-bfbb-799c61b19537','2026-03-24','2027-03-31','cie_emitida_aguardando_entrega',NULL,NULL,NULL,NULL,NULL,'2026-03-24 06:14:06',1,'administrador',1,NULL),(79,22,'18407b64-bfa4-4fcc-a7d4-d76ea2141945','2026-04-04','2027-03-31','aguardando_validacao',NULL,NULL,NULL,NULL,NULL,'2026-04-04 13:28:08',0,'estudante',0,NULL);
/*!40000 ALTER TABLE `inscricoes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `instituicoes`
--

DROP TABLE IF EXISTS `instituicoes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `instituicoes` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nome` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `endereco` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `cidade` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `estado` char(2) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `cep` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` enum('ativa','inativa') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'ativa',
  `criado_em` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `atualizado_em` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `instituicoes`
--

LOCK TABLES `instituicoes` WRITE;
/*!40000 ALTER TABLE `instituicoes` DISABLE KEYS */;
INSERT INTO `instituicoes` VALUES (1,'teste 1','teste endereco','Recife','PE','50800250','inativa','2026-02-09 19:32:02','2026-04-06 17:24:41'),(2,'testeMV','testeMV','testeMV','TE','','inativa','2026-02-10 02:11:24','2026-03-16 15:50:29'),(3,'UFPE','rua da ufpe','RECIFE','PE','20800650','inativa','2026-03-16 15:50:23','2026-04-06 17:24:44'),(4,'Centro Brasileiro Integrado de Educação (CBIE)','Q 104 Recanto das Emas lote 17 Loja 01 - Recanto das Emas, Brasília','Brasília','DF','72600400','ativa','2026-04-06 17:24:36','2026-04-06 17:24:36'),(5,'Aprova Nexus','Av. Rio Grande do Sul, 1599 - Estados, João Pessoa','João Pessoa','PB','58030021','ativa','2026-04-06 17:25:23','2026-04-06 17:25:23'),(6,'Instituto Nacional de EJA e Inovação Tecnológica (INET)','R. Dr. Carlos Augusto de Campos, 133 - Santo Amaro','São Paulo','SP','04750060','ativa','2026-04-06 17:26:07','2026-04-06 17:26:07'),(7,'Ivy Enber Christian University','4725 Sand Lake Rd #203','Orlando','FL','32819','ativa','2026-04-06 17:26:44','2026-04-06 17:26:44'),(8,'Supletivo Norte Brasil','Av. José Amador dos Reis, 369, Tancredo Neves','Porto Velho','RO','76829580','ativa','2026-04-06 17:27:20','2026-04-06 17:27:20');
/*!40000 ALTER TABLE `instituicoes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `logistica_entregas`
--

DROP TABLE IF EXISTS `logistica_entregas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `logistica_entregas` (
  `id` int NOT NULL AUTO_INCREMENT,
  `inscricao_id` int NOT NULL,
  `instituicao_id` int NOT NULL,
  `status` enum('saida_para_entrega','entregue_na_instituicao') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'saida_para_entrega',
  `responsavel_saida` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `data_saida` datetime DEFAULT NULL,
  `responsavel_entrega` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `data_entrega_instituicao` datetime DEFAULT NULL,
  `observacoes` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `registrado_por` int DEFAULT NULL,
  `criado_em` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `atualizado_em` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `inscricao_id` (`inscricao_id`),
  KEY `instituicao_id` (`instituicao_id`),
  KEY `registrado_por` (`registrado_por`),
  CONSTRAINT `logistica_entregas_ibfk_1` FOREIGN KEY (`inscricao_id`) REFERENCES `inscricoes` (`id`) ON DELETE CASCADE,
  CONSTRAINT `logistica_entregas_ibfk_2` FOREIGN KEY (`instituicao_id`) REFERENCES `instituicoes` (`id`) ON DELETE CASCADE,
  CONSTRAINT `logistica_entregas_ibfk_3` FOREIGN KEY (`registrado_por`) REFERENCES `usuarios` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `logistica_entregas`
--

LOCK TABLES `logistica_entregas` WRITE;
/*!40000 ALTER TABLE `logistica_entregas` DISABLE KEYS */;
/*!40000 ALTER TABLE `logistica_entregas` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `logs`
--

DROP TABLE IF EXISTS `logs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `logs` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int DEFAULT NULL,
  `acao` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `descricao` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `registro_id` int DEFAULT NULL,
  `tabela` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `data_hora` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `logs_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=227 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `logs`
--

LOCK TABLES `logs` WRITE;
/*!40000 ALTER TABLE `logs` DISABLE KEYS */;
INSERT INTO `logs` VALUES (174,NULL,'inscricao_publica_realizada','Estudante: Eliabe teste public, CPF: 12297095414, Código Inscrição: d31a40ce-5b73-42de-abd1-2e8809c20550',76,'inscricoes','2026-03-23 14:00:48'),(175,1,'admin_validou_comprovante_pagamento','Inscrição ID: 76 - Pagamento confirmado via validação manual',76,'inscricoes','2026-03-23 14:01:32'),(176,1,'estudante_aprovado_automatico','Estudante ID: 19 aprovado automaticamente após validação total dos documentos da inscrição 76',19,'estudantes','2026-03-23 14:21:13'),(177,1,'cie_digital_emitida','Inscrição ID: 76, CIE Código: d8d416db-1f6d-41c4-8753-b396f4a1d467',76,'inscricoes','2026-03-23 15:25:43'),(178,1,'cie_digital_emitida','Inscrição ID: 76, CIE Código: 03799dc9-8ab0-44b9-b707-1bf7a2c875db',76,'inscricoes','2026-03-23 15:55:10'),(179,1,'cie_digital_emitida','Inscrição ID: 76, CIE Código: 6ab6e303-bdc4-4e7a-84ad-708215da008c',76,'inscricoes','2026-03-23 15:56:00'),(180,1,'cie_digital_emitida','Inscrição ID: 76, CIE Código: 7f0cb763-b704-4cc9-9117-2399c03b6f43',76,'inscricoes','2026-03-23 15:58:57'),(181,1,'cie_digital_emitida','Inscrição ID: 76, CIE Código: 8434ddba-826a-428d-9189-2d77c0040ac3',76,'inscricoes','2026-03-23 15:59:28'),(182,1,'cie_digital_emitida','Inscrição ID: 76, CIE Código: bbae7d52-3c48-47f2-b070-515d243847b3',76,'inscricoes','2026-03-23 16:29:08'),(183,1,'cie_digital_emitida','Inscrição ID: 76, CIE Código: 57a59537-9ae5-40a3-bcd8-f6f02658f755',76,'inscricoes','2026-03-23 16:30:12'),(184,1,'cie_digital_emitida','Inscrição ID: 76, CIE Código: 399f22fa-1306-4611-80e8-093609deaa1f',76,'inscricoes','2026-03-23 16:43:32'),(185,1,'login_realizado','Usuário \'hants\' realizou login.',NULL,'sessoes','2026-03-23 18:05:02'),(186,1,'excluiu_estudante','ID: 19, Nome: Eliabe teste public',19,'estudantes','2026-03-23 18:10:35'),(187,NULL,'inscricao_publica_realizada','Estudante: eliabe teste publico, CPF: 12345678912, Código Inscrição: 2ebf3df0-fe55-41ba-a7c8-6825ca43ff1a',77,'inscricoes','2026-03-23 18:11:53'),(188,1,'login_realizado','Usuário \'hants\' realizou login.',NULL,'sessoes','2026-03-23 18:43:15'),(189,1,'admin_validou_comprovante_pagamento','Inscrição ID: 77 - Pagamento confirmado via validação manual',77,'inscricoes','2026-03-23 18:43:34'),(190,1,'estudante_aprovado_automatico','Estudante ID: 20 aprovado automaticamente após validação total dos documentos da inscrição 77',20,'estudantes','2026-03-23 18:44:23'),(191,1,'login_realizado','Usuário \'hants\' realizou login.',NULL,'sessoes','2026-03-23 20:18:19'),(192,1,'cie_digital_emitida','Inscrição ID: 77, CIE Código: c5a80fb9-53cc-4bb2-bbb0-164ddbe5c968',77,'inscricoes','2026-03-23 20:23:18'),(193,1,'login_realizado','Usuário \'hants\' realizou login.',NULL,'sessoes','2026-03-24 06:11:42'),(194,1,'anexou_e_validou_comprovante_matricula_admin','Inscrição ID: 78, Estudante ID: 21, Origem: administrador',78,'inscricoes','2026-03-24 06:14:06'),(195,1,'admin_validou_comprovante_pagamento','Inscrição ID: 78 - Pagamento confirmado via validação manual',78,'inscricoes','2026-03-24 06:17:57'),(196,1,'admin_validou_comprovante_pagamento','Inscrição ID: 78 - Pagamento confirmado via validação manual',78,'inscricoes','2026-03-24 06:18:02'),(197,1,'cie_digital_emitida','Inscrição ID: 78, CIE Código: 72afc3d8-cdeb-498c-99d9-297d4a4fa7c1',78,'inscricoes','2026-03-24 06:18:18'),(198,8,'login_realizado','Usuário \'Angelli Costa\' realizou login.',NULL,'sessoes','2026-03-24 10:18:35'),(199,8,'login_realizado','Usuário \'Angelli Costa\' realizou login.',NULL,'sessoes','2026-03-24 10:23:28'),(200,8,'login_realizado','Usuário \'Angelli Costa\' realizou login.',NULL,'sessoes','2026-03-30 07:34:57'),(201,NULL,'inscricao_publica_realizada','Estudante: Luan Santana Cosme Damião, CPF: 12343210998, Código Inscrição: 18407b64-bfa4-4fcc-a7d4-d76ea2141945',79,'inscricoes','2026-04-04 13:28:08'),(202,8,'login_realizado','Usuário \'Angelli Costa\' realizou login.',NULL,'sessoes','2026-04-06 14:19:51'),(203,8,'criou_instituicao','Nome: Centro Brasileiro Integrado de Educação (CBIE)',4,'instituicoes','2026-04-06 14:24:36'),(204,8,'desativou_instituicao','ID: 1, Nome: teste 1',1,'instituicoes','2026-04-06 14:24:41'),(205,8,'desativou_instituicao','ID: 3, Nome: UFPE',3,'instituicoes','2026-04-06 14:24:44'),(206,8,'criou_instituicao','Nome: Aprova Nexus',5,'instituicoes','2026-04-06 14:25:23'),(207,8,'desativou_instituicao','ID: 3, Nome: UFPE',3,'instituicoes','2026-04-06 14:25:23'),(208,8,'criou_instituicao','Nome: Instituto Nacional de EJA e Inovação Tecnológica (INET)',6,'instituicoes','2026-04-06 14:26:07'),(209,8,'desativou_instituicao','ID: 3, Nome: UFPE',3,'instituicoes','2026-04-06 14:26:07'),(210,8,'criou_instituicao','Nome: Ivy Enber Christian University',7,'instituicoes','2026-04-06 14:26:44'),(211,8,'desativou_instituicao','ID: 3, Nome: UFPE',3,'instituicoes','2026-04-06 14:26:44'),(212,8,'criou_instituicao','Nome: Supletivo Norte Brasil',8,'instituicoes','2026-04-06 14:27:20'),(213,8,'desativou_instituicao','ID: 3, Nome: UFPE',3,'instituicoes','2026-04-06 14:27:20'),(214,8,'criou_usuario','Nome: Fernando (INET), Email: fernando@inetnacionaleja.com.br, Tipo: user',10,'usuarios','2026-04-06 15:10:12'),(215,8,'criou_usuario','Nome: Victor (INET), Email: victor@inetnacionaleja.com.br, Tipo: user',11,'usuarios','2026-04-06 15:10:45'),(216,8,'logout_realizado','Usuário \'Angelli Costa\' realizou logout.',NULL,'sessoes','2026-04-06 15:11:19'),(217,8,'login_realizado','Usuário \'Angelli Costa\' realizou login.',NULL,'sessoes','2026-04-06 15:11:31'),(218,10,'login_realizado','Usuário \'Fernando (INET)\' realizou login.',NULL,'sessoes','2026-04-06 15:12:21'),(219,8,'logout_realizado','Usuário \'Angelli Costa\' realizou logout.',NULL,'sessoes','2026-04-06 15:12:32'),(220,11,'login_realizado','Usuário \'Victor (INET)\' realizou login.',NULL,'sessoes','2026-04-06 15:13:28'),(221,11,'logout_realizado','Usuário \'Victor (INET)\' realizou logout.',NULL,'sessoes','2026-04-06 15:13:48'),(222,8,'login_realizado','Usuário \'Angelli Costa\' realizou login.',NULL,'sessoes','2026-04-06 15:13:50'),(223,8,'editou_usuario','ID: 10, Nome: Fernando (INET), Email: fernando@inetnacionaleja.com.br, Tipo: admin',10,'usuarios','2026-04-06 15:13:57'),(224,8,'editou_usuario','ID: 11, Nome: Victor (INET), Email: victor@inetnacionaleja.com.br, Tipo: admin',11,'usuarios','2026-04-06 15:14:06'),(225,8,'logout_realizado','Usuário \'Angelli Costa\' realizou logout.',NULL,'sessoes','2026-04-06 15:14:12'),(226,11,'login_realizado','Usuário \'Victor (INET)\' realizou login.',NULL,'sessoes','2026-04-06 15:14:23');
/*!40000 ALTER TABLE `logs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `usuarios`
--

DROP TABLE IF EXISTS `usuarios`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `usuarios` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nome` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `senha` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `tipo` enum('admin','user') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'user',
  `criado_em` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `usuarios`
--

LOCK TABLES `usuarios` WRITE;
/*!40000 ALTER TABLE `usuarios` DISABLE KEYS */;
INSERT INTO `usuarios` VALUES (1,'hants','hants@ciesytem.local','$2y$10$F4wcNlLHQyyn5Zj1ReSGE.aPtq.W2yzuIVdO/sMnjQG/qWjUZOWF.','admin','2025-12-17 19:13:52'),(6,'Alcimar ADM','alcimar@teste.com','$2y$10$NsGiSIn9avbH8jzVT.cTJemxIv4e3fYIhzX6pnx7/RgnRho85Nr8m','admin','2026-03-20 12:45:12'),(7,'Joao Eduardo','joaoeduardo@aprovanexus.com.br','$2y$10$l3f87RMLjYfwwckgu94seOTpMrO5kunsKfO8G8alqMrhNMCFn1Og2','admin','2026-03-23 11:23:23'),(8,'Angelli Costa','angellicosta@enberuniversity.com','$2y$10$wH.D7UjJVFUbDwj8f2P8D.yvbWI7Xk9EPPTXxx2TkVy9bcwX9LAf6','admin','2026-03-23 11:24:05'),(9,'Camilla Elloy','camillaeloy@aprovanexus.com.br','$2y$10$RFJOtM6GlG0uOm54aFzhWuS1uppnhWOMACXebKNxeIWrreP6MWT0S','admin','2026-03-23 11:24:40'),(10,'Fernando (INET)','fernando@inetnacionaleja.com.br','$2y$10$mO99zkhlFib.N9n2Xjl11.n0wW8yZ1xzlPqw64M8oGxHmlwuIc7D6','admin','2026-04-06 15:10:12'),(11,'Victor (INET)','victor@inetnacionaleja.com.br','$2y$10$9sMqEDw.zvMUYJapRFStTedPVJrxxOwXKgHw5yNWGW5jQPEModmHa','admin','2026-04-06 15:10:45');
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

-- Dump completed on 2026-04-06 18:51:38
