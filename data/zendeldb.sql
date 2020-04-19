-- MySQL dump 10.13  Distrib 8.0.12, for Linux (x86_64)
--
-- Host: localhost    Database: zendel
-- ------------------------------------------------------
-- Server version	8.0.12

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
 SET NAMES utf8mb4 ;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `departament`
--

DROP TABLE IF EXISTS `departament`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
 SET character_set_client = utf8mb4 ;
CREATE TABLE `departament` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `status` tinyint(1) DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `departament`
--

LOCK TABLES `departament` WRITE;
/*!40000 ALTER TABLE `departament` DISABLE KEYS */;
INSERT INTO `departament` VALUES (1,'Suporte',1),(2,'Comercial',1),(3,'Financeiro',1),(4,'Administrativo',1),(5,'Produto',1),(6,'Recursos Humanos',1),(7,'Compras',1),(8,'Controladoria',1),(9,'CRM',1);
/*!40000 ALTER TABLE `departament` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `deployment_schedule`
--

DROP TABLE IF EXISTS `deployment_schedule`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
 SET character_set_client = utf8mb4 ;
CREATE TABLE `deployment_schedule` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_client` int(11) NOT NULL,
  `id_attendance_crm` int(11) DEFAULT NULL,
  `id_last_ds` int(11) DEFAULT NULL,
  `client_name` varchar(255) DEFAULT NULL,
  `date` date NOT NULL,
  `time` time NOT NULL,
  `time_end` time DEFAULT NULL,
  `status` tinyint(1) DEFAULT '1',
  `obs` text,
  `created` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `started` datetime DEFAULT NULL,
  `updated` datetime DEFAULT NULL,
  `finished` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_id_ds_ds_id_idx` (`id_last_ds`),
  CONSTRAINT `fk_id_ds_ds_id` FOREIGN KEY (`id_last_ds`) REFERENCES `deployment_schedule` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `deployment_schedule`
--

LOCK TABLES `deployment_schedule` WRITE;
/*!40000 ALTER TABLE `deployment_schedule` DISABLE KEYS */;
/*!40000 ALTER TABLE `deployment_schedule` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Temporary view structure for view `formulario_respostas`
--

DROP TABLE IF EXISTS `formulario_respostas`;
/*!50001 DROP VIEW IF EXISTS `formulario_respostas`*/;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8mb4;
/*!50001 CREATE VIEW `formulario_respostas` AS SELECT 
 1 AS `name`,
 1 AS `label`,
 1 AS `value`,
 1 AS `schedule`,
 1 AS `formulario`,
 1 AS `atendimento`*/;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `job`
--

DROP TABLE IF EXISTS `job`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
 SET character_set_client = utf8mb4 ;
CREATE TABLE `job` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `id_top_job` int(11) DEFAULT NULL,
  `status` tinyint(4) DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `job`
--

LOCK TABLES `job` WRITE;
/*!40000 ALTER TABLE `job` DISABLE KEYS */;
INSERT INTO `job` VALUES (1,'Diretor de Tecnologia',0,1),(2,'Gerente Geral do Suporte',1,1),(3,'Gerente de Suporte',2,1),(4,'Téccnico de Suporte',3,1),(5,'Técnico de Implantação',3,1);
/*!40000 ALTER TABLE `job` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `mail_template`
--

DROP TABLE IF EXISTS `mail_template`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
 SET character_set_client = utf8mb4 ;
CREATE TABLE `mail_template` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `header` mediumtext,
  `content` mediumtext,
  `footer` mediumtext,
  `image` varchar(255) DEFAULT NULL,
  `status` tinyint(1) DEFAULT '1',
  `identifier` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `mail_template`
--

LOCK TABLES `mail_template` WRITE;
/*!40000 ALTER TABLE `mail_template` DISABLE KEYS */;
INSERT INTO `mail_template` VALUES (1,'Recuperar de Senha','&lt;h2 style=&quot;font-family: Roboto;&quot;&gt;&lt;span rgb(255,=&quot;&quot; 0,=&quot;&quot; 0);&amp;quot;=&quot;&quot; style=&quot;color: rgb(255, 0, 0);&quot;&gt;Ol&aacute; {{nome}}&lt;/span&gt;&lt;/h2&gt;','&lt;p&gt;Por favor, clique no link abaixo para resetar a senha de acesso:&lt;/p&gt;&lt;p&gt;&lt;a href=&quot;http://{{recovery_link}}&quot;&gt;Clique aqui&lt;/a&gt;&lt;/p&gt;&lt;p&gt;Se voc&ecirc; n&atilde;o solicitou a mudan&ccedil;a de senha, ignore essa mensagem.&lt;/p&gt;&lt;p&gt;Favor n&atilde;o responder este e-mail, pois a caixa de e-mail de resposta no verificada.&lt;/p&gt;','&lt;p&gt;&lt;span style=&quot;font-weight: 700;&quot;&gt;Atenciosamente,&lt;/span&gt;&lt;/p&gt;&lt;p&gt;&lt;span style=&quot;font-weight: 700;&quot;&gt;Windel Sistemas&lt;/span&gt;&lt;/p&gt;&lt;p&gt;&lt;span style=&quot;font-weight: 700;&quot;&gt;&lt;br&gt;&lt;/span&gt;&lt;/p&gt;&lt;p&gt;Windel Sistemas Ltda&lt;/p&gt;&lt;p&gt;Rua Tupy, 91 , Bairro: Pio X, Caxias do Sul - RS, CEP: 95034-520&lt;/p&gt;&lt;p&gt;Fone: (54) 3025-2540 | 0800 600 2220 | E-mail: comercial@windel.com.br&lt;/p&gt;','phpMpyphz_5aea1e4beb05b6_81354486.jpg',1,'recovery_mail'),(2,'Confirmar implantação','&lt;p&gt;Ol&aacute; {{client_name}},&lt;/p&gt;&lt;p&gt;&lt;br&gt;&lt;/p&gt;','&lt;p&gt;Confirmamos que a sua implanta&ccedil;&atilde;o ser&aacute; realizada no dia {{implantation_date}} &aacute;s {{implantation_hour}}.&lt;/p&gt;&lt;p&gt;Fique preparado para receber a nossa equipe de implanta&ccedil;&atilde;o, mas caso n&atilde;o possa realizar no dia, voc&ecirc; pode cancelar a implanta&ccedil;&atilde;o e remarcar para outra data. Voc&ecirc; pode fazer isso atrav&eacute;s da nossa &aacute;rea at&eacute; o dia {{cancelamento_date}}.&lt;/p&gt;','&lt;p&gt;&lt;span style=&quot;font-weight: 700;&quot;&gt;Atenciosamente,&lt;/span&gt;&lt;/p&gt;&lt;p&gt;&lt;span style=&quot;font-weight: 700;&quot;&gt;Windel Sistemas&lt;/span&gt;&lt;/p&gt;&lt;p&gt;&lt;span style=&quot;font-weight: 700;&quot;&gt;&lt;br&gt;&lt;/span&gt;&lt;/p&gt;&lt;p&gt;Windel Sistemas Ltda&lt;/p&gt;&lt;p&gt;Rua Tupy, 91 , Bairro: Pio X, Caxias do Sul - RS, CEP: 95034-520&lt;/p&gt;&lt;p&gt;Fone: (54) 3025-2540 | 0800 600 2220 | E-mail: comercial@windel.com.br&lt;/p&gt;',NULL,1,'confirmar_implantacao'),(3,'Pausa de Implantação','&lt;p&gt;Ol&aacute; {{cliente_nome}},&lt;br&gt;&lt;/p&gt;','&lt;p&gt;Informamos que a sua implanta&ccedil;&atilde;o foi pausada.&lt;/p&gt;&lt;p&gt;Ser&aacute; retornado no dia {{implantation_date}}.&lt;/p&gt;','&lt;p&gt;&lt;span style=&quot;font-weight: 700;&quot;&gt;Atenciosamente,&lt;/span&gt;&lt;/p&gt;&lt;p&gt;&lt;span style=&quot;font-weight: 700;&quot;&gt;Windel Sistemas&lt;/span&gt;&lt;/p&gt;&lt;p&gt;&lt;span style=&quot;font-weight: 700;&quot;&gt;&lt;br&gt;&lt;/span&gt;&lt;/p&gt;&lt;p&gt;Windel Sistemas Ltda&lt;/p&gt;&lt;p&gt;Rua Tupy, 91 , Bairro: Pio X, Caxias do Sul - RS, CEP: 95034-520&lt;/p&gt;&lt;p&gt;Fone: (54) 3025-2540 | 0800 600 2220 | E-mail: comercial@windel.com.br&lt;/p&gt;',NULL,1,'pausar_implantacao'),(4,'Finalizar implantação','&lt;p&gt;Ol&aacute; {{cliente_nome}},&lt;/p&gt;','&lt;p&gt;Informamos que a sua implanta&ccedil;&atilde;o foi finalizada no dia {{implantacao_dia}} &aacute;s {{implantacao_hora}}.&lt;/p&gt;&lt;p&gt;Voc&ecirc; pode verificar o resumo da implanta&ccedil;&atilde;o no link abaixo:&lt;/p&gt;&lt;p&gt;&lt;a href=&quot;http://{{resumo_link}}&quot; target=&quot;_blank&quot;&gt;Resumo da sua implanta&ccedil;&atilde;o&lt;/a&gt;&lt;/p&gt;&lt;p&gt;&lt;br&gt;&lt;/p&gt;','&lt;p&gt;&lt;span style=&quot;font-weight: 700;&quot;&gt;Atenciosamente,&lt;/span&gt;&lt;/p&gt;&lt;p&gt;&lt;span style=&quot;font-weight: 700;&quot;&gt;Windel Sistemas&lt;/span&gt;&lt;/p&gt;&lt;p&gt;&lt;span style=&quot;font-weight: 700;&quot;&gt;&lt;br&gt;&lt;/span&gt;&lt;/p&gt;&lt;p&gt;Windel Sistemas Ltda&lt;/p&gt;&lt;p&gt;Rua Tupy, 91 , Bairro: Pio X, Caxias do Sul - RS, CEP: 95034-520&lt;/p&gt;&lt;p&gt;Fone: (54) 3025-2540 | 0800 600 2220 | E-mail: comercial@windel.com.br&lt;/p&gt;',NULL,1,'finalizar_implantacao');
/*!40000 ALTER TABLE `mail_template` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `migrations`
--

DROP TABLE IF EXISTS `migrations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
 SET character_set_client = utf8mb4 ;
CREATE TABLE `migrations` (
  `version` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`version`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `migrations`
--

LOCK TABLES `migrations` WRITE;
/*!40000 ALTER TABLE `migrations` DISABLE KEYS */;
INSERT INTO `migrations` VALUES ('20161209132215');
/*!40000 ALTER TABLE `migrations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `organization`
--

DROP TABLE IF EXISTS `organization`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
 SET character_set_client = utf8mb4 ;
CREATE TABLE `organization` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `status` tinyint(1) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `organization`
--

LOCK TABLES `organization` WRITE;
/*!40000 ALTER TABLE `organization` DISABLE KEYS */;
INSERT INTO `organization` VALUES (1,'Unidade Caxias do Sul',1),(2,'Unidade Passo Fundo',1),(3,'Unidade Salvador',0);
/*!40000 ALTER TABLE `organization` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `organization_departament`
--

DROP TABLE IF EXISTS `organization_departament`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
 SET character_set_client = utf8mb4 ;
CREATE TABLE `organization_departament` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_organization` int(11) NOT NULL,
  `id_departament` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_organization_departament_id_organization_organization_idx` (`id_organization`),
  KEY `fk_organization_departament_id_departament_departament_idx` (`id_departament`),
  CONSTRAINT `fk_organization_departament_id_departament_departament_id` FOREIGN KEY (`id_departament`) REFERENCES `departament` (`id`),
  CONSTRAINT `fk_organization_departament_id_organization_organization_id` FOREIGN KEY (`id_organization`) REFERENCES `organization` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `organization_departament`
--

LOCK TABLES `organization_departament` WRITE;
/*!40000 ALTER TABLE `organization_departament` DISABLE KEYS */;
/*!40000 ALTER TABLE `organization_departament` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `organization_office_hour`
--

DROP TABLE IF EXISTS `organization_office_hour`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
 SET character_set_client = utf8mb4 ;
CREATE TABLE `organization_office_hour` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_organization` int(11) NOT NULL,
  `day` enum('domingo','segunda','terca','quarta','quinta','sexta','sabado') DEFAULT NULL,
  `morning_start_time` time DEFAULT NULL,
  `morning_closing_time` time DEFAULT NULL,
  `afternoon_start_time` time DEFAULT NULL,
  `afternoon_closing_time` time DEFAULT NULL,
  `status_hour` tinyint(1) DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `organization_office_hour`
--

LOCK TABLES `organization_office_hour` WRITE;
/*!40000 ALTER TABLE `organization_office_hour` DISABLE KEYS */;
INSERT INTO `organization_office_hour` VALUES (6,1,'segunda','08:00:00','11:48:00','13:30:00','18:30:00',1),(7,1,'terca','08:00:00','11:48:00','13:30:00','18:30:00',1),(8,1,'quarta','08:00:00','11:48:00','13:30:00','18:30:00',1),(9,1,'quinta','08:00:00','11:48:00','13:30:00','18:30:00',1),(10,1,'sexta','08:00:00','11:48:00','13:30:00','18:30:00',1);
/*!40000 ALTER TABLE `organization_office_hour` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `permission`
--

DROP TABLE IF EXISTS `permission`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
 SET character_set_client = utf8mb4 ;
CREATE TABLE `permission` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(128) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `description` varchar(1024) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `date_created` datetime NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name_idx` (`name`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `permission`
--

LOCK TABLES `permission` WRITE;
/*!40000 ALTER TABLE `permission` DISABLE KEYS */;
INSERT INTO `permission` VALUES (1,'role.manage','Gerenciar cadastro de papéis de sistema.','2018-04-18 15:42:23'),(2,'permission.manage','Gerenciar cadastros de permissões de usuários.','2018-04-18 16:06:47'),(3,'mail-template.manage','Gerenciar templates de e-mail','2018-04-24 11:43:20'),(4,'implantation.manage','Gerenciamento de contratos para Implantação','2018-05-10 09:40:33'),(5,'quiz.manage','Gerenciamento de Cadastros de Formulários','2018-07-04 09:39:55'),(6,'basic.manage','Gerenciamento de cadastros básicos.','2018-07-26 16:28:36');
/*!40000 ALTER TABLE `permission` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `produto`
--

DROP TABLE IF EXISTS `produto`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
 SET character_set_client = utf8mb4 ;
CREATE TABLE `produto` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nome` varchar(255) NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `produto`
--

LOCK TABLES `produto` WRITE;
/*!40000 ALTER TABLE `produto` DISABLE KEYS */;
INSERT INTO `produto` VALUES (1,'01 - Windel Compacto ME',1),(2,'02 - Windel NF ME',1),(3,'03 - Windel CF ME',1),(4,'04 - Windel Nota Fiscal',1),(5,'05 - Windel Cupom Fiscal',1),(6,'06 - Windel Cupom Fiscal e Nota Fiscal',1),(7,'08 - Manufatura',1),(8,'09 - Receituário Agrícola',1),(9,'9 - Qualidade',1),(10,'10 - Orçamentos e Pedidos',1),(11,'07 - Windel NFSe ME',1),(12,'11 - NFSe',1),(13,'12 - NF-e',1),(14,'13 - Importador XML',1),(15,'Todos',1),(16,'Nenhum',1);
/*!40000 ALTER TABLE `produto` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `question`
--

DROP TABLE IF EXISTS `question`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
 SET character_set_client = utf8mb4 ;
CREATE TABLE `question` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_question_form` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `type` int(11) NOT NULL,
  `sequence` int(11) DEFAULT '1',
  `required` tinyint(1) DEFAULT '0',
  `status` tinyint(1) DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `fk_id_question_form_idx` (`id_question_form`),
  CONSTRAINT `fk_id_question_form` FOREIGN KEY (`id_question_form`) REFERENCES `question_form` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=69 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `question`
--

LOCK TABLES `question` WRITE;
/*!40000 ALTER TABLE `question` DISABLE KEYS */;
INSERT INTO `question` VALUES (1,1,'1') Regime Tributário',5,1,0,1),(2,1,'2) O sistema será instalado em mais de um computador?',5,2,0,1),(3,1,'3) Se o sistema for instalado em mais de um computador responda:',0,3,0,1),(4,1,'Quantos?',2,4,0,1),(5,1,'Os computadores estão em rede?',5,5,0,1),(6,1,'A rede é',5,6,0,1),(7,1,'4) Qual o sistema operacional do servidor?',5,7,0,1),(8,1,'O servidor é dedicado?',5,8,0,1),(9,1,'Nome ou IP do servidor',2,9,0,1),(10,1,'Usuário do servidor:',2,10,0,1),(11,1,'Senha do servidor (caso houver)',2,11,0,1),(12,1,'5) Quantos funcionários deverão ser treinados?',1,12,0,1),(13,1,'Cite os nomes dos funcionários',2,13,0,1),(14,1,'6) Quais documentos fiscais irá utilizar?',5,14,0,1),(15,1,'Se emitir documento eletrônico responda:',0,15,0,1),(16,1,'Quantas EFC\'s?',1,16,0,1),(17,1,'Quais modelos?',2,17,0,1),(18,1,'Comunicação:',5,18,0,1),(19,1,'O computador possui porta disponível?',2,19,0,1),(20,1,'Se emitir documento eletrônico responda:',0,20,0,1),(21,1,'O certificado digital é:',5,21,0,1),(22,1,'Emissor:',5,22,0,1),(23,1,'Outros',2,23,0,1),(24,1,'7) Utiliza impressora não fiscal?',5,24,0,1),(25,1,'Se a resposta for Sim, responda',0,25,0,1),(26,1,'Quantas?',1,26,0,1),(27,1,'Quais modelos?',2,27,0,1),(28,1,'Comunicação',5,28,0,1),(29,1,'O computador possui porta disponível?',2,29,0,1),(30,1,'8) Gera comissão?',5,30,0,1),(31,1,'Se a resposta for Sim, responda:',5,31,0,1),(32,1,'Quais documentos irão gerar comissão?',2,32,0,1),(33,1,'Observação (Descreva como é feito o processo)',3,33,0,1),(34,1,'9) Utiliza impressão de etiquetas?',5,34,0,1),(35,1,'Se a resposta for Sim, responda:',0,35,0,1),(36,1,'Possui impressora térmica?',5,36,0,1),(37,1,'Qual modelo?',2,37,0,1),(38,1,'Obs: O sistema WINDEL é compatível com impressoras Argox de linguagem PPLA, outras marcas é necessário fazer testes de compatibilidade',0,38,0,1),(39,1,'10) Informações financeiras',0,39,0,1),(40,1,'Emissão de boletos',5,40,0,1),(41,1,'Se a resposta for Sim, responda:',0,41,0,1),(42,1,'A impressão dos boletos será feita pelo:',5,42,0,1),(43,1,'O tipo de cobrança é',5,43,0,1),(44,1,'11) Controla estoque?',5,44,0,1),(45,1,'Se a resposta for Sim, responda',0,45,0,1),(46,1,'A baixa de estoque será feita pela:',5,46,0,1),(47,1,'A baixa do estoque será feita por qual documento?',5,47,0,1),(48,1,'12) Faz integração com o sistema contábil?',5,48,0,1),(49,1,'Se a resposta for Sim, responda:',0,49,0,1),(50,1,'Escritório contábil:',2,50,0,1),(51,1,'Nome do contador:',2,51,0,1),(52,1,'Telefone',2,52,0,1),(53,1,'Qual o sistema do escritório?',2,53,0,1),(54,1,'13) Quais os principais relatórios que costuma utilizar?',3,54,0,1),(55,1,'14) Observações Gerais:',3,55,0,1),(57,2,'Instalação do sistema',4,0,0,1),(58,3,'Sistema de backup',4,1,0,1),(59,4,'Módulo Cadastros',4,1,0,1),(60,5,'Utiliza documentos auxiliares?',5,1,0,1),(61,5,'Se a resposta for Sim, responda',4,2,0,1),(62,6,'Utiliza comissão?',5,1,0,1),(63,6,'Se a resposta for Sim, responda',4,2,0,1),(64,7,'Utiliza módulo financeiro?',5,1,0,1),(65,7,'Se a resposta for Sim, responda',4,2,0,1),(66,8,'Utiliza módulo caixa - bancos?',5,1,0,1),(67,8,'Se a resposta for Sim, responda',4,2,0,1),(68,9,'Utiliza módulo compras?',5,1,0,1);
/*!40000 ALTER TABLE `question` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `question_field`
--

DROP TABLE IF EXISTS `question_field`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
 SET character_set_client = utf8mb4 ;
CREATE TABLE `question_field` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_question` int(11) NOT NULL,
  `label` varchar(500) NOT NULL,
  `sequence` int(11) DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `fk_id_question_field_idx` (`id_question`),
  CONSTRAINT `fk_id_question_field` FOREIGN KEY (`id_question`) REFERENCES `question` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=161 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `question_field`
--

LOCK TABLES `question_field` WRITE;
/*!40000 ALTER TABLE `question_field` DISABLE KEYS */;
INSERT INTO `question_field` VALUES (1,1,'Optante pelo simples nacional',1),(2,1,'Lucro presumido',2),(3,1,'Lucro real',3),(4,2,'Não',1),(5,2,'Sim',2),(6,5,'Não',1),(7,5,'Sim',2),(8,6,'Cabeada',1),(9,6,'WIFI',2),(10,7,'Windows',1),(11,7,'Linux',2),(12,8,'Não',1),(13,8,'Sim',2),(14,14,'NF-e',1),(15,14,'NFS-e',2),(16,14,'NFC-e',3),(17,14,'NF MATRICIAL',4),(18,14,'CT-e',5),(19,14,'MDF-e',6),(20,14,'CUPOM FISCAL',7),(21,18,'USB',1),(22,18,'Serial',2),(23,21,'A1',1),(24,21,'A3 TOKEN',2),(25,21,'A3 Cartão + Leitora',3),(26,22,'Serasa',1),(27,22,'Certisign',2),(28,22,'Caixa',3),(29,22,'Safeweb',4),(30,24,'Não',1),(31,24,'Sim',2),(32,28,'USB',1),(33,28,'Serial',2),(34,30,'Não',1),(35,30,'Sim',2),(36,31,'Por tabela',1),(37,31,'Por faixa de desconto',2),(38,31,'Por vendedor',3),(39,31,'Por produto',4),(40,31,'Por cliente',5),(43,34,'Não',1),(44,34,'Sim',2),(45,36,'Não',1),(46,36,'Sim',2),(47,40,'Não',1),(48,40,'Sim',2),(49,42,'Cliente',1),(50,42,'Banco',2),(51,43,'Com registro',1),(52,43,'Sem registro',2),(53,44,'Não',1),(54,44,'Sim',2),(57,46,'Emissão',1),(58,46,'Troca de situação',2),(59,47,'Nota fiscal',1),(60,47,'Cupom Fiscal',2),(61,47,'Pedido de venda',3),(62,47,'Orçamento',4),(63,47,'Ordem de Serviço',5),(64,48,'Não',1),(65,48,'Sim',2),(77,58,'Como fazer a cópia do Sistema no computador.',1),(78,58,'Como fazer a cópia do Sistema no pendrive.',2),(79,58,'Como fazer a cópia do Sistema para o servidor da Windel.',3),(80,59,'Cadastro de Clientes',1),(81,59,'Cadastro de Fornecedores',2),(82,59,'Cadastro de Vendedores/Supervisores',3),(83,59,'Cadastro de Transportadoras',4),(84,59,'Cadastro de Funcionários',5),(85,59,'Cadastro de Ramos de Atividades',6),(86,59,'Cadastro de Condições de pagamento',7),(87,59,'Cadastro de Naturezas de Operações',8),(88,59,'Cadastro de Formas de pagamento',9),(89,59,'Cadastro de Feriados',10),(90,59,'Cadastro de Regiões',11),(91,59,'Cadastro de Classificação Fiscal',12),(92,59,'Cadastro de Unidades de medidas',13),(93,59,'Cadastro de Produtos',14),(94,59,'Cadastro de Situações Tributárias',15),(95,59,'Cadastro de Classificação dos Produtos',16),(96,59,'Cadastro de Alíquotas Internas de ICMS',17),(97,59,'Cadastro de Fatores X Unidades de Medida',18),(98,59,'Cadastro de Ajustes de Estoques',19),(99,59,'Cadastro de Grade (Refere-se a cadastro de produtos)',20),(100,60,'Não',1),(101,60,'Sim',2),(102,61,'Orçamentos',1),(103,61,'Pedidos',2),(104,61,'Ordem de Serviço',3),(105,61,'Consulta Documentos',4),(106,61,'Relatórios',5),(107,62,'Não',1),(108,62,'Sim',2),(112,63,'Comissão por tabela',1),(113,63,'Comissão por Faixa de Desconto',2),(114,63,'Relatório de comissões',3),(115,64,'Não',1),(116,64,'Sim',2),(117,65,'Emissão/Manutenção',1),(118,65,'Exclusão de Títulos',2),(119,65,'Baixa de Títulos',3),(120,65,'Pagamento Rápido / Recebimento Rápido',4),(121,65,'Imprimir Bloquetos Gráficos',5),(122,65,'Imprimir Carnê de Crediário',6),(123,65,'Geração de Notas Promissórias',7),(124,65,'Remessas Bancárias',8),(125,65,'Retornos Bancários',9),(126,65,'Renegociação de Títulos',10),(127,65,'Informativo Dia a Dia',11),(128,65,'Cálculo Automático de Juros e Multas',12),(129,65,'Relatórios à Pagar/ Receber',13),(130,66,'Não',1),(131,66,'Sim',2),(132,67,'Bancos',1),(133,67,'Grupos de Caixa',2),(134,67,'Históricos',3),(135,67,'Lançamentos em Banco',4),(136,67,'Lançamentos em Caixa',5),(137,67,'Transferência de Valores entre Empresas',6),(138,67,'Lançamentos de Partida Dobrada',7),(139,67,'Cheques Emitidos',8),(140,67,'Cheques Recebidos',9),(141,67,'Geração de Repasse para Cheques Recebidos',10),(142,67,'Baixa Automática de Cheques Recebidos',11),(143,67,'Recibos Avulsos',12),(144,67,'Relatórios',13),(145,68,'Não',1),(146,68,'Sim',2),(154,57,'Compartilhar a pasta Windel do servidor.',1),(155,57,'Instalar o Windel nos terminais e configurar o ômega.',2),(156,57,'Instalar aplicativo para Acesso Remoto no servidor e nos terminais.',3),(157,57,'Criar o usuário Windel, com a senha padrão: ledniw, para suporte do banco.',4),(158,57,'Instalar e configurar o programa de backup, fazer o agendamento automático, e testar seu funcionamento. Orientar o cliente para efetuar o backup em mídia externa (Pen Drive, CD, HD externo...) e explicar a importância desse procedimento, solicitar que ele confira periodicamente se o backup está sendo feito corretamente .',5),(159,57,'Instalar a Nota fiscal eletrônica nos computadores que irão emitir notas e configurar. (Para clientes que emitem notas).',6),(160,57,'Instalar ECF e configurar as alíquotas (Para clientes que emitem cupons).',7);
/*!40000 ALTER TABLE `question_field` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `question_field_filled_value`
--

DROP TABLE IF EXISTS `question_field_filled_value`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
 SET character_set_client = utf8mb4 ;
CREATE TABLE `question_field_filled_value` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_field` int(11) DEFAULT NULL,
  `id_question` int(11) NOT NULL,
  `id_deployment_schedule` int(11) DEFAULT NULL,
  `value` varchar(255) DEFAULT NULL,
  `value_text` text,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `question_field_filled_value`
--

LOCK TABLES `question_field_filled_value` WRITE;
/*!40000 ALTER TABLE `question_field_filled_value` DISABLE KEYS */;
/*!40000 ALTER TABLE `question_field_filled_value` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `question_form`
--

DROP TABLE IF EXISTS `question_form`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
 SET character_set_client = utf8mb4 ;
CREATE TABLE `question_form` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_produto` int(11) DEFAULT NULL,
  `name` varchar(255) NOT NULL,
  `local` varchar(255) DEFAULT NULL,
  `sequence` int(11) DEFAULT '999',
  `status` tinyint(1) DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `fk_id_produto_produto_id_idx` (`id_produto`),
  CONSTRAINT `fk_id_produto_produto_id` FOREIGN KEY (`id_produto`) REFERENCES `produto` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `question_form`
--

LOCK TABLES `question_form` WRITE;
/*!40000 ALTER TABLE `question_form` DISABLE KEYS */;
INSERT INTO `question_form` VALUES (1,15,'termo_de_levantamento_de_dados','implantacao',1,1),(2,15,'instalacao_do_sistema','implantacao',2,1),(3,1,'sistema_de_backup','implantacao',3,1),(4,1,'modulo_cadastros','implantacao',4,1),(5,1,'modulo_vendas','implantacao',5,1),(6,1,'comissao','implantacao',6,1),(7,1,'modulo_financeiro_–_contas_a_pagar_receber','implantacao',7,1),(8,1,'modulo_caixa_–_bancos','implantacao',8,1),(9,1,'modulo_compras','implantacao',9,1);
/*!40000 ALTER TABLE `question_form` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `role`
--

DROP TABLE IF EXISTS `role`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
 SET character_set_client = utf8mb4 ;
CREATE TABLE `role` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(128) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `description` varchar(1024) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `date_created` datetime NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name_idx` (`name`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `role`
--

LOCK TABLES `role` WRITE;
/*!40000 ALTER TABLE `role` DISABLE KEYS */;
INSERT INTO `role` VALUES (1,'Administrador','Admininistrador do Sistema','2018-04-18 15:43:20'),(2,'Operador','Operador do Sistema','2018-04-18 16:04:51'),(3,'Integrador','Sistemas Conectados','2018-05-16 09:28:16');
/*!40000 ALTER TABLE `role` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `role_hierarchy`
--

DROP TABLE IF EXISTS `role_hierarchy`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
 SET character_set_client = utf8mb4 ;
CREATE TABLE `role_hierarchy` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `parent_role_id` int(11) NOT NULL,
  `child_role_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_AB8EFB72A44B56EA` (`parent_role_id`),
  KEY `IDX_AB8EFB72B4B76AB7` (`child_role_id`),
  CONSTRAINT `role_role_child_role_id_fk` FOREIGN KEY (`child_role_id`) REFERENCES `role` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `role_role_parent_role_id_fk` FOREIGN KEY (`parent_role_id`) REFERENCES `role` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `role_hierarchy`
--

LOCK TABLES `role_hierarchy` WRITE;
/*!40000 ALTER TABLE `role_hierarchy` DISABLE KEYS */;
/*!40000 ALTER TABLE `role_hierarchy` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `role_permission`
--

DROP TABLE IF EXISTS `role_permission`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
 SET character_set_client = utf8mb4 ;
CREATE TABLE `role_permission` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `role_id` int(11) NOT NULL,
  `permission_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_6F7DF886D60322AC` (`role_id`),
  KEY `IDX_6F7DF886FED90CCA` (`permission_id`),
  CONSTRAINT `role_permission_permission_id_fk` FOREIGN KEY (`permission_id`) REFERENCES `permission` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `role_permission_role_id_fk` FOREIGN KEY (`role_id`) REFERENCES `role` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=43 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `role_permission`
--

LOCK TABLES `role_permission` WRITE;
/*!40000 ALTER TABLE `role_permission` DISABLE KEYS */;
INSERT INTO `role_permission` VALUES (32,1,6),(33,1,4),(34,1,3),(35,1,2),(36,1,5),(37,1,1),(41,2,4),(42,2,5);
/*!40000 ALTER TABLE `role_permission` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `user`
--

DROP TABLE IF EXISTS `user`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
 SET character_set_client = utf8mb4 ;
CREATE TABLE `user` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `first_name` varchar(255) NOT NULL,
  `last_name` varchar(255) DEFAULT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `id_job` int(11) DEFAULT NULL,
  `status` tinyint(1) DEFAULT '1',
  `date_created` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `pwd_reset_token` varchar(32) DEFAULT NULL,
  `pwd_reset_token_creation_date` datetime DEFAULT NULL,
  `user_id_crm` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_user_1_idx` (`id_job`),
  CONSTRAINT `fk_user_1` FOREIGN KEY (`id_job`) REFERENCES `job` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `user`
--

LOCK TABLES `user` WRITE;
/*!40000 ALTER TABLE `user` DISABLE KEYS */;
INSERT INTO `user` VALUES (1,'admin','','root@windel.com.br','$2y$10$8xDncJsGakJhuMUwWo26E.O76VSDw0kdFHnXNIdY/DYXsRvzEicD.',1,1,'2018-04-16 21:15:02','vj1vea13288p41v78f2qn0uapizgnrp6','2018-06-13 09:09:16',1),(2,'estacio','di fabio','estacio.junior@windel.com.br','$2y$10$g/DVji0eCb2gZugvQD5fQuJwp2oBd/FWHKTPjH9TJUVvWgXDIIw1y',1,1,'2018-04-16 21:19:48',NULL,NULL,NULL),(3,'Andrei','Fachinelli','andrei.fachinelli@windel.com.br','$2y$10$gPhCfi/uyWpCSudjlBZ0TOZRA.6Dosw/xygOnVnQUmIl0HYCKr8le',5,1,'2018-07-06 23:30:31',NULL,NULL,366),(4,'Rosângela','Fagundes','rosangela@windel.com.br','$2y$10$kcLwU3nxYQUNvefT30Bs4.EsppW1YaJrMSj.Jj6fmKjKF.DKXJOKS',2,1,'2018-07-06 23:31:39',NULL,NULL,NULL),(5,'Adilson','Flach','adilson@windel.com.br','$2y$10$wEUWOxzKj2TFEH2k7nW0CuOIMnIZdkVH0Wu9IOCDUPNTxxRdl9OFm',3,1,'2018-07-06 23:33:40',NULL,NULL,111);
/*!40000 ALTER TABLE `user` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `user_departament`
--

DROP TABLE IF EXISTS `user_departament`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
 SET character_set_client = utf8mb4 ;
CREATE TABLE `user_departament` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_user` int(11) NOT NULL,
  `id_departament` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_id_departament_departament_id_idx` (`id_departament`),
  KEY `fk_id_user_departament_user_id_idx` (`id_user`),
  CONSTRAINT `fk_user_departament_id_departament_departament_id` FOREIGN KEY (`id_departament`) REFERENCES `departament` (`id`),
  CONSTRAINT `fk_user_departament_id_user_user_id` FOREIGN KEY (`id_user`) REFERENCES `user` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `user_departament`
--

LOCK TABLES `user_departament` WRITE;
/*!40000 ALTER TABLE `user_departament` DISABLE KEYS */;
INSERT INTO `user_departament` VALUES (2,4,5),(3,4,1),(8,3,1),(9,5,1);
/*!40000 ALTER TABLE `user_departament` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `user_group`
--

DROP TABLE IF EXISTS `user_group`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
 SET character_set_client = utf8mb4 ;
CREATE TABLE `user_group` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_user` int(11) NOT NULL,
  `id_group` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_id_user_user_id_idx` (`id_user`),
  KEY `fk_id_group_group_id_idx` (`id_group`),
  CONSTRAINT `fk_id_group_group_id` FOREIGN KEY (`id_group`) REFERENCES `work_group` (`id`),
  CONSTRAINT `fk_id_user_user_id` FOREIGN KEY (`id_user`) REFERENCES `user` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=23 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `user_group`
--

LOCK TABLES `user_group` WRITE;
/*!40000 ALTER TABLE `user_group` DISABLE KEYS */;
INSERT INTO `user_group` VALUES (3,4,2),(4,4,1),(5,4,3),(6,4,4),(17,3,2),(18,3,1),(19,5,2),(20,5,1),(21,5,3),(22,5,4);
/*!40000 ALTER TABLE `user_group` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `user_organization`
--

DROP TABLE IF EXISTS `user_organization`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
 SET character_set_client = utf8mb4 ;
CREATE TABLE `user_organization` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_user` int(11) NOT NULL,
  `id_organization` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_user_organization_id_user_user_id_idx` (`id_user`),
  KEY `fk_user_organization_Id_organization_organization_id_idx` (`id_organization`),
  CONSTRAINT `fk_user_organization_Id_organization_organization_id` FOREIGN KEY (`id_organization`) REFERENCES `organization` (`id`),
  CONSTRAINT `fk_user_organization_id_user_user_id` FOREIGN KEY (`id_user`) REFERENCES `user` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `user_organization`
--

LOCK TABLES `user_organization` WRITE;
/*!40000 ALTER TABLE `user_organization` DISABLE KEYS */;
INSERT INTO `user_organization` VALUES (2,4,1),(3,4,2),(4,4,3),(9,3,1),(10,5,1);
/*!40000 ALTER TABLE `user_organization` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `user_role`
--

DROP TABLE IF EXISTS `user_role`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
 SET character_set_client = utf8mb4 ;
CREATE TABLE `user_role` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `role_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_2DE8C6A3A76ED395` (`user_id`),
  KEY `IDX_2DE8C6A3D60322AC` (`role_id`),
  CONSTRAINT `user_role_role_id_fk` FOREIGN KEY (`role_id`) REFERENCES `role` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `user_role_user_id_fk` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `user_role`
--

LOCK TABLES `user_role` WRITE;
/*!40000 ALTER TABLE `user_role` DISABLE KEYS */;
INSERT INTO `user_role` VALUES (5,3,2),(6,1,1),(7,5,2);
/*!40000 ALTER TABLE `user_role` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `work_group`
--

DROP TABLE IF EXISTS `work_group`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
 SET character_set_client = utf8mb4 ;
CREATE TABLE `work_group` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `status` tinyint(1) DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `work_group`
--

LOCK TABLES `work_group` WRITE;
/*!40000 ALTER TABLE `work_group` DISABLE KEYS */;
INSERT INTO `work_group` VALUES (1,'Normatização',1),(2,'Implantação',1),(3,'Revenda',1),(4,'Suporte',1);
/*!40000 ALTER TABLE `work_group` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Final view structure for view `formulario_respostas`
--

/*!50001 DROP VIEW IF EXISTS `formulario_respostas`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8 */;
/*!50001 SET character_set_results     = utf8 */;
/*!50001 SET collation_connection      = utf8_general_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`root`@`localhost` SQL SECURITY DEFINER */
/*!50001 VIEW `formulario_respostas` AS select `q`.`name` AS `name`,`qf`.`label` AS `label`,`qv`.`value` AS `value`,`qv`.`id_deployment_schedule` AS `schedule`,`f`.`name` AS `formulario`,`ds`.`id_attendance_crm` AS `atendimento` from ((((`question_field_filled_value` `qv` left join `question_field` `qf` on((`qv`.`id_field` = `qf`.`id`))) join `question` `q` on((`qv`.`id_question` = `q`.`id`))) join `question_form` `f` on((`f`.`id` = `q`.`id_question_form`))) join `deployment_schedule` `ds` on((`qv`.`id_deployment_schedule` = `ds`.`id`))) */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2018-09-24 14:05:06
