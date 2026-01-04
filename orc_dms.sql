/*
SQLyog Ultimate v11.11 (64 bit)
MySQL - 5.5.5-10.4.32-MariaDB : Database - orc_dms
*********************************************************************
*/

/*!40101 SET NAMES utf8 */;

/*!40101 SET SQL_MODE=''*/;

/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;
CREATE DATABASE /*!32312 IF NOT EXISTS*/`orc_dms` /*!40100 DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci */;

USE `orc_dms`;

/*Table structure for table `activity_logs` */

DROP TABLE IF EXISTS `activity_logs`;

CREATE TABLE `activity_logs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned DEFAULT NULL,
  `action` varchar(255) NOT NULL,
  `subject_type` varchar(255) DEFAULT NULL,
  `subject_id` bigint(20) unsigned DEFAULT NULL,
  `properties` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`properties`)),
  `ip_address` varchar(255) DEFAULT NULL,
  `user_agent` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `activity_logs_user_id_foreign` (`user_id`),
  CONSTRAINT `activity_logs_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=37 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `activity_logs` */

insert  into `activity_logs`(`id`,`user_id`,`action`,`subject_type`,`subject_id`,`properties`,`ip_address`,`user_agent`,`created_at`,`updated_at`) values (1,1,'template.created','App\\Models\\Template',1,'{\"key\":\"Memo_Template\",\"name\":\"Official Memo\",\"document_type_id\":\"1\",\"description\":null,\"is_active\":true}','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36 Edg/143.0.0.0','2026-01-03 08:04:21','2026-01-03 08:04:21'),(2,1,'document.created','App\\Models\\Document',1,'{\"doc_number\":\"2026\\/GEN\\/000001\"}','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36 Edg/143.0.0.0','2026-01-03 08:05:44','2026-01-03 08:05:44'),(3,1,'document.version_uploaded','App\\Models\\Document',1,'{\"version\":2}','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36 Edg/143.0.0.0','2026-01-03 08:07:03','2026-01-03 08:07:03'),(4,1,'document.created','App\\Models\\Document',2,'{\"doc_number\":\"2026\\/GEN\\/000002\",\"bulk\":true}','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36 Edg/143.0.0.0','2026-01-03 08:08:54','2026-01-03 08:08:54'),(5,1,'document.created','App\\Models\\Document',3,'{\"doc_number\":\"2026\\/GEN\\/000003\",\"bulk\":true}','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36 Edg/143.0.0.0','2026-01-03 08:08:54','2026-01-03 08:08:54'),(6,1,'document.created','App\\Models\\Document',4,'{\"doc_number\":\"2026\\/GEN\\/000004\"}','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36 Edg/143.0.0.0','2026-01-03 08:10:35','2026-01-03 08:10:35'),(7,1,'folder.created','App\\Models\\Folder',1,'{\"name\":\"Registrar Direct\",\"parent_id\":null,\"slug\":null,\"description\":\"For all registrar documents\",\"organization_unit_id\":null}','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36 Edg/143.0.0.0','2026-01-03 10:13:33','2026-01-03 10:13:33'),(8,1,'user.created','App\\Models\\User',2,'{\"role_id\":\"5\",\"organization_unit_id\":\"5\",\"status\":\"active\",\"clearance_level\":1}','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36 Edg/143.0.0.0','2026-01-03 10:44:15','2026-01-03 10:44:15'),(9,1,'workflow.definition.created','App\\Models\\WorkflowDefinition',1,'{\"key\":\"Level 1 Memo\",\"name\":\"Level 1 Memo\",\"description\":\"For all the general memos\",\"document_type_id\":\"1\",\"is_active\":true}','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36 Edg/143.0.0.0','2026-01-03 10:49:00','2026-01-03 10:49:00'),(10,1,'workflow.step.created','App\\Models\\WorkflowStep',1,'{\"workflow_definition_id\":1}','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36 Edg/143.0.0.0','2026-01-03 10:51:22','2026-01-03 10:51:22'),(11,1,'workflow.step.created','App\\Models\\WorkflowStep',2,'{\"workflow_definition_id\":1}','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36 Edg/143.0.0.0','2026-01-03 10:52:08','2026-01-03 10:52:08'),(12,1,'workflow.step.created','App\\Models\\WorkflowStep',3,'{\"workflow_definition_id\":1}','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36 Edg/143.0.0.0','2026-01-03 10:52:45','2026-01-03 10:52:45'),(13,1,'document.moved','App\\Models\\Document',4,'{\"document_type_id\":\"1\",\"origin_unit_id\":\"5\",\"classification_id\":\"3\",\"folder_id\":null}','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36 Edg/143.0.0.0','2026-01-03 10:56:25','2026-01-03 10:56:25'),(14,2,'workflow.started','App\\Models\\Document',5,'{\"workflow_definition_id\":1,\"current_step_id\":1}','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36 Edg/143.0.0.0','2026-01-03 11:09:26','2026-01-03 11:09:26'),(15,2,'document.created','App\\Models\\Document',5,'{\"doc_number\":\"2026\\/GEN\\/000005\"}','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36 Edg/143.0.0.0','2026-01-03 11:09:26','2026-01-03 11:09:26'),(16,1,'user.created','App\\Models\\User',3,'{\"role_id\":\"4\",\"organization_unit_id\":\"3\",\"status\":\"active\",\"clearance_level\":1}','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36 Edg/143.0.0.0','2026-01-03 11:53:02','2026-01-03 11:53:02'),(17,1,'user.created','App\\Models\\User',4,'{\"role_id\":\"2\",\"organization_unit_id\":\"1\",\"status\":\"active\",\"clearance_level\":1}','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36 Edg/143.0.0.0','2026-01-03 11:54:41','2026-01-03 11:54:41'),(18,1,'user.created','App\\Models\\User',5,'{\"role_id\":\"3\",\"organization_unit_id\":\"2\",\"status\":\"active\",\"clearance_level\":1}','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36 Edg/143.0.0.0','2026-01-03 11:55:49','2026-01-03 11:55:49'),(19,1,'user.updated','App\\Models\\User',2,'{\"role_id\":\"5\",\"organization_unit_id\":\"5\",\"status\":\"active\",\"clearance_level\":\"5\",\"phone\":\"0243198126\"}','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36 Edg/143.0.0.0','2026-01-03 12:10:11','2026-01-03 12:10:11'),(20,1,'user.updated','App\\Models\\User',5,'{\"role_id\":\"3\",\"organization_unit_id\":\"2\",\"status\":\"active\",\"clearance_level\":\"5\",\"phone\":\"0251234567\"}','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36 Edg/143.0.0.0','2026-01-03 12:10:23','2026-01-03 12:10:23'),(21,1,'user.updated','App\\Models\\User',4,'{\"role_id\":\"2\",\"organization_unit_id\":\"1\",\"status\":\"active\",\"clearance_level\":\"5\",\"phone\":\"0244112298\"}','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36 Edg/143.0.0.0','2026-01-03 12:10:34','2026-01-03 12:10:34'),(22,1,'user.updated','App\\Models\\User',3,'{\"role_id\":\"4\",\"organization_unit_id\":\"3\",\"status\":\"active\",\"clearance_level\":\"5\",\"phone\":\"0244112210\"}','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36 Edg/143.0.0.0','2026-01-03 12:10:45','2026-01-03 12:10:45'),(23,2,'workflow.started','App\\Models\\Document',6,'{\"workflow_definition_id\":1,\"current_step_id\":1}','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36 Edg/143.0.0.0','2026-01-03 12:12:41','2026-01-03 12:12:41'),(24,2,'document.created','App\\Models\\Document',6,'{\"doc_number\":\"2026\\/GEN\\/000006\"}','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36 Edg/143.0.0.0','2026-01-03 12:12:41','2026-01-03 12:12:41'),(25,2,'document.moved','App\\Models\\Document',6,'{\"document_type_id\":null,\"origin_unit_id\":\"5\",\"classification_id\":null,\"folder_id\":null}','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36 Edg/143.0.0.0','2026-01-03 12:13:50','2026-01-03 12:13:50'),(26,1,'workflow.step.deleted','App\\Models\\WorkflowStep',2,'{\"workflow_definition_id\":1}','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36 Edg/143.0.0.0','2026-01-03 12:26:28','2026-01-03 12:26:28'),(27,1,'workflow.step.created','App\\Models\\WorkflowStep',4,'{\"workflow_definition_id\":1}','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36 Edg/143.0.0.0','2026-01-03 12:35:49','2026-01-03 12:35:49'),(28,1,'workflow.step.deleted','App\\Models\\WorkflowStep',1,'{\"workflow_definition_id\":1}','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36 Edg/143.0.0.0','2026-01-03 13:35:38','2026-01-03 13:35:38'),(29,1,'workflow.step.deleted','App\\Models\\WorkflowStep',3,'{\"workflow_definition_id\":1}','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36 Edg/143.0.0.0','2026-01-03 13:35:44','2026-01-03 13:35:44'),(30,1,'workflow.step.created','App\\Models\\WorkflowStep',5,'{\"workflow_definition_id\":1}','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36 Edg/143.0.0.0','2026-01-03 13:36:05','2026-01-03 13:36:05'),(31,1,'workflow.step.created','App\\Models\\WorkflowStep',6,'{\"workflow_definition_id\":1}','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36 Edg/143.0.0.0','2026-01-03 13:36:29','2026-01-03 13:36:29'),(32,2,'notification.failed','App\\Models\\Document',9,'{\"step_id\":5,\"channel\":\"mail\",\"error\":\"Expected response code \\\"250\\/251\\/252\\\" but got code \\\"550\\\", with message \\\"550-The mail server could not deliver mail to michaelkusi@orcghana.com.  The\\r\\n550-account or domain may not exist, they may be blacklisted, or missing the\\r\\n550 proper dns entries.\\\".\"}','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36 Edg/143.0.0.0','2026-01-03 13:49:50','2026-01-03 13:49:50'),(33,2,'workflow.started','App\\Models\\Document',9,'{\"workflow_definition_id\":1,\"current_step_id\":5}','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36 Edg/143.0.0.0','2026-01-03 13:49:50','2026-01-03 13:49:50'),(34,2,'document.created','App\\Models\\Document',9,'{\"doc_number\":\"2026\\/GEN\\/000007\"}','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36 Edg/143.0.0.0','2026-01-03 13:49:50','2026-01-03 13:49:50'),(35,2,'workflow.step.approved','App\\Models\\Document',9,'{\"from_step_id\":5,\"to_step_id\":4}','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36 Edg/143.0.0.0','2026-01-03 13:50:11','2026-01-03 13:50:11'),(36,2,'notification.failed','App\\Models\\Document',9,'{\"step_id\":4,\"channel\":\"mail\",\"error\":\"Expected response code \\\"250\\/251\\/252\\\" but got code \\\"550\\\", with message \\\"550-The mail server could not deliver mail to sarahobrampong@orc.gov.  The\\r\\n550-account or domain may not exist, they may be blacklisted, or missing the\\r\\n550 proper dns entries.\\\".\"}','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36 Edg/143.0.0.0','2026-01-03 13:50:15','2026-01-03 13:50:15');

/*Table structure for table `classifications` */

DROP TABLE IF EXISTS `classifications`;

CREATE TABLE `classifications` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `key` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` varchar(255) DEFAULT NULL,
  `clearance_level` tinyint(3) unsigned NOT NULL DEFAULT 1,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `classifications_key_unique` (`key`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `classifications` */

insert  into `classifications`(`id`,`key`,`name`,`description`,`clearance_level`,`is_active`,`created_at`,`updated_at`) values (1,'public','Public','Accessible to all users',1,1,'2026-01-03 06:55:32','2026-01-03 06:55:32'),(2,'private','Private','Internal access only',1,1,'2026-01-03 06:55:32','2026-01-03 06:55:32'),(3,'confidential','Confidential','Restricted to authorized staff',2,1,'2026-01-03 06:55:32','2026-01-03 06:55:32'),(4,'restricted','Restricted','Strictly controlled access',3,1,'2026-01-03 06:55:32','2026-01-03 06:55:32');

/*Table structure for table `document_files` */

DROP TABLE IF EXISTS `document_files`;

CREATE TABLE `document_files` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `document_id` bigint(20) unsigned NOT NULL,
  `disk` varchar(255) NOT NULL DEFAULT 'local',
  `path` varchar(255) NOT NULL,
  `original_name` varchar(255) NOT NULL,
  `mime` varchar(255) DEFAULT NULL,
  `size` bigint(20) unsigned NOT NULL DEFAULT 0,
  `version` int(10) unsigned NOT NULL DEFAULT 1,
  `is_current` tinyint(1) NOT NULL DEFAULT 1,
  `checksum` varchar(64) DEFAULT NULL,
  `uploaded_by` bigint(20) unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `document_files_document_id_foreign` (`document_id`),
  KEY `document_files_uploaded_by_foreign` (`uploaded_by`),
  CONSTRAINT `document_files_document_id_foreign` FOREIGN KEY (`document_id`) REFERENCES `documents` (`id`) ON DELETE CASCADE,
  CONSTRAINT `document_files_uploaded_by_foreign` FOREIGN KEY (`uploaded_by`) REFERENCES `users` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `document_files` */

insert  into `document_files`(`id`,`document_id`,`disk`,`path`,`original_name`,`mime`,`size`,`version`,`is_current`,`checksum`,`uploaded_by`,`created_at`,`updated_at`) values (1,1,'public','documents/1/v1/Fp4boCowa9fcfwGP9AgNWPwK5XD8XAbVWpPzsvDU.pdf','Receipt-2263-5322.pdf','application/pdf',30896,1,0,'4a25c10af0129cef07c5e8d569a8c1074c4e1903d446f72fb0eccb44d01ea403',1,'2026-01-03 08:05:44','2026-01-03 08:07:02'),(2,1,'public','documents/1/v1/Weg5spWY2Mn7t46Awt1I7piciuNzAVOgSGdWtN0i.pdf','Invoice-4770433A-0029.pdf','application/pdf',30690,1,0,'5e1909ce8c0bdca7296c90d3064bce1685a3e82e448f51d66c1a1ada0b7b1558',1,'2026-01-03 08:05:44','2026-01-03 08:07:02'),(3,1,'public','documents/1/v2/Y4jeDK5yOFHNvwCpCZpiGJNJGzPy9Y0SWRpa7nuW.pdf','QUESTION ONE.pdf','application/pdf',139267,2,1,'cc6f9f766613a0a484fa8302deac2b1abc80a398dc7327266d54be2149d4c1d1',1,'2026-01-03 08:07:02','2026-01-03 08:07:02'),(4,2,'public','documents/2/v1/xs9e1N7blg0JTlx6jTuzDvkkn23DWNrQTCCoHlqD.pdf','Receipt-2263-5322.pdf','application/pdf',30896,1,1,'4a25c10af0129cef07c5e8d569a8c1074c4e1903d446f72fb0eccb44d01ea403',1,'2026-01-03 08:08:54','2026-01-03 08:08:54'),(5,3,'public','documents/3/v1/9qysF4SvrWqabBxukIxzFvo1ocam7qE7xFmVq5Mg.pdf','Invoice-4770433A-0029.pdf','application/pdf',30690,1,1,'5e1909ce8c0bdca7296c90d3064bce1685a3e82e448f51d66c1a1ada0b7b1558',1,'2026-01-03 08:08:54','2026-01-03 08:08:54'),(6,4,'public','documents/4/v1/TORJe8uKZLbchjcYmv4Y4CKsmOK5G6JsL5oyX4tZ.pdf','Jaycal sachet.pdf','application/pdf',355503,1,1,'9a25916383f95ccfbc1d6820d6e23b600bbbd060d826a1c7db5375da1379e606',1,'2026-01-03 08:10:35','2026-01-03 08:10:35'),(7,4,'public','documents/4/v1/fcCUlCZBLTRYrUgt0g8qLh1sJKwHy4SoRnVVMKo7.pdf','ISOD - APP & DB - Near Realtime ACH 1.pdf','application/pdf',1101854,1,1,'b150c10f83dc35a9d98d502c0412667b77c5d34bc353a3906d63f2da23c8f816',1,'2026-01-03 08:10:35','2026-01-03 08:10:35'),(8,4,'public','documents/4/v1/QSj2lEcsdeRsM8hrh6YnEJtXmBHnpCL2Tip5Afkf.pdf','1963512_Notice for Technical Services (3).pdf','application/pdf',38846,1,1,'bce38a2ff445aeeed62b6a58d5901efac3d5701602abfaab2253a3f9d95608b4',1,'2026-01-03 08:10:35','2026-01-03 08:10:35'),(9,5,'public','documents/5/v1/jyqyeX54aRIUv7uYIVz9LLafbGToBmzij14exA2j.pdf','99. TACKIE AND ANOTHER v. THE STATE.pdf','application/pdf',136592,1,1,'5f3bf1cd2039a83cfa1f01f6620b91a56e8374d5dc0cc25b81914619514315b2',2,'2026-01-03 11:09:20','2026-01-03 11:09:20'),(10,5,'public','documents/5/v1/G9xPnAouMxDkvvd5kkinoX1Nn2iRmIQCs6AUlOQi.pdf','08_search_234_tree.pdf','application/pdf',787194,1,1,'2970954b05e6d4ae80d42936ad2d995809e7c3a44bb9bdede522795ee1c83d2d',2,'2026-01-03 11:09:26','2026-01-03 11:09:26'),(11,6,'public','documents/6/v1/6GV07z0QVijrEYXEjSZ2JXsWyD6zrxN7XDQ2GBG4.pdf','Invoice-4770433A-0029.pdf','application/pdf',30690,1,1,'5e1909ce8c0bdca7296c90d3064bce1685a3e82e448f51d66c1a1ada0b7b1558',2,'2026-01-03 12:12:41','2026-01-03 12:12:41'),(14,9,'public','documents/9/v1/gSYKGB7fj0kGvKCsrmGElhbKeJRkqyFjXu8DS7MU.pdf','Receipt-2263-5322.pdf','application/pdf',30896,1,1,'4a25c10af0129cef07c5e8d569a8c1074c4e1903d446f72fb0eccb44d01ea403',2,'2026-01-03 13:49:44','2026-01-03 13:49:44');

/*Table structure for table `document_permissions` */

DROP TABLE IF EXISTS `document_permissions`;

CREATE TABLE `document_permissions` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `document_id` bigint(20) unsigned NOT NULL,
  `user_id` bigint(20) unsigned NOT NULL,
  `can_view` tinyint(1) NOT NULL DEFAULT 1,
  `can_edit` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `document_permissions_document_id_user_id_unique` (`document_id`,`user_id`),
  KEY `document_permissions_user_id_foreign` (`user_id`),
  CONSTRAINT `document_permissions_document_id_foreign` FOREIGN KEY (`document_id`) REFERENCES `documents` (`id`) ON DELETE CASCADE,
  CONSTRAINT `document_permissions_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `document_permissions` */

/*Table structure for table `document_types` */

DROP TABLE IF EXISTS `document_types`;

CREATE TABLE `document_types` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `key` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` varchar(255) DEFAULT NULL,
  `default_classification_id` bigint(20) unsigned DEFAULT NULL,
  `default_retention_months` int(10) unsigned NOT NULL DEFAULT 0,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `document_types_key_unique` (`key`),
  KEY `document_types_default_classification_id_foreign` (`default_classification_id`),
  CONSTRAINT `document_types_default_classification_id_foreign` FOREIGN KEY (`default_classification_id`) REFERENCES `classifications` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `document_types` */

insert  into `document_types`(`id`,`key`,`name`,`description`,`default_classification_id`,`default_retention_months`,`is_active`,`created_at`,`updated_at`) values (1,'memo','Memo','Internal memorandum',NULL,0,1,'2026-01-03 06:55:32','2026-01-03 06:55:32'),(2,'letter','Letter','Official letter',NULL,0,1,'2026-01-03 06:55:32','2026-01-03 06:55:32'),(3,'report','Report','Departmental report',NULL,0,1,'2026-01-03 06:55:32','2026-01-03 06:55:32'),(4,'invoice','Invoice','Vendor invoice',NULL,0,1,'2026-01-03 06:55:32','2026-01-03 06:55:32');

/*Table structure for table `documents` */

DROP TABLE IF EXISTS `documents`;

CREATE TABLE `documents` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `doc_number` varchar(255) NOT NULL,
  `title` varchar(255) NOT NULL,
  `document_type_id` bigint(20) unsigned NOT NULL,
  `template_version_id` bigint(20) unsigned DEFAULT NULL,
  `form_data` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`form_data`)),
  `classification_id` bigint(20) unsigned NOT NULL,
  `origin_unit_id` bigint(20) unsigned DEFAULT NULL,
  `folder_id` bigint(20) unsigned DEFAULT NULL,
  `workflow_definition_id` bigint(20) unsigned DEFAULT NULL,
  `current_position` int(10) unsigned NOT NULL DEFAULT 0,
  `status` varchar(255) NOT NULL DEFAULT 'draft',
  `archived_at` timestamp NULL DEFAULT NULL,
  `disposed_at` timestamp NULL DEFAULT NULL,
  `retention_policy_id` bigint(20) unsigned DEFAULT NULL,
  `retention_until` date DEFAULT NULL,
  `legal_hold` tinyint(1) NOT NULL DEFAULT 0,
  `search_text` longtext DEFAULT NULL,
  `created_by` bigint(20) unsigned NOT NULL,
  `updated_by` bigint(20) unsigned DEFAULT NULL,
  `locked_by` bigint(20) unsigned DEFAULT NULL,
  `locked_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `documents_doc_number_unique` (`doc_number`),
  KEY `documents_document_type_id_foreign` (`document_type_id`),
  KEY `documents_classification_id_foreign` (`classification_id`),
  KEY `documents_origin_unit_id_foreign` (`origin_unit_id`),
  KEY `documents_workflow_definition_id_foreign` (`workflow_definition_id`),
  KEY `documents_retention_policy_id_foreign` (`retention_policy_id`),
  KEY `documents_created_by_foreign` (`created_by`),
  KEY `documents_updated_by_foreign` (`updated_by`),
  KEY `documents_template_version_id_foreign` (`template_version_id`),
  KEY `documents_locked_by_foreign` (`locked_by`),
  KEY `documents_folder_id_foreign` (`folder_id`),
  FULLTEXT KEY `documents_title_doc_number_search_text_fulltext` (`title`,`doc_number`,`search_text`),
  CONSTRAINT `documents_classification_id_foreign` FOREIGN KEY (`classification_id`) REFERENCES `classifications` (`id`),
  CONSTRAINT `documents_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`),
  CONSTRAINT `documents_document_type_id_foreign` FOREIGN KEY (`document_type_id`) REFERENCES `document_types` (`id`) ON DELETE CASCADE,
  CONSTRAINT `documents_folder_id_foreign` FOREIGN KEY (`folder_id`) REFERENCES `folders` (`id`) ON DELETE SET NULL,
  CONSTRAINT `documents_locked_by_foreign` FOREIGN KEY (`locked_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `documents_origin_unit_id_foreign` FOREIGN KEY (`origin_unit_id`) REFERENCES `organization_units` (`id`) ON DELETE SET NULL,
  CONSTRAINT `documents_retention_policy_id_foreign` FOREIGN KEY (`retention_policy_id`) REFERENCES `retention_policies` (`id`) ON DELETE SET NULL,
  CONSTRAINT `documents_template_version_id_foreign` FOREIGN KEY (`template_version_id`) REFERENCES `template_versions` (`id`) ON DELETE SET NULL,
  CONSTRAINT `documents_updated_by_foreign` FOREIGN KEY (`updated_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `documents_workflow_definition_id_foreign` FOREIGN KEY (`workflow_definition_id`) REFERENCES `workflow_definitions` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `documents` */

insert  into `documents`(`id`,`doc_number`,`title`,`document_type_id`,`template_version_id`,`form_data`,`classification_id`,`origin_unit_id`,`folder_id`,`workflow_definition_id`,`current_position`,`status`,`archived_at`,`disposed_at`,`retention_policy_id`,`retention_until`,`legal_hold`,`search_text`,`created_by`,`updated_by`,`locked_by`,`locked_at`,`created_at`,`updated_at`) values (1,'2026/GEN/000001','Computers Vendor Agreement',1,NULL,NULL,3,NULL,NULL,NULL,0,'draft',NULL,NULL,NULL,NULL,0,NULL,1,NULL,NULL,NULL,'2026-01-03 08:05:44','2026-01-03 08:05:44'),(2,'2026/GEN/000002','Leasehold Property Invoice',4,NULL,NULL,4,NULL,NULL,NULL,0,'draft',NULL,NULL,NULL,NULL,0,NULL,1,NULL,NULL,NULL,'2026-01-03 08:08:54','2026-01-03 08:08:54'),(3,'2026/GEN/000003','Leasehold Property Invoice',4,NULL,NULL,4,NULL,NULL,NULL,0,'draft',NULL,NULL,NULL,NULL,0,NULL,1,NULL,NULL,NULL,'2026-01-03 08:08:54','2026-01-03 08:08:54'),(4,'2026/GEN/000004','Companies Registrations Exceptions',1,NULL,NULL,3,5,NULL,NULL,0,'draft',NULL,NULL,NULL,NULL,0,NULL,1,NULL,NULL,NULL,'2026-01-03 08:10:35','2026-01-03 10:56:25'),(5,'2026/GEN/000005','Change Over Memo',1,NULL,NULL,2,NULL,NULL,NULL,0,'submitted',NULL,NULL,NULL,NULL,0,NULL,2,NULL,NULL,NULL,'2026-01-03 11:09:20','2026-01-03 11:09:26'),(6,'2026/GEN/000006','Management Memo',1,NULL,NULL,4,5,1,NULL,0,'submitted',NULL,NULL,NULL,NULL,0,NULL,2,NULL,NULL,NULL,'2026-01-03 12:12:40','2026-01-03 12:13:50'),(9,'2026/GEN/000007','Gifts Policy',1,NULL,NULL,3,NULL,NULL,NULL,0,'submitted',NULL,NULL,NULL,NULL,0,NULL,2,NULL,NULL,NULL,'2026-01-03 13:49:44','2026-01-03 13:49:44');

/*Table structure for table `failed_jobs` */

DROP TABLE IF EXISTS `failed_jobs`;

CREATE TABLE `failed_jobs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `uuid` varchar(255) NOT NULL,
  `connection` text NOT NULL,
  `queue` text NOT NULL,
  `payload` longtext NOT NULL,
  `exception` longtext NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `failed_jobs` */

/*Table structure for table `folder_permissions` */

DROP TABLE IF EXISTS `folder_permissions`;

CREATE TABLE `folder_permissions` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `folder_id` bigint(20) unsigned NOT NULL,
  `user_id` bigint(20) unsigned NOT NULL,
  `can_view` tinyint(1) NOT NULL DEFAULT 1,
  `can_edit` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `folder_permissions_folder_id_user_id_unique` (`folder_id`,`user_id`),
  KEY `folder_permissions_user_id_foreign` (`user_id`),
  CONSTRAINT `folder_permissions_folder_id_foreign` FOREIGN KEY (`folder_id`) REFERENCES `folders` (`id`) ON DELETE CASCADE,
  CONSTRAINT `folder_permissions_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `folder_permissions` */

/*Table structure for table `folders` */

DROP TABLE IF EXISTS `folders`;

CREATE TABLE `folders` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `parent_id` bigint(20) unsigned DEFAULT NULL,
  `name` varchar(255) NOT NULL,
  `slug` varchar(255) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `organization_unit_id` bigint(20) unsigned DEFAULT NULL,
  `created_by` bigint(20) unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `folders_parent_id_foreign` (`parent_id`),
  KEY `folders_organization_unit_id_foreign` (`organization_unit_id`),
  KEY `folders_created_by_foreign` (`created_by`),
  CONSTRAINT `folders_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `folders_organization_unit_id_foreign` FOREIGN KEY (`organization_unit_id`) REFERENCES `organization_units` (`id`) ON DELETE SET NULL,
  CONSTRAINT `folders_parent_id_foreign` FOREIGN KEY (`parent_id`) REFERENCES `folders` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `folders` */

insert  into `folders`(`id`,`parent_id`,`name`,`slug`,`description`,`organization_unit_id`,`created_by`,`created_at`,`updated_at`) values (1,NULL,'Registrar Direct',NULL,'For all registrar documents',NULL,1,'2026-01-03 10:13:33','2026-01-03 10:13:33');

/*Table structure for table `jobs` */

DROP TABLE IF EXISTS `jobs`;

CREATE TABLE `jobs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `queue` varchar(255) NOT NULL,
  `payload` longtext NOT NULL,
  `attempts` tinyint(3) unsigned NOT NULL,
  `reserved_at` int(10) unsigned DEFAULT NULL,
  `available_at` int(10) unsigned NOT NULL,
  `created_at` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `jobs_queue_index` (`queue`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `jobs` */

insert  into `jobs`(`id`,`queue`,`payload`,`attempts`,`reserved_at`,`available_at`,`created_at`) values (3,'default','{\"uuid\":\"0ee367be-9ee2-433c-8a9c-5c3501971fd9\",\"displayName\":\"App\\\\Jobs\\\\ExtractDocumentText\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"data\":{\"commandName\":\"App\\\\Jobs\\\\ExtractDocumentText\",\"command\":\"O:28:\\\"App\\\\Jobs\\\\ExtractDocumentText\\\":1:{s:6:\\\"fileId\\\";i:14;}\"}}',0,NULL,1767448184,1767448184);

/*Table structure for table `migrations` */

DROP TABLE IF EXISTS `migrations`;

CREATE TABLE `migrations` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) NOT NULL,
  `batch` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=29 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `migrations` */

insert  into `migrations`(`id`,`migration`,`batch`) values (1,'2014_10_12_000000_create_users_table',1),(2,'2014_10_12_100000_create_password_resets_table',1),(3,'2019_08_19_000000_create_failed_jobs_table',1),(4,'2019_12_14_000001_create_personal_access_tokens_table',1),(5,'2026_01_02_000001_create_organization_units_table',1),(6,'2026_01_02_000002_create_roles_table',1),(7,'2026_01_02_000003_alter_users_add_role_and_unit',1),(8,'2026_01_02_000004_create_classifications_table',1),(9,'2026_01_02_000005_create_document_types_table',1),(10,'2026_01_02_000006_create_workflow_definitions_table',1),(11,'2026_01_02_000007_create_workflow_steps_table',1),(12,'2026_01_02_000008_create_templates_table',1),(13,'2026_01_02_000009_create_retention_policies_table',1),(14,'2026_01_02_000010_create_documents_table',1),(15,'2026_01_02_000011_create_template_versions_table',1),(16,'2026_01_02_000012_create_workflow_instances_table',1),(17,'2026_01_02_000013_create_document_files_table',1),(18,'2026_01_02_000014_alter_documents_add_template_and_archive_fields',1),(19,'2026_01_02_000015_alter_document_files_add_version_and_checksum',2),(20,'2026_01_02_000016_alter_documents_add_locking_columns',2),(21,'2026_01_02_000017_create_activity_logs_table',3),(22,'2026_01_02_000018_create_document_permissions_table',4),(23,'2026_01_03_000019_create_saved_searches_table',4),(24,'2026_01_03_000020_alter_documents_add_search_text_fulltext',5),(25,'2026_01_03_000021_create_jobs_table',6),(26,'2026_01_03_000040_create_folders_table',7),(27,'2026_01_03_000041_create_folder_permissions_table',7),(28,'2026_01_03_000042_alter_documents_add_folder_id',7);

/*Table structure for table `organization_units` */

DROP TABLE IF EXISTS `organization_units`;

CREATE TABLE `organization_units` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `code` varchar(255) DEFAULT NULL,
  `type` varchar(255) NOT NULL DEFAULT 'department',
  `parent_id` bigint(20) unsigned DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `organization_units_code_unique` (`code`),
  KEY `organization_units_parent_id_foreign` (`parent_id`),
  CONSTRAINT `organization_units_parent_id_foreign` FOREIGN KEY (`parent_id`) REFERENCES `organization_units` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `organization_units` */

insert  into `organization_units`(`id`,`name`,`code`,`type`,`parent_id`,`is_active`,`created_at`,`updated_at`) values (1,'Registrar General Directorate','RGD','directorate',NULL,1,'2026-01-03 06:55:32','2026-01-03 06:55:32'),(2,'Registry Operations','REG','department',1,1,'2026-01-03 06:55:32','2026-01-03 06:55:32'),(3,'Compliance & Inspection','COMPL','department',1,1,'2026-01-03 06:55:32','2026-01-03 06:55:32'),(4,'Legal Services','LEGAL','department',1,1,'2026-01-03 06:55:32','2026-01-03 06:55:32'),(5,'Information Technology','IT','department',1,1,'2026-01-03 06:55:32','2026-01-03 06:55:32'),(6,'Finance & Accounts','FIN','department',1,1,'2026-01-03 06:55:32','2026-01-03 06:55:32'),(7,'Customer Service','CS','department',1,1,'2026-01-03 06:55:32','2026-01-03 06:55:32');

/*Table structure for table `password_resets` */

DROP TABLE IF EXISTS `password_resets`;

CREATE TABLE `password_resets` (
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  KEY `password_resets_email_index` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `password_resets` */

/*Table structure for table `personal_access_tokens` */

DROP TABLE IF EXISTS `personal_access_tokens`;

CREATE TABLE `personal_access_tokens` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `tokenable_type` varchar(255) NOT NULL,
  `tokenable_id` bigint(20) unsigned NOT NULL,
  `name` varchar(255) NOT NULL,
  `token` varchar(64) NOT NULL,
  `abilities` text DEFAULT NULL,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `personal_access_tokens_token_unique` (`token`),
  KEY `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`,`tokenable_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `personal_access_tokens` */

/*Table structure for table `retention_policies` */

DROP TABLE IF EXISTS `retention_policies`;

CREATE TABLE `retention_policies` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `key` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `retain_months` int(10) unsigned NOT NULL DEFAULT 0,
  `allow_hold` tinyint(1) NOT NULL DEFAULT 1,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `retention_policies_key_unique` (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `retention_policies` */

/*Table structure for table `roles` */

DROP TABLE IF EXISTS `roles`;

CREATE TABLE `roles` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `key` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `roles_key_unique` (`key`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `roles` */

insert  into `roles`(`id`,`key`,`name`,`created_at`,`updated_at`) values (1,'admin','Administrator','2026-01-03 06:55:32','2026-01-03 06:55:32'),(2,'registrar','Registrar General Director','2026-01-03 06:55:32','2026-01-03 06:55:32'),(3,'registry','Registry Officer','2026-01-03 06:55:32','2026-01-03 06:55:32'),(4,'dept_head','Department Head/Lead','2026-01-03 06:55:32','2026-01-03 06:55:32'),(5,'staff','Staff User','2026-01-03 06:55:32','2026-01-03 06:55:32');

/*Table structure for table `saved_searches` */

DROP TABLE IF EXISTS `saved_searches`;

CREATE TABLE `saved_searches` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned NOT NULL,
  `name` varchar(255) NOT NULL,
  `scope` varchar(255) NOT NULL,
  `params` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`params`)),
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `saved_searches_user_id_foreign` (`user_id`),
  CONSTRAINT `saved_searches_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `saved_searches` */

/*Table structure for table `template_versions` */

DROP TABLE IF EXISTS `template_versions`;

CREATE TABLE `template_versions` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `template_id` bigint(20) unsigned NOT NULL,
  `version` int(10) unsigned NOT NULL,
  `schema` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`schema`)),
  `sample_data` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`sample_data`)),
  `is_published` tinyint(1) NOT NULL DEFAULT 0,
  `created_by` bigint(20) unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `template_versions_template_id_version_unique` (`template_id`,`version`),
  KEY `template_versions_created_by_foreign` (`created_by`),
  CONSTRAINT `template_versions_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `template_versions_template_id_foreign` FOREIGN KEY (`template_id`) REFERENCES `templates` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `template_versions` */

/*Table structure for table `templates` */

DROP TABLE IF EXISTS `templates`;

CREATE TABLE `templates` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `key` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `document_type_id` bigint(20) unsigned DEFAULT NULL,
  `description` text DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `templates_key_unique` (`key`),
  KEY `templates_document_type_id_foreign` (`document_type_id`),
  CONSTRAINT `templates_document_type_id_foreign` FOREIGN KEY (`document_type_id`) REFERENCES `document_types` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `templates` */

insert  into `templates`(`id`,`key`,`name`,`document_type_id`,`description`,`is_active`,`created_at`,`updated_at`) values (1,'Memo_Template','Official Memo',1,NULL,1,'2026-01-03 08:04:21','2026-01-03 08:04:21');

/*Table structure for table `users` */

DROP TABLE IF EXISTS `users`;

CREATE TABLE `users` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `role_id` bigint(20) unsigned DEFAULT NULL,
  `organization_unit_id` bigint(20) unsigned DEFAULT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `phone` varchar(255) DEFAULT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `remember_token` varchar(100) DEFAULT NULL,
  `status` varchar(255) NOT NULL DEFAULT 'active',
  `clearance_level` tinyint(3) unsigned NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`),
  KEY `users_role_id_foreign` (`role_id`),
  KEY `users_organization_unit_id_foreign` (`organization_unit_id`),
  CONSTRAINT `users_organization_unit_id_foreign` FOREIGN KEY (`organization_unit_id`) REFERENCES `organization_units` (`id`) ON DELETE SET NULL,
  CONSTRAINT `users_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `users` */

insert  into `users`(`id`,`role_id`,`organization_unit_id`,`name`,`email`,`phone`,`email_verified_at`,`password`,`remember_token`,`status`,`clearance_level`,`created_at`,`updated_at`) values (1,1,1,'System Administrator','admin@orc.local',NULL,NULL,'$2y$10$kpJVbSpCdXsvdPUKdAHeieFAv6qwldyoeXsOGiaMchbpoMPGJVVJ2',NULL,'active',3,'2026-01-02 22:18:09','2026-01-02 22:18:09'),(2,5,5,'Michael Kusi','michaelkusi@orcghana.com','0243198126',NULL,'$2y$10$64SX7bWK/uTC9BW4ZMyw1.cMYYFFyHDIaBwsYNtZ1yDBYUQdtKzs2',NULL,'active',5,'2026-01-03 10:44:15','2026-01-03 12:10:11'),(3,4,3,'Sarah Obrempong','sarahobrampong@orc.gov','0244112210',NULL,'$2y$10$1B53xdrscoodcglRW9r/UuzJAI35hocOfN.OWVquk/ACm1fd4mSCe',NULL,'active',5,'2026-01-03 11:53:02','2026-01-03 12:10:45'),(4,2,1,'Maame Samma Peprah','maamesamma@orc.gov','0244112298',NULL,'$2y$10$eKYJIOquOvHRLQolPqrGc.V4z32g4p.lr..xyPlhnxEqyXP5sZfSG',NULL,'active',5,'2026-01-03 11:54:41','2026-01-03 12:10:34'),(5,3,2,'Gerald Owusu','geraldowusu@orc.gov','0251234567',NULL,'$2y$10$Ff7N93b8xMna9TLtyo9le..15kar.ZO3Q/VS1AYOAe0Kpmo61I11S',NULL,'active',5,'2026-01-03 11:55:49','2026-01-03 12:10:23');

/*Table structure for table `workflow_definitions` */

DROP TABLE IF EXISTS `workflow_definitions`;

CREATE TABLE `workflow_definitions` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `key` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` varchar(255) DEFAULT NULL,
  `document_type_id` bigint(20) unsigned DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `workflow_definitions_key_unique` (`key`),
  KEY `workflow_definitions_document_type_id_foreign` (`document_type_id`),
  CONSTRAINT `workflow_definitions_document_type_id_foreign` FOREIGN KEY (`document_type_id`) REFERENCES `document_types` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `workflow_definitions` */

insert  into `workflow_definitions`(`id`,`key`,`name`,`description`,`document_type_id`,`is_active`,`created_at`,`updated_at`) values (1,'Level 1 Memo','Level 1 Memo','For all the general memos',1,1,'2026-01-03 10:49:00','2026-01-03 10:49:00');

/*Table structure for table `workflow_instances` */

DROP TABLE IF EXISTS `workflow_instances`;

CREATE TABLE `workflow_instances` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `document_id` bigint(20) unsigned NOT NULL,
  `workflow_definition_id` bigint(20) unsigned NOT NULL,
  `status` varchar(255) NOT NULL DEFAULT 'running',
  `current_step_id` bigint(20) unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `workflow_instances_document_id_foreign` (`document_id`),
  KEY `workflow_instances_workflow_definition_id_foreign` (`workflow_definition_id`),
  KEY `workflow_instances_current_step_id_foreign` (`current_step_id`),
  CONSTRAINT `workflow_instances_current_step_id_foreign` FOREIGN KEY (`current_step_id`) REFERENCES `workflow_steps` (`id`) ON DELETE SET NULL,
  CONSTRAINT `workflow_instances_document_id_foreign` FOREIGN KEY (`document_id`) REFERENCES `documents` (`id`) ON DELETE CASCADE,
  CONSTRAINT `workflow_instances_workflow_definition_id_foreign` FOREIGN KEY (`workflow_definition_id`) REFERENCES `workflow_definitions` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `workflow_instances` */

insert  into `workflow_instances`(`id`,`document_id`,`workflow_definition_id`,`status`,`current_step_id`,`created_at`,`updated_at`) values (1,5,1,'running',NULL,'2026-01-03 11:09:26','2026-01-03 11:09:26'),(2,6,1,'running',NULL,'2026-01-03 12:12:41','2026-01-03 12:12:41'),(5,9,1,'running',4,'2026-01-03 13:49:44','2026-01-03 13:50:11');

/*Table structure for table `workflow_steps` */

DROP TABLE IF EXISTS `workflow_steps`;

CREATE TABLE `workflow_steps` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `workflow_definition_id` bigint(20) unsigned NOT NULL,
  `position` int(10) unsigned NOT NULL,
  `key` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `assignee_type` varchar(255) NOT NULL DEFAULT 'role',
  `assignee_value` varchar(255) DEFAULT NULL,
  `requires_approval` tinyint(1) NOT NULL DEFAULT 1,
  `allow_edit` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `workflow_steps_workflow_definition_id_position_unique` (`workflow_definition_id`,`position`),
  CONSTRAINT `workflow_steps_workflow_definition_id_foreign` FOREIGN KEY (`workflow_definition_id`) REFERENCES `workflow_definitions` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `workflow_steps` */

insert  into `workflow_steps`(`id`,`workflow_definition_id`,`position`,`key`,`name`,`assignee_type`,`assignee_value`,`requires_approval`,`allow_edit`,`created_at`,`updated_at`) values (4,1,2,'SecondLevel','SecondLevel','unit','3',1,1,'2026-01-03 12:35:48','2026-01-03 13:37:04'),(5,1,1,'FirstLevel','FirstLevel','role','5',1,1,'2026-01-03 13:36:05','2026-01-03 13:37:04'),(6,1,3,'FinalLevel','FinalLevel','registrar',NULL,1,0,'2026-01-03 13:36:29','2026-01-03 13:36:57');

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
