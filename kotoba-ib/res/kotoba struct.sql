-- MySQL dump 10.13  Distrib 5.1.33, for Win32 (ia32)
--
-- Host: localhost    Database: kotoba2
-- ------------------------------------------------------
-- Server version	5.1.33-community

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
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
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
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `bans`
--

DROP TABLE IF EXISTS `bans`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `bans` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `range_beg` bigint(11) NOT NULL,
  `range_end` bigint(11) NOT NULL,
  `reason` text COLLATE utf8_unicode_ci,
  `untill` datetime NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `ip_range` (`range_beg`,`range_end`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `board_upload_types`
--

DROP TABLE IF EXISTS `board_upload_types`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `board_upload_types` (
  `board` int(11) NOT NULL,
  `upload_type` int(11) NOT NULL,
  UNIQUE KEY `board` (`board`,`upload_type`),
  KEY `upload_type` (`upload_type`),
  CONSTRAINT `board_upload_types_ibfk_1` FOREIGN KEY (`board`) REFERENCES `boards` (`id`),
  CONSTRAINT `board_upload_types_ibfk_2` FOREIGN KEY (`upload_type`) REFERENCES `upload_types` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `boards`
--

DROP TABLE IF EXISTS `boards`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `boards` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(16) COLLATE utf8_unicode_ci NOT NULL,
  `title` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `bump_limit` int(11) NOT NULL,
  `force_anonymous` bit(1) NOT NULL,
  `default_name` varchar(128) COLLATE utf8_unicode_ci DEFAULT NULL,
  `with_files` bit(1) NOT NULL,
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
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `categories`
--

DROP TABLE IF EXISTS `categories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `categories` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `groups`
--

DROP TABLE IF EXISTS `groups`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `groups` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `hidden_threads`
--

DROP TABLE IF EXISTS `hidden_threads`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `hidden_threads` (
  `user` int(11) DEFAULT NULL,
  `thread` int(11) DEFAULT NULL,
  UNIQUE KEY `user` (`user`,`thread`),
  KEY `thread` (`thread`),
  CONSTRAINT `hidden_threads_ibfk_1` FOREIGN KEY (`user`) REFERENCES `users` (`id`),
  CONSTRAINT `hidden_threads_ibfk_2` FOREIGN KEY (`thread`) REFERENCES `threads` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `languages`
--

DROP TABLE IF EXISTS `languages`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `languages` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `popdown_handlers`
--

DROP TABLE IF EXISTS `popdown_handlers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `popdown_handlers` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `posts`
--

DROP TABLE IF EXISTS `posts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `posts` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `board` int(11) NOT NULL,
  `thread` int(11) NOT NULL,
  `number` int(11) NOT NULL,
  `user` int(11) NOT NULL,
  `password` varchar(128) COLLATE utf8_unicode_ci DEFAULT NULL,
  `name` varchar(128) COLLATE utf8_unicode_ci DEFAULT NULL,
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
) ENGINE=InnoDB AUTO_INCREMENT=776 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `posts_uploads`
--

DROP TABLE IF EXISTS `posts_uploads`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `posts_uploads` (
  `post` int(11) NOT NULL,
  `upload` int(11) NOT NULL,
  UNIQUE KEY `post` (`post`,`upload`),
  KEY `upload` (`upload`),
  CONSTRAINT `posts_uploads_ibfk_2` FOREIGN KEY (`post`) REFERENCES `posts` (`id`),
  CONSTRAINT `posts_uploads_ibfk_3` FOREIGN KEY (`upload`) REFERENCES `uploads` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `stylesheets`
--

DROP TABLE IF EXISTS `stylesheets`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `stylesheets` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `threads`
--

DROP TABLE IF EXISTS `threads`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `threads` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `board` int(11) NOT NULL,
  `original_post` int(11) DEFAULT NULL,
  `bump_limit` int(11) DEFAULT NULL,
  `deleted` bit(1) NOT NULL,
  `archived` bit(1) NOT NULL,
  `sticky` bit(1) NOT NULL DEFAULT b'0',
  `sage` bit(1) NOT NULL,
  `with_files` bit(1) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `board` (`board`),
  CONSTRAINT `threads_ibfk_1` FOREIGN KEY (`board`) REFERENCES `boards` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=809 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `upload_handlers`
--

DROP TABLE IF EXISTS `upload_handlers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `upload_handlers` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `upload_types`
--

DROP TABLE IF EXISTS `upload_types`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
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
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `uploads`
--

DROP TABLE IF EXISTS `uploads`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `uploads` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `board` int(11) NOT NULL,
  `hash` varchar(32) COLLATE utf8_unicode_ci DEFAULT NULL,
  `is_image` bit(1) NOT NULL,
  `file_name` varchar(256) COLLATE utf8_unicode_ci NOT NULL,
  `file_w` int(11) DEFAULT NULL,
  `file_h` int(11) DEFAULT NULL,
  `size` int(11) NOT NULL,
  `thumbnail_name` varchar(256) COLLATE utf8_unicode_ci DEFAULT NULL,
  `thumbnail_w` int(11) DEFAULT NULL,
  `thumbnail_h` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `board` (`board`),
  CONSTRAINT `uploads_ibfk_1` FOREIGN KEY (`board`) REFERENCES `boards` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=398 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `user_groups`
--

DROP TABLE IF EXISTS `user_groups`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `user_groups` (
  `user` int(11) NOT NULL,
  `group` int(11) NOT NULL,
  UNIQUE KEY `user` (`user`,`group`),
  KEY `group` (`group`),
  CONSTRAINT `user_groups_ibfk_1` FOREIGN KEY (`group`) REFERENCES `groups` (`id`),
  CONSTRAINT `user_groups_ibfk_2` FOREIGN KEY (`user`) REFERENCES `users` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `keyword` varchar(32) COLLATE utf8_unicode_ci DEFAULT NULL,
  `posts_per_thread` int(11) DEFAULT NULL,
  `threads_per_page` int(11) DEFAULT NULL,
  `lines_per_post` int(11) DEFAULT NULL,
  `language` int(11) NOT NULL,
  `stylesheet` int(11) NOT NULL,
  `rempass` varchar(12) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `key` (`keyword`),
  KEY `language` (`language`),
  KEY `stylesheet` (`stylesheet`),
  CONSTRAINT `users_ibfk_1` FOREIGN KEY (`language`) REFERENCES `languages` (`id`),
  CONSTRAINT `users_ibfk_2` FOREIGN KEY (`stylesheet`) REFERENCES `stylesheets` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping routines for database 'kotoba2'
--
/*!50003 DROP FUNCTION IF EXISTS `check_thread_view` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'STRICT_TRANS_TABLES,NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50020 DEFINER=`root`@`localhost`*/ /*!50003 FUNCTION `check_thread_view`(
	thread_id int,
	group_id int
) RETURNS bit(1)
begin
	declare is_visible bit default false;



	return is_visible;
end */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP FUNCTION IF EXISTS `func_test_ret` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'STRICT_TRANS_TABLES,NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50020 DEFINER=`root`@`localhost`*/ /*!50003 FUNCTION `func_test_ret`() RETURNS int(11)
begin
	return 3;
end */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP FUNCTION IF EXISTS `get_board_id` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'STRICT_TRANS_TABLES,NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50020 DEFINER=`root`@`localhost`*/ /*!50003 FUNCTION `get_board_id`(_board_name varchar(16)) RETURNS int(11)
    DETERMINISTIC
begin
	declare boardid int default 0;
	select id into boardid from boards where name = _board_name;
	return boardid;
end */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP FUNCTION IF EXISTS `get_next_post_on_board` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'STRICT_TRANS_TABLES,NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50020 DEFINER=`root`@`localhost`*/ /*!50003 FUNCTION `get_next_post_on_board`(
	
	_board_name varchar(16)
) RETURNS int(11)
BEGIN
	DECLARE postnumber int;

	SELECT max(p.number) into postnumber from posts p
	join boards b on (b.name = _board_name and b.id = p.board);
	if postnumber is null then
		set postnumber = 0;
	end if;

	set postnumber = postnumber + 1;

	RETURN postnumber;
END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP FUNCTION IF EXISTS `get_posts_count` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'STRICT_TRANS_TABLES,NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50020 DEFINER=`root`@`localhost`*/ /*!50003 FUNCTION `get_posts_count`(
	_thread int
) RETURNS int(11)
begin
	declare count int default 0;

	select count(p.id) into count from posts p
	where p.thread = _thread
	and p.deleted <> 1;

	return count;
end */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `sp_acl_add` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'STRICT_TRANS_TABLES,NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50020 DEFINER=`root`@`localhost`*/ /*!50003 PROCEDURE `sp_acl_add`(
	group_id int,
	board_id int,
	thread_num int,
	post_num int,
	_view bit,
	_change bit,
	_moderate bit
)
begin
	insert into acl (`group`, `board`, `thread`, `post`, `view`, `change`,
		`moderate`)
	values (group_id, board_id, thread_num, post_num, _view, _change,
		_moderate);
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
/*!50003 SET sql_mode              = 'STRICT_TRANS_TABLES,NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION' */ ;
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
		and ((`board` = board_id) or (coalesce(`board`, board_id) is null))
		and ((`thread` = thread_id) or (coalesce(`thread`, thread_id) is null))
		and ((`post` = post_id) or (coalesce(`post`, post_id) is null));
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
/*!50003 SET sql_mode              = 'STRICT_TRANS_TABLES,NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50020 DEFINER=`root`@`localhost`*/ /*!50003 PROCEDURE `sp_acl_edit`(
	group_id int,
	board_id int,
	thread_num int,
	post_num int,
	_view bit,
	_change bit,
	_moderate bit
)
begin
	update acl set `view` = _view, `change` = _change, `moderate` = _moderate
	where ((`group` = group_id) or (coalesce(`group`, group_id) is null))
		and ((`board` = board_id) or (coalesce(`board`, board_id) is null))
		and ((`thread` = thread_num) or (coalesce(`thread`, thread_num) is null))
		and ((`post` = post_num) or (coalesce(`post`, post_num) is null));
end */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `sp_acl_get` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'STRICT_TRANS_TABLES,NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50020 DEFINER=`root`@`localhost`*/ /*!50003 PROCEDURE `sp_acl_get`()
begin
	select `group`, `board`, `thread`, `post`, `view`, `change`, `moderate` from acl order by `group`, `board`, `thread`, `post`;
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
/*!50003 SET sql_mode              = 'STRICT_TRANS_TABLES,NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50020 DEFINER=`root`@`localhost`*/ /*!50003 PROCEDURE `sp_acl_get_all`()
begin
	select `group`, `board`, `thread`, `post`, `view`, `change`, `moderate`
	from acl order by `group`, `board`, `thread`, `post`;
end */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `sp_ban` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'STRICT_TRANS_TABLES,NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50020 DEFINER=`root`@`localhost`*/ /*!50003 PROCEDURE `sp_ban`(
	_range_beg int,
	_range_end int,
	_reason text,
	_untill datetime
)
begin
call sp_refresh_banlist();

if _reason = '' or _reason is null
then
	insert into bans (range_beg, range_end, reason, untill) values (_range_beg, _range_end, null, _untill);
else
	insert into bans (range_beg, range_end, reason, untill) values (_range_beg, _range_end, _reason, _untill);
end if;
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
/*!50003 SET sql_mode              = 'STRICT_TRANS_TABLES,NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION' */ ;
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
/*!50003 SET sql_mode              = 'STRICT_TRANS_TABLES,NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION' */ ;
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
/*!50003 DROP PROCEDURE IF EXISTS `sp_bans_delete` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'STRICT_TRANS_TABLES,NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50020 DEFINER=`root`@`localhost`*/ /*!50003 PROCEDURE `sp_bans_delete`(
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
/*!50003 DROP PROCEDURE IF EXISTS `sp_bans_get` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'STRICT_TRANS_TABLES,NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50020 DEFINER=`root`@`localhost`*/ /*!50003 PROCEDURE `sp_bans_get`()
begin
	select id, range_beg, range_end, reason, untill from bans;
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
/*!50003 SET sql_mode              = 'STRICT_TRANS_TABLES,NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION' */ ;
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
/*!50003 SET sql_mode              = 'STRICT_TRANS_TABLES,NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION' */ ;
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
/*!50003 DROP PROCEDURE IF EXISTS `sp_bans_unban` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'STRICT_TRANS_TABLES,NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50020 DEFINER=`root`@`localhost`*/ /*!50003 PROCEDURE `sp_bans_unban`(
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
/*!50003 DROP PROCEDURE IF EXISTS `sp_boards_add` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'STRICT_TRANS_TABLES,NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50020 DEFINER=`root`@`localhost`*/ /*!50003 PROCEDURE `sp_boards_add`(
	_name varchar(16),
	_title varchar(50),
	_bump_limit int,
	_force_anonymous bit,
	_default_name varchar(128),
	_with_files bit,
	_same_upload varchar(32),
	_popdown_handler int,
	_category int
)
begin
	insert into boards (`name`, title, bump_limit, force_anonymous,
		default_name, with_files, same_upload, popdown_handler, category)
	values (_name, _title, _bump_limit, _force_anonymous, _default_name,
		_with_files, _same_upload, _popdown_handler, _category);
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
/*!50003 SET sql_mode              = 'STRICT_TRANS_TABLES,NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION' */ ;
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
/*!50003 SET sql_mode              = 'STRICT_TRANS_TABLES,NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50020 DEFINER=`root`@`localhost`*/ /*!50003 PROCEDURE `sp_boards_edit`(
	_id int,
	_title varchar(50),
	_bump_limit int,
	_force_anonymous bit,
	_default_name varchar(128),
	_with_files bit,
	_same_upload varchar(32),
	_popdown_handler int,
	_category int
)
begin
	update boards set title = _title, bump_limit = _bump_limit,
		force_anonymous = _force_anonymous, default_name = _default_name,
		with_files = _with_files, same_upload = _same_upload,
		popdown_handler = _popdown_handler, category = _category
	where id = _id;
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
/*!50003 SET sql_mode              = 'STRICT_TRANS_TABLES,NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50020 DEFINER=`root`@`localhost`*/ /*!50003 PROCEDURE `sp_boards_get_all`()
begin
	select id, `name`, title, bump_limit, force_anonymous, default_name,
		with_files, same_upload, popdown_handler, category
	from boards;
end */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `sp_boards_get_allowed` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'STRICT_TRANS_TABLES,NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50020 DEFINER=`root`@`localhost`*/ /*!50003 PROCEDURE `sp_boards_get_allowed`(
	user int
)
begin
	select b.id, b.`name`, b.title, b.bump_limit, b.same_upload, b.popdown_handler, b.category
	from boards b
	join user_groups ug on ug.user = user
	left join acl a1 on ug.`group` = a1.`group` and a1.board is null and a1.thread is null and a1.post is null 
	left join acl a2 on a2.`group` is null and b.id = a2.board and a2.thread is null and a2.post is null 
	left join acl a3 on ug.`group` = a3.`group` and b.id = a3.board and a2.thread is null and a2.post is null 
	group by b.id
	having max(coalesce(a3.view, a2.view, a1.view)) = 1
	order by b.category, b.`name`;
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
/*!50003 DROP PROCEDURE IF EXISTS `sp_boards_get_preview` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'STRICT_TRANS_TABLES,NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50020 DEFINER=`root`@`localhost`*/ /*!50003 PROCEDURE `sp_boards_get_preview`(
	_user int
)
begin
	select b.id, b.`name`, b.title, b.bump_limit, b.same_upload, b.popdown_handler, b.category, count(distinct t.id) as threads_count
	from boards b
	join user_groups ug on ug.user = _user
	left join threads t on t.board = b.id
	left join hidden_threads ht on ht.user = _user and ht.thread = t.id
	left join acl a1 on ug.`group` = a1.`group` and a1.board is null and a1.thread is null and a1.post is null
	left join acl a2 on a2.`group` is null and b.id = a2.board
	left join acl a3 on ug.`group` = a3.`group` and b.id = a3.board
	left join acl a4 on a4.`group` is null and t.id = a4.thread
	left join acl a5 on ug.`group` = a5.`group` and t.id = a5.thread
	where ht.thread is null
	group by b.id
	having max(coalesce(a3.view, a2.view, a1.view)) = 1 and (max(coalesce(a4.view, a5.view)) = 1 or max(coalesce(a4.view, a5.view)) is null)
	order by b.category, b.`name`;
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
/*!50003 DROP PROCEDURE IF EXISTS `sp_boards_get_specifed_view` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'STRICT_TRANS_TABLES,NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50020 DEFINER=`root`@`localhost`*/ /*!50003 PROCEDURE `sp_boards_get_specifed_view`(
	board_name varchar(16),
	user_id int
)
begin
	declare board_id int;
	select id into board_id from boards where `name` = board_name;
	if(board_id is null)
	then
		select 'NOT_FOUND' as error;
	else
		select b.id, b.`name`, b.title, b.bump_limit, b.same_upload,
			b.popdown_handler, b.category
		from boards b
		join user_groups ug on ug.`user` = user_id
		left join acl a1 on a1.`group` = ug.`group` and b.id = a1.board
		left join acl a2 on a2.`group` is null and b.id = a2.board
		left join acl a3 on a3.`group` = ug.`group` and a3.board is null and a3.thread is null and a3.post is null
		where b.id = board_id
			and (coalesce(a1.view, a2.view, a3.view) = 1 or coalesce(a1.view, a2.view, a3.view) is null)
		group by b.id;
	end if;
end */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `sp_boards_get_view` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'STRICT_TRANS_TABLES,NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50020 DEFINER=`root`@`localhost`*/ /*!50003 PROCEDURE `sp_boards_get_view`(
	_user int
)
begin
	select b.id, b.`name`, b.title, b.bump_limit, b.same_upload, b.popdown_handler, b.category, count(distinct t.id) as threads_count
	from boards b
	join user_groups ug on ug.user = _user
	left join threads t on t.board = b.id
	left join hidden_threads ht on ht.user = _user and ht.thread = t.id
	left join acl a1 on ug.`group` = a1.`group` and a1.board is null and a1.thread is null and a1.post is null
	left join acl a2 on a2.`group` is null and b.id = a2.board
	left join acl a3 on ug.`group` = a3.`group` and b.id = a3.board
	left join acl a4 on a4.`group` is null and t.id = a4.thread
	left join acl a5 on ug.`group` = a5.`group` and t.id = a5.thread
	where ht.thread is null
	group by b.id
	having max(coalesce(a3.view, a2.view, a1.view)) = 1 and (max(coalesce(a4.view, a5.view)) = 1 or max(coalesce(a4.view, a5.view)) is null)
	order by b.category, b.`name`;
end */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `sp_board_get_settings` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'STRICT_TRANS_TABLES,NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50020 DEFINER=`root`@`localhost`*/ /*!50003 PROCEDURE `sp_board_get_settings`(
	_board_name varchar(16)
)
begin
	select id, same_upload
	from boards
	where name = _board_name;
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
/*!50003 SET sql_mode              = 'STRICT_TRANS_TABLES,NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50020 DEFINER=`root`@`localhost`*/ /*!50003 PROCEDURE `sp_board_upload_types_add`(
	_board int,
	_upload_type int
)
begin
	insert into board_upload_types (board, upload_type)
	values (_board, _upload_type);
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
/*!50003 SET sql_mode              = 'STRICT_TRANS_TABLES,NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION' */ ;
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
/*!50003 DROP PROCEDURE IF EXISTS `sp_board_upload_types_get` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'STRICT_TRANS_TABLES,NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50020 DEFINER=`root`@`localhost`*/ /*!50003 PROCEDURE `sp_board_upload_types_get`()
begin
	select board, upload_type from board_upload_types;
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
/*!50003 SET sql_mode              = 'STRICT_TRANS_TABLES,NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION' */ ;
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
/*!50003 SET sql_mode              = 'STRICT_TRANS_TABLES,NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50020 DEFINER=`root`@`localhost`*/ /*!50003 PROCEDURE `sp_categories_add`(
	_name varchar(50)
)
begin
	insert into categories (`name`) values (_name);
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
/*!50003 SET sql_mode              = 'STRICT_TRANS_TABLES,NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION' */ ;
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
/*!50003 DROP PROCEDURE IF EXISTS `sp_categories_get` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'STRICT_TRANS_TABLES,NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50020 DEFINER=`root`@`localhost`*/ /*!50003 PROCEDURE `sp_categories_get`()
begin
	select id, `name` from categories;
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
/*!50003 SET sql_mode              = 'STRICT_TRANS_TABLES,NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50020 DEFINER=`root`@`localhost`*/ /*!50003 PROCEDURE `sp_categories_get_all`()
begin
	select id, `name` from categories;
end */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `sp_check_ban` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'STRICT_TRANS_TABLES,NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50020 DEFINER=`root`@`localhost`*/ /*!50003 PROCEDURE `sp_check_ban`(
	ip int
)
begin
call sp_refresh_banlist();
select range_beg, range_end, untill, reason from bans where range_beg <= ip and range_end >= ip order by range_end desc limit 1;
end */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `sp_create_thread` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'STRICT_TRANS_TABLES,NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50020 DEFINER=`root`@`localhost`*/ /*!50003 PROCEDURE `sp_create_thread`(
	_board_name varchar(16),
	_post_number int
)
begin
	declare bumplimit int;
	declare boardid int;
	select id, bump_limit into boardid, bumplimit from boards where name = _board_name;

	insert into threads (board, original_post, bump_limit, sage)
	values (boardid, _post_number, bumplimit, 0);

end */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `sp_get_preview_data` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'STRICT_TRANS_TABLES,NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50020 DEFINER=`root`@`localhost`*/ /*!50003 PROCEDURE `sp_get_preview_data`(
	board_id int,
	page int,
	user_id int,
	threads_per_page int,
	posts_per_thread int
)
begin
	declare done_threads int default 0;
	declare done_posts int default 0;
	declare thread_id int;
	declare counter int default 0;

	declare cur cursor for select t.id
		from threads t
		join boards b on b.id = t.board and b.id = board_id 
		join posts p on p.board = board_id and p.thread = t.id 
		join user_groups ug on ug.`user` = user_id 
		left join hidden_threads ht on t.id = ht.thread and ug.`user` = ht.`user` 
		left join acl a1 on a1.`group` = ug.`group` and a1.thread = t.id 
		left join acl a2 on a2.`group` is null and a2.thread = t.id 
		left join acl a3 on a3.`group` = ug.`group` and a3.post = p.id 
		left join acl a4 on a4.`group` is null and a4.post = p.id 
		where (t.deleted = 0 or t.deleted is null) and (t.archived = 0 or t.archived is null) and ht.thread is null and (p.sage is null or p.sage = 0) 
		group by t.id
		having (max(coalesce(a1.view, a2.view)) = 1 or max(coalesce(a1.view, a2.view)) is null) and (max(coalesce(a3.view, a3.view)) = 1 or max(coalesce(a3.view, a4.view)) is null) 
		order by max(p.`number`) desc;
	declare continue handler for not found set done_threads = 1;
	drop temporary table if exists temp_threads;
	drop temporary table if exists temp_posts;
	drop temporary table if exists temp_posts_uploads;
	create temporary table temp_threads
	(
		id int,
		board int not null,
		original_post int not null,
		bump_limit int,
		sage bit,
		with_images bit
	);
	create temporary table temp_posts
	(
		id int not null,
		thread int not null,
		`number` int not null,
		`user` int not null,
		password varchar(128) default null,
		`name` varchar(128) default null,
		ip bigint default null,
		subject varchar(128) default null,
		date_time datetime default null,
		text text default null
	);
	create temporary table temp_posts_uploads
	(
		post int not null,
		upload int not null
	);
	prepare saving_posts from	'insert into temp_posts select p.id, p.thread, p.number, p.user, p.password, p.name, p.ip, p.subject, p.date_time, p.text
									from posts p
									join threads t on p.board = t.board and p.thread = t.id
									join user_groups ug on ug.user = ?
									left join acl a1 on a1.group = ug.group and a1.post = p.id
									left join acl a2 on a2.group is null and a2.post = p.id
									where p.thread = ? and p.board = ? and p.number != t.original_post
									group by p.id
									having max(coalesce(a1.view, a2.view)) = 1 or max(coalesce(a1.view, a2.view)) is null
									order by p.number
									limit ?';
	open cur;
	repeat
		fetch cur into thread_id;
		if not done_threads
		then
			if(counter between threads_per_page * page - 1 and threads_per_page * page - 1 + threads_per_page - 1)
			then
				insert into temp_threads select id, board, original_post, bump_limit, sage, with_images from threads where id = thread_id;
				insert into temp_posts select p.id, p.thread, p.number, p.user, p.password, p.name, p.ip, p.subject, p.date_time, p.text
					from posts p
					join threads t on p.board = t.board and p.board = board_id and p.thread = t.id and p.thread = thread_id and p.number = t.original_post;
				set @user_id = user_id;
				set @thread_id = thread_id;
				set @board_id = board_id;
				set @posts_per_thread = posts_per_thread;
				execute saving_posts using @user_id, @thread_id, @board_id, @posts_per_thread;
			end if;
			set counter = counter + 1;
		end if;
	until done_threads end repeat;
	close cur;
	select id, board, original_post, bump_limit, sage, with_images from temp_threads;
	select id, thread, `number`, `user`, password, `name`, ip, subject, date_time, text from temp_posts;
	insert into temp_posts_uploads select post, upload
		from posts_uploads pu
		join temp_posts tp on pu.post = tp.id;
	select post, upload from temp_posts_uploads;
	select id, `hash`, is_image, file_name, file_w, file_h, `size`, thumbnail_name, thumbnail_w, thumbnail_h from uploads u
		join temp_posts_uploads tpu on tpu.upload = u.id;
	
	deallocate prepare saving_posts;
	drop temporary table if exists temp_threads;
	drop temporary table if exists temp_posts;
	drop temporary table if exists temp_posts_uploads;
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
/*!50003 SET sql_mode              = 'STRICT_TRANS_TABLES,NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50020 DEFINER=`root`@`localhost`*/ /*!50003 PROCEDURE `sp_groups_add`(
	_name varchar(50)
)
begin
	declare group_id int;
	insert into groups (`name`) values (_name);
	select id into group_id from groups where name = _name;
	
	insert into acl (`group`, `view`, `change`, moderate) values (group_id, 1, 0, 0);
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
/*!50003 SET sql_mode              = 'STRICT_TRANS_TABLES,NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50020 DEFINER=`root`@`localhost`*/ /*!50003 PROCEDURE `sp_groups_delete`(
	_id int
)
begin
	
	delete from acl where `group` = _id;
	delete from user_groups where `group` = _id;
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
/*!50003 SET sql_mode              = 'STRICT_TRANS_TABLES,NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50020 DEFINER=`root`@`localhost`*/ /*!50003 PROCEDURE `sp_groups_get_all`()
begin
	select id, `name` from groups order by id;
end */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `sp_group_add` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'STRICT_TRANS_TABLES,NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50020 DEFINER=`root`@`localhost`*/ /*!50003 PROCEDURE `sp_group_add`(
	_name varchar(50)
)
begin
	declare group_id int;
	start transaction;
	insert into groups (`name`) values (_name);
	select id into group_id from groups where name = _name;
	
	insert into acl (`group`, `view`, `change`, moderate) values (group_id, 1, 0, 0);
	commit;
end */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `sp_group_delete` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'STRICT_TRANS_TABLES,NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50020 DEFINER=`root`@`localhost`*/ /*!50003 PROCEDURE `sp_group_delete`(
	_id int
)
begin
	start transaction;
	
	delete from acl where `group` = _id;
	delete from user_groups where `group` = _id;
	delete from groups where id = _id;
	commit;
end */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `sp_group_get` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'STRICT_TRANS_TABLES,NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50020 DEFINER=`root`@`localhost`*/ /*!50003 PROCEDURE `sp_group_get`()
begin
	select id, `name` from groups order by id;
end */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `sp_hidden_threads_get_all` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'STRICT_TRANS_TABLES,NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50020 DEFINER=`root`@`localhost`*/ /*!50003 PROCEDURE `sp_hidden_threads_get_all`(
	board_id int,
	user_id int
)
begin
	select t.id, t.original_post
	from hidden_threads ht
	join threads t on ht.thread = t.id and t.board = board_id
	where ht.user = user_id;
end */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `sp_hidden_threads_get_board` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'STRICT_TRANS_TABLES,NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50020 DEFINER=`root`@`localhost`*/ /*!50003 PROCEDURE `sp_hidden_threads_get_board`(
	board_id int,
	user_id int
)
begin
	select t.id, t.original_post
	from hidden_threads ht
	join threads t on ht.thread = t.id and t.board = board_id
	where ht.user = user_id;
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
/*!50003 SET sql_mode              = 'STRICT_TRANS_TABLES,NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50020 DEFINER=`root`@`localhost`*/ /*!50003 PROCEDURE `sp_languages_add`(
	_name varchar(50)
)
begin
	insert into languages (`name`) values (_name);
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
/*!50003 SET sql_mode              = 'STRICT_TRANS_TABLES,NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION' */ ;
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
/*!50003 DROP PROCEDURE IF EXISTS `sp_languages_get` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'STRICT_TRANS_TABLES,NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50020 DEFINER=`root`@`localhost`*/ /*!50003 PROCEDURE `sp_languages_get`()
begin
	select id, `name` from languages;
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
/*!50003 SET sql_mode              = 'STRICT_TRANS_TABLES,NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50020 DEFINER=`root`@`localhost`*/ /*!50003 PROCEDURE `sp_languages_get_all`()
begin
	select id, `name` from languages;
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
/*!50003 SET sql_mode              = 'STRICT_TRANS_TABLES,NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50020 DEFINER=`root`@`localhost`*/ /*!50003 PROCEDURE `sp_popdown_handlers_add`(
	_name varchar(50)
)
begin
	insert into popdown_handlers (`name`) values (_name);
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
/*!50003 SET sql_mode              = 'STRICT_TRANS_TABLES,NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION' */ ;
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
/*!50003 DROP PROCEDURE IF EXISTS `sp_popdown_handlers_get` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'STRICT_TRANS_TABLES,NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50020 DEFINER=`root`@`localhost`*/ /*!50003 PROCEDURE `sp_popdown_handlers_get`()
begin
	select id, `name` from popdown_handlers;
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
/*!50003 SET sql_mode              = 'STRICT_TRANS_TABLES,NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50020 DEFINER=`root`@`localhost`*/ /*!50003 PROCEDURE `sp_popdown_handlers_get_all`()
begin
	select id, `name` from popdown_handlers;
end */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `sp_post` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'STRICT_TRANS_TABLES,NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50020 DEFINER=`root`@`localhost`*/ /*!50003 PROCEDURE `sp_post`(
	
	_board_name varchar(16),
	
	_open_post int,
	
	_post_name varchar(128),
	
	_post_trip varchar(10),
	
	_post_subject varchar(128),
	
	_post_password varchar(128),
	
	_post_userid int,
	
	_post_sessionid varchar(128),
	
	_post_ip int,
	
	_post_text text,
	
	_datetime datetime,
	
	_sage tinyint
)
BEGIN
	
	declare threadid int;
	
	declare count_posts int;
	
	declare post_number int;
	
	declare bumplimit int;
	
	declare threadsage bit;

	
	if _datetime is null then
		select now() into _datetime;
	end if;

	
	set post_number = get_next_post_on_board(_board_name);
	if _open_post = 0 then
		
		call sp_create_thread(_board_name, post_number);
		set threadid = LAST_INSERT_ID();
		
		set _sage = 0;
	else
		set threadid = get_thread_id(_board_name, _open_post);
	end if;
	
	select get_posts_count(threadid) into count_posts;
	
	select bump_limit into bumplimit from threads
	where id = threadid;

	
	select sage into threadsage from threads where id = threadid;
	if threadsage is not null then
		set _sage = threadsage;
	end if;

	
	if count_posts > bumplimit then
		set _sage = 1;
	end if;

	
	if _sage = 0 then
		update threads set last_post = _datetime
		where id = threadid and board = get_board_id(_board_name);
	end if;

	
	insert into posts(board, thread, number, user,
		name, tripcode, subject, text, password,
		session_id, ip, date_time, sage)
	values
	(get_board_id(_board_name), threadid, post_number, _post_userid,
		_post_name, _post_trip, _post_subject, _post_text, _post_password,
		_post_sessionid, _post_ip, _datetime, _sage);

	select post_number;
END */;;
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
/*!50003 SET sql_mode              = 'STRICT_TRANS_TABLES,NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50020 DEFINER=`root`@`localhost`*/ /*!50003 PROCEDURE `sp_posts_add`(
	_board_id int,
	_thread_id int,
	_user_id int,
	_password varchar(128),
	_name varchar(128),
	_ip bigint,
	_subject varchar(128),
	_datetime datetime,
	_text text,
	_sage bit
)
begin
	declare count_posts int;	
	declare post_number int;	
	declare bumplimit int;		
	declare threadsage bit;		
	declare post_id int;
	select max(`number`) into post_number from posts where board = _board_id;
	if(post_number is null)
	then
		set post_number = 1;
	else
		set post_number = post_number + 1;
	end if;
	select bump_limit into bumplimit from threads where id = _thread_id;
	select count(id) into count_posts from posts where thread = _thread_id;
	select sage into threadsage from threads where id = _thread_id;
	if(threadsage is not null and threadsage = 1)
	then
		set _sage = 1;
	end if;
	if(count_posts > bumplimit)
	then
		set _sage = 1;
	end if;
	if(_datetime is null)
	then
		set _datetime = now();
	end if;
	insert into posts (board, thread, `number`, `user`, password, `name`, ip,
		subject, date_time, text, sage, deleted)
	values (_board_id, _thread_id, post_number, _user_id, _password, _name, _ip,
		_subject, _datetime, _text, _sage, 0);
	select last_insert_id() into post_id;
	select * from posts where id = post_id;
end */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `sp_posts_add_reply` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'STRICT_TRANS_TABLES,NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50020 DEFINER=`root`@`localhost`*/ /*!50003 PROCEDURE `sp_posts_add_reply`(
	_board_id int,
	_thread_id int,
	_user_id int,
	_password varchar(128),
	_name varchar(128),
	_ip bigint,
	_subject varchar(128),
	_datetime datetime,
	_text text,
	_sage bit
)
begin
	declare count_posts int;	
	declare post_number int;	
	declare bumplimit int;		
	declare threadsage bit;		
	select max(`number`) into post_number from posts where board = _board_id;
	if(post_number is null)
	then
		set post_number = 1;
	else
		set post_number = post_number + 1;
	end if;
	select bump_limit into bumplimit from threads where id = _thread_id;
	select count(id) into count_posts from posts where thread = _thread_id;
	select sage into threadsage from threads where id = _thread_id;
	if(threadsage is not null)
	then
		set _sage = threadsage;
	end if;
	if(count_posts > bumplimit)
	then
		set _sage = 1;
	end if;
	if(_datetime is null)
	then
		set _datetime = now();
	end if;
	insert into posts (board, thread, `number`, `user`, password, `name`, ip,
		subject, date_time, text, sage)
	values (_board_id, _thread_id, post_number, _user_id, _password, _name, _ip,
		_subject, _datetime, _text, _sage);
	select last_insert_id() as `id`;
end */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `sp_posts_get` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'STRICT_TRANS_TABLES,NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50020 DEFINER=`root`@`localhost`*/ /*!50003 PROCEDURE `sp_posts_get`(
	board_id int,
	page int,
	user_id int,
	threads_per_page int,
	posts_per_thread int
)
begin
	declare done_threads int default 0;
	declare done_posts int default 0;
	declare thread_id int;
	declare counter int default 0;
	declare cur cursor for select t.id
		from threads t
		join boards b on b.id = t.board and b.id = board_id 
		join posts p on p.board = board_id and p.thread = t.id 
		join user_groups ug on ug.`user` = user_id 
		left join hidden_threads ht on t.id = ht.thread and ug.`user` = ht.`user` 
		left join acl a1 on a1.`group` = ug.`group` and a1.thread = t.id 
		left join acl a2 on a2.`group` is null and a2.thread = t.id 
		left join acl a3 on a3.`group` = ug.`group` and a3.post = p.id 
		left join acl a4 on a4.`group` is null and a4.post = p.id 
		where (t.deleted = 0 or t.deleted is null) and (t.archived = 0 or t.archived is null) and ht.thread is null and (p.sage is null or p.sage = 0) 
		group by t.id
		having (max(coalesce(a1.view, a2.view)) = 1 or max(coalesce(a1.view, a2.view)) is null) and (max(coalesce(a3.view, a3.view)) = 1 or max(coalesce(a3.view, a4.view)) is null) 
		order by max(p.`number`) desc;
	declare continue handler for not found set done_threads = 1;
	drop temporary table if exists temp_posts;
	create temporary table temp_posts
	(
		id int not null,
		thread int not null,
		`number` int not null,
		`user` int not null,
		password varchar(128) default null,
		`name` varchar(128) default null,
		ip bigint default null,
		subject varchar(128) default null,
		date_time datetime default null,
		text text default null
	);
	prepare saving_posts from 'insert into temp_posts select id, thread, `number`, `user`, password, `name`, ip, subject, date_time, text from posts where thread = ? and board = ? order by `number` limit ?';
	open cur;
	repeat
		fetch cur into thread_id;
		if not done_threads
		then
			if(counter between threads_per_page * page - 1 and threads_per_page * page - 1 + threads_per_page - 1)
			then
				set @thread_id = thread_id;
				set @board_id = board_id;
				set @posts_per_thread = posts_per_thread;
				execute saving_posts using @thread_id, @board_id, @posts_per_thread;
			end if;
			set counter = counter + 1;
		end if;
	until done_threads end repeat;
	close cur;
	select id, thread, `number`, `user`, password, `name`, ip, subject, date_time, text from temp_posts;
	deallocate prepare saving_posts;
	drop temporary table if exists temp_posts;
end */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `sp_posts_get_preview` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'STRICT_TRANS_TABLES,NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50020 DEFINER=`root`@`localhost`*/ /*!50003 PROCEDURE `sp_posts_get_preview`(
	thread_id int,
	user_id int,
	posts_per_thread int
)
begin
	prepare stmnt from
		'select p.id, p.thread, p.number, p.password, p.name, p.ip, p.subject, p.date_time, p.text, p.sage
		from posts p
		join threads t on p.board = t.board and p.thread = t.id
		join user_groups ug on ug.user = ?
		left join acl a1 on a1.group = ug.group and a1.post = p.id
		left join acl a2 on a2.group is null and a2.post = p.id
		where p.thread = ? and p.number != t.original_post
		group by p.id
		having max(coalesce(a1.view, a2.view)) = 1 or max(coalesce(a1.view, a2.view)) is null
		limit ?
		union all
		select p.id, p.thread, p.number, p.password, p.name, p.ip, p.subject, p.date_time, p.text, p.sage
		from posts p
		join threads t on p.board = t.board and p.thread = t.id
		where p.number = t.original_post and p.thread = ?
		order by number desc';
	set @user_id = user_id;
	set @thread_id = thread_id;
	set @limit = posts_per_thread;
	execute stmnt using @user_id, @thread_id, @limit, @thread_id;
	deallocate prepare stmnt;
end */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `sp_posts_get_threads_view` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'STRICT_TRANS_TABLES,NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50020 DEFINER=`root`@`localhost`*/ /*!50003 PROCEDURE `sp_posts_get_threads_view`(
	thread_id int,
	user_id int,
	posts_per_thread int
)
begin
	prepare stmnt from
		'select p.id, p.thread, p.number, p.password, p.name, p.ip, p.subject,
			p.date_time, p.text, p.sage
		from posts p
		join threads t on t.board = p.board and t.id = p.thread
		join user_groups ug on ug.user = ?
		-- Правило для конкретной группы и сообщения.
		left join acl a1 on a1.`group` = ug.`group` and a1.post = p.id
		-- Правило для всех групп и конкретного сообщения.
		left join acl a2 on a2.`group` is null and a2.post = p.id
		-- Правила для конкретной группы и нити.
		left join acl a3 on a3.`group` = ug.`group` and a3.thread = t.id
		-- Правило для всех групп и конкретной нити.
		left join acl a4 on a4.`group` is null and a4.thread = t.id
		-- Правила для конкретной группы и доски.
		left join acl a5 on a5.`group` = ug.`group` and a5.board = p.board
		-- Правило для всех групп и конкретной доски.
		left join acl a6 on a6.`group` is null and a6.board = p.board
		-- Правило для конкретной групы.
		left join acl a7 on a7.`group` = ug.`group` and a7.board is null and a7.thread is null and a7.post is null
		where p.thread = ?
			and p.number != t.original_post
			and (p.deleted = 0 or p.deleted is null)
			-- Сообщение должно быть доступно для просмотра, чтобы правильно подсчитать их количество в нити.
				-- Просмотр сообщения не запрещен конкретной группе и
			and ((a1.`view` = 1 or a1.`view` is null)
				-- просмотр сообщения не запрещен всем группам и
				and (a2.`view` = 1 or a2.`view` is null)
				-- просмотр нити не запрещен конкретной группе и
				and (a3.`view` = 1 or a3.`view` is null)
				-- просмотр нити не запрещен всем группам и
				and (a4.`view` = 1 or a4.`view` is null)
				-- просмотр доски не запрещен конкретной группе и
				and (a5.`view` = 1 or a5.`view` is null)
				-- просмотр доски не запрещен всем группам и
				and (a6.`view` = 1 or a6.`view` is null)
				-- просмотр разрешен конкретной группе.
				and a7.`view` = 1)
		group by p.id
		limit ?
		union all
		select p.id, p.thread, p.number, p.password, p.name, p.ip, p.subject,
			p.date_time, p.text, p.sage
		from posts p
		join threads t on t.board = p.board and t.id = p.thread
		where p.number = t.original_post and p.thread = ?
		order by number desc';
	set @user_id = user_id;
	set @thread_id = thread_id;
	set @limit = posts_per_thread;
	execute stmnt using @user_id, @thread_id, @limit, @thread_id;
	deallocate prepare stmnt;
end */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `sp_posts_get_thread_view` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'STRICT_TRANS_TABLES,NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50020 DEFINER=`root`@`localhost`*/ /*!50003 PROCEDURE `sp_posts_get_thread_view`(
	thread_id int,
	user_id int,
	posts_per_thread int
)
begin
	prepare stmnt from
		'select q.id, q.thread, q.number, q.password, q.name, q.ip, q.subject,
			q.date_time, q.text, q.sage
		from (select p.id, p.thread, p.number, p.password, p.name, p.ip, p.subject,
			p.date_time, p.text, p.sage
		from posts p
		join threads t on t.board = p.board and t.id = p.thread
		join user_groups ug on ug.user = ?
		-- Правило для конкретной группы и сообщения.
		left join acl a1 on a1.`group` = ug.`group` and a1.post = p.id
		-- Правило для всех групп и конкретного сообщения.
		left join acl a2 on a2.`group` is null and a2.post = p.id
		-- Правила для конкретной группы и нити.
		left join acl a3 on a3.`group` = ug.`group` and a3.thread = t.id
		-- Правило для всех групп и конкретной нити.
		left join acl a4 on a4.`group` is null and a4.thread = t.id
		-- Правила для конкретной группы и доски.
		left join acl a5 on a5.`group` = ug.`group` and a5.board = p.board
		-- Правило для всех групп и конкретной доски.
		left join acl a6 on a6.`group` is null and a6.board = p.board
		-- Правило для конкретной групы.
		left join acl a7 on a7.`group` = ug.`group` and a7.board is null and a7.thread is null and a7.post is null
		where p.thread = ?
			and p.number != t.original_post
			and (p.deleted = 0 or p.deleted is null)
			-- Сообщение должно быть доступно для просмотра, чтобы правильно подсчитать их количество в нити.
				-- Просмотр сообщения не запрещен конкретной группе и
			and ((a1.`view` = 1 or a1.`view` is null)
				-- просмотр сообщения не запрещен всем группам и
				and (a2.`view` = 1 or a2.`view` is null)
				-- просмотр нити не запрещен конкретной группе и
				and (a3.`view` = 1 or a3.`view` is null)
				-- просмотр нити не запрещен всем группам и
				and (a4.`view` = 1 or a4.`view` is null)
				-- просмотр доски не запрещен конкретной группе и
				and (a5.`view` = 1 or a5.`view` is null)
				-- просмотр доски не запрещен всем группам и
				and (a6.`view` = 1 or a6.`view` is null)
				-- просмотр разрешен конкретной группе.
				and a7.`view` = 1)
		group by p.id
		order by number desc
		limit ?) q
		union all
		select p.id, p.thread, p.number, p.password, p.name, p.ip, p.subject,
			p.date_time, p.text, p.sage
		from posts p
		join threads t on t.board = p.board and t.id = p.thread
		where p.number = t.original_post and p.thread = ?
		order by number asc';
	set @user_id = user_id;
	set @thread_id = thread_id;
	set @limit = posts_per_thread;
	execute stmnt using @user_id, @thread_id, @limit, @thread_id;
	deallocate prepare stmnt;
end */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `sp_posts_get_view` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'STRICT_TRANS_TABLES,NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50020 DEFINER=`root`@`localhost`*/ /*!50003 PROCEDURE `sp_posts_get_view`(
	thread_id int,
	user_id int,
	posts_per_thread int
)
begin
	prepare stmnt from
		'select p.id, p.thread, p.number, p.password, p.name, p.ip, p.subject, p.date_time, p.text, p.sage
		from posts p
		join threads t on p.board = t.board and p.thread = t.id
		join user_groups ug on ug.user = ?
		left join acl a1 on a1.group = ug.group and a1.post = p.id
		left join acl a2 on a2.group is null and a2.post = p.id
		where p.thread = ? and p.number != t.original_post
		group by p.id
		having max(coalesce(a1.view, a2.view)) = 1 or max(coalesce(a1.view, a2.view)) is null
		limit ?
		union all
		select p.id, p.thread, p.number, p.password, p.name, p.ip, p.subject, p.date_time, p.text, p.sage
		from posts p
		join threads t on p.board = t.board and p.thread = t.id
		where p.number = t.original_post and p.thread = ?
		order by number asc';
	set @user_id = user_id;
	set @thread_id = thread_id;
	set @limit = posts_per_thread;
	execute stmnt using @user_id, @thread_id, @limit, @thread_id;
	deallocate prepare stmnt;
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
/*!50003 DROP PROCEDURE IF EXISTS `sp_posts_uploads_get_all` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'STRICT_TRANS_TABLES,NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50020 DEFINER=`root`@`localhost`*/ /*!50003 PROCEDURE `sp_posts_uploads_get_all`(
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
/*!50003 DROP PROCEDURE IF EXISTS `sp_posts_uploads_get_post` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'STRICT_TRANS_TABLES,NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50020 DEFINER=`root`@`localhost`*/ /*!50003 PROCEDURE `sp_posts_uploads_get_post`(
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
/*!50003 DROP PROCEDURE IF EXISTS `sp_post_upload` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'STRICT_TRANS_TABLES,NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50020 DEFINER=`root`@`localhost`*/ /*!50003 PROCEDURE `sp_post_upload`(
	_board_name varchar(16),
	_post int,
	_upload int
)
begin
	insert into posts_uploads (post, upload)
	values ((select id from posts where board = get_board_id(_board_name) and number = _post),
		_upload);
end */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `sp_refresh_banlist` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'STRICT_TRANS_TABLES,NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50020 DEFINER=`root`@`localhost`*/ /*!50003 PROCEDURE `sp_refresh_banlist`()
begin
delete from bans where untill <= now();
end */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `sp_save_user_settings` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'STRICT_TRANS_TABLES,NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50020 DEFINER=`root`@`localhost`*/ /*!50003 PROCEDURE `sp_save_user_settings`(
	_keyword varchar(32),
	_threads_per_page int,
	_posts_per_thread int,
	_lines_per_post int,
	_stylesheet varchar(50),
	_language varchar(50),
	_rempass varchar(12)
)
begin
	declare user_id int;
	declare stylesheet_id int;
	declare language_id int;
	set @user_id = null;
	select id into user_id from users where keyword = _keyword;
	select id into stylesheet_id from stylesheets where name = _stylesheet;
	select id into language_id from languages where name = _language;
	if(_rempass = '')
	then
		set _rempass = null;
	end if;
	if(user_id is null)
	then
		
		start transaction;
		insert into users (keyword, threads_per_page, posts_per_thread, lines_per_post, stylesheet, `language`, rempass)
		values (_keyword, _threads_per_page, _posts_per_thread, _lines_per_post, stylesheet_id, language_id, _rempass);
		select last_insert_id() into user_id;
		insert into user_groups (`user`, `group`) select user_id, id from groups where name = 'Users';
		commit;
	else
		
		update users set threads_per_page = _threads_per_page, posts_per_thread = _posts_per_thread, lines_per_post = _lines_per_post, stylesheet = stylesheet_id, `language` = language_id, rempass = _rempass where id = user_id;
	end if;
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
/*!50003 SET sql_mode              = 'STRICT_TRANS_TABLES,NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50020 DEFINER=`root`@`localhost`*/ /*!50003 PROCEDURE `sp_stylesheets_add`(
	_name varchar(50)
)
begin
	insert into stylesheets (`name`) values (_name);
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
/*!50003 SET sql_mode              = 'STRICT_TRANS_TABLES,NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION' */ ;
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
/*!50003 DROP PROCEDURE IF EXISTS `sp_stylesheets_get` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'STRICT_TRANS_TABLES,NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50020 DEFINER=`root`@`localhost`*/ /*!50003 PROCEDURE `sp_stylesheets_get`()
begin
	select id, `name` from stylesheets;
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
/*!50003 SET sql_mode              = 'STRICT_TRANS_TABLES,NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50020 DEFINER=`root`@`localhost`*/ /*!50003 PROCEDURE `sp_stylesheets_get_all`()
begin
	select id, `name` from stylesheets;
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
/*!50003 SET sql_mode              = 'STRICT_TRANS_TABLES,NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50020 DEFINER=`root`@`localhost`*/ /*!50003 PROCEDURE `sp_threads_add`(
	_board_id int,
	_original_post int,
	_bump_limit int,
	_sage bit,
	_with_files bit
)
begin
	declare thread_id int;
	insert into threads (board, original_post, bump_limit, deleted, archived,
		sage, with_files)
	values (_board_id, _original_post, _bump_limit, 0, 0,
		_sage, _with_files);
	select last_insert_id() into thread_id;
	select * from threads where id = thread_id;
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
/*!50003 DROP PROCEDURE IF EXISTS `sp_threads_edit` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'STRICT_TRANS_TABLES,NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50020 DEFINER=`root`@`localhost`*/ /*!50003 PROCEDURE `sp_threads_edit`(
	_id int,
	_bump_limit int,
	_sticky bit,
	_sage bit,
	_with_files bit
)
begin
	update threads set bump_limit = _bump_limit, sticky = _sticky, sage = _sage,
		with_files = _with_files
	where id = _id;
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
/*!50003 DROP PROCEDURE IF EXISTS `sp_threads_get_all` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'STRICT_TRANS_TABLES,NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50020 DEFINER=`root`@`localhost`*/ /*!50003 PROCEDURE `sp_threads_get_all`()
begin
	select id, board, original_post, bump_limit, sticky, sage, with_files
	from threads
	where deleted = 0 and archived = 0
	order by id desc;
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
/*!50003 DROP PROCEDURE IF EXISTS `sp_threads_get_all_view` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'STRICT_TRANS_TABLES,NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50020 DEFINER=`root`@`localhost`*/ /*!50003 PROCEDURE `sp_threads_get_all_view`(
	board_id int,
	page int,
	user_id int,
	threads_per_page int
)
begin
	
	prepare stmnt from
		'select t.id, t.original_post, t.bump_limit, t.sage, t.with_images, count(distinct p.id) as posts_count
		from threads t
		join boards b on b.id = t.board and b.id = ?
		join posts p on p.board = ? and p.thread = t.id
		join user_groups ug on ug.`user` = ?
		left join hidden_threads ht on t.id = ht.thread and ug.`user` = ht.`user`
		-- Правило для конкретной группы и сообщения.
		left join acl a1 on a1.`group` = ug.`group` and a1.post = p.id
		-- Правило для всех групп и конкретного сообщения.
		left join acl a2 on a2.`group` is null and a2.post = p.id
		-- Правила для конкретной группы и нити.
		left join acl a3 on a3.`group` = ug.`group` and a3.thread = t.id
		-- Правило для всех групп и конкретной нити.
		left join acl a4 on a4.`group` is null and a4.thread = t.id
		-- Правила для конкретной группы и доски.
		left join acl a5 on a5.`group` = ug.`group` and a5.board = b.id
		-- Правило для всех групп и конкретной доски.
		left join acl a6 on a6.`group` is null and a6.board = b.id
		-- Правило для конкретной групы.
		left join acl a7 on a7.`group` = ug.`group` and a7.board is null and a7.thread is null and a7.post is null
		where (t.deleted = 0 or t.deleted is null)
			and (t.archived = 0 or t.archived is null)
			and ht.thread is null
			and (p.deleted = 0 or p.deleted is null)
			and (p.sage is null or p.sage = 0)
			-- Нить должна быть доступна для просмотра.
			and ((a3.`view` = 1 or a3.`view` is null)		-- Просмотр нити не запрещен конкретной группе и
				and (a4.`view` = 1 or a4.`view` is null)	-- просмотр нити не запрещен всем группам и
				and (a5.`view` = 1 or a5.`view` is null)	-- просмотр доски не запрещен конкретной группе и
				and (a6.`view` = 1 or a6.`view` is null)	-- просмотр доски не запрещен всем группам и
				and a7.`view` = 1)							-- просмотр разрешен конкретной группе.
			-- Сообщение должно быть доступно для просмотра, чтобы правильно подсчитать их количество в нити.
			and ((a1.`view` = 1 or a1.`view` is null)		-- Просмотр сообщения не запрещен конкретной группе и
				and (a2.`view` = 1 or a2.`view` is null)	-- просмотр сообщения не запрещен всем группам и
				and (a3.`view` = 1 or a3.`view` is null)	-- просмотр нити не запрещен конкретной группе и
				and (a4.`view` = 1 or a4.`view` is null)	-- просмотр нити не запрещен всем группам и
				and (a5.`view` = 1 or a5.`view` is null)	-- просмотр доски не запрещен конкретной группе и
				and (a6.`view` = 1 or a6.`view` is null)	-- просмотр доски не запрещен всем группам и
				and a7.`view` = 1)							-- просмотр разрешен конкретной группе.
		group by t.id
		order by max(p.`number`) desc
		limit ? offset ?';
	
	set @board_id = board_id;
	set @user_id = user_id;
	if(page = 1) then
		set @offset = 0;
		set @limit = threads_per_page;
	else
		set @offset = threads_per_page * (page - 1);
		set @limit = threads_per_page + (page - 1);
	end if;
	execute stmnt using @board_id, @board_id, @user_id, @limit, @offset;
	deallocate prepare stmnt;
end */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `sp_threads_get_board_view` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'STRICT_TRANS_TABLES,NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50020 DEFINER=`root`@`localhost`*/ /*!50003 PROCEDURE `sp_threads_get_board_view`(
	board_id int,
	page int,
	user_id int,
	threads_per_page int,
	sticky bit
)
begin
	
	prepare stmnt from
		'-- Выберем нити, отсортированные по последнему сообщению без сажи.
		select q1.id, q1.original_post, q1.bump_limit, q1.sticky, q1.sage, q1.with_files,
			q1.posts_count, q1.last_post_num
		from (
			-- Без учёта постов с сажей вычислим последнее сообщение в нити.
			select q.id, q.original_post, q.bump_limit, q.sticky, q.sage, q.with_files,
				q.posts_count, max(p.`number`) as last_post_num
			from posts p
			join (
				-- Выберем видимые нити и подсчитаем количество видимых сообщений.
				select t.id, t.original_post, t.bump_limit, t.sticky, t.sage, t.with_files,
					count(distinct p.id) as posts_count
				from posts p
				join threads t on t.id = p.thread and t.board = ?
				join user_groups ug on ug.`user` = ?
				left join hidden_threads ht on ht.thread = t.id and ht.`user` = ug.`user`
				-- Правило для конкретной группы и сообщения.
				left join acl a1 on a1.`group` = ug.`group` and a1.post = p.id
				-- Правило для всех групп и конкретного сообщения.
				left join acl a2 on a2.`group` is null and a2.post = p.id
				-- Правила для конкретной группы и нити.
				left join acl a3 on a3.`group` = ug.`group` and a3.thread = p.thread
				-- Правило для всех групп и конкретной нити.
				left join acl a4 on a4.`group` is null and a4.thread = p.thread
				-- Правила для конкретной группы и доски.
				left join acl a5 on a5.`group` = ug.`group` and a5.board = p.board
				-- Правило для всех групп и конкретной доски.
				left join acl a6 on a6.`group` is null and a6.board = p.board
				-- Правило для конкретной групы.
				left join acl a7 on a7.`group` = ug.`group` and a7.board is null
					and a7.thread is null and a7.post is null
				where t.deleted = 0
					and t.archived = 0
					and t.sticky = ?
					and ht.thread is null
					and p.deleted = 0
					-- Нить должна быть доступна для просмотра.
						-- Просмотр нити не запрещен конкретной группе и
					and ((a3.`view` = 1 or a3.`view` is null)
						-- просмотр нити не запрещен всем группам и
						and (a4.`view` = 1 or a4.`view` is null)
						-- просмотр доски не запрещен конкретной группе и
						and (a5.`view` = 1 or a5.`view` is null)
						-- просмотр доски не запрещен всем группам и
						and (a6.`view` = 1 or a6.`view` is null)
						-- просмотр разрешен конкретной группе.
						and a7.`view` = 1)
					-- Сообщение должно быть доступно для просмотра, чтобы правильно
					-- подсчитать их количество в нити.
						-- Просмотр сообщения не запрещен конкретной группе и
					and ((a1.`view` = 1 or a1.`view` is null)
						-- просмотр сообщения не запрещен всем группам и
						and (a2.`view` = 1 or a2.`view` is null)
						-- просмотр нити не запрещен конкретной группе и
						and (a3.`view` = 1 or a3.`view` is null)
						-- просмотр нити не запрещен всем группам и
						and (a4.`view` = 1 or a4.`view` is null)
						-- просмотр доски не запрещен конкретной группе и
						and (a5.`view` = 1 or a5.`view` is null)
						-- просмотр доски не запрещен всем группам и
						and (a6.`view` = 1 or a6.`view` is null)
						-- просмотр разрешен конкретной группе.
						and a7.`view` = 1)
				group by t.id) q on q.id = p.thread and (p.sage = 0 or p.sage is null)
			group by q.id) q1
		order by q1.last_post_num desc
		limit ? offset ?';
	
	set @board_id = board_id;
	set @user_id = user_id;
	set @limit = threads_per_page;
	set @sticky = sticky;
	if(page = 1) then
		set @offset = 0;
	else
		set @offset = threads_per_page * (page - 1);
	end if;
	execute stmnt using @board_id, @user_id, @sticky, @limit, @offset;
	deallocate prepare stmnt;
end */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `sp_threads_get_mod` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'STRICT_TRANS_TABLES,NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50020 DEFINER=`root`@`localhost`*/ /*!50003 PROCEDURE `sp_threads_get_mod`(
	user_id int
)
begin
	select t.id, t.board, t.original_post, t.bump_limit, t.sage, t.with_images
	from threads t
	join boards b on t.board = b.id
	join user_groups ug on ug.`user` = user_id
	left join hidden_threads ht on t.id = ht.thread and ug.`user` = ht.`user`
	left join acl a1 on a1.`group` = ug.`group` and a1.board is null and a1.thread is null and a1.post is null
	left join acl a2 on a2.`group` is null and a2.board = b.id
	left join acl a3 on a3.`group` = ug.`group` and a3.board = b.id
	left join acl a4 on a4.`group` is null and a4.thread = t.id
	left join acl a5 on a5.`group` = ug.`group` and a5.thread = t.id
	where (t.deleted = 0 or t.deleted is null) and (t.archived = 0 or t.archived is null) and ht.thread is null
	group by t.id
	having max(coalesce(a5.moderate, a4.moderate, a3.moderate, a2.moderate, a1.moderate)) = 1
	order by t.id desc;
end */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `sp_threads_get_mod_specifed` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'STRICT_TRANS_TABLES,NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50020 DEFINER=`root`@`localhost`*/ /*!50003 PROCEDURE `sp_threads_get_mod_specifed`(
	user_id int,
	thread_id int
)
begin
	select t.id, t.board, t.original_post, t.bump_limit, t.sage, t.with_images
	from threads t
	join boards b on t.board = b.id
	join user_groups ug on ug.`user` = user_id
	left join hidden_threads ht on t.id = ht.thread and ug.`user` = ht.`user`
	left join acl a1 on a1.`group` = ug.`group` and a1.board is null and a1.thread is null and a1.post is null
	left join acl a2 on a2.`group` is null and a2.board = b.id
	left join acl a3 on a3.`group` = ug.`group` and a3.board = b.id
	left join acl a4 on a4.`group` is null and a4.thread = t.id
	left join acl a5 on a5.`group` = ug.`group` and a5.thread = t.id
	where (t.deleted = 0 or t.deleted is null) and (t.archived = 0 or t.archived is null) and ht.thread is null and t.id = thread_id
	group by t.id
	having max(coalesce(a5.moderate, a4.moderate, a3.moderate, a2.moderate, a1.moderate)) = 1
	order by t.id desc;
end */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `sp_threads_get_preview` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'STRICT_TRANS_TABLES,NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50020 DEFINER=`root`@`localhost`*/ /*!50003 PROCEDURE `sp_threads_get_preview`(
	board_id int,
	page int,
	user_id int,
	threads_per_page int
)
begin
	
	prepare stmnt from
		'select t.id, t.original_post, t.bump_limit, t.sage, t.with_images, count(distinct p.id) as posts_count
		from threads t
		join boards b on b.id = t.board and b.id = ?
		join posts p on p.board = ? and p.thread = t.id
		join user_groups ug on ug.`user` = ?
		left join hidden_threads ht on t.id = ht.thread and ug.`user` = ht.`user`
		left join acl a1 on a1.`group` = ug.`group` and a1.thread = t.id
		left join acl a2 on a2.`group` is null and a2.thread = t.id
		left join acl a3 on a3.`group` = ug.`group` and a3.post = p.id
		left join acl a4 on a4.`group` is null and a4.post = p.id
		where (t.deleted = 0 or t.deleted is null) and (t.archived = 0 or t.archived is null) and ht.thread is null and (p.sage is null or p.sage = 0)
		group by t.id
		having (max(coalesce(a1.view, a2.view)) = 1 or max(coalesce(a1.view, a2.view)) is null) and (max(coalesce(a3.view, a3.view)) = 1 or max(coalesce(a3.view, a4.view)) is null)
		order by max(p.`number`) desc
		limit ? offset ?';
	
	set @board_id = board_id;
	set @user_id = user_id;
	if(page = 1) then
		set @offset = 0;
		set @limit = threads_per_page;
	else
		set @offset = threads_per_page * (page - 1);
		set @limit = threads_per_page + (page - 1);
	end if;
	execute stmnt using @board_id, @board_id, @user_id, @limit, @offset;
	deallocate prepare stmnt;
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
/*!50003 DROP PROCEDURE IF EXISTS `sp_threads_get_specifed_moderate` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'STRICT_TRANS_TABLES,NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50020 DEFINER=`root`@`localhost`*/ /*!50003 PROCEDURE `sp_threads_get_specifed_moderate`(
	thread_id int,
	user_id int
)
begin
	select id into thread_id from threads where id = thread_id;
	if thread_id is null
	then
		select 'NOT_FOUND' as error;
	else
		select t.id, b.`name` as board_name, t.original_post, t.bump_limit, t.archived, t.sage, t.with_images
		from threads t
		join user_groups ug on ug.`user` = user_id
		join boards b on t.board = b.id
		left join hidden_threads ht on t.id = ht.thread and ug.`user` = ht.`user`
		left join acl a1 on a1.`group` = ug.`group` and a1.thread = t.id
		left join acl a2 on a2.`group` is null and a2.thread = t.id
		left join acl a3 on a3.`group` = ug.`group` and a3.board is null and a3.thread is null and a3.post is null
		where (t.deleted = 0 or t.deleted is null)
			and ht.thread is null
			and t.id = thread_id
			
			and coalesce(a1.moderate, a2.moderate, a3.moderate) = 1
		group by t.id;
	end if;	
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
/*!50003 DROP PROCEDURE IF EXISTS `sp_threads_get_specifed_view_threadscount` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'STRICT_TRANS_TABLES,NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50020 DEFINER=`root`@`localhost`*/ /*!50003 PROCEDURE `sp_threads_get_specifed_view_threadscount`(
	user_id int,
	board_id int
)
begin
	select count(t.id) as threads_count
	from threads t
	join user_groups ug on ug.user = user_id
	left join hidden_threads ht on ht.thread = t.id and ht.`user` = ug.`user`
	
	left join acl a1 on a1.`group` = ug.`group` and a1.thread = t.id
	
	left join acl a2 on a2.`group` is null and a2.thread = t.id
	
	left join acl a3 on a3.`group` = ug.`group` and a3.board = t.board
	
	left join acl a4 on a4.`group` is null and a4.board = t.board
	
	left join acl a5 on a5.`group` = ug.`group` and a5.board is null and a5.thread is null and a5.post is null
	where t.board = board_id
		and (t.deleted = 0 or t.deleted is null)
		and (t.archived = 0 or t.archived is null)
		and ht.thread is null
		
			
		and ((a1.`view` = 1 or a1.`view` is null)
			
			and (a2.`view` = 1 or a2.`view` is null)
			
			and (a3.`view` = 1 or a3.`view` is null)
			
			and (a4.`view` = 1 or a4.`view` is null)
			
			and a5.`view` = 1)
	group by t.id;
end */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `sp_threads_get_view` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'STRICT_TRANS_TABLES,NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50020 DEFINER=`root`@`localhost`*/ /*!50003 PROCEDURE `sp_threads_get_view`(
	board_id int,
	page int,
	user_id int,
	threads_per_page int
)
begin
	
	prepare stmnt from
		'select t.id, t.original_post, t.bump_limit, t.sage, t.with_images, count(distinct p.id) as posts_count
		from threads t
		join boards b on b.id = t.board and b.id = ?
		join posts p on p.board = ? and p.thread = t.id
		join user_groups ug on ug.`user` = ?
		left join hidden_threads ht on t.id = ht.thread and ug.`user` = ht.`user`
		left join acl a1 on a1.`group` = ug.`group` and a1.thread = t.id
		left join acl a2 on a2.`group` is null and a2.thread = t.id
		left join acl a3 on a3.`group` = ug.`group` and a3.post = p.id
		left join acl a4 on a4.`group` is null and a4.post = p.id
		where (t.deleted = 0 or t.deleted is null) and (t.archived = 0 or t.archived is null) and ht.thread is null and (p.sage is null or p.sage = 0)
		group by t.id
		having (max(coalesce(a1.view, a2.view)) = 1 or max(coalesce(a1.view, a2.view)) is null) and (max(coalesce(a3.view, a3.view)) = 1 or max(coalesce(a3.view, a4.view)) is null)
		order by max(p.`number`) desc
		limit ? offset ?';
	
	set @board_id = board_id;
	set @user_id = user_id;
	if(page = 1) then
		set @offset = 0;
		set @limit = threads_per_page;
	else
		set @offset = threads_per_page * (page - 1);
		set @limit = threads_per_page + (page - 1);
	end if;
	execute stmnt using @board_id, @board_id, @user_id, @limit, @offset;
	deallocate prepare stmnt;
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
/*!50003 DROP PROCEDURE IF EXISTS `sp_upload` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'STRICT_TRANS_TABLES,NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50020 DEFINER=`root`@`localhost`*/ /*!50003 PROCEDURE `sp_upload`(
	_board_name varchar(16),
	_file_size int,
	_hash varchar(32),
	_image bit,
	_file varchar(256),
	_x int,
	_y int,
	_thumbnail varchar(256),
	_thumbx int,
	_thumby int
)
begin
	insert into uploads (board, hash, is_image, file_name, file_w, file_h, size, thumbnail_name, thumbnail_w, thumbnail_h)
	values
	(get_board_id(_board_name), _hash, _image, _file, _x, _y, _file_size, _thumbnail, _thumbx, _thumby);
	select last_insert_id();
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
	_board_id int,
	_hash varchar(32),
	_is_image bit,
	_file_name varchar(256),
	_file_w int,
	_file_h int,
	_size int,
	_thumbnail_name varchar(256),
	_thumbnail_w int,
	_thumbnail_h int
)
begin
	insert into uploads (board, `hash`, is_image, file_name, file_w, file_h,
		`size`, thumbnail_name, thumbnail_w, thumbnail_h)
	values
	(_board_id, _hash, _is_image, _file_name, _file_w, _file_h,
		_size, _thumbnail_name, _thumbnail_w, _thumbnail_h);
	select last_insert_id() as `id`;
end */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `sp_uploads_get_all` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'STRICT_TRANS_TABLES,NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50020 DEFINER=`root`@`localhost`*/ /*!50003 PROCEDURE `sp_uploads_get_all`(
	post_id int
)
begin
	select id, `hash`, is_image, file_name, file_w, file_h, `size`,
		thumbnail_name, thumbnail_w, thumbnail_h
	from uploads u
	join posts_uploads pu on pu.upload = u.id and pu.post = post_id;
end */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `sp_uploads_get_post` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'STRICT_TRANS_TABLES,NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50020 DEFINER=`root`@`localhost`*/ /*!50003 PROCEDURE `sp_uploads_get_post`(
	post_id int
)
begin
	select id, `hash`, is_image, file_name, file_w, file_h, `size`,
		thumbnail_name, thumbnail_w, thumbnail_h
	from uploads u
	join posts_uploads pu on pu.upload = u.id and pu.post = post_id;
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
	select u.id, u.`hash`, u.is_image, u.file_name, u.file_w, u.file_h,
		u.`size`, u.thumbnail_name, u.thumbnail_w, u.thumbnail_h,
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
	
	left join acl a7 on a7.`group` = ug.`group` and a7.board is null and a7.thread is null and a7.post is null
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
/*!50003 SET sql_mode              = 'STRICT_TRANS_TABLES,NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50020 DEFINER=`root`@`localhost`*/ /*!50003 PROCEDURE `sp_upload_handlers_add`(
	_name varchar(50)
)
begin
	insert into upload_handlers (`name`) values (_name);
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
/*!50003 SET sql_mode              = 'STRICT_TRANS_TABLES,NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION' */ ;
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
/*!50003 DROP PROCEDURE IF EXISTS `sp_upload_handlers_get` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'STRICT_TRANS_TABLES,NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50020 DEFINER=`root`@`localhost`*/ /*!50003 PROCEDURE `sp_upload_handlers_get`()
begin
	select id, `name` from upload_handlers;
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
/*!50003 SET sql_mode              = 'STRICT_TRANS_TABLES,NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50020 DEFINER=`root`@`localhost`*/ /*!50003 PROCEDURE `sp_upload_handlers_get_all`()
begin
	select id, `name` from upload_handlers;
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
/*!50003 SET sql_mode              = 'STRICT_TRANS_TABLES,NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION' */ ;
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
/*!50003 SET sql_mode              = 'STRICT_TRANS_TABLES,NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION' */ ;
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
/*!50003 SET sql_mode              = 'STRICT_TRANS_TABLES,NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION' */ ;
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
/*!50003 DROP PROCEDURE IF EXISTS `sp_upload_types_get` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'STRICT_TRANS_TABLES,NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50020 DEFINER=`root`@`localhost`*/ /*!50003 PROCEDURE `sp_upload_types_get`(
	board_id int
)
begin
	select ut.id, ut.extension, ut.store_extension, ut.upload_handler,
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
/*!50003 DROP PROCEDURE IF EXISTS `sp_upload_types_get_all` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'STRICT_TRANS_TABLES,NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION' */ ;
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
/*!50003 DROP PROCEDURE IF EXISTS `sp_upload_types_get_preview` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'STRICT_TRANS_TABLES,NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50020 DEFINER=`root`@`localhost`*/ /*!50003 PROCEDURE `sp_upload_types_get_preview`(
	board_id int
)
begin
	select ut.extension
	from upload_types ut
	join board_upload_types but on ut.id = but.upload_type and but.board = board_id;
end */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `sp_upload_types_get_view` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'STRICT_TRANS_TABLES,NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50020 DEFINER=`root`@`localhost`*/ /*!50003 PROCEDURE `sp_upload_types_get_view`(
	board_id int
)
begin
	select ut.extension
	from upload_types ut
	join board_upload_types but on ut.id = but.upload_type and but.board = board_id;
end */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `sp_upload_type_get` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'STRICT_TRANS_TABLES,NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50020 DEFINER=`root`@`localhost`*/ /*!50003 PROCEDURE `sp_upload_type_get`(
	_extension varchar(10)
)
begin
	select u.extension, u.store_extension, h.name, u.thumbnail_image from upload_types u
	join upload_handlers h on (u.upload_handler = h.id)
	where u.extension = _extension;
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
	_rempass varchar(12)
)
begin
	declare user_id int;
	set @user_id = null;
	select id into user_id from users where keyword = _keyword;
	if(_rempass = '')
	then
		set _rempass = null;
	end if;
	if(user_id is null)
	then
		
		insert into users (keyword, threads_per_page, posts_per_thread,
			lines_per_post, stylesheet, `language`, rempass)
		values (_keyword, _threads_per_page, _posts_per_thread,
			_lines_per_post, _stylesheet, _language, _rempass);
		select last_insert_id() into user_id;
		insert into user_groups (`user`, `group`) select user_id, id from groups
			where name = 'Users';
	else
		
		update users set threads_per_page = _threads_per_page,
			posts_per_thread = _posts_per_thread,
			lines_per_post = _lines_per_post,
			stylesheet = _stylesheet,
			`language` = _language,
			rempass = _rempass
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
/*!50003 SET sql_mode              = 'STRICT_TRANS_TABLES,NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION' */ ;
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
/*!50003 DROP PROCEDURE IF EXISTS `sp_users_get_settings` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'STRICT_TRANS_TABLES,NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50020 DEFINER=`root`@`localhost`*/ /*!50003 PROCEDURE `sp_users_get_settings`(
	_keyword varchar(32)
)
begin
	declare user_id int;
	select id into user_id from users where keyword = _keyword;

	select u.id, u.posts_per_thread, u.threads_per_page, u.lines_per_post,
		l.`name` as `language`, s.`name` as `stylesheet`, u.rempass
	from users u
	join stylesheets s on u.stylesheet = s.id
	join languages l on u.`language` = l.id
	where u.keyword = _keyword;

	select g.`name` from user_groups ug
	join users u on ug.`user` = u.id and u.id = user_id
	join groups g on ug.`group` = g.id;
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
/*!50003 SET sql_mode              = 'STRICT_TRANS_TABLES,NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50020 DEFINER=`root`@`localhost`*/ /*!50003 PROCEDURE `sp_user_groups_add`(
	user_id int,
	group_id int
)
begin
	insert into user_groups (`user`, `group`) values (user_id, group_id);
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
/*!50003 SET sql_mode              = 'STRICT_TRANS_TABLES,NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50020 DEFINER=`root`@`localhost`*/ /*!50003 PROCEDURE `sp_user_groups_delete`(
	user_id int,
	group_id int
)
begin
	delete from user_groups where `user` = user_id and `group` = group_id;
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
/*!50003 SET sql_mode              = 'STRICT_TRANS_TABLES,NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50020 DEFINER=`root`@`localhost`*/ /*!50003 PROCEDURE `sp_user_groups_edit`(
	user_id int,
	old_group_id int,
	new_group_id int
)
begin
	update user_groups set `group` = new_group_id
	where `user` = user_id and `group` = old_group_id;
end */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `sp_user_groups_get` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'STRICT_TRANS_TABLES,NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50020 DEFINER=`root`@`localhost`*/ /*!50003 PROCEDURE `sp_user_groups_get`()
begin
	select `user`, `group` from user_groups order by `user`, `group`;
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
/*!50003 SET sql_mode              = 'STRICT_TRANS_TABLES,NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50020 DEFINER=`root`@`localhost`*/ /*!50003 PROCEDURE `sp_user_groups_get_all`()
begin
	select `user`, `group` from user_groups order by `user`, `group`;
end */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `sp_user_settings_get` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'STRICT_TRANS_TABLES,NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50020 DEFINER=`root`@`localhost`*/ /*!50003 PROCEDURE `sp_user_settings_get`(
	_keyword varchar(32)
)
begin
	declare user_id int;
	select id into user_id from users where keyword = _keyword;

	select u.id, u.posts_per_thread, u.threads_per_page, u.lines_per_post,
		l.`name` as `language`, s.`name` as `stylesheet`, u.rempass
	from users u
	join stylesheets s on u.stylesheet = s.id
	join languages l on u.`language` = l.id
	where u.keyword = _keyword;

	select g.`name` from user_groups ug
	join users u on ug.`user` = u.id and u.id = user_id
	join groups g on ug.`group` = g.id;
end */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `test_inparam` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'STRICT_TRANS_TABLES,NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50020 DEFINER=`root`@`localhost`*/ /*!50003 PROCEDURE `test_inparam`(_id int)
begin
	-- set _id = null;
	select _id;
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

-- Dump completed on 2009-12-05 19:24:46
