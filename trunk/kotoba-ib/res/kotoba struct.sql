-- MySQL dump 10.13  Distrib 5.1.32, for Win32 (ia32)
--
-- Host: localhost    Database: kotoba2
-- ------------------------------------------------------
-- Server version	5.1.32-community

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Current Database: `kotoba2`
--

CREATE DATABASE /*!32312 IF NOT EXISTS*/ `kotoba2` /*!40100 DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci */;

USE `kotoba2`;

--
-- Table structure for table `acl`
--

DROP TABLE IF EXISTS `acl`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `acl` (
  `group` int(11) DEFAULT NULL,
  `board` int(11) DEFAULT NULL,
  `thread` int(11) DEFAULT NULL,
  `post` int(11) DEFAULT NULL,
  `view` bit(1) NOT NULL,
  `change` bit(1) NOT NULL,
  `moderate` bit(1) NOT NULL,
  UNIQUE KEY `group` (`group`,`board`,`thread`,`post`),
  KEY `board` (`board`),
  KEY `thread` (`thread`),
  KEY `post` (`post`),
  CONSTRAINT `acl_ibfk_1` FOREIGN KEY (`group`) REFERENCES `groups` (`id`),
  CONSTRAINT `acl_ibfk_2` FOREIGN KEY (`board`) REFERENCES `boards` (`id`),
  CONSTRAINT `acl_ibfk_3` FOREIGN KEY (`thread`) REFERENCES `threads` (`id`),
  CONSTRAINT `acl_ibfk_4` FOREIGN KEY (`post`) REFERENCES `posts` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `bans`
--

DROP TABLE IF EXISTS `bans`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `bans` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `range_beg` bigint(11) NOT NULL,
  `range_end` bigint(11) NOT NULL,
  `reason` text COLLATE utf8_unicode_ci,
  `untill` datetime NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `ip_range` (`range_beg`,`range_end`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `board_upload_types`
--

DROP TABLE IF EXISTS `board_upload_types`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `board_upload_types` (
  `board` int(11) NOT NULL,
  `upload_type` int(11) NOT NULL,
  UNIQUE KEY `board` (`board`,`upload_type`),
  KEY `upload_type` (`upload_type`),
  CONSTRAINT `board_upload_types_ibfk_1` FOREIGN KEY (`board`) REFERENCES `boards` (`id`),
  CONSTRAINT `board_upload_types_ibfk_2` FOREIGN KEY (`upload_type`) REFERENCES `upload_types` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `boards`
--

DROP TABLE IF EXISTS `boards`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `boards` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(16) COLLATE utf8_unicode_ci NOT NULL,
  `title` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `annotation` text COLLATE utf8_unicode_ci,
  `bump_limit` int(11) NOT NULL,
  `force_anonymous` bit(1) NOT NULL,
  `default_name` varchar(128) COLLATE utf8_unicode_ci DEFAULT NULL,
  `with_attachments` bit(1) NOT NULL,
  `enable_macro` bit(1) DEFAULT NULL,
  `enable_youtube` bit(1) DEFAULT NULL,
  `enable_captcha` bit(1) DEFAULT NULL,
  `same_upload` varchar(32) COLLATE utf8_unicode_ci NOT NULL,
  `popdown_handler` int(11) NOT NULL,
  `category` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`),
  KEY `category` (`category`),
  KEY `popdown_handler` (`popdown_handler`),
  CONSTRAINT `boards_ibfk_1` FOREIGN KEY (`category`) REFERENCES `categories` (`id`),
  CONSTRAINT `boards_ibfk_2` FOREIGN KEY (`popdown_handler`) REFERENCES `popdown_handlers` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `categories`
--

DROP TABLE IF EXISTS `categories`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `categories` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `files`
--

DROP TABLE IF EXISTS `files`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `files` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `hash` varchar(32) COLLATE utf8_unicode_ci DEFAULT NULL,
  `name` varchar(256) COLLATE utf8_unicode_ci NOT NULL,
  `size` int(11) NOT NULL,
  `thumbnail` varchar(256) COLLATE utf8_unicode_ci NOT NULL,
  `thumbnail_w` int(11) NOT NULL,
  `thumbnail_h` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `groups`
--

DROP TABLE IF EXISTS `groups`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `groups` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `hidden_threads`
--

DROP TABLE IF EXISTS `hidden_threads`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `hidden_threads` (
  `user` int(11) DEFAULT NULL,
  `thread` int(11) DEFAULT NULL,
  UNIQUE KEY `user` (`user`,`thread`),
  KEY `thread` (`thread`),
  CONSTRAINT `hidden_threads_ibfk_1` FOREIGN KEY (`user`) REFERENCES `users` (`id`),
  CONSTRAINT `hidden_threads_ibfk_2` FOREIGN KEY (`thread`) REFERENCES `threads` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `images`
--

DROP TABLE IF EXISTS `images`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `images` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `hash` varchar(32) COLLATE utf8_unicode_ci DEFAULT NULL,
  `name` varchar(256) COLLATE utf8_unicode_ci NOT NULL,
  `widht` int(11) NOT NULL,
  `height` int(11) NOT NULL,
  `size` int(11) NOT NULL,
  `thumbnail` varchar(256) COLLATE utf8_unicode_ci NOT NULL,
  `thumbnail_w` int(11) NOT NULL,
  `thumbnail_h` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=431 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `languages`
--

DROP TABLE IF EXISTS `languages`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `languages` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `code` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `links`
--

DROP TABLE IF EXISTS `links`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `links` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `url` varchar(2048) COLLATE utf8_unicode_ci NOT NULL,
  `widht` int(11) NOT NULL,
  `height` int(11) NOT NULL,
  `size` int(11) NOT NULL,
  `thumbnail` varchar(2048) COLLATE utf8_unicode_ci NOT NULL,
  `thumbnail_w` int(11) NOT NULL,
  `thumbnail_h` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `popdown_handlers`
--

DROP TABLE IF EXISTS `popdown_handlers`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `popdown_handlers` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `posts`
--

DROP TABLE IF EXISTS `posts`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `posts` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `board` int(11) NOT NULL,
  `thread` int(11) NOT NULL,
  `number` int(11) NOT NULL,
  `user` int(11) NOT NULL,
  `password` varchar(12) COLLATE utf8_unicode_ci DEFAULT NULL,
  `name` varchar(128) COLLATE utf8_unicode_ci DEFAULT NULL,
  `tripcode` varchar(128) COLLATE utf8_unicode_ci DEFAULT NULL,
  `ip` bigint(20) DEFAULT NULL,
  `subject` varchar(128) COLLATE utf8_unicode_ci DEFAULT NULL,
  `date_time` datetime DEFAULT NULL,
  `text` text COLLATE utf8_unicode_ci,
  `sage` bit(1) DEFAULT NULL,
  `deleted` bit(1) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `board` (`board`),
  KEY `user` (`user`),
  KEY `thread` (`thread`),
  CONSTRAINT `posts_ibfk_1` FOREIGN KEY (`board`) REFERENCES `boards` (`id`),
  CONSTRAINT `posts_ibfk_2` FOREIGN KEY (`user`) REFERENCES `users` (`id`),
  CONSTRAINT `posts_ibfk_3` FOREIGN KEY (`thread`) REFERENCES `threads` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=924 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `posts_files`
--

DROP TABLE IF EXISTS `posts_files`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `posts_files` (
  `post` int(11) NOT NULL,
  `file` int(11) NOT NULL,
  `deleted` bit(1) NOT NULL,
  UNIQUE KEY `post` (`post`,`file`),
  KEY `file` (`file`),
  CONSTRAINT `posts_files_ibfk_1` FOREIGN KEY (`post`) REFERENCES `posts` (`id`),
  CONSTRAINT `posts_files_ibfk_2` FOREIGN KEY (`file`) REFERENCES `files` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `posts_images`
--

DROP TABLE IF EXISTS `posts_images`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `posts_images` (
  `post` int(11) NOT NULL,
  `image` int(11) NOT NULL,
  `deleted` bit(1) NOT NULL,
  UNIQUE KEY `post` (`post`,`image`),
  KEY `image` (`image`),
  CONSTRAINT `posts_images_ibfk_1` FOREIGN KEY (`post`) REFERENCES `posts` (`id`),
  CONSTRAINT `posts_images_ibfk_2` FOREIGN KEY (`image`) REFERENCES `images` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `posts_links`
--

DROP TABLE IF EXISTS `posts_links`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `posts_links` (
  `post` int(11) NOT NULL,
  `link` int(11) NOT NULL,
  `deleted` bit(1) NOT NULL,
  UNIQUE KEY `post` (`post`,`link`),
  KEY `link` (`link`),
  CONSTRAINT `posts_links_ibfk_1` FOREIGN KEY (`post`) REFERENCES `posts` (`id`),
  CONSTRAINT `posts_links_ibfk_2` FOREIGN KEY (`link`) REFERENCES `links` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `posts_uploads`
--

DROP TABLE IF EXISTS `posts_uploads`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `posts_uploads` (
  `post` int(11) NOT NULL,
  `upload` int(11) NOT NULL,
  UNIQUE KEY `post` (`post`,`upload`),
  KEY `upload` (`upload`),
  CONSTRAINT `posts_uploads_ibfk_2` FOREIGN KEY (`post`) REFERENCES `posts` (`id`),
  CONSTRAINT `posts_uploads_ibfk_3` FOREIGN KEY (`upload`) REFERENCES `uploads` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `posts_videos`
--

DROP TABLE IF EXISTS `posts_videos`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `posts_videos` (
  `post` int(11) NOT NULL,
  `video` int(11) NOT NULL,
  `deleted` bit(1) NOT NULL,
  UNIQUE KEY `post` (`post`,`video`),
  KEY `video` (`video`),
  CONSTRAINT `posts_videos_ibfk_1` FOREIGN KEY (`post`) REFERENCES `posts` (`id`),
  CONSTRAINT `posts_videos_ibfk_2` FOREIGN KEY (`video`) REFERENCES `videos` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `stylesheets`
--

DROP TABLE IF EXISTS `stylesheets`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `stylesheets` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `threads`
--

DROP TABLE IF EXISTS `threads`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `threads` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `board` int(11) NOT NULL,
  `original_post` int(11) DEFAULT NULL,
  `bump_limit` int(11) DEFAULT NULL,
  `deleted` bit(1) NOT NULL,
  `archived` bit(1) NOT NULL,
  `sticky` bit(1) NOT NULL DEFAULT b'0',
  `sage` bit(1) NOT NULL,
  `with_attachments` bit(1) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `board` (`board`),
  CONSTRAINT `threads_ibfk_1` FOREIGN KEY (`board`) REFERENCES `boards` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=840 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `upload_handlers`
--

DROP TABLE IF EXISTS `upload_handlers`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `upload_handlers` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `upload_types`
--

DROP TABLE IF EXISTS `upload_types`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `upload_types` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `extension` varchar(10) COLLATE utf8_unicode_ci NOT NULL,
  `store_extension` varchar(10) COLLATE utf8_unicode_ci DEFAULT NULL,
  `is_image` bit(1) NOT NULL,
  `upload_handler` int(11) NOT NULL,
  `thumbnail_image` varchar(256) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `extension` (`extension`),
  KEY `upload_handler` (`upload_handler`),
  CONSTRAINT `upload_types_ibfk_1` FOREIGN KEY (`upload_handler`) REFERENCES `upload_handlers` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `uploads`
--

DROP TABLE IF EXISTS `uploads`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `uploads` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `hash` varchar(32) COLLATE utf8_unicode_ci DEFAULT NULL,
  `is_image` bit(1) NOT NULL,
  `upload_type` tinyint(4) NOT NULL,
  `file` varchar(2048) COLLATE utf8_unicode_ci NOT NULL,
  `image_w` int(11) DEFAULT NULL,
  `image_h` int(11) DEFAULT NULL,
  `size` int(11) NOT NULL,
  `thumbnail` varchar(2048) COLLATE utf8_unicode_ci DEFAULT NULL,
  `thumbnail_w` int(11) DEFAULT NULL,
  `thumbnail_h` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=441 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `user_groups`
--

DROP TABLE IF EXISTS `user_groups`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `user_groups` (
  `user` int(11) NOT NULL,
  `group` int(11) NOT NULL,
  UNIQUE KEY `user` (`user`,`group`),
  KEY `group` (`group`),
  CONSTRAINT `user_groups_ibfk_1` FOREIGN KEY (`group`) REFERENCES `groups` (`id`),
  CONSTRAINT `user_groups_ibfk_2` FOREIGN KEY (`user`) REFERENCES `users` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `keyword` varchar(32) COLLATE utf8_unicode_ci DEFAULT NULL,
  `posts_per_thread` int(11) DEFAULT NULL,
  `threads_per_page` int(11) DEFAULT NULL,
  `lines_per_post` int(11) DEFAULT NULL,
  `language` int(11) NOT NULL,
  `stylesheet` int(11) NOT NULL,
  `password` varchar(12) COLLATE utf8_unicode_ci DEFAULT NULL,
  `goto` varchar(32) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `key` (`keyword`),
  KEY `language` (`language`),
  KEY `stylesheet` (`stylesheet`),
  CONSTRAINT `users_ibfk_1` FOREIGN KEY (`language`) REFERENCES `languages` (`id`),
  CONSTRAINT `users_ibfk_2` FOREIGN KEY (`stylesheet`) REFERENCES `stylesheets` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=24 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `videos`
--

DROP TABLE IF EXISTS `videos`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `videos` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `code` varchar(256) COLLATE utf8_unicode_ci NOT NULL,
  `widht` int(11) NOT NULL,
  `height` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
SET character_set_client = @saved_cs_client;

--
-- Dumping routines for database 'kotoba2'
--
/*!50003 DROP PROCEDURE IF EXISTS `sp_acl_add` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'STRICT_TRANS_TABLES' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50020 DEFINER=`root`@`localhost`*/ /*!50003 PROCEDURE `sp_acl_add`(
    group_id int,
    board_id int,
    thread_id int,
    post_id int,
    _view bit,
    _change bit,
    _moderate bit
)
begin
    insert into acl (`group`, board, thread, post, `view`, `change`, moderate)
    values (group_id, board_id, thread_id, post_id, _view, _change, _moderate);
end */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `sp_acl_delete` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'STRICT_TRANS_TABLES' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50020 DEFINER=`root`@`localhost`*/ /*!50003 PROCEDURE `sp_acl_delete`(
    group_id int,
    board_id int,
    thread_id int,
    post_id int
)
begin
    delete from acl
    where ((`group` = group_id) or (coalesce(`group`, group_id) is null))
        and ((board = board_id) or (coalesce(board, board_id) is null))
        and ((thread = thread_id) or (coalesce(thread, thread_id) is null))
        and ((post = post_id) or (coalesce(post, post_id) is null));
end */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `sp_acl_edit` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'STRICT_TRANS_TABLES' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50020 DEFINER=`root`@`localhost`*/ /*!50003 PROCEDURE `sp_acl_edit`(
    group_id int,
    board_id int,
    thread_id int,
    post_id int,
    _view bit,
    _change bit,
    _moderate bit
)
begin
    update acl set `view` = _view, `change` = _change, moderate = _moderate
    where ((`group` = group_id) or (coalesce(`group`, group_id) is null))
        and ((board = board_id) or (coalesce(board, board_id) is null))
        and ((thread = thread_id) or (coalesce(thread, thread_id) is null))
        and ((post = post_id) or (coalesce(post, post_id) is null));
end */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `sp_acl_get_all` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'STRICT_TRANS_TABLES' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50020 DEFINER=`root`@`localhost`*/ /*!50003 PROCEDURE `sp_acl_get_all`()
begin
    select `group`, board, thread, post, `view`, `change`, moderate
    from acl order by `group`, board, thread, post;
end */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `sp_bans_add` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'STRICT_TRANS_TABLES' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50020 DEFINER=`root`@`localhost`*/ /*!50003 PROCEDURE `sp_bans_add`(
    _range_beg int,
    _range_end int,
    _reason text,
    _untill datetime
)
begin
    call sp_refresh_banlist();
    insert into bans (range_beg, range_end, reason, untill)
        values (_range_beg, _range_end, _reason, _untill);
end */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `sp_bans_check` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'STRICT_TRANS_TABLES' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50020 DEFINER=`root`@`localhost`*/ /*!50003 PROCEDURE `sp_bans_check`(
    ip int
)
begin
    call sp_bans_refresh();
    select range_beg, range_end, untill, reason
        from bans
        where range_beg <= ip and range_end >= ip
        order by range_end desc limit 1;
end */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `sp_bans_delete_byid` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'STRICT_TRANS_TABLES,NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50020 DEFINER=`root`@`localhost`*/ /*!50003 PROCEDURE `sp_bans_delete_byid`(
	_id int
)
begin
	delete from bans where id = _id;
end */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `sp_bans_delete_byip` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'STRICT_TRANS_TABLES,NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50020 DEFINER=`root`@`localhost`*/ /*!50003 PROCEDURE `sp_bans_delete_byip`(
	ip int
)
begin
	delete from bans where range_beg <= ip and range_end >= ip;
end */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `sp_bans_delete_by_id` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'STRICT_TRANS_TABLES' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50020 DEFINER=`root`@`localhost`*/ /*!50003 PROCEDURE `sp_bans_delete_by_id`(
    _id int
)
begin
    delete from bans where id = _id;
end */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `sp_bans_delete_by_ip` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'STRICT_TRANS_TABLES' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50020 DEFINER=`root`@`localhost`*/ /*!50003 PROCEDURE `sp_bans_delete_by_ip`(
    ip int
)
begin
    delete from bans where range_beg <= ip and range_end >= ip;
end */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `sp_bans_get_all` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'STRICT_TRANS_TABLES' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50020 DEFINER=`root`@`localhost`*/ /*!50003 PROCEDURE `sp_bans_get_all`()
begin
    select id, range_beg, range_end, reason, untill from bans;
end */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `sp_bans_refresh` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'STRICT_TRANS_TABLES' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50020 DEFINER=`root`@`localhost`*/ /*!50003 PROCEDURE `sp_bans_refresh`()
begin
    delete from bans where untill <= now();
end */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `sp_boards_add` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'STRICT_TRANS_TABLES' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50020 DEFINER=`root`@`localhost`*/ /*!50003 PROCEDURE `sp_boards_add`(
    _name varchar(16),
    _title varchar(50),
    _annotation text,
    _bump_limit int,
    _force_anonymous bit,
    _default_name varchar(128),
    _with_attachments bit,
    _enable_macro bit,
    _enable_youtube bit,
    _enable_captcha bit,
    _same_upload varchar(32),
    _popdown_handler int,
    _category int
)
begin
    insert into boards (name, title, annotation, bump_limit, force_anonymous,
            default_name, with_attachments, enable_macro, enable_youtube,
            enable_captcha, same_upload, popdown_handler, category)
        values (_name, _title, _annotation, _bump_limit, _force_anonymous,
            _default_name, _with_attachments, _enable_macro, _enable_youtube,
            _enable_captcha, _same_upload, _popdown_handler, _category);
end */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `sp_boards_delete` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'STRICT_TRANS_TABLES' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50020 DEFINER=`root`@`localhost`*/ /*!50003 PROCEDURE `sp_boards_delete`(
	_id int
)
begin
	delete from boards where id = _id;
end */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `sp_boards_edit` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'STRICT_TRANS_TABLES' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50020 DEFINER=`root`@`localhost`*/ /*!50003 PROCEDURE `sp_boards_edit`(
    _id int,
    _title varchar(50),
    _annotation text,
    _bump_limit int,
    _force_anonymous bit,
    _default_name varchar(128),
    _with_attachments bit,
    _enable_macro bit,
    _enable_youtube bit,
    _enable_captcha bit,
    _same_upload varchar(32),
    _popdown_handler int,
    _category int
)
begin
    update boards set title = _title, annotation = _annotation,
            bump_limit = _bump_limit, force_anonymous = _force_anonymous,
            default_name = _default_name, with_attachments = _with_attachments,
            enable_macro = _enable_macro, enable_youtube = _enable_youtube,
            enable_captcha = _enable_captcha, same_upload = _same_upload,
            popdown_handler = _popdown_handler, category = _category
        where id = _id;
end */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `sp_boards_edit_annotation` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'STRICT_TRANS_TABLES,NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50020 DEFINER=`root`@`localhost`*/ /*!50003 PROCEDURE `sp_boards_edit_annotation`(
	_id int,
	_annotation text
)
begin
	update boards set annotation = _annotation where id = _id;
end */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `sp_boards_get_all` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'STRICT_TRANS_TABLES' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50020 DEFINER=`root`@`localhost`*/ /*!50003 PROCEDURE `sp_boards_get_all`()
begin
    select id, name, title, annotation, bump_limit, force_anonymous,
            default_name, with_attachments, enable_macro, enable_youtube,
            enable_captcha, same_upload, popdown_handler, category
        from boards;
end */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `sp_boards_get_all_change` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'STRICT_TRANS_TABLES,NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50020 DEFINER=`root`@`localhost`*/ /*!50003 PROCEDURE `sp_boards_get_all_change`(
	user_id int
)
begin
	select b.id, b.`name`, b.title, b.bump_limit, b.force_anonymous,
		b.default_name, b.with_files, b.same_upload, b.popdown_handler,
		ct.`name` as category
	from boards b
	join categories ct on ct.id = b.category
	join user_groups ug on ug.user = user_id
	left join acl a1 on ug.`group` = a1.`group` and b.id = a1.board
	left join acl a2 on a2.`group` is null and b.id = a2.board
	left join acl a3 on ug.`group` = a3.`group` and a3.board is null
		and a3.thread is null and a3.post is null
	where
			
		((a1.`view` = 1 or a1.`view` is null)
			
			and (a2.`view` = 1 or a2.`view` is null)
			
			and a3.`view` = 1)
			
		and (a1.change = 1
			
			
			or (a1.change is null and a2.change = 1)
			
			
			or (a1.change is null and a2.change is null and a3.change = 1))
	group by b.id
	order by b.category, b.`name`;
end */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `sp_boards_get_all_view` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'STRICT_TRANS_TABLES,NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50020 DEFINER=`root`@`localhost`*/ /*!50003 PROCEDURE `sp_boards_get_all_view`(
	user_id int
)
begin
	select b.id, b.`name`, b.title, b.annotation, b.bump_limit, b.force_anonymous,
		b.default_name, b.with_files, b.same_upload, b.popdown_handler,
		ct.`name` as category
	from boards b
	join categories ct on ct.id = b.category
	join user_groups ug on ug.user = user_id
	left join acl a1 on ug.`group` = a1.`group` and b.id = a1.board
	left join acl a2 on a2.`group` is null and b.id = a2.board
	left join acl a3 on ug.`group` = a3.`group` and a3.board is null
		and a3.thread is null and a3.post is null
	where
		
		(a1.`view` = 1 or a1.`view` is null)
		
		and (a2.`view` = 1 or a2.`view` is null)
		
		and a3.`view` = 1
	group by b.id
	order by b.category, b.`name`;
end */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `sp_boards_get_by_id` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'STRICT_TRANS_TABLES' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50020 DEFINER=`root`@`localhost`*/ /*!50003 PROCEDURE `sp_boards_get_by_id`(
    board_id int
)
begin
    select id, name, title, annotation, bump_limit, force_anonymous,
            default_name, with_attachments, enable_macro, enable_youtube,
            enable_captcha, same_upload, popdown_handler, category
        from boards where id = board_id;
end */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `sp_boards_get_by_name` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'STRICT_TRANS_TABLES' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50020 DEFINER=`root`@`localhost`*/ /*!50003 PROCEDURE `sp_boards_get_by_name`(
    board_name varchar(16)
)
begin
    select id, name, title, annotation, bump_limit, force_anonymous,
            default_name, with_attachments, enable_macro, enable_youtube,
            enable_captcha, same_upload, popdown_handler, category
        from boards where name = board_name;
end */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `sp_boards_get_changeable` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'STRICT_TRANS_TABLES' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50020 DEFINER=`root`@`localhost`*/ /*!50003 PROCEDURE `sp_boards_get_changeable`(
    user_id int
)
begin
    select b.id, b.name, b.title, b.annotation, b.bump_limit, b.force_anonymous,
            b.default_name, b.with_attachments, b.enable_macro,
            b.enable_youtube, b.enable_captcha, b.same_upload,
            b.popdown_handler, b.category, ct.name as category_name
        from boards b
        join categories ct on ct.id = b.category
        join user_groups ug on ug.user = user_id
        left join acl a1 on ug.`group` = a1.`group` and b.id = a1.board
        left join acl a2 on a2.`group` is null and b.id = a2.board
        left join acl a3 on ug.`group` = a3.`group` and a3.board is null
            and a3.thread is null and a3.post is null
        where
            
            ((a1.`view` = 1 or a1.`view` is null)
                
                and (a2.`view` = 1 or a2.`view` is null)
                
                and a3.`view` = 1)
            
            and (a1.change = 1
                
                
                or (a1.change is null and a2.change = 1)
                
                
                or (a1.change is null and a2.change is null and a3.change = 1))
        group by b.id
        order by b.category, b.name;
end */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `sp_boards_get_changeable_by_id` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'STRICT_TRANS_TABLES' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50020 DEFINER=`root`@`localhost`*/ /*!50003 PROCEDURE `sp_boards_get_changeable_by_id`(
	_board_id int,
	user_id int
)
begin
	declare board_id int;
	select id into board_id from boards where id = _board_id;
	if(board_id is null) then
		select 'NOT_FOUND' as error;
	else
		select b.id, b.name, b.title, b.annotation, b.bump_limit, b.force_anonymous,
			b.default_name, b.with_attachments, b.enable_macro, b.enable_youtube,
			b.enable_captcha, b.same_upload, b.popdown_handler, b.category,
			ct.name as category_name
		from boards b
		join categories ct on ct.id = b.category
		join user_groups ug on ug.user = user_id
		
		left join acl a1 on ug.`group` = a1.`group` and b.id = a1.board
		
		left join acl a2 on a2.`group` is null and b.id = a2.board
		
		left join acl a3 on ug.`group` = a3.`group` and a3.board is null
			and a3.thread is null and a3.post is null
		where
			b.id = board_id
			and
				
			((a1.`view` = 1 or a1.`view` is null)
				
				and (a2.`view` = 1 or a2.`view` is null)
				
				and a3.`view` = 1)
				
			and (a1.change = 1
				
				
				or (a1.change is null and a2.change = 1)
				
				
				or (a1.change is null and a2.change is null and a3.change = 1))
		group by b.id
		order by b.category, b.name;
	end if;
end */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `sp_boards_get_changeable_by_name` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'STRICT_TRANS_TABLES' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50020 DEFINER=`root`@`localhost`*/ /*!50003 PROCEDURE `sp_boards_get_changeable_by_name`(
	board_name varchar(16),
	user_id int
)
begin
	declare board_id int;
	select id into board_id from boards where name = board_name;
	if(board_id is null) then
		select 'NOT_FOUND' as error;
	else
		select b.id, b.name, b.title, b.annotation, b.bump_limit, b.force_anonymous,
			b.default_name, b.with_attachments, b.enable_macro, b.enable_youtube,
			b.enable_captcha, b.same_upload, b.popdown_handler, b.category,
			ct.name as category_name
		from boards b
		join categories ct on ct.id = b.category
		join user_groups ug on ug.user = user_id
		
		left join acl a1 on ug.`group` = a1.`group` and b.id = a1.board
		
		left join acl a2 on a2.`group` is null and b.id = a2.board
		
		left join acl a3 on ug.`group` = a3.`group` and a3.board is null
			and a3.thread is null and a3.post is null
		where
			b.id = board_id
			and
				
			((a1.`view` = 1 or a1.`view` is null)
				
				and (a2.`view` = 1 or a2.`view` is null)
				
				and a3.`view` = 1)
				
			and (a1.change = 1
				
				
				or (a1.change is null and a2.change = 1)
				
				
				or (a1.change is null and a2.change is null and a3.change = 1))
		group by b.id
		order by b.category, b.name;
	end if;
end */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `sp_boards_get_moderatable` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'STRICT_TRANS_TABLES' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50020 DEFINER=`root`@`localhost`*/ /*!50003 PROCEDURE `sp_boards_get_moderatable`(
	user_id int
)
begin
	select b.id, b.name, b.title, b.annotation, b.bump_limit, b.force_anonymous,
		b.default_name, b.with_attachments, b.enable_macro, b.enable_youtube,
		b.enable_captcha, b.same_upload, b.popdown_handler, b.category
	from boards b
	join user_groups ug on ug.user = user_id
	
	left join acl a1 on ug.`group` = a1.`group` and b.id = a1.board
	
	left join acl a2 on a2.`group` is null and b.id = a2.board
	
	left join acl a3 on ug.`group` = a3.`group` and a3.board is null
		and a3.thread is null and a3.post is null
	where
			
		((a1.`view` = 1 or a1.`view` is null)
			
			and (a2.`view` = 1 or a2.`view` is null)
			
			and a3.`view` = 1)
			
		and (a1.moderate = 1
			
			
			or (a1.moderate is null and a2.moderate = 1)
			
			
			or (a1.moderate is null and a2.moderate is null
				and a3.moderate = 1))
	group by b.id
	order by b.name;
end */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `sp_boards_get_specifed` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'STRICT_TRANS_TABLES,NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50020 DEFINER=`root`@`localhost`*/ /*!50003 PROCEDURE `sp_boards_get_specifed`(
	board_id int
)
begin
	select id, `name`, title, bump_limit, force_anonymous, default_name,
		with_files, same_upload, popdown_handler, category
	from boards where id = board_id;
end */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `sp_boards_get_specifed_byname` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'STRICT_TRANS_TABLES,NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50020 DEFINER=`root`@`localhost`*/ /*!50003 PROCEDURE `sp_boards_get_specifed_byname`(
	board_name varchar(16)
)
begin
	select id, `name`, title, bump_limit, force_anonymous, default_name,
		with_files, same_upload, popdown_handler, category
	from boards where `name` = board_name;
end */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `sp_boards_get_specifed_change` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'STRICT_TRANS_TABLES,NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50020 DEFINER=`root`@`localhost`*/ /*!50003 PROCEDURE `sp_boards_get_specifed_change`(
	_board_id int,
	user_id int
)
begin
	declare board_id int;
	select id into board_id from boards where id = _board_id;
	if(board_id is null) then
		select 'NOT_FOUND' as error;
	else
		select b.id, b.`name`, b.title, b.bump_limit, b.force_anonymous,
			b.default_name, b.with_files, b.same_upload, b.popdown_handler,
			ct.`name` as category
		from boards b
		join categories ct on ct.id = b.category
		join user_groups ug on ug.user = user_id
		
		left join acl a1 on ug.`group` = a1.`group` and b.id = a1.board
		
		left join acl a2 on a2.`group` is null and b.id = a2.board
		
		left join acl a3 on ug.`group` = a3.`group` and a3.board is null
			and a3.thread is null and a3.post is null
		where
			b.id = board_id
			and
				
			((a1.`view` = 1 or a1.`view` is null)
				
				and (a2.`view` = 1 or a2.`view` is null)
				
				and a3.`view` = 1)
				
			and (a1.change = 1
				
				
				or (a1.change is null and a2.change = 1)
				
				
				or (a1.change is null and a2.change is null and a3.change = 1))
		group by b.id
		order by b.category, b.`name`;
	end if;
end */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `sp_boards_get_specifed_change_byname` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'STRICT_TRANS_TABLES,NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50020 DEFINER=`root`@`localhost`*/ /*!50003 PROCEDURE `sp_boards_get_specifed_change_byname`(
	board_name varchar(16),
	user_id int
)
begin
	declare board_id int;
	select id into board_id from boards where `name` = board_name;
	if(board_id is null) then
		select 'NOT_FOUND' as error;
	else
		select b.id, b.`name`, b.title, b.bump_limit, b.force_anonymous,
			b.default_name, b.with_files, b.same_upload, b.popdown_handler,
			ct.`name` as category
		from boards b
		join categories ct on ct.id = b.category
		join user_groups ug on ug.user = user_id
		
		left join acl a1 on ug.`group` = a1.`group` and b.id = a1.board
		
		left join acl a2 on a2.`group` is null and b.id = a2.board
		
		left join acl a3 on ug.`group` = a3.`group` and a3.board is null
			and a3.thread is null and a3.post is null
		where
			b.id = board_id
			and
				
			((a1.`view` = 1 or a1.`view` is null)
				
				and (a2.`view` = 1 or a2.`view` is null)
				
				and a3.`view` = 1)
				
			and (a1.change = 1
				
				
				or (a1.change is null and a2.change = 1)
				
				
				or (a1.change is null and a2.change is null and a3.change = 1))
		group by b.id
		order by b.category, b.`name`;
	end if;
end */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `sp_boards_get_visible` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'STRICT_TRANS_TABLES' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50020 DEFINER=`root`@`localhost`*/ /*!50003 PROCEDURE `sp_boards_get_visible`(
	user_id int
)
begin
	select b.id, b.name, b.title, b.annotation, b.bump_limit, b.force_anonymous,
		b.default_name, b.with_attachments, b.enable_macro, b.enable_youtube,
		b.enable_captcha, b.same_upload, b.popdown_handler, b.category,
		ct.name as category_name
	from boards b
	join categories ct on ct.id = b.category
	join user_groups ug on ug.user = user_id
	left join acl a1 on ug.`group` = a1.`group` and b.id = a1.board
	left join acl a2 on a2.`group` is null and b.id = a2.board
	left join acl a3 on ug.`group` = a3.`group` and a3.board is null
		and a3.thread is null and a3.post is null
	where
		
		(a1.`view` = 1 or a1.`view` is null)
		
		and (a2.`view` = 1 or a2.`view` is null)
		
		and a3.`view` = 1
	group by b.id
	order by b.category, b.name;
end */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `sp_board_upload_types_add` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'STRICT_TRANS_TABLES' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50020 DEFINER=`root`@`localhost`*/ /*!50003 PROCEDURE `sp_board_upload_types_add`(
    board_id int,
    upload_type_id int
)
begin
    insert into board_upload_types (board, upload_type)
        values (board_id, upload_type_id);
end */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `sp_board_upload_types_delete` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'STRICT_TRANS_TABLES' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50020 DEFINER=`root`@`localhost`*/ /*!50003 PROCEDURE `sp_board_upload_types_delete`(
    _board int,
    _upload_type int
)
begin
    delete from board_upload_types
        where board = _board and upload_type = _upload_type;
end */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `sp_board_upload_types_get_all` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'STRICT_TRANS_TABLES' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50020 DEFINER=`root`@`localhost`*/ /*!50003 PROCEDURE `sp_board_upload_types_get_all`()
begin
    select board, upload_type from board_upload_types;
end */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `sp_categories_add` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'STRICT_TRANS_TABLES' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50020 DEFINER=`root`@`localhost`*/ /*!50003 PROCEDURE `sp_categories_add`(
	_name varchar(50)
)
begin
	insert into categories (name) values (_name);
end */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `sp_categories_delete` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'STRICT_TRANS_TABLES' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50020 DEFINER=`root`@`localhost`*/ /*!50003 PROCEDURE `sp_categories_delete`(
	_id int
)
begin
	delete from categories where id = _id;
end */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `sp_categories_get_all` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'STRICT_TRANS_TABLES' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50020 DEFINER=`root`@`localhost`*/ /*!50003 PROCEDURE `sp_categories_get_all`()
begin
	select id, name from categories;
end */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `sp_files_add` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'STRICT_TRANS_TABLES' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50020 DEFINER=`root`@`localhost`*/ /*!50003 PROCEDURE `sp_files_add`(
	_hash varchar(32),
	_name varchar(256),
	_size int,
	_thumbnail varchar(256),
	_thumbnail_w int,
	_thumbnail_h int
)
begin
    insert into files (hash, name, size, thumbnail, thumbnail_w, thumbnail_h)
        values (_hash, _name, _size, _thumbnail, _thumbnail_w, _thumbnail_h);
    select last_insert_id() as id;
end */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `sp_files_get_by_post` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'STRICT_TRANS_TABLES' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50020 DEFINER=`root`@`localhost`*/ /*!50003 PROCEDURE `sp_files_get_by_post`(
	post_id int
)
begin
	select f.id, f.hash, f.name, f.size, f.thumbnail, f.thumbnail_w,
		f.thumbnail_h
	from posts_files pf
	join files f on f.id = pf.file and pf.post = post_id;
end */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `sp_files_get_same` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'STRICT_TRANS_TABLES' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50020 DEFINER=`root`@`localhost`*/ /*!50003 PROCEDURE `sp_files_get_same`(
    board_id int,
    user_id int,
    file_hash varchar(32)
)
begin
	select f.id, f.hash, f.name, f.size, f.thumbnail, f.thumbnail_w,
            f.thumbnail_h, max(case when a1.`view` = 0 then 0
                                    when a2.`view` = 0 then 0
                                    when a3.`view` = 0 then 0
                                    when a4.`view` = 0 then 0
                                    when a5.`view` = 0 then 0
                                    when a6.`view` = 0 then 0
                                    when a7.`view` = 0 then 0
                                    else 1 end) as `view`
        from posts_files pf
        join files f on f.id = pf.file
        join posts p on p.id = pf.post and p.board = board_id
        join threads t on t.id = p.thread
        join user_groups ug on ug.user = user_id
        
        left join acl a1 on a1.`group` = ug.`group` and a1.post = p.id
        
        left join acl a2 on a2.`group` is null and a2.post = p.id
        
        left join acl a3 on a3.`group` = ug.`group` and a3.thread = p.thread
        
        left join acl a4 on a4.`group` is null and a4.thread = p.thread
        
        left join acl a5 on a5.`group` = ug.`group` and a5.board = p.board
        
        left join acl a6 on a6.`group` is null and a6.board = p.board
        
        left join acl a7 on a7.`group` = ug.`group` and a7.board is null
            and a7.thread is null and a7.post is null
        where f.`hash` = file_hash and pf.deleted is null
        group by f.id, p.id;
end */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `sp_groups_add` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'STRICT_TRANS_TABLES' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50020 DEFINER=`root`@`localhost`*/ /*!50003 PROCEDURE `sp_groups_add`(
	_name varchar(50)
)
begin
	insert into groups (name) values (_name);
	select id from groups where name = _name;
	
	
end */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `sp_groups_delete` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'STRICT_TRANS_TABLES' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50020 DEFINER=`root`@`localhost`*/ /*!50003 PROCEDURE `sp_groups_delete`(
	_id int
)
begin
	delete from groups where id = _id;
end */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `sp_groups_get_all` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'STRICT_TRANS_TABLES' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50020 DEFINER=`root`@`localhost`*/ /*!50003 PROCEDURE `sp_groups_get_all`()
begin
	select id, name from groups order by id;
end */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `sp_hidden_threads_add` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'STRICT_TRANS_TABLES' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50020 DEFINER=`root`@`localhost`*/ /*!50003 PROCEDURE `sp_hidden_threads_add`(
	thread_id int,
	user_id int
)
begin
	insert into hidden_threads (user, thread) values (user_id, thread_id);
end */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `sp_hidden_threads_delete` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'STRICT_TRANS_TABLES' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50020 DEFINER=`root`@`localhost`*/ /*!50003 PROCEDURE `sp_hidden_threads_delete`(
	thread_id int,
	user_id int
)
begin
	delete from hidden_threads where user = user_id and thread = thread_id;
end */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `sp_hidden_threads_get_by_board` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'STRICT_TRANS_TABLES' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50020 DEFINER=`root`@`localhost`*/ /*!50003 PROCEDURE `sp_hidden_threads_get_by_board`(
	board_id int
)
begin
	select ht.thread, t.original_post, ht.user
	from hidden_threads ht
	join threads t on t.id = ht.thread and t.board = board_id;
end */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `sp_hidden_threads_get_visible` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'STRICT_TRANS_TABLES' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50020 DEFINER=`root`@`localhost`*/ /*!50003 PROCEDURE `sp_hidden_threads_get_visible`(
	board_id int,
	thread_num int,
	user_id int
)
begin
	declare thread_id int;
	select id into thread_id from threads
	where original_post = thread_num and board = board_id;
	if thread_id is null
	then
		select 'NOT_FOUND' as error;
	else
		select t.id, t.original_post, t.bump_limit, t.archived, t.sage,
			t.sticky, t.with_attachments, count(p.id) as posts_count
		from posts p
		join threads t on t.id = p.thread
		join user_groups ug on ug.`user` = user_id
		left join hidden_threads ht on t.id = ht.thread
			and ug.`user` = ht.`user`
		
		left join acl a1 on a1.`group` = ug.`group` and a1.post = p.id
		
		left join acl a2 on a2.`group` is null and a2.post = p.id
		
		left join acl a3 on a3.`group` = ug.`group` and a3.thread = t.id
		
		left join acl a4 on a4.`group` is null and a4.thread = t.id
		
		left join acl a5 on a5.`group` = ug.`group` and a5.board = t.board
		
		left join acl a6 on a6.`group` is null and a6.board = t.board
		
		left join acl a7 on a7.`group` = ug.`group` and a7.board is null
			and a7.thread is null and a7.post is null
		where t.id = thread_id
			and t.deleted = 0
			and ht.thread is not null
			and p.deleted = 0
			
				
			and ((a3.`view` = 1 or a3.`view` is null)
				
				and (a4.`view` = 1 or a4.`view` is null)
				
				and (a5.`view` = 1 or a5.`view` is null)
				
				and (a6.`view` = 1 or a6.`view` is null)
				
				and a7.`view` = 1)
			
			
				
			and ((a1.`view` = 1 or a1.`view` is null)
				
				and (a2.`view` = 1 or a2.`view` is null)
				
				and (a3.`view` = 1 or a3.`view` is null)
				
				and (a4.`view` = 1 or a4.`view` is null)
				
				and (a5.`view` = 1 or a5.`view` is null)
				
				and (a6.`view` = 1 or a6.`view` is null)
				
				and a7.`view` = 1)
		group by t.id;
	end if;
end */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `sp_images_get_by_post` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'STRICT_TRANS_TABLES' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50020 DEFINER=`root`@`localhost`*/ /*!50003 PROCEDURE `sp_images_get_by_post`(
	post_id int
)
begin
	select i.id, i.hash, i.name, i.widht, i.height, i.size, i.thumbnail,
		i.thumbnail_w, i.thumbnail_h
	from posts_images pi
	join images i on i.id = pi.image and pi.post = post_id;
end */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `sp_images_get_same` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'STRICT_TRANS_TABLES' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50020 DEFINER=`root`@`localhost`*/ /*!50003 PROCEDURE `sp_images_get_same`(
	board_id int,
	user_id int,
	image_hash varchar(32)
)
begin
	select i.id, i.hash, i.name, i.widht, i.height, i.size, i.thumbnail,
		i.thumbnail_w, i.thumbnail_h, p.number, t.original_post,
		max(case
			when a1.`view` = 0 then 0
			when a2.`view` = 0 then 0
			when a3.`view` = 0 then 0
			when a4.`view` = 0 then 0
			when a5.`view` = 0 then 0
			when a6.`view` = 0 then 0
			when a7.`view` = 0 then 0
			else 1 end) as `view`
	from images i
	join posts_images pi on pi.image = i.id
	join posts p on p.id = pi.post and p.board = board_id
	join threads t on t.id = p.thread
	join user_groups ug on ug.`user` = user_id
	
	left join acl a1 on a1.`group` = ug.`group` and a1.post = p.id
	
	left join acl a2 on a2.`group` is null and a2.post = p.id
	
	left join acl a3 on a3.`group` = ug.`group` and a3.thread = p.thread
	
	left join acl a4 on a4.`group` is null and a4.thread = p.thread
	
	left join acl a5 on a5.`group` = ug.`group` and a5.board = p.board
	
	left join acl a6 on a6.`group` is null and a6.board = p.board
	
	left join acl a7 on a7.`group` = ug.`group` and a7.board is null
		and a7.thread is null and a7.post is null
	where i.hash = image_hash and pi.deleted is null
	group by i.id, p.id;
end */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `sp_languages_add` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'STRICT_TRANS_TABLES' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50020 DEFINER=`root`@`localhost`*/ /*!50003 PROCEDURE `sp_languages_add`(
	_code char(3)
)
begin
	insert into languages (code) values (_code);
end */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `sp_languages_delete` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'STRICT_TRANS_TABLES' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50020 DEFINER=`root`@`localhost`*/ /*!50003 PROCEDURE `sp_languages_delete`(
	_id int
)
begin
	delete from languages where id = _id;
end */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `sp_languages_get_all` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'STRICT_TRANS_TABLES' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50020 DEFINER=`root`@`localhost`*/ /*!50003 PROCEDURE `sp_languages_get_all`()
begin
	select id, code from languages;
end */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `sp_links_get_by_post` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'STRICT_TRANS_TABLES' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50020 DEFINER=`root`@`localhost`*/ /*!50003 PROCEDURE `sp_links_get_by_post`(
	post_id int
)
begin
	select l.id, l.url, l.widht, l.height, l.size, l.thumbnail, l.thumbnail_w,
		l.thumbnail_h
	from posts_links pl
	join links l on l.id = pl.link and pl.post = post_id;
end */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `sp_popdown_handlers_add` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'STRICT_TRANS_TABLES' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50020 DEFINER=`root`@`localhost`*/ /*!50003 PROCEDURE `sp_popdown_handlers_add`(
	_name varchar(50)
)
begin
	insert into popdown_handlers (name) values (_name);
end */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `sp_popdown_handlers_delete` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'STRICT_TRANS_TABLES' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50020 DEFINER=`root`@`localhost`*/ /*!50003 PROCEDURE `sp_popdown_handlers_delete`(
	_id int
)
begin
	delete from popdown_handlers where id = _id;
end */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `sp_popdown_handlers_get_all` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'STRICT_TRANS_TABLES' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50020 DEFINER=`root`@`localhost`*/ /*!50003 PROCEDURE `sp_popdown_handlers_get_all`()
begin
	select id, name from popdown_handlers;
end */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `sp_posts_add` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'STRICT_TRANS_TABLES' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50020 DEFINER=`root`@`localhost`*/ /*!50003 PROCEDURE `sp_posts_add`(
	board_id int,
	thread_id int,
	user_id int,
	_password varchar(128),
	_name varchar(128),
	_tripcode varchar(128),
	_ip bigint,
	_subject varchar(128),
	_date_time datetime,
	_text text,
	_sage bit
)
begin
	declare count_posts int;	
	declare post_number int;	
	declare bumplimit int;		
	declare threadsage bit;		
	declare post_id int;
	select max(number) into post_number from posts where board = board_id;
	if(post_number is null)
	then
		set post_number = 1;
	else
		set post_number = post_number + 1;
	end if;
	select bump_limit into bumplimit from threads where id = thread_id;
	select count(id) into count_posts from posts where thread = thread_id;
	select sage into threadsage from threads where id = thread_id;
	if(threadsage is not null and threadsage = 1)
	then
		set _sage = 1;
	end if;
	if(count_posts > bumplimit)
	then
		set _sage = 1;
	end if;
	if(_date_time is null)
	then
		set _date_time = now();
	end if;
	insert into posts (board, thread, number, user, password, name,
		tripcode, ip, subject, date_time, text, sage, deleted)
	values (board_id, thread_id, post_number, user_id, _password, _name,
		_tripcode, _ip, _subject, _date_time, _text, _sage, 0);
	select last_insert_id() into post_id;
	select id, board, thread, number, user, password, name, tripcode, ip,
		subject, date_time, `text`, sage
	from posts where id = post_id;
end */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `sp_posts_delete` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'STRICT_TRANS_TABLES' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50020 DEFINER=`root`@`localhost`*/ /*!50003 PROCEDURE `sp_posts_delete`(
	_id int
)
begin
	declare thread_id int;
	set thread_id = null;
	
	
	select p.thread into thread_id
	from posts p
	join threads t on t.id = p.thread and p.id = _id
		and p.number = t.original_post;
	if(thread_id is null) then
		update posts set deleted = 1 where id = _id;
	else
		update threads set deleted = 1 where id = thread_id;
		update posts set deleted = 1 where thread = thread_id;
	end if;
end */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `sp_posts_delete_all_marked` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'STRICT_TRANS_TABLES,NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50020 DEFINER=`root`@`localhost`*/ /*!50003 PROCEDURE `sp_posts_delete_all_marked`()
begin
	delete pu from posts_uploads pu
	join posts p on p.id = pu.post
	where p.deleted = 1;

	delete a from acl a
	join posts p on p.id = a.post
	where p.deleted = 1;

	delete from posts where deleted = 1;

	delete ht from hidden_threads ht
	join threads t on t.id = ht.thread
	where t.deleted = 1;

	delete from threads where deleted = 1;
end */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `sp_posts_delete_last` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'STRICT_TRANS_TABLES' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50020 DEFINER=`root`@`localhost`*/ /*!50003 PROCEDURE `sp_posts_delete_last`(
	_id int,
	_date_time datetime
)
begin
	declare _ip bigint;
	declare done int default 0;
	declare thread_id int;
	declare `c` cursor for
		select t.id
		from posts p
		join (select ip from posts where id = _id) q on q.ip = p.ip
		join threads t on t.id = p.thread and p.`date_time` > _date_time
			and p.`number` = t.original_post;
	declare continue handler for not found set done = 1;
	open `c`;
	repeat
	fetch `c` into thread_id;
	if(not done) then
		call sp_threads_edit_deleted(thread_id);
	end if;
	until done end repeat;
	close `c`;
	select ip into _ip from posts where id = _id;
	if(_ip is not null) then
		update posts set deleted = 1 where ip = _ip and `date_time` > _date_time;
	end if;
end */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `sp_posts_delete_marked` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'STRICT_TRANS_TABLES' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50020 DEFINER=`root`@`localhost`*/ /*!50003 PROCEDURE `sp_posts_delete_marked`()
begin
	delete pu from posts_uploads pu
	join posts p on p.id = pu.post
	where p.deleted = 1;

	delete a from acl a
	join posts p on p.id = a.post
	where p.deleted = 1;

	delete from posts where deleted = 1;

	delete ht from hidden_threads ht
	join threads t on t.id = ht.thread
	where t.deleted = 1;

	delete from threads where deleted = 1;
end */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `sp_posts_edit_specifed_addtext` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'STRICT_TRANS_TABLES,NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50020 DEFINER=`root`@`localhost`*/ /*!50003 PROCEDURE `sp_posts_edit_specifed_addtext`(
	_id int,
	_text text
)
begin
	update posts set text = concat(text, _text) where id = _id;
end */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `sp_posts_edit_text_by_id` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'STRICT_TRANS_TABLES' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50020 DEFINER=`root`@`localhost`*/ /*!50003 PROCEDURE `sp_posts_edit_text_by_id`(
	_id int,
	_text text
)
begin
	update posts set text = concat(text, _text) where id = _id;
end */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `sp_posts_files_add` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'STRICT_TRANS_TABLES' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50020 DEFINER=`root`@`localhost`*/ /*!50003 PROCEDURE `sp_posts_files_add`(
    _post int,
    _file int,
    _deleted bit
)
begin
    insert into posts_files (post, file, deleted)
        values (_post, _file, _deleted);
end */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `sp_posts_files_get_by_post` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'STRICT_TRANS_TABLES' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50020 DEFINER=`root`@`localhost`*/ /*!50003 PROCEDURE `sp_posts_files_get_by_post`(
    post_id int
)
begin
    select post, file, deleted from posts_files where post = post_id;
end */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `sp_posts_get_all` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'STRICT_TRANS_TABLES' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50020 DEFINER=`root`@`localhost`*/ /*!50003 PROCEDURE `sp_posts_get_all`()
begin
	select p.id, p.board, b.name as board_name, p.thread,
		t.original_post as thread_number, p.number, p.password, p.name,
		p.tripcode, p.ip, p.subject, p.date_time, p.text, p.sage
	from posts p
	join threads t on t.id = p.thread
	join boards b on b.id = p.board
	where p.deleted = 0 and t.deleted = 0 and t.archived = 0
	order by p.date_time desc;
end */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `sp_posts_get_all_numbers` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'STRICT_TRANS_TABLES,NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50020 DEFINER=`root`@`localhost`*/ /*!50003 PROCEDURE `sp_posts_get_all_numbers`()
begin
	select p.`number` as post, t.`original_post` as thread, b.`name` as board
	from posts p
	join threads t on t.id = p.thread
	join boards b on b.id = p.board
	where p.deleted = 0 and t.deleted = 0 and t.archived = 0
	order by p.`number`, t.`original_post`, b.`name` asc;
end */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `sp_posts_get_by_board` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'STRICT_TRANS_TABLES' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50020 DEFINER=`root`@`localhost`*/ /*!50003 PROCEDURE `sp_posts_get_by_board`(
	board_id int
)
begin
	select p.id, p.thread, t.original_post as thread_number, p.board,
		b.name as board_name, p.number, p.password, p.name, p.tripcode,
		p.ip, p.subject, p.date_time, p.text, p.sage
	from posts p
	join threads t on t.id = p.thread
	join boards b on b.id = p.board
	where p.deleted = 0 and t.deleted = 0 and t.archived = 0
		and p.board = board_id
	order by p.date_time desc;
end */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `sp_posts_get_by_thread` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'STRICT_TRANS_TABLES' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50020 DEFINER=`root`@`localhost`*/ /*!50003 PROCEDURE `sp_posts_get_by_thread`(
	thread_id int
)
begin
	select id, thread, number, password, name, tripcode, ip, subject,
		date_time, text, sage
	from posts p
	where thread = thread_id;
end */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `sp_posts_get_specifed_view_bynumber` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'STRICT_TRANS_TABLES,NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50020 DEFINER=`root`@`localhost`*/ /*!50003 PROCEDURE `sp_posts_get_specifed_view_bynumber`(
	board_id int,
	post_num int,
	user_id int
)
begin
	select p.id, p.thread, p.`number`, p.password, p.`name`, p.tripcode, p.ip,
		p.subject, p.date_time, p.text, p.sage
	from posts p
	join user_groups ug on ug.`user` = user_id
	
	left join acl a1 on a1.`group` = ug.`group` and a1.post = p.id
	
	left join acl a2 on a2.`group` is null and a2.post = p.id
	
	left join acl a3 on a3.`group` = ug.`group` and a3.thread = p.thread
	
	left join acl a4 on a4.`group` is null and a4.thread = p.thread
	
	left join acl a5 on a5.`group` = ug.`group` and a5.board = p.board
	
	left join acl a6 on a6.`group` is null and a6.board = p.board
	
	left join acl a7 on a7.`group` = ug.`group` and a7.board is null and
		a7.thread is null and a7.post is null
	where p.board = board_id
		and p.`number` = post_num
		and p.deleted = 0
		
			
		and ((a1.`view` = 1 or a1.`view` is null)
			
			and (a2.`view` = 1 or a2.`view` is null)
			
			and (a3.`view` = 1 or a3.`view` is null)
			
			and (a4.`view` = 1 or a4.`view` is null)
			
			and (a5.`view` = 1 or a5.`view` is null)
			
			and (a6.`view` = 1 or a6.`view` is null)
			
			and a7.`view` = 1)
	group by p.id;
end */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `sp_posts_get_thread` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'STRICT_TRANS_TABLES,NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50020 DEFINER=`root`@`localhost`*/ /*!50003 PROCEDURE `sp_posts_get_thread`(
	thread_id int
)
begin
	select id, thread, `number`, password, `name`, tripcode, ip, subject,
		date_time, text, sage
	from posts p
	where thread = thread_id;
end */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `sp_posts_get_visible_by_id` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'STRICT_TRANS_TABLES' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50020 DEFINER=`root`@`localhost`*/ /*!50003 PROCEDURE `sp_posts_get_visible_by_id`(
	post_id int,
	user_id int
)
begin
	select p.id, p.thread, p.board, p.number, p.password, p.name,
		p.tripcode, p.ip, p.subject, p.date_time, p.text, p.sage
	from posts p
	left join threads t on t.id = p.thread
	join user_groups ug on ug.user = user_id
	
	left join acl a1 on a1.`group` = ug.`group` and a1.post = p.id
	
	left join acl a2 on a2.`group` is null and a2.post = p.id
	
	left join acl a3 on a3.`group` = ug.`group` and a3.thread = p.thread
	
	left join acl a4 on a4.`group` is null and a4.thread = p.thread
	
	left join acl a5 on a5.`group` = ug.`group` and a5.board = p.board
	
	left join acl a6 on a6.`group` is null and a6.board = p.board
	
	left join acl a7 on a7.`group` = ug.`group` and a7.board is null and
		a7.thread is null and a7.post is null
	where p.id = post_id and p.deleted = 0 and t.deleted = 0 and t.archived = 0
		
			
		and ((a1.`view` = 1 or a1.`view` is null)
			
			and (a2.`view` = 1 or a2.`view` is null)
			
			and (a3.`view` = 1 or a3.`view` is null)
			
			and (a4.`view` = 1 or a4.`view` is null)
			
			and (a5.`view` = 1 or a5.`view` is null)
			
			and (a6.`view` = 1 or a6.`view` is null)
			
			and a7.`view` = 1)
	group by p.id;
end */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `sp_posts_get_visible_by_number` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'STRICT_TRANS_TABLES' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50020 DEFINER=`root`@`localhost`*/ /*!50003 PROCEDURE `sp_posts_get_visible_by_number`(
	board_id int,
	post_number int,
	user_id int
)
begin
	select p.id, p.thread, p.number, p.password, p.name, p.tripcode, p.ip,
		p.subject, p.date_time, p.text, p.sage
	from posts p
	join user_groups ug on ug.`user` = user_id
	
	left join acl a1 on a1.`group` = ug.`group` and a1.post = p.id
	
	left join acl a2 on a2.`group` is null and a2.post = p.id
	
	left join acl a3 on a3.`group` = ug.`group` and a3.thread = p.thread
	
	left join acl a4 on a4.`group` is null and a4.thread = p.thread
	
	left join acl a5 on a5.`group` = ug.`group` and a5.board = p.board
	
	left join acl a6 on a6.`group` is null and a6.board = p.board
	
	left join acl a7 on a7.`group` = ug.`group` and a7.board is null and
		a7.thread is null and a7.post is null
	where p.board = board_id
		and p.number = post_number
		and p.deleted = 0
		
			
		and ((a1.`view` = 1 or a1.`view` is null)
			
			and (a2.`view` = 1 or a2.`view` is null)
			
			and (a3.`view` = 1 or a3.`view` is null)
			
			and (a4.`view` = 1 or a4.`view` is null)
			
			and (a5.`view` = 1 or a5.`view` is null)
			
			and (a6.`view` = 1 or a6.`view` is null)
			
			and a7.`view` = 1)
	group by p.id;
end */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `sp_posts_get_visible_by_thread` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'STRICT_TRANS_TABLES' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50020 DEFINER=`root`@`localhost`*/ /*!50003 PROCEDURE `sp_posts_get_visible_by_thread`(
	thread_id int,
	user_id int
)
begin
	select p.id, p.thread, p.number, p.password, p.name, p.tripcode,
			p.ip, p.subject, p.date_time, p.text, p.sage
	from posts p
	join threads t on t.board = p.board and t.id = p.thread
	join user_groups ug on ug.user = user_id
	
	left join acl a1 on a1.`group` = ug.`group` and a1.post = p.id
	
	left join acl a2 on a2.`group` is null and a2.post = p.id
	
	left join acl a3 on a3.`group` = ug.`group` and a3.thread = t.id
	
	left join acl a4 on a4.`group` is null and a4.thread = t.id
	
	left join acl a5 on a5.`group` = ug.`group` and a5.board = p.board
	
	left join acl a6 on a6.`group` is null and a6.board = p.board
	
	left join acl a7 on a7.`group` = ug.`group` and a7.board is null
		and a7.thread is null and a7.post is null
	where p.thread = thread_id
		and p.deleted = 0 and t.deleted = 0 and t.archived = 0
		
			
		and ((a1.`view` = 1 or a1.`view` is null)
			
			and (a2.`view` = 1 or a2.`view` is null)
			
			and (a3.`view` = 1 or a3.`view` is null)
			
			and (a4.`view` = 1 or a4.`view` is null)
			
			and (a5.`view` = 1 or a5.`view` is null)
			
			and (a6.`view` = 1 or a6.`view` is null)
			
			and a7.`view` = 1)
	group by p.id
	order by p.number asc;
end */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `sp_posts_images_get_by_post` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'STRICT_TRANS_TABLES' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50020 DEFINER=`root`@`localhost`*/ /*!50003 PROCEDURE `sp_posts_images_get_by_post`(
    post_id int
)
begin
    select post, image, deleted from posts_images where post = post_id;
end */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `sp_posts_links_get_by_post` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'STRICT_TRANS_TABLES' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50020 DEFINER=`root`@`localhost`*/ /*!50003 PROCEDURE `sp_posts_links_get_by_post`(
    post_id int
)
begin
    select post, link, deleted from posts_links where post = post_id;
end */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `sp_posts_uploads_add` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'STRICT_TRANS_TABLES,NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50020 DEFINER=`root`@`localhost`*/ /*!50003 PROCEDURE `sp_posts_uploads_add`(
	_post_id int,
	_upload_id int
)
begin
	insert into posts_uploads (post, upload) values (_post_id, _upload_id);
end */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `sp_posts_uploads_delete_by_post` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'STRICT_TRANS_TABLES,NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50020 DEFINER=`root`@`localhost`*/ /*!50003 PROCEDURE `sp_posts_uploads_delete_by_post`(
	_post_id int
)
begin
	delete from posts_uploads where post = _post_id;
end */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `sp_posts_uploads_get_by_post` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'STRICT_TRANS_TABLES,NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50020 DEFINER=`root`@`localhost`*/ /*!50003 PROCEDURE `sp_posts_uploads_get_by_post`(
	post_id int
)
begin
	select post, upload from posts_uploads where post = post_id;
end */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `sp_posts_videos_get_by_post` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'STRICT_TRANS_TABLES' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50020 DEFINER=`root`@`localhost`*/ /*!50003 PROCEDURE `sp_posts_videos_get_by_post`(
    post_id int
)
begin
    select post, video, deleted from posts_videos where post = post_id;
end */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `sp_stylesheets_add` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'STRICT_TRANS_TABLES' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50020 DEFINER=`root`@`localhost`*/ /*!50003 PROCEDURE `sp_stylesheets_add`(
    _name varchar(50)
)
begin
    insert into stylesheets (name) values (_name);
end */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `sp_stylesheets_delete` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'STRICT_TRANS_TABLES' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50020 DEFINER=`root`@`localhost`*/ /*!50003 PROCEDURE `sp_stylesheets_delete`(
	_id int
)
begin
	delete from stylesheets where id = _id;
end */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `sp_stylesheets_get_all` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'STRICT_TRANS_TABLES' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50020 DEFINER=`root`@`localhost`*/ /*!50003 PROCEDURE `sp_stylesheets_get_all`()
begin
	select id, name from stylesheets;
end */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `sp_test_mysql_bit` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'STRICT_TRANS_TABLES,NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50020 DEFINER=`root`@`localhost`*/ /*!50003 PROCEDURE `sp_test_mysql_bit`()
begin
	insert into uploads (is_image, upload_type, `file`, `size`) values (1, 0, 'test mysql bit', 0);
	insert into uploads (is_image, upload_type, `file`, `size`) values (0, 0, 'test mysql bit', 0);
	select is_image from uploads where `file` = 'test mysql bit';
	delete from uploads where `file` = 'test mysql bit';
end */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `sp_threads_add` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'STRICT_TRANS_TABLES' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50020 DEFINER=`root`@`localhost`*/ /*!50003 PROCEDURE `sp_threads_add`(
	board_id int,
	_original_post int,
	_bump_limit int,
	_sage bit,
	_with_attachments bit
)
begin
    declare thread_id int;
    insert into threads (board, original_post, bump_limit, deleted, archived,
            sage, sticky, with_attachments)
        values (board_id, _original_post, _bump_limit, 0, 0,
            _sage, 0, _with_attachments);
    select last_insert_id() into thread_id;
    select id, board, original_post, bump_limit, sage, sticky, with_attachments
        from threads where id = thread_id;
end */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `sp_threads_check_specifed_moderate` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'STRICT_TRANS_TABLES,NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50020 DEFINER=`root`@`localhost`*/ /*!50003 PROCEDURE `sp_threads_check_specifed_moderate`(
	thread_id int,
	user_id int
)
begin
	select t.id
	from threads t
	join user_groups ug on ug.`user` = user_id
	left join hidden_threads ht on t.id = ht.thread and ug.`user` = ht.`user`
	
	left join acl a1 on a1.`group` = ug.`group` and a1.thread = t.id
	
	left join acl a2 on a2.`group` is null and a2.thread = t.id
	
	left join acl a3 on a3.`group` = ug.`group` and a3.board = t.board
	
	left join acl a4 on a4.`group` is null and a4.board = t.board
	
	left join acl a5 on a5.`group` = ug.`group` and a5.board is null
		and a5.thread is null and a5.post is null
	where t.id = thread_id
		and (t.deleted = 0 or t.deleted is null)
		and	(t.archived = 0 or t.archived is null)
		and ht.thread is null
		
			
		and ((a1.`view` = 1 or a1.`view` is null)
			
			and (a2.`view` = 1 or a2.`view` is null)
			
			and (a3.`view` = 1 or a3.`view` is null)
			
			and (a4.`view` = 1 or a4.`view` is null)
			
			and a5.`view` = 1)
		
			
		and (a1.change = 1
				
				or (a1.change is null and a2.change = 1)
				
				or (a1.change is null and a2.change is null and a5.change = 1))
		
			
		and (a1.moderate = 1
			
			or (a1.moderate is null and a2.moderate = 1)
			
			or (a1.moderate is null and a2.moderate is null and a5.moderate = 1))
	group by t.id;
end */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `sp_threads_delete_specifed` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'STRICT_TRANS_TABLES,NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50020 DEFINER=`root`@`localhost`*/ /*!50003 PROCEDURE `sp_threads_delete_specifed`(
	_id int
)
begin
	update threads set deleted = 1 where id = _id;
	update posts set deleted = 1 where thread = _id;
end */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `sp_threads_edit` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'STRICT_TRANS_TABLES' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50020 DEFINER=`root`@`localhost`*/ /*!50003 PROCEDURE `sp_threads_edit`(
	_id int,
	_bump_limit int,
	_sticky bit,
	_sage bit,
	_with_attachments bit
)
begin
	update threads set bump_limit = _bump_limit, sticky = _sticky, sage = _sage,
		with_attachments = _with_attachments
	where id = _id;
end */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `sp_threads_edit_archived_postlimit` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'STRICT_TRANS_TABLES' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50020 DEFINER=`root`@`localhost`*/ /*!50003 PROCEDURE `sp_threads_edit_archived_postlimit`(
	board_id int,
	x int
)
begin
	declare board_bump_limit int;
	declare done int default 0;
	declare thread_id int;
	declare posts_count int;
	declare total int default 0;
	declare `c` cursor for
		select q2.id, q2.posts_count
		from (
			
			select q1.id, q1.posts_count, max(p.`number`) as last_post_num
			from posts p
			join(
				
				select t.id, count(distinct p.id) as posts_count
				from posts p
				join threads t on t.id = p.thread and t.board = board_id
				where t.deleted = 0 and t.archived = 0 and p.deleted = 0
				group by t.id) q1 on q1.id = p.thread
					and (p.sage = 0 or p.sage is null)
			group by q1.id) q2
		order by q2.last_post_num desc;
	declare continue handler for not found set done = 1;
	select bump_limit into board_bump_limit from boards where id = board_id;
	set x = x * board_bump_limit;
	open `c`;
	repeat
	fetch `c` into thread_id, posts_count;
	if(not done) then
		set total = total + posts_count;
		if(total > x) then
			update threads set archived = 1 where id = thread_id;
		end if;
	end if;
	until done end repeat;
	close `c`;
end */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `sp_threads_edit_deleted` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'STRICT_TRANS_TABLES' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50020 DEFINER=`root`@`localhost`*/ /*!50003 PROCEDURE `sp_threads_edit_deleted`(
	_id int
)
begin
	update threads set deleted = 1 where id = _id;
	update posts set deleted = 1 where thread = _id;
end */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `sp_threads_edit_originalpost` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'STRICT_TRANS_TABLES,NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50020 DEFINER=`root`@`localhost`*/ /*!50003 PROCEDURE `sp_threads_edit_originalpost`(
	_id int,
	_original_post int
)
begin
	update threads set original_post = _original_post
	where id = _id;
end */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `sp_threads_edit_original_post` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'STRICT_TRANS_TABLES' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50020 DEFINER=`root`@`localhost`*/ /*!50003 PROCEDURE `sp_threads_edit_original_post`(
	_id int,
	_original_post int
)
begin
	update threads set original_post = _original_post where id = _id;
end */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `sp_threads_get_all` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'STRICT_TRANS_TABLES' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50020 DEFINER=`root`@`localhost`*/ /*!50003 PROCEDURE `sp_threads_get_all`()
begin
	select id, board, original_post, bump_limit, sage, sticky, with_attachments
	from threads
	where deleted = 0 and archived = 0
	order by id desc;
end */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `sp_threads_get_all_archived` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'STRICT_TRANS_TABLES,NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50020 DEFINER=`root`@`localhost`*/ /*!50003 PROCEDURE `sp_threads_get_all_archived`()
begin
	select id, board, original_post, bump_limit, sticky, sage, with_files
	from threads
	where deleted = 0 and archived = 1;
end */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `sp_threads_get_all_moderate` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'STRICT_TRANS_TABLES,NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50020 DEFINER=`root`@`localhost`*/ /*!50003 PROCEDURE `sp_threads_get_all_moderate`(
	user_id int
)
begin
	select t.id, t.board, t.original_post, t.bump_limit, t.sticky, t.sage,
		t.with_files
	from threads t
	join user_groups ug on ug.`user` = user_id
	left join hidden_threads ht on t.id = ht.thread and ug.`user` = ht.`user`
	
	left join acl a1 on a1.`group` = ug.`group` and a1.thread = t.id
	
	left join acl a2 on a2.`group` is null and a2.thread = t.id
	
	left join acl a3 on a3.`group` = ug.`group` and a3.board = t.board
	
	left join acl a4 on a4.`group` is null and a4.board = t.board
	
	left join acl a5 on a5.`group` = ug.`group` and a5.board is null
		and a5.thread is null and a5.post is null
	where t.deleted = 0
		and t.archived = 0
		and ht.thread is null
		
			
		and ((a1.`view` = 1 or a1.`view` is null)
			
			and (a2.`view` = 1 or a2.`view` is null)
			
			and (a3.`view` = 1 or a3.`view` is null)
			
			and (a4.`view` = 1 or a4.`view` is null)
			
			and a5.`view` = 1)
		
			
		and (a1.change = 1
			
			
			or (a1.change is null and a2.change = 1)
			
			
			or (a1.change is null and a2.change is null and a5.change = 1))
		
			
		and (a1.moderate = 1
			
			
			or (a1.moderate is null and a2.moderate = 1)
			
			
			or (a1.moderate is null and a2.moderate is null and a5.moderate = 1))
	group by t.id
	order by t.id desc;
end */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `sp_threads_get_archived` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'STRICT_TRANS_TABLES' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50020 DEFINER=`root`@`localhost`*/ /*!50003 PROCEDURE `sp_threads_get_archived`()
begin
	select id, board, original_post, bump_limit, sage, sticky, with_attachments
	from threads
	where deleted = 0 and archived = 1;
end */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `sp_threads_get_changeable_by_id` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'STRICT_TRANS_TABLES' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50020 DEFINER=`root`@`localhost`*/ /*!50003 PROCEDURE `sp_threads_get_changeable_by_id`(
	thread_id int,
	user_id int
)
begin
	select t.id, t.board, t.original_post, t.bump_limit, t.archived, t.sage,
		t.with_attachments
	from threads t
	join user_groups ug on ug.user = user_id
	left join hidden_threads ht on t.id = ht.thread and ug.user = ht.user
	
	left join acl a1 on a1.`group` = ug.`group` and a1.thread = t.id
	
	left join acl a2 on a2.`group` is null and a2.thread = t.id
	
	left join acl a3 on a3.`group` = ug.`group` and a3.board = t.board
	
	left join acl a4 on a4.`group` is null and a4.board = t.board
	
	left join acl a5 on a5.`group` = ug.`group` and a5.board is null
		and a5.thread is null and a5.post is null
	where t.id = thread_id
		and (t.deleted = 0 or t.deleted is null)
		and ht.thread is null
		
			
		and ((a1.`view` = 1 or a1.`view` is null)
			
			and (a2.`view` = 1 or a2.`view` is null)
			
			and (a3.`view` = 1 or a3.`view` is null)
			
			and (a4.`view` = 1 or a4.`view` is null)
			
			and a5.`view` = 1)
		
			
		and (a1.change = 1
				
				
				or (a1.change is null and a2.change = 1)
				
				
				or (a1.change is null and a2.change is null and a5.change = 1))
	group by t.id;
end */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `sp_threads_get_moderatable` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'STRICT_TRANS_TABLES' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50020 DEFINER=`root`@`localhost`*/ /*!50003 PROCEDURE `sp_threads_get_moderatable`(
	user_id int
)
begin
	select t.id, t.board, t.original_post, t.bump_limit, t.sage, t.sticky,
		t.with_attachments
	from threads t
	join user_groups ug on ug.user = user_id
	left join hidden_threads ht on t.id = ht.thread and ug.user = ht.user
	
	left join acl a1 on a1.`group` = ug.`group` and a1.thread = t.id
	
	left join acl a2 on a2.`group` is null and a2.thread = t.id
	
	left join acl a3 on a3.`group` = ug.`group` and a3.board = t.board
	
	left join acl a4 on a4.`group` is null and a4.board = t.board
	
	left join acl a5 on a5.`group` = ug.`group` and a5.board is null
		and a5.thread is null and a5.post is null
	where t.deleted = 0
		and t.archived = 0
		and ht.thread is null
		
			
		and ((a1.`view` = 1 or a1.`view` is null)
			
			and (a2.`view` = 1 or a2.`view` is null)
			
			and (a3.`view` = 1 or a3.`view` is null)
			
			and (a4.`view` = 1 or a4.`view` is null)
			
			and a5.`view` = 1)
		
			
		and (a1.change = 1
			
			
			or (a1.change is null and a2.change = 1)
			
			
			or (a1.change is null and a2.change is null and a5.change = 1))
		
			
		and (a1.moderate = 1
			
			
			or (a1.moderate is null and a2.moderate = 1)
			
			
			or (a1.moderate is null and a2.moderate is null and a5.moderate = 1))
	group by t.id
	order by t.id desc;
end */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `sp_threads_get_moderatable_by_id` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'STRICT_TRANS_TABLES' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50020 DEFINER=`root`@`localhost`*/ /*!50003 PROCEDURE `sp_threads_get_moderatable_by_id`(
	thread_id int,
	user_id int
)
begin
	select t.id
	from threads t
	join user_groups ug on ug.user = user_id
	left join hidden_threads ht on t.id = ht.thread and ug.user = ht.user
	
	left join acl a1 on a1.`group` = ug.`group` and a1.thread = t.id
	
	left join acl a2 on a2.`group` is null and a2.thread = t.id
	
	left join acl a3 on a3.`group` = ug.`group` and a3.board = t.board
	
	left join acl a4 on a4.`group` is null and a4.board = t.board
	
	left join acl a5 on a5.`group` = ug.`group` and a5.board is null
		and a5.thread is null and a5.post is null
	where t.id = thread_id
		and (t.deleted = 0 or t.deleted is null)
		and	(t.archived = 0 or t.archived is null)
		and ht.thread is null
		
			
		and ((a1.`view` = 1 or a1.`view` is null)
			
			and (a2.`view` = 1 or a2.`view` is null)
			
			and (a3.`view` = 1 or a3.`view` is null)
			
			and (a4.`view` = 1 or a4.`view` is null)
			
			and a5.`view` = 1)
		
			
		and (a1.change = 1
				
				or (a1.change is null and a2.change = 1)
				
				or (a1.change is null and a2.change is null and a5.change = 1))
		
			
		and (a1.moderate = 1
			
			or (a1.moderate is null and a2.moderate = 1)
			
			or (a1.moderate is null and a2.moderate is null and a5.moderate = 1))
	group by t.id;
end */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `sp_threads_get_specifed_change` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'STRICT_TRANS_TABLES,NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50020 DEFINER=`root`@`localhost`*/ /*!50003 PROCEDURE `sp_threads_get_specifed_change`(
	thread_id int,
	user_id int
)
begin
	select t.id, t.board, t.original_post, t.bump_limit, t.archived, t.sage,
		t.with_files
	from threads t
	join user_groups ug on ug.`user` = user_id
	left join hidden_threads ht on t.id = ht.thread and ug.`user` = ht.`user`
	
	left join acl a1 on a1.`group` = ug.`group` and a1.thread = t.id
	
	left join acl a2 on a2.`group` is null and a2.thread = t.id
	
	left join acl a3 on a3.`group` = ug.`group` and a3.board = t.board
	
	left join acl a4 on a4.`group` is null and a4.board = t.board
	
	left join acl a5 on a5.`group` = ug.`group` and a5.board is null
		and a5.thread is null and a5.post is null
	where t.id = thread_id
		and (t.deleted = 0 or t.deleted is null)
		and ht.thread is null
		
			
		and ((a1.`view` = 1 or a1.`view` is null)
			
			and (a2.`view` = 1 or a2.`view` is null)
			
			and (a3.`view` = 1 or a3.`view` is null)
			
			and (a4.`view` = 1 or a4.`view` is null)
			
			and a5.`view` = 1)
		
			
		and (a1.change = 1
				
				
				or (a1.change is null and a2.change = 1)
				
				
				or (a1.change is null and a2.change is null and a5.change = 1))
	group by t.id;
end */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `sp_threads_get_specifed_view` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'STRICT_TRANS_TABLES,NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50020 DEFINER=`root`@`localhost`*/ /*!50003 PROCEDURE `sp_threads_get_specifed_view`(
	board_id int,
	thread_num int,
	user_id int
)
begin
	declare thread_id int;
	select id into thread_id from threads
	where original_post = thread_num and board = board_id;
	if thread_id is null
	then
		select 'NOT_FOUND' as error;
	else
		select t.id, t.original_post, t.bump_limit, t.sticky, t.archived, t.sage,
			t.with_files, count(p.id) as visible_posts_count
		from posts p
		join threads t on t.id = p.thread
		join user_groups ug on ug.`user` = user_id
		left join hidden_threads ht on t.id = ht.thread
			and ug.`user` = ht.`user`
		
		left join acl a1 on a1.`group` = ug.`group` and a1.post = p.id
		
		left join acl a2 on a2.`group` is null and a2.post = p.id
		
		left join acl a3 on a3.`group` = ug.`group` and a3.thread = t.id
		
		left join acl a4 on a4.`group` is null and a4.thread = t.id
		
		left join acl a5 on a5.`group` = ug.`group` and a5.board = t.board
		
		left join acl a6 on a6.`group` is null and a6.board = t.board
		
		left join acl a7 on a7.`group` = ug.`group` and a7.board is null
			and a7.thread is null and a7.post is null
		where t.id = thread_id
			and (t.deleted = 0 or t.deleted is null)
			and ht.thread is null
			and (p.deleted = 0 or p.deleted is null)
			
				
			and ((a3.`view` = 1 or a3.`view` is null)
				
				and (a4.`view` = 1 or a4.`view` is null)
				
				and (a5.`view` = 1 or a5.`view` is null)
				
				and (a6.`view` = 1 or a6.`view` is null)
				
				and a7.`view` = 1)
			
			
				
			and ((a1.`view` = 1 or a1.`view` is null)
				
				and (a2.`view` = 1 or a2.`view` is null)
				
				and (a3.`view` = 1 or a3.`view` is null)
				
				and (a4.`view` = 1 or a4.`view` is null)
				
				and (a5.`view` = 1 or a5.`view` is null)
				
				and (a6.`view` = 1 or a6.`view` is null)
				
				and a7.`view` = 1)
		group by t.id;
	end if;
end */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `sp_threads_get_specifed_view_hiden` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'STRICT_TRANS_TABLES,NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50020 DEFINER=`root`@`localhost`*/ /*!50003 PROCEDURE `sp_threads_get_specifed_view_hiden`(
	board_id int,
	thread_num int,
	user_id int
)
begin
	declare thread_id int;
	select id into thread_id from threads
	where original_post = thread_num and board = board_id;
	if thread_id is null
	then
		select 'NOT_FOUND' as error;
	else
		select t.id, t.original_post, t.bump_limit, t.sticky, t.archived,
			t.sage, t.with_files, count(p.id) as visible_posts_count
		from posts p
		join threads t on t.id = p.thread
		join user_groups ug on ug.`user` = user_id
		left join hidden_threads ht on t.id = ht.thread
			and ug.`user` = ht.`user`
		
		left join acl a1 on a1.`group` = ug.`group` and a1.post = p.id
		
		left join acl a2 on a2.`group` is null and a2.post = p.id
		
		left join acl a3 on a3.`group` = ug.`group` and a3.thread = t.id
		
		left join acl a4 on a4.`group` is null and a4.thread = t.id
		
		left join acl a5 on a5.`group` = ug.`group` and a5.board = t.board
		
		left join acl a6 on a6.`group` is null and a6.board = t.board
		
		left join acl a7 on a7.`group` = ug.`group` and a7.board is null
			and a7.thread is null and a7.post is null
		where t.id = thread_id
			and t.deleted = 0
			and ht.thread is not null
			and p.deleted = 0
			
				
			and ((a3.`view` = 1 or a3.`view` is null)
				
				and (a4.`view` = 1 or a4.`view` is null)
				
				and (a5.`view` = 1 or a5.`view` is null)
				
				and (a6.`view` = 1 or a6.`view` is null)
				
				and a7.`view` = 1)
			
			
				
			and ((a1.`view` = 1 or a1.`view` is null)
				
				and (a2.`view` = 1 or a2.`view` is null)
				
				and (a3.`view` = 1 or a3.`view` is null)
				
				and (a4.`view` = 1 or a4.`view` is null)
				
				and (a5.`view` = 1 or a5.`view` is null)
				
				and (a6.`view` = 1 or a6.`view` is null)
				
				and a7.`view` = 1)
		group by t.id;
	end if;
end */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `sp_threads_get_view_threadscount` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'STRICT_TRANS_TABLES,NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50020 DEFINER=`root`@`localhost`*/ /*!50003 PROCEDURE `sp_threads_get_view_threadscount`(
	user_id int,
	board_id int
)
begin
	select count(q.id) as threads_count
	from (select t.id
	from threads t
	join user_groups ug on ug.user = user_id
	left join hidden_threads ht on ht.thread = t.id and ht.`user` = ug.`user`
	
	left join acl a1 on a1.`group` = ug.`group` and a1.thread = t.id
	
	left join acl a2 on a2.`group` is null and a2.thread = t.id
	
	left join acl a3 on a3.`group` = ug.`group` and a3.board = t.board
	
	left join acl a4 on a4.`group` is null and a4.board = t.board
	
	left join acl a5 on a5.`group` = ug.`group` and a5.board is null
		and a5.thread is null and a5.post is null
	where t.board = board_id
		and t.deleted = 0
		and t.archived = 0
		and ht.thread is null
		
			
		and ((a1.`view` = 1 or a1.`view` is null)
			
			and (a2.`view` = 1 or a2.`view` is null)
			
			and (a3.`view` = 1 or a3.`view` is null)
			
			and (a4.`view` = 1 or a4.`view` is null)
			
			and a5.`view` = 1)
	group by t.id) q;
end */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `sp_threads_get_visible_by_board` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'STRICT_TRANS_TABLES' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50020 DEFINER=`root`@`localhost`*/ /*!50003 PROCEDURE `sp_threads_get_visible_by_board`(
	board_id int,
	user_id int
)
begin
	select q1.id, q1.original_post, q1.bump_limit, q1.sticky, q1.sage,
		q1.with_attachments, q1.posts_count, q1.last_post_num
	from (
		
		select q.id, q.original_post, q.bump_limit, q.sticky, q.sage,
			q.with_attachments, q.posts_count, max(p.number) as last_post_num
		from posts p
		join (
			
			select t.id, t.original_post, t.bump_limit, t.sticky, t.sage,
				t.with_attachments, count(distinct p.id) as posts_count
			from posts p
			join threads t on t.id = p.thread and t.board = board_id
			join user_groups ug on ug.`user` = user_id
			left join hidden_threads ht on ht.thread = t.id
				and ht.user = ug.user
			
			left join acl a1 on a1.`group` = ug.`group` and a1.post = p.id
			
			left join acl a2 on a2.`group` is null and a2.post = p.id
			
			left join acl a3 on a3.`group` = ug.`group` and a3.thread = p.thread
			
			left join acl a4 on a4.`group` is null and a4.thread = p.thread
			
			left join acl a5 on a5.`group` = ug.`group` and a5.board = p.board
			
			left join acl a6 on a6.`group` is null and a6.board = p.board
			
			left join acl a7 on a7.`group` = ug.`group` and a7.board is null
				and a7.thread is null and a7.post is null
			where t.deleted = 0 and t.archived = 0 and ht.thread is null
				and p.deleted = 0
				
					
				and ((a3.`view` = 1 or a3.`view` is null)
					
					and (a4.`view` = 1 or a4.`view` is null)
					
					and (a5.`view` = 1 or a5.`view` is null)
					
					and (a6.`view` = 1 or a6.`view` is null)
					
					and a7.`view` = 1)
				
				
					
				and ((a1.`view` = 1 or a1.`view` is null)
					
					and (a2.`view` = 1 or a2.`view` is null)
					
					and (a3.`view` = 1 or a3.`view` is null)
					
					and (a4.`view` = 1 or a4.`view` is null)
					
					and (a5.`view` = 1 or a5.`view` is null)
					
					and (a6.`view` = 1 or a6.`view` is null)
					
					and a7.`view` = 1)
			group by t.id) q on q.id = p.thread
				and (p.sage = 0 or p.sage is null) and p.deleted = 0
		group by q.id) q1
	order by q1.last_post_num desc;
end */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `sp_threads_get_visible_by_id` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'STRICT_TRANS_TABLES' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50020 DEFINER=`root`@`localhost`*/ /*!50003 PROCEDURE `sp_threads_get_visible_by_id`(
	board_id int,
	thread_num int,
	user_id int
)
begin
	declare thread_id int;
	select id into thread_id from threads
	where original_post = thread_num and board = board_id;
	if thread_id is null
	then
		select 'NOT_FOUND' as error;
	else
		select t.id, t.original_post, t.bump_limit, t.sticky, t.archived, t.sage,
			t.with_attachments, count(p.id) as visible_posts_count
		from posts p
		join threads t on t.id = p.thread
		join user_groups ug on ug.`user` = user_id
		left join hidden_threads ht on t.id = ht.thread
			and ug.user = ht.user
		
		left join acl a1 on a1.`group` = ug.`group` and a1.post = p.id
		
		left join acl a2 on a2.`group` is null and a2.post = p.id
		
		left join acl a3 on a3.`group` = ug.`group` and a3.thread = t.id
		
		left join acl a4 on a4.`group` is null and a4.thread = t.id
		
		left join acl a5 on a5.`group` = ug.`group` and a5.board = t.board
		
		left join acl a6 on a6.`group` is null and a6.board = t.board
		
		left join acl a7 on a7.`group` = ug.`group` and a7.board is null
			and a7.thread is null and a7.post is null
		where t.id = thread_id
			and (t.deleted = 0 or t.deleted is null)
			and ht.thread is null
			and (p.deleted = 0 or p.deleted is null)
			
				
			and ((a3.`view` = 1 or a3.`view` is null)
				
				and (a4.`view` = 1 or a4.`view` is null)
				
				and (a5.`view` = 1 or a5.`view` is null)
				
				and (a6.`view` = 1 or a6.`view` is null)
				
				and a7.`view` = 1)
			
			
				
			and ((a1.`view` = 1 or a1.`view` is null)
				
				and (a2.`view` = 1 or a2.`view` is null)
				
				and (a3.`view` = 1 or a3.`view` is null)
				
				and (a4.`view` = 1 or a4.`view` is null)
				
				and (a5.`view` = 1 or a5.`view` is null)
				
				and (a6.`view` = 1 or a6.`view` is null)
				
				and a7.`view` = 1)
		group by t.id;
	end if;
end */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `sp_threads_get_visible_count` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'STRICT_TRANS_TABLES' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50020 DEFINER=`root`@`localhost`*/ /*!50003 PROCEDURE `sp_threads_get_visible_count`(
	user_id int,
	board_id int
)
begin
	select count(q.id) as threads_count
	from (select t.id
	from threads t
	join user_groups ug on ug.user = user_id
	left join hidden_threads ht on ht.thread = t.id and ht.user = ug.user
	
	left join acl a1 on a1.`group` = ug.`group` and a1.thread = t.id
	
	left join acl a2 on a2.`group` is null and a2.thread = t.id
	
	left join acl a3 on a3.`group` = ug.`group` and a3.board = t.board
	
	left join acl a4 on a4.`group` is null and a4.board = t.board
	
	left join acl a5 on a5.`group` = ug.`group` and a5.board is null
		and a5.thread is null and a5.post is null
	where t.board = board_id
		and t.deleted = 0
		and t.archived = 0
		and ht.thread is null
		
			
		and ((a1.`view` = 1 or a1.`view` is null)
			
			and (a2.`view` = 1 or a2.`view` is null)
			
			and (a3.`view` = 1 or a3.`view` is null)
			
			and (a4.`view` = 1 or a4.`view` is null)
			
			and a5.`view` = 1)
	group by t.id) q;
end */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `sp_uploads_add` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'STRICT_TRANS_TABLES,NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50020 DEFINER=`root`@`localhost`*/ /*!50003 PROCEDURE `sp_uploads_add`(
	_hash varchar(32),
	_is_image bit,
	_upload_type int,
	_file varchar(256),
	_image_w int,
	_image_h int,
	_size int,
	_thumbnail varchar(256),
	_thumbnail_w int,
	_thumbnail_h int
)
begin
	insert into uploads (`hash`, is_image, upload_type, `file`, image_w,
		image_h, `size`, thumbnail, thumbnail_w, thumbnail_h)
	values
	(_hash, _is_image, _upload_type, _file, _image_w,
		_image_h, _size, _thumbnail, _thumbnail_w, _thumbnail_h);
	select last_insert_id() as id;
end */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `sp_uploads_delete_by_id` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'STRICT_TRANS_TABLES,NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50020 DEFINER=`root`@`localhost`*/ /*!50003 PROCEDURE `sp_uploads_delete_by_id`(
	_id int
)
begin
	delete from uploads where id = _id;
end */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `sp_uploads_get_by_post` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'STRICT_TRANS_TABLES,NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50020 DEFINER=`root`@`localhost`*/ /*!50003 PROCEDURE `sp_uploads_get_by_post`(
	post_id int
)
begin
	select id, `hash`, is_image, upload_type, `file`, image_w, image_h, `size`,
		thumbnail, thumbnail_w, thumbnail_h
	from uploads u
	join posts_uploads pu on pu.upload = u.id and pu.post = post_id;
end */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `sp_uploads_get_dangling` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'STRICT_TRANS_TABLES,NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50020 DEFINER=`root`@`localhost`*/ /*!50003 PROCEDURE `sp_uploads_get_dangling`()
begin
	select u.id, u.`hash`, u.is_image, u.link_type, u.`file`, u.file_w,
		u.file_h, u.`size`, u.thumbnail, u.thumbnail_w, u.thumbnail_h
	from uploads u
	left join posts_uploads pu on pu.upload = u.id
	where pu.upload is null;
end */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `sp_uploads_get_same` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'STRICT_TRANS_TABLES,NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50020 DEFINER=`root`@`localhost`*/ /*!50003 PROCEDURE `sp_uploads_get_same`(
	_board_id int,
	_hash varchar(32),
	_user_id int
)
begin
	select u.id, u.`hash`, u.is_image, u.upload_type, u.`file`, u.image_w,
		u.image_h, u.`size`, u.thumbnail, u.thumbnail_w, u.thumbnail_h,
		p.`number`, t.original_post, max(case
		when a1.`view` = 0 then 0
		when a2.`view` = 0 then 0
		when a3.`view` = 0 then 0
		when a4.`view` = 0 then 0
		when a5.`view` = 0 then 0
		when a6.`view` = 0 then 0
		when a7.`view` = 0 then 0
		else 1 end) as `view`
	from uploads u
	join posts_uploads pu on pu.upload = u.id
	join posts p on p.id = pu.post and p.board = _board_id
	join threads t on t.id = p.thread
	join user_groups ug on ug.`user` = _user_id
	
	left join acl a1 on a1.`group` = ug.`group` and a1.post = p.id
	
	left join acl a2 on a2.`group` is null and a2.post = p.id
	
	left join acl a3 on a3.`group` = ug.`group` and a3.thread = p.thread
	
	left join acl a4 on a4.`group` is null and a4.thread = p.thread
	
	left join acl a5 on a5.`group` = ug.`group` and a5.board = p.board
	
	left join acl a6 on a6.`group` is null and a6.board = p.board
	
	left join acl a7 on a7.`group` = ug.`group` and a7.board is null
		and a7.thread is null and a7.post is null
	where u.`hash` = _hash
	group by u.id, p.id;
end */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `sp_upload_handlers_add` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'STRICT_TRANS_TABLES' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50020 DEFINER=`root`@`localhost`*/ /*!50003 PROCEDURE `sp_upload_handlers_add`(
	_name varchar(50)
)
begin
	insert into upload_handlers (name) values (_name);
end */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `sp_upload_handlers_delete` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'STRICT_TRANS_TABLES' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50020 DEFINER=`root`@`localhost`*/ /*!50003 PROCEDURE `sp_upload_handlers_delete`(
	_id int
)
begin
	delete from upload_handlers where id = _id;
end */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `sp_upload_handlers_get_all` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'STRICT_TRANS_TABLES' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50020 DEFINER=`root`@`localhost`*/ /*!50003 PROCEDURE `sp_upload_handlers_get_all`()
begin
	select id, name from upload_handlers;
end */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `sp_upload_types_add` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'STRICT_TRANS_TABLES' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50020 DEFINER=`root`@`localhost`*/ /*!50003 PROCEDURE `sp_upload_types_add`(
	_extension varchar(10),
	_store_extension varchar(10),
	_is_image bit,
	_upload_handler_id int,
	_thumbnail_image varchar(256)
)
begin
	insert into upload_types (extension, store_extension, is_image,
		upload_handler, thumbnail_image)
	values (_extension, _store_extension, _is_image, _upload_handler_id,
		_thumbnail_image);
end */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `sp_upload_types_delete` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'STRICT_TRANS_TABLES' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50020 DEFINER=`root`@`localhost`*/ /*!50003 PROCEDURE `sp_upload_types_delete`(
	_id int
)
begin
	delete from upload_types where id = _id;
end */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `sp_upload_types_edit` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'STRICT_TRANS_TABLES' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50020 DEFINER=`root`@`localhost`*/ /*!50003 PROCEDURE `sp_upload_types_edit`(
	_id int,
	_store_extension varchar(10),
	_is_image bit,
	_upload_handler_id int,
	_thumbnail_image varchar(256)
)
begin
	update upload_types set store_extension = _store_extension,
		is_image = _is_image, upload_handler = _upload_handler_id,
		thumbnail_image = _thumbnail_image
	where id = _id;
end */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `sp_upload_types_get_all` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'STRICT_TRANS_TABLES' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50020 DEFINER=`root`@`localhost`*/ /*!50003 PROCEDURE `sp_upload_types_get_all`()
begin
	select id, extension, store_extension, is_image, upload_handler,
		thumbnail_image
	from upload_types;
end */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `sp_upload_types_get_board` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'STRICT_TRANS_TABLES,NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50020 DEFINER=`root`@`localhost`*/ /*!50003 PROCEDURE `sp_upload_types_get_board`(
	board_id int
)
begin
	select ut.id, ut.extension, ut.store_extension, ut.is_image, ut.upload_handler,
		uh.`name` as upload_handler_name, ut.thumbnail_image
	from upload_types ut
	join board_upload_types but on ut.id = but.upload_type and but.board = board_id
	join upload_handlers uh on uh.id = ut.upload_handler;
end */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `sp_upload_types_get_by_board` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'STRICT_TRANS_TABLES' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50020 DEFINER=`root`@`localhost`*/ /*!50003 PROCEDURE `sp_upload_types_get_by_board`(
	board_id int
)
begin
	select ut.id, ut.extension, ut.store_extension, ut.is_image, ut.upload_handler,
		uh.name as upload_handler_name, ut.thumbnail_image
	from upload_types ut
	join board_upload_types but on ut.id = but.upload_type and but.board = board_id
	join upload_handlers uh on uh.id = ut.upload_handler;
end */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `sp_users_edit_bykeyword` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'STRICT_TRANS_TABLES,NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50020 DEFINER=`root`@`localhost`*/ /*!50003 PROCEDURE `sp_users_edit_bykeyword`(
	_keyword varchar(32),
	_threads_per_page int,
	_posts_per_thread int,
	_lines_per_post int,
	_stylesheet int,
	_language int,
	_password varchar(12),
	_goto varchar(32)
)
begin
	declare user_id int;
	set @user_id = null;
	select id into user_id from users where keyword = _keyword;
	if(user_id is null)
	then
		
		insert into users (keyword, threads_per_page, posts_per_thread,
			lines_per_post, stylesheet, `language`, password, `goto`)
		values (_keyword, _threads_per_page, _posts_per_thread,
			_lines_per_post, _stylesheet, _language, _password, _goto);
		select last_insert_id() into user_id;
		insert into user_groups (`user`, `group`) select user_id, id from groups
			where name = 'Users';
	else
		
		update users set threads_per_page = _threads_per_page,
			posts_per_thread = _posts_per_thread,
			lines_per_post = _lines_per_post,
			stylesheet = _stylesheet,
			`language` = _language,
			password = _password,
			`goto` = _goto
		where id = user_id;
	end if;
end */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `sp_users_edit_by_keyword` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'STRICT_TRANS_TABLES' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50020 DEFINER=`root`@`localhost`*/ /*!50003 PROCEDURE `sp_users_edit_by_keyword`(
	_keyword varchar(32),
	_posts_per_thread int,
	_threads_per_page int,
	_lines_per_post int,
	_language int,
	_stylesheet int,
	_password varchar(12),
	_goto varchar(32)
)
begin
	declare user_id int;
	set @user_id = null;
	select id into user_id from users where keyword = _keyword;
	if(user_id is null)
	then
		
		insert into users (keyword, threads_per_page, posts_per_thread,
			lines_per_post, stylesheet, language, password, `goto`)
		values (_keyword, _threads_per_page, _posts_per_thread,
			_lines_per_post, _stylesheet, _language, _password, _goto);
		select last_insert_id() into user_id;
		insert into user_groups (user, `group`) select user_id, id from groups
			where name = 'Users';
	else
		
		update users set threads_per_page = _threads_per_page,
			posts_per_thread = _posts_per_thread,
			lines_per_post = _lines_per_post,
			stylesheet = _stylesheet,
			language = _language,
			password = _password,
			`goto` = _goto
		where id = user_id;
	end if;
end */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `sp_users_get_all` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'STRICT_TRANS_TABLES' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50020 DEFINER=`root`@`localhost`*/ /*!50003 PROCEDURE `sp_users_get_all`()
begin
	select id from users;
end */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `sp_users_get_by_keyword` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'STRICT_TRANS_TABLES' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50020 DEFINER=`root`@`localhost`*/ /*!50003 PROCEDURE `sp_users_get_by_keyword`(
	_keyword varchar(32)
)
begin
    declare user_id int;

    select id into user_id from users where keyword = _keyword;

    select u.id, u.posts_per_thread, u.threads_per_page, u.lines_per_post,
            l.code as language, s.name as stylesheet, u.password, u.`goto`
        from users u
        join stylesheets s on u.stylesheet = s.id
        join languages l on u.language = l.id
        where u.keyword = _keyword;

    select g.name from user_groups ug
        join users u on ug.user = u.id and u.id = user_id
        join groups g on ug.`group` = g.id;
end */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `sp_users_set_goto` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'STRICT_TRANS_TABLES' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50020 DEFINER=`root`@`localhost`*/ /*!50003 PROCEDURE `sp_users_set_goto`(
	_id int,
	_goto varchar(32)
)
begin
	update users set `goto` = _goto where id = _id;
end */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `sp_users_set_password` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'STRICT_TRANS_TABLES' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50020 DEFINER=`root`@`localhost`*/ /*!50003 PROCEDURE `sp_users_set_password`(
	_id int,
	_password varchar(12)
)
begin
	update users set password = _password where id = _id;
end */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `sp_user_groups_add` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'STRICT_TRANS_TABLES' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50020 DEFINER=`root`@`localhost`*/ /*!50003 PROCEDURE `sp_user_groups_add`(
	user_id int,
	group_id int
)
begin
	insert into user_groups (user, `group`) values (user_id, group_id);
end */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `sp_user_groups_delete` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'STRICT_TRANS_TABLES' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50020 DEFINER=`root`@`localhost`*/ /*!50003 PROCEDURE `sp_user_groups_delete`(
	user_id int,
	group_id int
)
begin
	delete from user_groups where user = user_id and `group` = group_id;
end */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `sp_user_groups_edit` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'STRICT_TRANS_TABLES' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50020 DEFINER=`root`@`localhost`*/ /*!50003 PROCEDURE `sp_user_groups_edit`(
	user_id int,
	old_group_id int,
	new_group_id int
)
begin
	update user_groups set `group` = new_group_id
	where user = user_id and `group` = old_group_id;
end */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `sp_user_groups_get_all` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'STRICT_TRANS_TABLES' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50020 DEFINER=`root`@`localhost`*/ /*!50003 PROCEDURE `sp_user_groups_get_all`()
begin
	select user, `group` from user_groups order by user, `group`;
end */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `sp_videos_get_by_post` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'STRICT_TRANS_TABLES' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50020 DEFINER=`root`@`localhost`*/ /*!50003 PROCEDURE `sp_videos_get_by_post`(
	post_id int
)
begin
	select v.id, v.code, v.widht, v.height
	from posts_videos pv
	join videos v on v.id = pv.video and pv.post = post_id;
end */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2010-05-23  7:05:54
