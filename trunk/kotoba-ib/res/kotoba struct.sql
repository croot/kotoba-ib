-- MySQL dump 10.11
--
-- Host: localhost    Database: kotoba
-- ------------------------------------------------------
-- Server version	5.0.67-community-nt

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
-- Current Database: `kotoba`
--

CREATE DATABASE /*!32312 IF NOT EXISTS*/ `kotoba` /*!40100 DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci */;

USE `kotoba`;

--
-- Table structure for table `boards`
--

DROP TABLE IF EXISTS `boards`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `boards` (
  `id` int(11) NOT NULL auto_increment,
  `Name` varchar(16) collate utf8_unicode_ci NOT NULL,
  `MaxPostNum` int(11) default '0',
  `Board Settings` varchar(300) collate utf8_unicode_ci default NULL,
  PRIMARY KEY  (`id`),
  UNIQUE KEY `Name Index` (`Name`)
) ENGINE=InnoDB AUTO_INCREMENT=24 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='Boards table.';
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `posts`
--

DROP TABLE IF EXISTS `posts`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `posts` (
  `id` int(11) NOT NULL,
  `thread` int(11) NOT NULL,
  `board` int(11) NOT NULL,
  `Time` datetime default NULL,
  `Text` text collate utf8_unicode_ci,
  `Post Settings` varchar(1000) collate utf8_unicode_ci default NULL,
  PRIMARY KEY  (`id`,`thread`,`board`),
  KEY `thread` (`thread`,`board`),
  CONSTRAINT `posts_ibfk_1` FOREIGN KEY (`thread`, `board`) REFERENCES `threads` (`id`, `board`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='Posts table.';
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `threads`
--

DROP TABLE IF EXISTS `threads`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `threads` (
  `id` int(11) NOT NULL,
  `board` int(11) NOT NULL,
  `Thread Settings` varchar(300) collate utf8_unicode_ci default NULL,
  PRIMARY KEY  (`id`,`board`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='Threads table.';
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `users` (
  `id` int(11) NOT NULL auto_increment,
  `Key` varchar(32) collate utf8_unicode_ci NOT NULL,
  `SID` varchar(32) collate utf8_unicode_ci default NULL,
  `User Settings` varchar(300) collate utf8_unicode_ci default NULL,
  PRIMARY KEY  (`id`),
  UNIQUE KEY `Key Index` (`Key`),
  UNIQUE KEY `SID Index` (`SID`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='Users table. Key - Keyword hash.';
SET character_set_client = @saved_cs_client;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2009-04-08 13:25:25
