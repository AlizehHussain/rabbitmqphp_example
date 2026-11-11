-- MySQL dump 10.13  Distrib 8.4.11, for Linux (x86_64)
--
-- Host: localhost    Database: webdb
-- ------------------------------------------------------
-- Server version	8.4.11-0ubuntu0.26.04.1

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Current Database: `webdb`
--

CREATE DATABASE /*!32312 IF NOT EXISTS*/ `webdb` /*!40100 DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci */ /*!80016 DEFAULT ENCRYPTION='N' */;

USE `webdb`;

--
-- Table structure for table `Events`
--

DROP TABLE IF EXISTS `Events`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `Events` (
  `EventID` varchar(100) NOT NULL,
  `EventName` varchar(255) NOT NULL,
  `EventDate` datetime DEFAULT NULL,
  `Venue` varchar(255) DEFAULT NULL,
  `City` varchar(100) DEFAULT NULL,
  `TicketURL` text,
  `UpdatedAt` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `Category` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`EventID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `Events`
--

LOCK TABLES `Events` WRITE;
/*!40000 ALTER TABLE `Events` DISABLE KEYS */;
INSERT INTO `Events` VALUES ('1A9Zk37GkdXg-zV','Eagles Live at Sphere','2026-11-14 00:00:00','Sphere','Las Vegas','https://www.ticketmaster.com/eagles-live-at-sphere-las-vegas-nevada-11-14-2026/event/17006484FED61E34','2026-10-28 15:18:07','Music'),('1A9Zk37GkdXUsyv','Eagles Live at Sphere','2026-11-27 00:00:00','Sphere','Las Vegas','https://www.ticketmaster.com/eagles-live-at-sphere-las-vegas-nevada-11-27-2026/event/17006484FEDB1E41','2026-10-28 15:18:07','Music'),('1A9Zk37GkdXVozi','Eagles Live at Sphere','2026-11-13 00:00:00','Sphere','Las Vegas','https://www.ticketmaster.com/eagles-live-at-sphere-las-vegas-nevada-11-13-2026/event/17006484FED11E2C','2026-10-28 15:18:07','Music'),('1A9Zk37GkdXzvy5','Eagles Live at Sphere','2026-11-28 00:00:00','Sphere','Las Vegas','https://www.ticketmaster.com/eagles-live-at-sphere-las-vegas-nevada-11-28-2026/event/17006484FEE01E4B','2026-10-28 15:18:07','Music'),('1kAYvP7_GA25ajZ','Oasis Live \'27','2027-08-20 00:00:00','Allegiant Stadium','Las Vegas','https://www.ticketmaster.com/oasis-live-27-las-vegas-nevada-08-20-2027/event/170065119F2C9700','2026-10-28 06:29:40','Music'),('1kAYvP7_GA2Gaje','Oasis Live \'27','2027-08-21 00:00:00','Allegiant Stadium','Las Vegas','https://www.ticketmaster.com/oasis-live-27-las-vegas-nevada-08-21-2027/event/170065119F309702','2026-10-28 06:29:40','Music'),('1kAYvP7_GA2u_jk','Oasis Live \'27','2027-08-24 00:00:00','Allegiant Stadium','Las Vegas','https://www.ticketmaster.com/oasis-live-27-las-vegas-nevada-08-24-2027/event/170065119F359706','2026-10-28 06:29:40','Music'),('G5diZ_o6t6bbc','New York Knicks vs. Brooklyn Nets','2026-11-02 00:00:00','Madison Square Garden','New York','https://www.ticketmaster.com/new-york-knicks-vs-brooklyn-nets-new-york-new-york-11-02-2026/event/3B006511E91D862D','2026-10-28 06:29:41','Sports'),('G5diZ_o6t6IbR','New York Knicks vs. Chicago Bulls','2026-11-04 00:00:00','Madison Square Garden','New York','https://www.ticketmaster.com/new-york-knicks-vs-chicago-bulls-new-york-new-york-11-04-2026/event/3B006511E91E862F','2026-10-28 06:29:41','Sports'),('G5diZ_o6tab_6','New York Knicks vs. San Antonio Spurs','2026-12-25 00:00:00','Madison Square Garden','New York','https://www.ticketmaster.com/new-york-knicks-vs-san-antonio-new-york-new-york-12-25-2026/event/3B006511E9258647','2026-10-28 06:29:41','Sports'),('G5diZ_o6tCFQG','New York Knicks vs. Boston Celtics','2027-04-04 00:00:00','Madison Square Garden','New York','https://www.ticketmaster.com/new-york-knicks-vs-boston-celtics-new-york-new-york-04-04-2027/event/3B006511E938868C','2026-10-28 06:29:41','Sports'),('G5diZ_o6tGz_y','New York Knicks vs. Boston Celtics','2027-03-14 00:00:00','Madison Square Garden','New York','https://www.ticketmaster.com/new-york-knicks-vs-boston-celtics-new-york-new-york-03-14-2027/event/3B006511E9338679','2026-10-28 06:29:41','Sports'),('vv1AvZkfgGke08hPi','Oasis Live \'27','2027-08-14 00:00:00','Gillette Stadium','Foxborough','https://www.ticketmaster.com/oasis-live-27-foxborough-massachusetts-08-14-2027/event/01006535A441B96C','2026-10-28 06:29:40','Music'),('vv1AvZkfgGke0GhPZ','Oasis Live \'27','2027-08-13 00:00:00','Gillette Stadium','Foxborough','https://www.ticketmaster.com/oasis-live-27-foxborough-massachusetts-08-13-2027/event/01006535A431B940','2026-10-28 15:18:07','Music'),('vv1AvZkftGkeHnJ-E','Oasis Live \'27','2027-08-10 00:00:00','Gillette Stadium','Foxborough','https://www.ticketmaster.com/oasis-live-27-foxborough-massachusetts-08-10-2027/event/01006529BCAA6867','2026-10-28 06:29:40','Music');
/*!40000 ALTER TABLE `Events` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `FriendGroups`
--

DROP TABLE IF EXISTS `FriendGroups`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `FriendGroups` (
  `GroupID` int NOT NULL AUTO_INCREMENT,
  `GroupName` varchar(100) NOT NULL,
  `CreatedID` int NOT NULL,
  `CreatedTime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`GroupID`),
  KEY `CreatedID` (`CreatedID`),
  CONSTRAINT `FriendGroups_ibfk_1` FOREIGN KEY (`CreatedID`) REFERENCES `users` (`uid`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `FriendGroups`
--

LOCK TABLES `FriendGroups` WRITE;
/*!40000 ALTER TABLE `FriendGroups` DISABLE KEYS */;
INSERT INTO `FriendGroups` VALUES (1,'trio',42,'2026-10-30 23:10:09'),(2,'trio',42,'2026-10-30 23:29:56'),(3,'testgroup',42,'2026-10-30 23:33:03'),(4,'test',39,'2026-10-31 03:46:54'),(5,'group1',39,'2026-10-31 05:38:09'),(6,'testGroup',39,'2026-10-31 23:40:37'),(7,'evetplanner',45,'2026-11-03 15:24:11');
/*!40000 ALTER TABLE `FriendGroups` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `Friends`
--

DROP TABLE IF EXISTS `Friends`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `Friends` (
  `FriendID` int NOT NULL AUTO_INCREMENT,
  `SenderID` int NOT NULL,
  `ReceiverID` int NOT NULL,
  `Status` enum('pending','accepted','declined') DEFAULT 'pending',
  `CreatedAt` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`FriendID`),
  UNIQUE KEY `SenderID` (`SenderID`,`ReceiverID`),
  KEY `ReceiverID` (`ReceiverID`),
  CONSTRAINT `Friends_ibfk_1` FOREIGN KEY (`SenderID`) REFERENCES `users` (`uid`),
  CONSTRAINT `Friends_ibfk_2` FOREIGN KEY (`ReceiverID`) REFERENCES `users` (`uid`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `Friends`
--

LOCK TABLES `Friends` WRITE;
/*!40000 ALTER TABLE `Friends` DISABLE KEYS */;
INSERT INTO `Friends` VALUES (1,22,39,'accepted','2026-10-31 05:26:53'),(2,42,43,'accepted','2026-10-31 06:37:51'),(3,42,44,'accepted','2026-10-31 06:38:30'),(4,39,30,'accepted','2026-10-31 23:38:28'),(5,45,22,'accepted','2026-11-01 19:09:10'),(6,43,45,'accepted','2026-11-03 15:25:19'),(7,45,39,'pending','2026-11-03 15:55:31');
/*!40000 ALTER TABLE `Friends` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `GroupInvites`
--

DROP TABLE IF EXISTS `GroupInvites`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `GroupInvites` (
  `InviteID` int NOT NULL AUTO_INCREMENT,
  `GroupID` int NOT NULL,
  `InviteToken` varchar(255) NOT NULL,
  `CreatedBy` int NOT NULL,
  `ExpiresAt` timestamp NOT NULL,
  `CreatedTime` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`InviteID`),
  UNIQUE KEY `InviteToken` (`InviteToken`),
  KEY `GroupID` (`GroupID`),
  KEY `CreatedBy` (`CreatedBy`),
  CONSTRAINT `GroupInvites_ibfk_1` FOREIGN KEY (`GroupID`) REFERENCES `FriendGroups` (`GroupID`) ON DELETE CASCADE,
  CONSTRAINT `GroupInvites_ibfk_2` FOREIGN KEY (`CreatedBy`) REFERENCES `users` (`uid`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `GroupInvites`
--

LOCK TABLES `GroupInvites` WRITE;
/*!40000 ALTER TABLE `GroupInvites` DISABLE KEYS */;
INSERT INTO `GroupInvites` VALUES (1,1,'65f068c1eb725eab48c10ae6b8bfcef5d1fe6cfeefebafbc605887b341b8901f',42,'2026-11-07 00:10:18','2026-10-30 23:10:18'),(2,2,'7cc7cf06ff5130fdc84af69399f27f955a68e9c37c3d00e5182206bb64583415',42,'2026-11-07 00:30:19','2026-10-30 23:30:19'),(3,1,'d199001f23e4046692ee0e809a29507e2fe157880c867e9d72f19d723d929785',42,'2026-11-07 00:30:21','2026-10-30 23:30:21'),(4,3,'29988dce48124870e0730de82a63ba146a9e91ce6e4bf0c19017aebda462e11a',42,'2026-11-07 00:33:11','2026-10-30 23:33:11'),(5,4,'13e7036f4b80f9884bf050148ed1226e806a0e08687dcfc879496b5543bdff03',39,'2026-11-07 04:46:58','2026-10-31 03:46:58'),(6,5,'8743e8e805fe29a9f41aef1804f00f73b1b66ac38ed37cafda417fce7796d4fb',39,'2026-11-07 06:38:11','2026-10-31 05:38:11'),(7,6,'79390a643df70c0ca867ce48eb530b464b299e5236f4a475ab559f1023c9406e',39,'2026-11-08 00:40:41','2026-10-31 23:40:41'),(8,7,'bc29de7162351412fac81aead3b95d97cfbecf521f4f7953e652ce3a687f2a92',45,'2026-11-10 15:24:14','2026-11-03 15:24:14');
/*!40000 ALTER TABLE `GroupInvites` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `GroupMembers`
--

DROP TABLE IF EXISTS `GroupMembers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `GroupMembers` (
  `GroupID` int NOT NULL,
  `MemberID` int NOT NULL,
  PRIMARY KEY (`GroupID`,`MemberID`),
  KEY `MemeberID` (`MemberID`),
  CONSTRAINT `GroupMembers_ibfk_1` FOREIGN KEY (`GroupID`) REFERENCES `FriendGroups` (`GroupID`) ON DELETE CASCADE,
  CONSTRAINT `GroupMembers_ibfk_2` FOREIGN KEY (`MemberID`) REFERENCES `users` (`uid`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `GroupMembers`
--

LOCK TABLES `GroupMembers` WRITE;
/*!40000 ALTER TABLE `GroupMembers` DISABLE KEYS */;
INSERT INTO `GroupMembers` VALUES (4,22),(5,22),(6,22),(4,39),(5,39),(6,39),(1,42),(2,42),(3,42),(1,43),(3,43),(7,43),(3,44),(7,45);
/*!40000 ALTER TABLE `GroupMembers` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `HangoutMembers`
--

DROP TABLE IF EXISTS `HangoutMembers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `HangoutMembers` (
  `HangoutID` int NOT NULL,
  `MemberID` int NOT NULL,
  `Response` enum('invited','accepted','declined','maybe') DEFAULT 'invited',
  PRIMARY KEY (`HangoutID`,`MemberID`),
  KEY `fk_hangout_members_member` (`MemberID`),
  CONSTRAINT `fk_hangout_members_hangout` FOREIGN KEY (`HangoutID`) REFERENCES `Hangouts` (`HangoutID`) ON DELETE CASCADE,
  CONSTRAINT `fk_hangout_members_member` FOREIGN KEY (`MemberID`) REFERENCES `users` (`uid`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `HangoutMembers`
--

LOCK TABLES `HangoutMembers` WRITE;
/*!40000 ALTER TABLE `HangoutMembers` DISABLE KEYS */;
INSERT INTO `HangoutMembers` VALUES (40,22,'invited'),(40,39,'accepted'),(41,22,'invited'),(41,39,'accepted'),(42,42,'accepted'),(42,43,'accepted'),(42,44,'accepted'),(43,42,'declined'),(43,43,'accepted'),(43,44,'maybe'),(44,30,'accepted'),(44,39,'accepted'),(45,39,'invited'),(45,43,'invited'),(45,45,'accepted'),(46,22,'invited'),(46,43,'invited'),(46,45,'accepted'),(47,39,'invited'),(47,43,'accepted'),(47,45,'invited'),(48,39,'invited'),(48,43,'accepted'),(48,45,'accepted');
/*!40000 ALTER TABLE `HangoutMembers` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `HangoutSuggestions`
--

DROP TABLE IF EXISTS `HangoutSuggestions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `HangoutSuggestions` (
  `HangoutSuggestionID` int NOT NULL AUTO_INCREMENT,
  `HangoutID` int NOT NULL,
  `EventID` varchar(100) NOT NULL,
  `SuggestedBy` int NOT NULL,
  `CreatedTime` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`HangoutSuggestionID`),
  UNIQUE KEY `unique_suggestion` (`HangoutID`,`EventID`),
  CONSTRAINT `HangoutSuggestions_ibfk_1` FOREIGN KEY (`HangoutID`) REFERENCES `Hangouts` (`HangoutID`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=29 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `HangoutSuggestions`
--

LOCK TABLES `HangoutSuggestions` WRITE;
/*!40000 ALTER TABLE `HangoutSuggestions` DISABLE KEYS */;
INSERT INTO `HangoutSuggestions` VALUES (16,40,'G5diZ_o6t6IbR',39,'2026-10-31 06:17:27'),(17,41,'1A9Zk37GkdXUsyv',39,'2026-10-31 06:19:11'),(18,42,'1A9Zk37GkdXVozi',43,'2026-10-31 06:31:39'),(19,42,'1A9Zk37GkdXg-zV',43,'2026-10-31 06:31:54'),(20,42,'1A9Zk37GkdXzvy5',44,'2026-10-31 06:36:49'),(21,42,'vv1AvZkftGkeHnJ-E',44,'2026-10-31 07:44:53'),(22,43,'1kAYvP7_GA2Gaje',44,'2026-10-31 07:46:13'),(23,44,'1A9Zk37GkdXVozi',39,'2026-10-31 23:42:37'),(24,44,'1kAYvP7_GA25ajZ',30,'2026-10-31 23:43:26'),(25,45,'1A9Zk37GkdXVozi',45,'2026-11-02 02:18:14'),(26,46,'G5diZ_o6t6IbR',45,'2026-11-03 15:23:37'),(27,48,'1A9Zk37GkdXUsyv',43,'2026-11-03 16:09:53'),(28,48,'vv1AvZkftGkeHnJ-E',45,'2026-11-03 16:10:27');
/*!40000 ALTER TABLE `HangoutSuggestions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `HangoutVotes`
--

DROP TABLE IF EXISTS `HangoutVotes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `HangoutVotes` (
  `HangoutSuggestionID` int NOT NULL,
  `UserID` int NOT NULL,
  `VotedAt` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`HangoutSuggestionID`,`UserID`),
  KEY `UserID` (`UserID`),
  CONSTRAINT `HangoutVotes_ibfk_1` FOREIGN KEY (`HangoutSuggestionID`) REFERENCES `HangoutSuggestions` (`HangoutSuggestionID`) ON DELETE CASCADE,
  CONSTRAINT `HangoutVotes_ibfk_2` FOREIGN KEY (`UserID`) REFERENCES `users` (`uid`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `HangoutVotes`
--

LOCK TABLES `HangoutVotes` WRITE;
/*!40000 ALTER TABLE `HangoutVotes` DISABLE KEYS */;
INSERT INTO `HangoutVotes` VALUES (16,39,'2026-10-31 06:17:30'),(17,39,'2026-10-31 06:19:12'),(18,42,'2026-10-31 06:34:39'),(18,43,'2026-10-31 06:35:09'),(18,44,'2026-10-31 07:45:35'),(19,44,'2026-10-31 06:36:29'),(20,44,'2026-10-31 06:36:51'),(21,44,'2026-10-31 07:45:32'),(23,30,'2026-10-31 23:43:31'),(23,39,'2026-10-31 23:42:46'),(24,30,'2026-10-31 23:43:34'),(26,45,'2026-11-03 15:23:55'),(27,43,'2026-11-03 16:09:58'),(27,45,'2026-11-03 16:10:21');
/*!40000 ALTER TABLE `HangoutVotes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `Hangouts`
--

DROP TABLE IF EXISTS `Hangouts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `Hangouts` (
  `HangoutID` int NOT NULL AUTO_INCREMENT,
  `EventID` varchar(100) NOT NULL,
  `CreatedID` int NOT NULL,
  `HangoutName` varchar(255) DEFAULT NULL,
  `CreatedTime` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`HangoutID`),
  KEY `CreatedID` (`CreatedID`),
  KEY `fk_hangouts_event` (`EventID`),
  CONSTRAINT `fk_hangouts_event` FOREIGN KEY (`EventID`) REFERENCES `Events` (`EventID`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=49 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `Hangouts`
--

LOCK TABLES `Hangouts` WRITE;
/*!40000 ALTER TABLE `Hangouts` DISABLE KEYS */;
INSERT INTO `Hangouts` VALUES (40,'G5diZ_o6t6bbc',39,NULL,'2026-10-31 06:17:20'),(41,'G5diZ_o6t6bbc',39,NULL,'2026-10-31 06:19:05'),(42,'1A9Zk37GkdXVozi',42,NULL,'2026-10-31 06:29:50'),(43,'1A9Zk37GkdXVozi',43,NULL,'2026-10-31 06:32:25'),(44,'vv1AvZkftGkeHnJ-E',39,NULL,'2026-10-31 23:42:10'),(45,'1A9Zk37GkdXg-zV',45,NULL,'2026-11-01 19:00:38'),(46,'G5diZ_o6t6IbR',45,NULL,'2026-11-03 15:23:30'),(47,'G5diZ_o6t6IbR',43,NULL,'2026-11-03 16:08:31'),(48,'1A9Zk37GkdXzvy5',43,NULL,'2026-11-03 16:09:39');
/*!40000 ALTER TABLE `Hangouts` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `Ratings`
--

DROP TABLE IF EXISTS `Ratings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `Ratings` (
  `RatingID` int NOT NULL AUTO_INCREMENT,
  `EventID` varchar(64) NOT NULL,
  `UserID` int NOT NULL,
  `Rating` tinyint unsigned NOT NULL,
  PRIMARY KEY (`RatingID`),
  UNIQUE KEY `one_rating_per_user` (`EventID`,`UserID`),
  KEY `fk_ratings_user` (`UserID`),
  CONSTRAINT `fk_ratings_event` FOREIGN KEY (`EventID`) REFERENCES `Events` (`EventID`) ON DELETE CASCADE,
  CONSTRAINT `fk_ratings_user` FOREIGN KEY (`UserID`) REFERENCES `users` (`uid`) ON DELETE CASCADE,
  CONSTRAINT `chk_rating` CHECK ((`Rating` between 1 and 5))
) ENGINE=InnoDB AUTO_INCREMENT=36 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `Ratings`
--

LOCK TABLES `Ratings` WRITE;
/*!40000 ALTER TABLE `Ratings` DISABLE KEYS */;
INSERT INTO `Ratings` VALUES (1,'G5diZ_o6t6bbc',22,2),(2,'G5diZ_o6t6bbc',39,2),(4,'G5diZ_o6t6bbc',43,5),(8,'G5diZ_o6t6IbR',39,1),(11,'G5diZ_o6t6IbR',22,3),(22,'G5diZ_o6tab_6',39,3),(27,'vv1AvZkfgGke0GhPZ',39,4),(28,'1A9Zk37GkdXUsyv',45,4),(29,'G5diZ_o6t6bbc',45,5),(30,'G5diZ_o6t6IbR',45,5),(33,'G5diZ_o6tab_6',45,4),(35,'1A9Zk37GkdXVozi',45,4);
/*!40000 ALTER TABLE `Ratings` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `Reviews`
--

DROP TABLE IF EXISTS `Reviews`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `Reviews` (
  `ReviewID` int NOT NULL AUTO_INCREMENT,
  `uid` int NOT NULL,
  `EventID` varchar(100) NOT NULL,
  `Comment` text NOT NULL,
  `CreatedAt` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`ReviewID`),
  KEY `uid` (`uid`),
  KEY `EventID` (`EventID`),
  CONSTRAINT `Reviews_ibfk_1` FOREIGN KEY (`uid`) REFERENCES `users` (`uid`) ON DELETE CASCADE,
  CONSTRAINT `Reviews_ibfk_2` FOREIGN KEY (`EventID`) REFERENCES `Events` (`EventID`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `Reviews`
--

LOCK TABLES `Reviews` WRITE;
/*!40000 ALTER TABLE `Reviews` DISABLE KEYS */;
INSERT INTO `Reviews` VALUES (1,45,'G5diZ_o6t6IbR','test','2026-11-02 01:50:41'),(2,45,'G5diZ_o6t6bbc','a test','2026-11-02 02:10:26'),(3,45,'G5diZ_o6t6bbc','testing reviews','2026-11-02 02:27:33'),(4,45,'G5diZ_o6tab_6','test','2026-11-03 15:29:30'),(5,45,'G5diZ_o6tab_6','testing','2026-11-03 15:29:57'),(6,45,'1A9Zk37GkdXVozi','test','2026-11-03 16:14:31');
/*!40000 ALTER TABLE `Reviews` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `UserPicks`
--

DROP TABLE IF EXISTS `UserPicks`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `UserPicks` (
  `UserID` int NOT NULL,
  `EventID` varchar(100) NOT NULL,
  `PickedAt` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `reminder_sent` tinyint NOT NULL DEFAULT '0',
  PRIMARY KEY (`UserID`,`EventID`),
  KEY `EventID` (`EventID`),
  CONSTRAINT `UserPicks_ibfk_1` FOREIGN KEY (`UserID`) REFERENCES `users` (`uid`),
  CONSTRAINT `UserPicks_ibfk_2` FOREIGN KEY (`EventID`) REFERENCES `Events` (`EventID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `UserPicks`
--

LOCK TABLES `UserPicks` WRITE;
/*!40000 ALTER TABLE `UserPicks` DISABLE KEYS */;
INSERT INTO `UserPicks` VALUES (22,'1A9Zk37GkdXg-zV','2026-10-28 19:48:39',0),(22,'1A9Zk37GkdXVozi','2026-10-28 19:48:37',0),(22,'G5diZ_o6t6bbc','2026-10-28 18:33:35',0),(22,'G5diZ_o6t6IbR','2026-10-28 18:33:28',0),(22,'vv1AvZkftGkeHnJ-E','2026-10-28 19:48:42',0),(39,'1A9Zk37GkdXUsyv','2026-10-31 23:24:57',0),(39,'1A9Zk37GkdXVozi','2026-10-31 03:44:27',0),(39,'1A9Zk37GkdXzvy5','2026-10-31 23:20:45',0),(39,'1kAYvP7_GA25ajZ','2026-10-31 23:16:16',0),(39,'1kAYvP7_GA2u_jk','2026-10-31 10:16:28',0),(39,'G5diZ_o6t6bbc','2026-10-31 05:32:02',0),(39,'G5diZ_o6t6IbR','2026-10-31 05:27:22',0),(39,'G5diZ_o6tab_6','2026-10-31 23:25:07',0),(39,'vv1AvZkftGkeHnJ-E','2026-10-31 23:16:23',0),(42,'G5diZ_o6t6bbc','2026-10-31 07:46:35',0),(43,'G5diZ_o6t6bbc','2026-10-31 03:23:44',0),(43,'G5diZ_o6tab_6','2026-11-03 15:27:00',0),(45,'1A9Zk37GkdXg-zV','2026-10-31 23:58:06',0),(45,'1A9Zk37GkdXUsyv','2026-11-01 00:14:56',0),(45,'1A9Zk37GkdXVozi','2026-11-01 19:07:24',0),(45,'1kAYvP7_GA2Gaje','2026-11-03 15:28:36',0),(45,'1kAYvP7_GA2u_jk','2026-11-02 02:35:05',0),(45,'G5diZ_o6t6bbc','2026-11-02 02:27:35',0),(45,'G5diZ_o6t6IbR','2026-11-03 15:54:28',0),(45,'G5diZ_o6tab_6','2026-11-02 02:34:47',0),(45,'G5diZ_o6tCFQG','2026-11-02 02:28:25',0),(45,'G5diZ_o6tGz_y','2026-10-31 23:47:58',0);
/*!40000 ALTER TABLE `UserPicks` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `VenueRatings`
--

DROP TABLE IF EXISTS `VenueRatings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `VenueRatings` (
  `RatingID` int NOT NULL AUTO_INCREMENT,
  `Venue` varchar(255) NOT NULL,
  `City` varchar(100) NOT NULL DEFAULT '',
  `UserID` int NOT NULL,
  `Rating` tinyint unsigned NOT NULL,
  PRIMARY KEY (`RatingID`),
  UNIQUE KEY `one_rating_per_user` (`Venue`,`City`,`UserID`),
  KEY `UserID` (`UserID`),
  CONSTRAINT `VenueRatings_ibfk_1` FOREIGN KEY (`UserID`) REFERENCES `users` (`uid`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `VenueRatings`
--

LOCK TABLES `VenueRatings` WRITE;
/*!40000 ALTER TABLE `VenueRatings` DISABLE KEYS */;
INSERT INTO `VenueRatings` VALUES (1,'Madison Square Garden','New York',45,5);
/*!40000 ALTER TABLE `VenueRatings` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `VenueReviews`
--

DROP TABLE IF EXISTS `VenueReviews`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `VenueReviews` (
  `ReviewID` int NOT NULL AUTO_INCREMENT,
  `uid` int NOT NULL,
  `Venue` varchar(255) NOT NULL,
  `City` varchar(100) NOT NULL DEFAULT '',
  `Comment` text NOT NULL,
  `CreatedAt` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`ReviewID`),
  KEY `uid` (`uid`),
  KEY `idx_venue` (`Venue`,`City`),
  CONSTRAINT `VenueReviews_ibfk_1` FOREIGN KEY (`uid`) REFERENCES `users` (`uid`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `VenueReviews`
--

LOCK TABLES `VenueReviews` WRITE;
/*!40000 ALTER TABLE `VenueReviews` DISABLE KEYS */;
INSERT INTO `VenueReviews` VALUES (1,45,'Madison Square Garden','New York','testeing','2026-11-02 01:51:42'),(2,45,'Madison Square Garden','New York','test','2026-11-03 15:29:44');
/*!40000 ALTER TABLE `VenueReviews` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sessions`
--

DROP TABLE IF EXISTS `sessions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `sessions` (
  `sid` int NOT NULL AUTO_INCREMENT,
  `uid` int NOT NULL,
  `session_token` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `expires_time` timestamp NOT NULL,
  PRIMARY KEY (`sid`),
  UNIQUE KEY `session_token` (`session_token`),
  KEY `uid` (`uid`),
  CONSTRAINT `sessions_ibfk_1` FOREIGN KEY (`uid`) REFERENCES `users` (`uid`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=287 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sessions`
--

LOCK TABLES `sessions` WRITE;
/*!40000 ALTER TABLE `sessions` DISABLE KEYS */;
INSERT INTO `sessions` VALUES (1,40,'e401477fd259cffa3b019eb9d10135b0c6732ceeb777ce8a3d8f632e5fcf1fd3','2026-10-28 16:36:31','2026-10-28 17:06:31'),(2,40,'2b689ba87349d76d2e67fee2e49c44affb9caee29b9600d4467aff1db0c9ea42','2026-10-28 16:38:04','2026-10-28 17:08:04'),(3,40,'f5c08c2a672fff1382d0d8dd10fe33aa61c10f20d06ab4cff618606c6277cee7','2026-10-28 16:40:22','2026-10-28 17:10:22'),(4,40,'2b94c0f3ce2858327b3d307b25577d4478350409004b4b2a69a0b47e8aec57ab','2026-10-28 16:40:27','2026-10-28 17:10:27'),(5,40,'163336e37d339764975165420bce9beaf39f8dcb5e91aec95c3c5c8efe2f96a8','2026-10-28 16:42:00','2026-10-28 17:12:00'),(6,40,'94be499a165b9878fc244f5ed3d865f1b2ebede13045756e18844d4280936836','2026-10-28 16:42:29','2026-10-28 17:12:29'),(7,40,'5f4ba8333669d6329dd33a23c6d5a8916ae97c91c465e0c110ad10a9bd43b3a6','2026-10-28 17:03:44','2026-10-28 17:33:44'),(8,40,'1831604d66a121e86c37e1abfc030e11d744a5fd59a21c1d0f13c0d520635af0','2026-10-28 17:05:03','2026-10-28 17:35:03'),(9,40,'b39ba9788278abb4bdc0a673b92e15c703622c3c0fa17907a3fccfe3e1333298','2026-10-28 17:05:14','2026-10-28 17:35:14'),(10,40,'28120553973c6a9d217f5999566a738ad553f47403c6229d3bf5c110c9ab303d','2026-10-28 17:05:15','2026-10-28 17:35:15'),(11,40,'cc0d098f746f3c476f2faaa8a50f8014a4e3e4ac0d28fdea007652287bad67c6','2026-10-28 17:05:38','2026-10-28 17:35:38'),(12,40,'6ee35548ee50bf14e4e3499a40547022ecd05d0e4c53f1cf9141fc69065ed4c1','2026-10-28 17:05:39','2026-10-28 17:35:39'),(13,40,'6c96cd27e460bb3f27a9cb2f2d44950d69841c32df5778d4fa83ce0ed0d8263a','2026-10-28 17:06:33','2026-10-28 17:36:33'),(14,40,'fb3ac851891ca0b4166e6fcd6494e1cdfa3dab3f6c6d2d7cb408aafb94ded60e','2026-10-28 17:07:26','2026-10-28 17:37:26'),(15,40,'d040b6c2eae8b092e5881fd018350f8310418bca6dd222ca6f7e5cbaabb4a2d4','2026-10-28 17:18:54','2026-10-28 17:48:54'),(16,40,'806fa0c7c8e4b9a37e52fd34320cd9ed32b2e219357b776d795a80501f0e0505','2026-10-28 17:18:55','2026-10-28 17:48:55'),(17,40,'518d18ad4cde36858f3a613849083a954a76b75768d795b35dc2665fe68b85aa','2026-10-28 17:18:55','2026-10-28 17:48:55'),(18,40,'e6a0c64b1874c6b074939622dd67c0ccfa2390282b203990412319dbbdb52965','2026-10-28 17:19:45','2026-10-28 17:49:45'),(19,42,'702ac9637396bad1a231b3e2e0ed7492ea8a4875b784ab64772f58608a253886','2026-10-28 17:31:37','2026-10-28 18:01:37'),(20,42,'27615363ac79ae6dd0a2f28477cc91a6be5d2b6137463e08214dfafa70304a8b','2026-10-28 17:36:55','2026-10-28 18:06:55'),(21,43,'e296c047224e24ff67841f9841e756144bf279df3358f53a43b5c91dc23b69ef','2026-10-28 17:37:39','2026-10-28 18:07:39'),(22,22,'18e9a01f6750a649a48223a36453f647527b8aed3ee3ceab3e4820ef9bfb6875','2026-10-28 18:31:49','2026-10-28 19:01:49'),(23,22,'7ca9550a8f6aed1783531033c5fdc3cf5794738f604df96365a277dbcc0d2215','2026-10-28 18:31:49','2026-10-28 19:01:49'),(24,22,'96396e8c0b4e522ef5df106a13e21c651b2d722b1836b9799929274a6dda74b2','2026-10-28 18:31:50','2026-10-28 19:01:50'),(25,22,'e849169ad3581f50dfc9a20d91c1287e95441c0593ac7f8c693b51ea8dd210e4','2026-10-28 18:31:50','2026-10-28 19:01:50'),(26,22,'1b7054205a440523b868b75cc85680123a3a0b17671d2253ab357f9162f37963','2026-10-28 18:31:51','2026-10-28 19:01:51'),(27,22,'d9d2010779086fb9aea639854277678752aa9ad1fbe6535555a042e3102b29a8','2026-10-28 18:31:51','2026-10-28 19:01:51'),(28,22,'e88bec2b3c7a43e20e89145110a577a41f06cb4edc3918ce0f5bf433e995df0d','2026-10-28 18:31:52','2026-10-28 19:01:52'),(29,22,'5ee589fad39158bd6418fde0a23ea8a4d497944fa043b71574056fff0fe45464','2026-10-28 18:31:52','2026-10-28 19:01:52'),(30,22,'6404c3685c86ad13876e3ca786183e1331e9387d22c7b53f4af095584a4baabc','2026-10-28 18:31:52','2026-10-28 19:01:52'),(31,22,'7bba5737ffbd82be61e3eee3701ba3c23691653557ce162d11869e91d0048e8d','2026-10-28 18:31:53','2026-10-28 19:01:53'),(32,22,'3091bc48ed33bccadcae345e3b5f1a8f3c965eca52ffb8204ca9c8f9aabb81cc','2026-10-28 18:31:53','2026-10-28 19:01:53'),(33,22,'c30a428522f0a5442c8d92ca4cb8dc7698a1da04185063c69f5454d83f013447','2026-10-28 18:31:54','2026-10-28 19:01:54'),(34,22,'63831679eb303d4cc374a3d2699062e94ecf82656242c6a2c1131580fd94b5cd','2026-10-28 18:31:54','2026-10-28 19:01:54'),(35,22,'1ca069191d07af3ed0a1d24bfeda6d68ba20e1cf9e59ab0158b00053af16e06b','2026-10-28 18:31:55','2026-10-28 19:01:55'),(36,22,'766ca03945797a95843f0b46795b66cf0044ab8e2c6932e93b5353ec11ace55c','2026-10-28 18:31:56','2026-10-28 19:01:56'),(37,22,'fe21977295147fa8f11e0cb888fadc1caeee52e13668ab3cdad22892d70a0558','2026-10-28 18:31:56','2026-10-28 19:01:56'),(38,22,'8b8301a08a03e4a64315b36c03d7c8078de6adfcc7c4ce913da1f9114680a36f','2026-10-28 18:31:56','2026-10-28 19:01:56'),(39,22,'bb88de2b62bc6f1b25c00ff78314afe7248d974857576c0e1cbfd0a4a3c84314','2026-10-28 18:31:57','2026-10-28 19:01:57'),(40,22,'ec8344876d7de4d0bc90e69042cd0fee1de1be3ed1fef341a6a4fd138bea4afa','2026-10-28 18:31:57','2026-10-28 19:01:57'),(41,22,'d5e360b41c327ff2e1f46e612249f464dd95ec85f616c6e5f1e1d8b458884a6a','2026-10-28 18:31:58','2026-10-28 19:01:58'),(42,22,'f4d6b708293e9acf11397018cfffa96aa7b8cd02806db552772dc33596347344','2026-10-28 18:31:58','2026-10-28 19:01:58'),(43,22,'2fc72104e26a9260ba8a5a850cb9706a2378a28753dca9301fa3e4b942b83746','2026-10-28 18:31:59','2026-10-28 19:01:59'),(44,22,'402ad70f427262df30025b919442358732cea9deec317a429ad87b1a0e35280e','2026-10-28 18:31:59','2026-10-28 19:01:59'),(45,22,'d5b13c2ca12edbb31508e470fb892d67aa2331f8afe3d469f5f7b7d36be85762','2026-10-28 18:32:00','2026-10-28 19:02:00'),(46,22,'72ad5d39cc863fb9aae4e1925b2de65384e0336ab2ce31798aeec58283103aaa','2026-10-28 18:32:00','2026-10-28 19:02:00'),(47,22,'33c9461313abb933a484480fc3bef61a2e0b50e69dbb66e0f6a17641ac578bdc','2026-10-28 18:32:00','2026-10-28 19:02:00'),(48,22,'9bb5e3d3fbad44eb137498df4b0e02a437c4c882808b35adc04fd34702ff264b','2026-10-28 18:32:01','2026-10-28 19:02:01'),(49,22,'16b8a6f55560242fcd9b519a7b061a8be9035137e5b098313f85514ac0332d7d','2026-10-28 18:32:01','2026-10-28 19:02:01'),(50,22,'4c0da46521be48a67fcec431a1227cf300f5fd557d37298f3e637b25e27df6ee','2026-10-28 18:32:02','2026-10-28 19:02:02'),(51,22,'f1de7f18d8a46da4c5690d704afc5f3049b2b4ebf5eeb6d4bccf618e15a4bdb5','2026-10-28 18:32:02','2026-10-28 19:02:02'),(52,22,'0edc19d9cbe5343ecaa46fb3a7a250cfe1f7e1606bd2cd00bfd4fbf2d8efa3bb','2026-10-28 18:32:03','2026-10-28 19:02:03'),(53,22,'8958f83ac839d3a64f00cdeb8bbe49c8183406df73699d93334ca3673cbef46c','2026-10-28 18:32:03','2026-10-28 19:02:03'),(54,22,'f7f8ee56735b849d2e74344a0f782515b9c2746be38b011f9b8fa78a827b41ac','2026-10-28 18:32:03','2026-10-28 19:02:03'),(55,22,'a820d0b3f29319c3b5a1b62b9a52c466f537d68f2dddf3b483a789edb996af77','2026-10-28 18:32:04','2026-10-28 19:02:04'),(56,22,'3ba0085132ca975e4738c20bb112ba1c1b143bb482bb30f6c0f0bed95dd1505d','2026-10-28 18:32:04','2026-10-28 19:02:04'),(57,22,'c591be6e3706f89556f4e34b04c98e83cb717697c7a3ac09c809f8d0ac796b31','2026-10-28 18:32:05','2026-10-28 19:02:05'),(58,22,'76ed68ac1fe07b43e81bee4284aca287d94daced5278b0e76999b00092ca52f7','2026-10-28 18:32:05','2026-10-28 19:02:05'),(59,22,'60e19bca99fe8a5e37227df3376471e6a90409bcf416cbb67ada63c1de280477','2026-10-28 18:32:05','2026-10-28 19:02:05'),(61,22,'e69e9e11c23b6c254e2a35919579d5a787d0a68263330ba00be742f39f6d53c2','2026-10-28 18:41:49','2026-10-28 19:11:49'),(62,22,'ad7a7aa4c030980b004511ef0ba16b4f897f8117f8b53d54bb8a832eeac22112','2026-10-28 19:30:36','2026-10-28 20:00:36'),(63,22,'98d27757ddea0134ee36e39598baf9fce6fe81c723e18626de166bcf06ba43e5','2026-10-28 19:30:36','2026-10-28 20:00:36'),(65,22,'99fcca0ae597eabdabbc2987bd372fc394920c9f89c679cd038e777144473a78','2026-10-28 19:47:20','2026-10-28 20:17:20'),(66,42,'bad6baf4585ad098b3387f3bfff610ef593555e2d7514498f1c384245fc92b62','2026-10-28 20:10:42','2026-10-28 20:40:42'),(67,42,'c8ab9af63ffa1e7a063c589799b7dac4b4250adb5f115213807debeec4813bb1','2026-10-28 20:10:42','2026-10-28 20:40:42'),(68,42,'ba6fda6ffb977bd772bd4ff6536e04df4ce6dd21f396332dbbcc278d6ec1f8c3','2026-10-28 20:13:08','2026-10-28 20:43:08'),(70,42,'cb8ed55b73d6441bda91697f8b9c732b629a6642a0d6cd575c64175a3da1023b','2026-10-28 21:01:32','2026-10-28 21:31:32'),(71,43,'90c7e62a3e348fc071781912be776c1d759173380d1827e1f7c92568acac1ab5','2026-10-28 21:01:49','2026-10-28 21:31:49'),(72,42,'a039356bca051ee22c06ef845aece00baba469769d5eade337400d867959d1c5','2026-10-28 21:02:34','2026-10-28 21:32:34'),(73,42,'56457f62d2c98baf2faaeec7dd1076ab8361f181169742f4379ea21b016df5e7','2026-10-28 21:14:46','2026-10-28 21:44:46'),(76,44,'acb2b5cf69b6b4b0d82d4a3da3ab113354fd39273e13b3fe72eb53187a80c334','2026-10-30 19:46:41','2026-10-30 20:16:41'),(77,42,'4a64cee5c9865abf081eb30908667c8abcac01356b4e8deccd517462679478c8','2026-10-30 22:34:49','2026-10-30 23:04:49'),(78,42,'4a5f3825f2c8075aa729ee8859bec5d03adb858aecc357c0a6eae3f6e0a9252d','2026-10-30 23:09:56','2026-10-30 23:39:56'),(79,43,'6113e1c645a3a797aff8f7df31e5bd77874e07fbb8f5664eaf0dfcc037f02e9a','2026-10-30 23:11:21','2026-10-30 23:41:21'),(80,44,'8aa0629ba0be4fea062841ac8d68c95aa9b68b91313f2b510cfc347cddd7d930','2026-10-30 23:33:43','2026-10-31 00:03:43'),(89,43,'2dcd23cceef38938190e306ce06dad94b6662657c9a86c9a35a4f0d77c7817b4','2026-10-31 02:22:23','2026-10-31 02:52:23'),(90,39,'4d813d3a6e6da4ffc177accbf1ac9174f2bb914c4995f4a94e136b3962e72ec4','2026-10-31 02:22:40','2026-10-31 02:52:40'),(92,43,'ed1c01ac08267bd273bbc1106169e4ad3da814945e082276ab93a50d02604e1c','2026-10-31 02:44:17','2026-10-31 03:14:17'),(93,43,'86499ab5b9442a107f6634e990d57363554a5a12ce576802dc0e39050825e715','2026-10-31 02:44:18','2026-10-31 03:14:18'),(94,39,'d60fff32eb56c57b73ce7d7078afadfc1d13ebd7e46b7ecd7b411f387b6dc9f9','2026-10-31 02:44:35','2026-10-31 03:14:35'),(95,22,'a8a38184e9d95e3187492303366d2a134d08ea2531e76ef362fb96411438e972','2026-10-31 02:51:03','2026-10-31 03:21:03'),(96,22,'96ad3a936817271d591ca377a6a04231c248bdc44e63ee5c6c190d346c6a29b6','2026-10-31 02:51:04','2026-10-31 03:21:04'),(98,39,'8e08db9e9d6532e29850aa5f72ff955798152807721a8d62756992fc7512a986','2026-10-31 03:14:46','2026-10-31 03:44:46'),(99,39,'08ca4d779be35fd7d40a608f15bd6a630c554c8ffda7c3e3a031b090adf17bee','2026-10-31 03:16:46','2026-10-31 03:46:46'),(100,43,'3d9e80ad268f64e693c58bae8d0f6c6b37d470d1b0520956021321940e79b07a','2026-10-31 03:21:20','2026-10-31 03:51:20'),(101,39,'ea72ede265fa827ae0bded8b4949610bee7c1802947b45092d8c5aca76253c48','2026-10-31 03:25:11','2026-10-31 03:55:11'),(102,39,'f4b56ad1eda194958c9a3a2e682bbe54acd666f00a247bf1c614f4862c59ca6c','2026-10-31 03:25:11','2026-10-31 03:55:11'),(103,39,'abdd3ad40e62841dd71dd99ce25a6471f3c5d6722cad7fb840449f17bde804a7','2026-10-31 03:25:11','2026-10-31 03:55:11'),(104,39,'30fe24f930c855ba2a5518e2485adb9326463354e7fdbb3da6d1e52565b6674a','2026-10-31 03:25:12','2026-10-31 03:55:12'),(105,39,'34a7fed0b7035e07cb07ad1254e4b41923685cdf6007f1bb7c1b29f647fa3b27','2026-10-31 03:25:24','2026-10-31 03:55:24'),(106,39,'2dc15508e5efc6c84ea76fe91d36323700f428bece794fc90860802a28fcfdb5','2026-10-31 03:29:07','2026-10-31 03:59:07'),(107,39,'61614f16b1f6d58fd0f5fedaf47b94b9ab1e2bfa756d985fa1826f90aae64078','2026-10-31 03:30:42','2026-10-31 04:00:42'),(108,39,'b80f385c22d763b657440b46ee180e7ae1e960a7f9319bddd413f85ed915ff7c','2026-10-31 03:30:48','2026-10-31 04:00:48'),(109,39,'94b21bbce687e2cc888bff0e9bee441ac3995bd05f2740c72346dea55fce025c','2026-10-31 03:31:27','2026-10-31 04:01:27'),(110,39,'3e4e771db8607d4e385055c652afda0ef9921140ff673fb27aa58c4b6a53438a','2026-10-31 03:33:01','2026-10-31 04:03:01'),(111,39,'6c53b584e32edfb418c4b2f529b5783f09520dbefc5d5f632e626bcddecd8c6b','2026-10-31 03:33:20','2026-10-31 04:03:20'),(112,39,'a078a6ebd37a699b23d74ddb08616ac5757e92436feb342e57f4b48c3124f941','2026-10-31 03:33:40','2026-10-31 04:03:40'),(113,39,'98d9c2bf579dc8d005a744602e438ef440cb17a87a61c6837c44d49ce7f7f165','2026-10-31 03:34:07','2026-10-31 04:04:07'),(114,39,'a9b586b648a926cb1f61615aa00a14f9f85a1cda8f050dcc6be0a62f87f8f9d7','2026-10-31 03:35:55','2026-10-31 04:05:55'),(115,39,'7604d7a48e4721bedcb71b2fc64076831799b9ad2a78e48b6edbebfc2f8b3b83','2026-10-31 03:36:01','2026-10-31 04:06:01'),(116,39,'bcd2cad9aac5e620fc7a1d9976b4c590e1f87b20d7b1d05a436f510a82831896','2026-10-31 03:37:17','2026-10-31 04:07:17'),(117,39,'849cd900ec9877e6364718b11e15169eb9ede2bdbdc2802774a1905c6a3fddd1','2026-10-31 03:40:06','2026-10-31 04:10:06'),(118,39,'983233f0a6c591c03451c35ea32652ec9cf5c2061429a5aeb02dd792926807ae','2026-10-31 03:40:33','2026-10-31 04:10:33'),(119,39,'b678a817af3cfb89dcc3902a81a6b4c1b04b08ee286a29b4e914926e65523e71','2026-10-31 03:40:43','2026-10-31 04:10:43'),(120,39,'7cbc3de7e0f4c0519cffd8952629930d4a832e2d3ac073c7a34bb13a4c3cb23a','2026-10-31 03:43:59','2026-10-31 04:13:59'),(121,39,'b13f52fba409a29f9a96734cf93de39b36bae62df7a4609b2278ce0634efb963','2026-10-31 03:45:22','2026-10-31 04:15:22'),(122,39,'b60d17ac9545da86e88224677fe89d60637a6e45304c147921c54c55f10cd0f7','2026-10-31 03:45:23','2026-10-31 04:15:23'),(123,39,'dc97f7648774042a9854beb5ea0cd8bf1f9a10ba34c305f9a20e7bfe2c3452b4','2026-10-31 03:45:23','2026-10-31 04:15:23'),(124,39,'04bc5d7def64fb1ee16cd854b6775c72f092e81720306c7fac8b05b752e3433f','2026-10-31 03:45:29','2026-10-31 04:15:29'),(126,22,'dbbd96f2ecc7e6199309d7a7f75a0323559f64cacaa862141adfddfaad30d2d2','2026-10-31 03:47:23','2026-10-31 04:17:23'),(127,39,'d0b7e0a1278ce50b01209d8b126eb134f9b8eba9a81a4468bbcda13fb3f788ce','2026-10-31 03:50:18','2026-10-31 04:20:18'),(128,43,'c9cb99710ca4769282209dabff40c41e3720914f6431c894208cacfde5f767dc','2026-10-31 03:50:36','2026-10-31 04:20:36'),(130,22,'2ad4afefc360bd8b1eee266dfe7a36cd335d08541043e42cd2eb50b0cde3ea7f','2026-10-31 05:21:05','2026-10-31 05:51:05'),(132,39,'ea771215ef3a27a7665d41e419c82db70b853b08fe19b63294f71a8b6456e461','2026-10-31 05:30:54','2026-10-31 06:00:54'),(133,39,'4ad2f2edcdaa8795cfc97ace5a257b9db2d0993123dbfc65d7698d1fd8b578db','2026-10-31 05:30:55','2026-10-31 06:00:55'),(134,39,'e4b685ee6efba2b1b3af4df34271cdcef02390c88afc071a3556177cbdc03674','2026-10-31 05:30:55','2026-10-31 06:00:55'),(135,39,'37897699cf4dfa5248789277e876dabee8ae2f543e77cecb7215d6a79d9ed728','2026-10-31 05:30:56','2026-10-31 06:00:56'),(136,39,'d578a27cc8f93a771a2469a0fca1dff4933a3d3325a57ef8b9a4a54915961d71','2026-10-31 05:30:57','2026-10-31 06:00:57'),(137,39,'724409c4689363a10bb4abc7d8fd02034818c51f25452b38a4744df4b3f7b02e','2026-10-31 05:30:58','2026-10-31 06:00:58'),(138,39,'7cfdcc776903e7da2af0ea74a999d7765b6376562f88b24c5a9bdbafe52f6220','2026-10-31 05:31:16','2026-10-31 06:01:16'),(139,39,'e962d2497860bb37ea3f0c3a0f3dab1f51c83c32b98429e43db7a5a34f808aa5','2026-10-31 05:31:20','2026-10-31 06:01:20'),(140,39,'1ac2e4e13662b974ac8313ab79c9e1f0af463499a724f31f7ffdda3b285e779f','2026-10-31 05:31:33','2026-10-31 06:01:33'),(141,39,'bb3186a03ff4954dc27791e0a2f4ca1cdef98b53d667be716d9d94192840ac50','2026-10-31 05:33:23','2026-10-31 06:03:23'),(142,39,'0b233e83441309187306a9310d9ee36ee2ac497a5ae287fc05a356a431c77383','2026-10-31 05:36:51','2026-10-31 06:06:51'),(143,39,'d1df43bc3dda96c4c6ce5d0a2d3982ef8c5bcb5d73da038ec07e80fcc0a2d0e6','2026-10-31 05:36:57','2026-10-31 06:06:57'),(144,39,'b64e8ebb8582315b121b61f4c68727abe9e23dc6f3077c7f9a5fa848f0408466','2026-10-31 05:37:05','2026-10-31 06:07:05'),(145,39,'8977401eded015b6a7a0e9f45b54dfa13b1aeccc88117db5dcfa22b4c0aacbec','2026-10-31 05:37:28','2026-10-31 06:07:28'),(146,39,'74561336550a2a3c74c661c27785e7119963e29f638bcd5e26628870019c76a2','2026-10-31 05:37:29','2026-10-31 06:07:29'),(148,22,'39ee908e064faf79acaaafb78e0e704c953958dbe5138262ebdec1f59f8b9331','2026-10-31 05:38:40','2026-10-31 06:08:40'),(150,39,'550a5f3c8cc9f763727feedc76c3b7806df3782fb88c8c93c8b6ff399da1ae7d','2026-10-31 06:16:51','2026-10-31 06:46:51'),(151,39,'312651155064259a9c85104a88ba688b4712ef56647ac7aff98684776faf4df7','2026-10-31 06:21:00','2026-10-31 06:51:00'),(152,39,'a9fd9709c5d768df886b38213eb507263ffd1a7f73bf8f3e440cec4b94fb0439','2026-10-31 06:21:06','2026-10-31 06:51:06'),(153,42,'bd5eff8f51fdd6172ada79801b0edbd7f47d2ba5dbdc14b6bf0af33221b8f290','2026-10-31 06:23:28','2026-10-31 06:53:28'),(154,42,'aa0a212f9ad48353bc730dd426b589a08b3350e7d2b8eb19b8f77a865e01cc69','2026-10-31 06:25:29','2026-10-31 06:55:29'),(155,42,'20f7d744bb4bba1f020bfdceca166693d3e991f7364b2b9c460e0d5c0a6fa349','2026-10-31 06:27:03','2026-10-31 06:57:03'),(156,43,'84e950735a0845df1f46f2a8962e98a475c37a1739a7d90cd77da62a9c3a3b07','2026-10-31 06:31:19','2026-10-31 07:01:19'),(157,44,'9f1986b99850a5820d6673824fb215fb6519b54a6b53bc5b2cff6bada9e37b89','2026-10-31 06:35:59','2026-10-31 07:05:59'),(158,42,'9b4bd5ffdef58beb279810f22ead846f4cf25c22dfbddc7db049a6627f7d6e50','2026-10-31 07:38:22','2026-10-31 08:08:22'),(159,43,'d8b8ebbf671a7ea0f046020bc110292f8dccb539e87892b980454cf6793469a6','2026-10-31 07:43:34','2026-10-31 08:13:34'),(160,44,'2e4a902b1c6d6f8953957421dd5f4cfd202488116ea73b38f5180ce49a0f3a3c','2026-10-31 07:44:26','2026-10-31 08:14:26'),(161,39,'b324d70642adb8b73590d0bd5deb75272fe6319909e29751e3709cd047a37a2e','2026-10-31 10:15:23','2026-10-31 10:45:23'),(162,39,'731f1de0e54ab26469bcbac874218c341cdbf9f39cd19b5f2494b5484eb971d6','2026-10-31 11:34:21','2026-10-31 12:04:21'),(163,39,'45792ba1bad907ba36ee22a84c013a20c4b73eeb697010cb8e1f7f1eda56ca52','2026-10-31 11:34:22','2026-10-31 12:04:22'),(164,39,'b1b63fb328f582c0b3fdfa7022f7253999af69102697942299c8905f0c0c1954','2026-10-31 11:34:22','2026-10-31 12:04:22'),(165,39,'2221b5216ee65fe92ee3d9db93461a5e74d0feceb02954cbfa1a7e76b5bebb1e','2026-10-31 11:34:23','2026-10-31 12:04:23'),(166,39,'3dbea488f19db95145c08487cf33394b5ec29dea8a0613126507117d044f5df8','2026-10-31 11:34:24','2026-10-31 12:04:24'),(167,39,'58850c56881a2fe01e2ee566c74452da2ed2b96d86e3f47ce05a995390f8e9e8','2026-10-31 11:34:24','2026-10-31 12:04:24'),(168,39,'fe10b5ea0096ffe563eefb460ab0bb5a5295a1e18d1c85be8cc5507ecf47fa38','2026-10-31 11:34:25','2026-10-31 12:04:25'),(169,39,'8b5d9b19d63b451ad107b6157bafa739bbb24db2d5a535756b52e874977b1ed0','2026-10-31 11:34:25','2026-10-31 12:04:25'),(170,39,'c8bd765e1f0f1b1015ba3d11a041890eda70604753b9c238d1b73db2495d67da','2026-10-31 11:34:26','2026-10-31 12:04:26'),(171,39,'38184f887e918b602e5f2c19ce21d80d3d7da5817158444465395209dc792b68','2026-10-31 11:34:27','2026-10-31 12:04:27'),(172,39,'c6a77260754bfebb05f706c2b6d87f0a1adc7847d4aa452c7788b4b43e19d833','2026-10-31 11:34:28','2026-10-31 12:04:28'),(173,39,'75a8bb4a64435327670f67b9b3800db7d363f84b523c2305108048c0b007716d','2026-10-31 23:15:55','2026-10-31 23:45:55'),(174,39,'28c0f5124f1ad9f42a56400e061856c1f59da1ebffd27d903f4c17b2676b8202','2026-10-31 23:24:37','2026-10-31 23:54:37'),(175,39,'fd6fadf13c105d641b69e21b54a97d5e71a0c16834218cb03f6d6e6bba333d33','2026-10-31 23:27:03','2026-10-31 23:57:03'),(176,39,'aa85b3befc922ef72a6b947bcf0dcbc5d6e7796631bff6e5f088aade0b738444','2026-10-31 23:28:36','2026-10-31 23:58:36'),(177,39,'bcfd132e55beae68131dd00c4bc22ff4029adaa96d912189b67b9e8f4693e345','2026-10-31 23:31:55','2026-11-01 00:01:55'),(179,22,'720aa93f4f0824df3df007ef3c528252cb376a7e4a73fe5af3d46e5ceb495da0','2026-10-31 23:41:05','2026-11-01 00:11:05'),(180,45,'99356379469cb764877260cf4ff047c6e19fc75247a82bec53782f9f51a02967','2026-10-31 23:47:49','2026-11-01 00:17:49'),(181,39,'402ba1f6893bd8187b3cd895748d456ddcfc9a0353573343b76fedfa3986b19f','2026-10-31 23:58:09','2026-11-01 00:28:09'),(182,45,'ab4198ad95b06cf1457efca06f76ede72443e14b0453755f7ea0f78f92b4e9ae','2026-11-01 00:13:54','2026-11-01 00:43:54'),(183,45,'b8e47688c6170b8f45dc69d0a2451f36a90f8e56f48c42f26d54a186109d7330','2026-11-01 17:13:22','2026-11-01 17:43:22'),(184,45,'17133140c9693ee7853fcc80f8b4a8c5d3b769488733fe3195dad9c0406d3d60','2026-11-01 17:13:35','2026-11-01 17:43:35'),(185,45,'24245cc537b6f0f0d6b6d8c4c13656ad3817bc817252915b1f8662516f06d9bb','2026-11-01 17:21:01','2026-11-01 17:51:01'),(186,45,'822fcadb4be85928423c6cd10f68ec6224efc87e80134bb80baecdccd2ddcd0d','2026-11-01 17:22:16','2026-11-01 17:52:16'),(187,45,'2ba717c43799068d3f31d4de1d39f4b28c8eb5943cde0c0ffdb1d062967beef1','2026-11-01 17:23:43','2026-11-01 17:53:43'),(188,45,'063a1a5dd4954fe0608e9b10be4c8a74892d0b7a958043a31ff716003c24be78','2026-11-01 17:30:54','2026-11-01 18:00:54'),(189,45,'30bba7bb99adcc42f50bb5cadbfabfb1353f915d241d05c618860a25812a1dbd','2026-11-01 17:31:07','2026-11-01 18:01:07'),(190,45,'5257ff32a571764605c7747b070f370f40eca9da0b35c94aad37cb8a4188d984','2026-11-01 17:31:43','2026-11-01 18:01:43'),(191,45,'07005ed68343f02e65f4179223fe0bd00e9cda74924e969fe0a89d1642bca0cf','2026-11-01 17:39:16','2026-11-01 18:09:16'),(192,45,'a46ab3872d43e2c73e3bcbb046cd8dd3c38c4326c37739420db865658604aadc','2026-11-01 17:39:57','2026-11-01 18:09:57'),(193,45,'a5a88c6a127fcdbec9aff7eb40027e11c576fa1037c11d425cb0da9fa8f4528e','2026-11-01 17:41:54','2026-11-01 18:11:54'),(194,45,'c4d9877ba685397ff3ddbb19c8a79ca995db303dbb37b0ab33c0037e5a7595c8','2026-11-01 18:15:31','2026-11-01 18:45:31'),(195,45,'35c687988b9e4755fbc25db420d6611be133baad8e3ec90008e6f18fa76e6462','2026-11-01 18:15:43','2026-11-01 18:45:43'),(196,45,'fdeb5ad236d6add04b10bee7f6a3325b218e3b97a88abce726cdbf9ea9f818eb','2026-11-01 18:17:45','2026-11-01 18:47:45'),(197,45,'475182c3b150a382c80e5dc9a776582a2dd7f7da4449cf0cf817c3417fa1888d','2026-11-01 18:19:24','2026-11-01 18:49:24'),(198,45,'7e0f1bc1d81aa81400aba4a965e851571e6178ce6d0b3d3bc54ca683713210da','2026-11-01 18:19:50','2026-11-01 18:49:50'),(199,45,'e59abd6fe72eab45b4c40fab331ede40a9f752cb5f9f8e8e3ac7e5de49389e1c','2026-11-01 18:41:42','2026-11-01 19:11:42'),(200,45,'835ea8c94a2d19b41758abd17db9a1f5cd1fe99e92959a098413e389f36807cf','2026-11-01 18:43:29','2026-11-01 19:13:29'),(201,45,'976f6d09e8214dd49bac0569970cd8502a53348fbe156839f9cbd2ce0efb3881','2026-11-01 18:59:15','2026-11-01 19:29:15'),(202,39,'7f78ff0c71e1d4e5e2b5f06cf3f2439138249a2a9c1f094258ea9fa804119a15','2026-11-01 19:01:09','2026-11-01 19:31:09'),(203,39,'c0a0ea280624ffeb126128cd9fa295c7fb46ba9ca08c870f34913c4cb3be01d0','2026-11-01 19:01:35','2026-11-01 19:31:35'),(204,39,'46b54ec1bd7e4b7bd10967851789b3dcaf202bbbc0a4f768e2d06d01ffee6b08','2026-11-01 19:01:51','2026-11-01 19:31:51'),(205,39,'6f3f96a0794e629dac74bff26438d6328e6118683ecd7df56bc5aaf49ea729ed','2026-11-01 19:02:55','2026-11-01 19:32:55'),(206,39,'feafda154cfa3ac43a1331fbca8fa6de47563f0e2568405ae5646f766f4ab4ee','2026-11-01 19:03:16','2026-11-01 19:33:16'),(208,45,'d20cbe6950717d36e75289efadde35c357f98ccdbb6f2566d5a1f9105f0532f3','2026-11-01 19:07:16','2026-11-01 19:37:16'),(209,22,'218a15ff80f11cb2a425b6610b2ccd5bdbb9120c2429b05a5c743f3c4edb1fe3','2026-11-01 19:09:36','2026-11-01 19:39:36'),(212,45,'173e2fb5e762c8705cff224e1ffc8a2eebc92b43357e027a6616c300378be8fb','2026-11-02 01:42:42','2026-11-02 02:12:42'),(213,45,'98a882d50acdeb4fadd58a8e5a2229a0c1ed93bef855077f24029fa0216b2f46','2026-11-02 01:42:42','2026-11-02 02:12:42'),(214,45,'1a922cf2ba27dbd93a46dfd79252bb690e47ad55f6848bc4582cb13a46b46ffa','2026-11-02 01:42:42','2026-11-02 02:12:42'),(215,45,'4dfcf159c2ce18304a6bb0f64e797348f81c3a6363ccdcbc81eec4be07fd9ad4','2026-11-02 01:42:42','2026-11-02 02:12:42'),(216,45,'76f8ccf21328e834855cb2cef15152f41b24008515058dd5bc8bb8b5fa3bf54a','2026-11-02 01:42:43','2026-11-02 02:12:43'),(217,45,'282753e9bfac90a663efe5e51e7751d22f2f7e6979c06870f8bf482c3d392501','2026-11-02 01:42:43','2026-11-02 02:12:43'),(218,45,'61d0f3231a8290caba7eab710f21eba61d3824d3f380fdb75980e730a4aa977c','2026-11-02 01:42:43','2026-11-02 02:12:43'),(219,45,'1bf991a666b41dad006b1ee512d6a07104601c80037fdef5b226e8f361ffdbfc','2026-11-02 01:42:43','2026-11-02 02:12:43'),(220,45,'8e2fabf1f5ff986580b13af5e36c51e764368f486ed0d4fcc73c5a663dcceb76','2026-11-02 01:42:44','2026-11-02 02:12:44'),(221,45,'3226e7a1162562544b13d42b6518727f3afe5eed9be177732f6fbf9c0f33bddc','2026-11-02 01:42:44','2026-11-02 02:12:44'),(222,45,'956f9bdcaea937c1b304360707f33f21a37a4fa9f2e51b8a803f6de651c1a8b7','2026-11-02 01:42:44','2026-11-02 02:12:44'),(223,45,'31ba7205b85235d437b608bca3ff3d93bee5d135aaeb7f3821d704c4ba6b8c82','2026-11-02 01:42:45','2026-11-02 02:12:45'),(224,45,'a5e2d52c42bc426c6c0464fec318fcbcd7eea29fcc8ee1af580b738c6aa9db11','2026-11-02 01:42:45','2026-11-02 02:12:45'),(225,45,'58e083c25ae9ba78ad90b7bd61281a6cb0f796d831d1c9555a621783c4fc51a5','2026-11-02 01:47:20','2026-11-02 02:17:20'),(226,45,'75f202b887753e3235f903f31830c4892edb3317866f9a1a12ff194343243ea3','2026-11-02 01:49:51','2026-11-02 02:19:51'),(227,45,'4958a86a3a971307b256e9cded86ad4a8aeb2e181aa4a9d070285aacd0056ca9','2026-11-02 01:50:11','2026-11-02 02:20:11'),(229,45,'38654aad7c2bf70c3e2214a3da09a2964e2bb8deaa7fd64b5a5f1738311c00d9','2026-11-02 02:08:29','2026-11-02 02:38:29'),(231,45,'bb9c783a3533086afa754748d1413d0df622e7e0ef21c7561f2932877cef4a54','2026-11-02 02:10:40','2026-11-02 02:40:40'),(232,45,'c04ff1d9cf1fae3254f5d5b8a912d82fdab7aa39b290b466155cb43d91425b4c','2026-11-02 02:13:38','2026-11-02 02:43:38'),(233,45,'8ce77eb21207ac6a2e137379123ed744e07c826daed02f9dfa94ab54419e23e9','2026-11-02 02:13:38','2026-11-02 02:43:38'),(234,45,'c32da7599b897b02196d0319395a7e89326f7275f9c4040a8d845f625b41a523','2026-11-02 02:13:39','2026-11-02 02:43:39'),(235,45,'86d47f39b573a13495cb8bf7e8506f253d5dbdbb14d59886ba82d41db33a585f','2026-11-02 02:13:40','2026-11-02 02:43:40'),(238,45,'d4dea53a2b3b4edf98d953c49cce775807075e74db26d26200ad8d9c76e83a0d','2026-11-03 15:18:32','2026-11-03 15:48:32'),(239,45,'485884339578e2a079ffd88816afa3d52f77d869ead3f32961ad08299869952d','2026-11-03 15:18:33','2026-11-03 15:48:33'),(240,45,'dd2d524eaffb61f9a71e44a06eecba02d7d19ce54b25cb49dd54063e17e70ae8','2026-11-03 15:18:33','2026-11-03 15:48:33'),(241,45,'b31df5d9094d9405e5f13a8c93560b5b51726a481d24ddf37419dd2e72e43f76','2026-11-03 15:18:34','2026-11-03 15:48:34'),(242,45,'c5b8f51d4bec2eada8906771235f3455fe3c0d397beef720e58fce0a5d2df575','2026-11-03 15:18:34','2026-11-03 15:48:34'),(243,45,'ff2a34b5e7beb0b78ca779feb51907944e51ff61bed57c880e1ff3862f8dd18d','2026-11-03 15:18:35','2026-11-03 15:48:35'),(244,45,'28efcf248e80d78c7ff9989a33fc9ca25bfa4b31a90568b5e74bf455cbfb7daa','2026-11-03 15:22:29','2026-11-03 15:52:29'),(245,45,'e477711f0752d277b38ce1109d0f4f69552ddcce78fce2eb9460dbec21c78ee4','2026-11-03 15:22:30','2026-11-03 15:52:30'),(246,45,'45f60363462802209a1989ac941a4ec77e8a0d9898b4e1abefe64d5d95f6cc2c','2026-11-03 15:22:31','2026-11-03 15:52:31'),(247,45,'0f118a54d609ab7f1ef016a62c2e4600b9581d95b5d25054665dbfb51a78042f','2026-11-03 15:22:31','2026-11-03 15:52:31'),(248,45,'86b5d2197cecf7d4df428be4907b53f09d9f5e6b5db869ac3841a547732a8cce','2026-11-03 15:22:32','2026-11-03 15:52:32'),(249,45,'8365a0ff9de14de4826bf6664b5e60376b4e77cd6f5cc1bdc6c9a5a910f67f4f','2026-11-03 15:22:34','2026-11-03 15:52:34'),(250,45,'7faae3da562fa84a0f5667a9d37d7040fa18d6d79e3beb7cf203c21baec334c5','2026-11-03 15:22:45','2026-11-03 15:52:45'),(255,45,'9ada12091f32c1a2545e76c20313b4f025dc7496c043876cd605807632d961e7','2026-11-03 15:52:19','2026-11-03 16:22:19'),(256,45,'43f7722f20a8e7727ae01c8c1ec8a4eb319438253bfb6b931dff4f4a8ce33b14','2026-11-03 15:57:06','2026-11-03 16:27:06'),(257,45,'2c19369b43dd054369ee1e42a873aabe40e5cd91688eed93bef0a0fa5181ea4a','2026-11-03 15:57:09','2026-11-03 16:27:09'),(258,45,'5c5ad86c51f1d01a8ff7c5c37a2b906b9da9191c402e3b82199843d2039824e6','2026-11-03 15:57:24','2026-11-03 16:27:24'),(259,45,'e4bb6f6204f98fdb141e62fbcd7d64ec936e64824963cc44ff7016ae91cada81','2026-11-03 15:57:29','2026-11-03 16:27:29'),(260,45,'4c04487d1c0ce05aa8bb42543a461b1dedaee70023ab063e73e1ad5632548036','2026-11-03 15:57:55','2026-11-03 16:27:55'),(261,45,'9923099699dd3274139251de7a183bb2f9f0328378f9967b43215c26565083fb','2026-11-03 15:58:16','2026-11-03 16:28:16'),(262,45,'0804ded695f45914158aae9bff4104514f15c8f4121a00c27e19afc8ead5bcfa','2026-11-03 15:58:16','2026-11-03 16:28:16'),(263,45,'e3cc49cc73070e3522bd30d37dc7e58c1076ef5aa859df2bb87946598b8833ce','2026-11-03 15:58:21','2026-11-03 16:28:21'),(264,45,'f44dfd4045eea52ddebc37dca41d06696321c8a474677236d2ce8a394b05aff3','2026-11-03 15:58:37','2026-11-03 16:28:37'),(265,45,'c025cc5cc20d5e423f42b8460f4f0b00fbd8e2d2c6d2f23b7eb6a268bbbb7241','2026-11-03 15:58:49','2026-11-03 16:28:49'),(266,45,'0b50ddc854bf26f56537d77a5a5379cc41091b26cabc7df943872bba93f404bc','2026-11-03 15:58:56','2026-11-03 16:28:56'),(267,45,'07f57e42e460e125ede732b928f466dab5acedb49532177eaa0846bf67f4455e','2026-11-03 15:59:15','2026-11-03 16:29:15'),(268,45,'6c7558cf0961eca5ed31c56b41f3b9fc25ab8b44e12362db92ed2569946413c2','2026-11-03 15:59:27','2026-11-03 16:29:27'),(269,45,'097a50d4ba5e9422672434212296caa68a8ec9e9361962fbe3130a2b2a9bc4b8','2026-11-03 15:59:29','2026-11-03 16:29:29'),(270,45,'54747820008db4e416c102afba9ea22f595a16b62de83e4cadc40324c4309d23','2026-11-03 15:59:33','2026-11-03 16:29:33'),(271,45,'1a1a4ac1f393a89d98fdd76ddee51dbf393c0451080e7ce1a8a8aa83f9eb813f','2026-11-03 15:59:34','2026-11-03 16:29:34'),(272,45,'04f14acfa77f747c190693a221955117614d1499024398ace16e4e0adcef5cf8','2026-11-03 15:59:35','2026-11-03 16:29:35'),(273,45,'cfec035650052a432407e3ab7cdeb9558a89d099240c0656c412efcac71b9eb8','2026-11-03 15:59:43','2026-11-03 16:29:43'),(274,45,'e96c8287a449c4d6edd5ab93595c038b662ca46445dece26c05b43dab0e8ec40','2026-11-03 15:59:52','2026-11-03 16:29:52'),(275,45,'b2fd1bd070197c7c7ecb7dde2b9f282596b55297716751f9731bd01a16c39cd8','2026-11-03 16:00:10','2026-11-03 16:30:10'),(276,45,'1c8d7e4d9542c893804db5000bc48958c8f604df8b39cf9c2dbdae0afdb4331d','2026-11-03 16:00:24','2026-11-03 16:30:24'),(277,45,'4143ee38edaace265295f450330699afa9db46a192765a36b4f274c746cb2bb7','2026-11-03 16:00:27','2026-11-03 16:30:27'),(278,45,'0d6ce9f135b1d1d94752f19d0d59fb4e19fb3b2ef1ee05ccd5f90cbdefff0a72','2026-11-03 16:00:49','2026-11-03 16:30:49'),(279,45,'ff9ee6d4de5108bb6ccbb9f6576b60682095d55145c2286819596de13451b60e','2026-11-03 16:00:54','2026-11-03 16:30:54'),(280,45,'fccdabf38e8c28b5a4dd72b78472baf629655fcbfec192c280b091327c032b29','2026-11-03 16:01:04','2026-11-03 16:31:04'),(281,45,'2c2b13c45611d845afb1e39447b8c794cad5c437ebd38ab8d3b812b32e829b0a','2026-11-03 16:01:10','2026-11-03 16:31:10'),(282,45,'2dc90d0972ab2c484a59a8c450b90bef02103121f77453426065dea37ce33ca8','2026-11-03 16:05:27','2026-11-03 16:35:27'),(283,45,'75aba3ac95ef799251d31ca2818a8d69010fb011a8efbcbade755aa32c31253d','2026-11-03 16:05:28','2026-11-03 16:35:28'),(284,45,'b06a4db61606cda12c460ea3e4574129785bc8a03d4b46718c627dbdccef5342','2026-11-03 16:05:53','2026-11-03 16:35:53'),(286,45,'92045ae81ca8f657f7a3170f12768a5c931930a18a84eabce644015a4b4a90f8','2026-11-03 16:13:39','2026-11-03 16:43:39');
/*!40000 ALTER TABLE `sessions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `users` (
  `uid` int NOT NULL AUTO_INCREMENT,
  `username` varchar(100) NOT NULL,
  `email` varchar(150) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  PRIMARY KEY (`uid`),
  UNIQUE KEY `username` (`username`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=46 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (1,'testuser','testuser@gmail.com','$2y$12$HpzzXzyriFGxArIXwNVpF.upcs/xdfKv.JCb8kniaSzNQnX.P8Wqm'),(2,'liz123','liz@gmail.com','$2y$12$o1FJbdg0lc5NVqYMtKNCBu6qQCza1zBNxZpja4LOpkgoTNolxZT0W'),(3,'sam','sam@gmail.com','$2y$12$VvflTGHu3RXmydpwabwQ9OT4NxqNW8J8fm5Sa.zQqLoM1IUgVBXm2'),(5,'john','john@gmail.com','$2y$12$OIkVAlC9SMo0T4wxSeJUY.ZmrIAEVaYUPAuB3KXu3qZQWPZPHdEC2'),(6,'testuser2','testuser2@gmail.com','$2y$12$8Rd4pJncz8e/UwlXIURULuTRo2AwpAivPFjiFuWBvuCDdW088qeA.'),(7,'pleasework','pleasework@gmail.com','$2y$12$tKJPj/0WXjZUjx5FPRRSk.5LwqSXhsD/cLGHMuNL8nufVF1bO4xHK'),(8,'testclass','testclass@gmail.com','$2y$12$tLO5GtGPyrmr8zVc0h8qieCplVHylvOBkzGwKZujYjuVKp47.Jbny'),(9,'guestuser','guestuser@gmail.com','$2y$12$iX7rr3q.OFICryIsdHZ3quuNaoGK7cDXTUGuuHLxzQzJvXl2fwUqm'),(10,'testuser1','testuser1@gmail.com','$2y$12$7G/yv/Vgpcz4xL36Kgui4uhMViSa.nSF7gCmPQf9Yq8tR5684bo0e'),(12,'testingUser','testinguser@gmail.com','$2y$12$c2hKFQxI4/.M5WHYhaucjewAXhC74RFmD6tmTkuZXh3g/HMETFB8u'),(13,'atest','atest@gmail.com','$2y$12$BPIUjhGcISiwLwGS2m5qIOSOqjip7g8/MKxdGeSiUDN1bRzS0jUgO'),(15,'Btest','Btest@gmail.com','$2y$12$HS90iecwuN7IDCC1t5VXzubdl.qFBdFJfZ9o9cuDFm53yIJuINLXO'),(16,'testing1','testing1@gmail.com','$2y$12$MfFLCiFKIfA5pG.4HbiGke/f/OQ57rMD0tOuUVgMFESvxDouwmkLC'),(19,'Ctest','Ctest@gmail.com','$2y$12$eMK7aiPFIZuAfMLypRNGYeayqcIAht66P7t2WW/0D45TU3rT.KlLC'),(20,'Dtest','Dtest@gmail.com','$2y$12$z5uJRsi10wPwsCrVzFyyL.G0mgu3SmYJ5UDkwS9iWrX65wUGQZC6u'),(21,'Etest','Etest@gmail.com','$2y$12$og46YtQyTr2htUtct/fS8uYvxQ59Iqb4rQk.XjzU8eBMw35Mp/J9q'),(22,'Test','test@email.com','$2y$12$Pea9zKUjxVI2PKAA5BFRrOktONBSkyL01hDmY4mZE0WXHGejC/wB.'),(23,'Fake','fake@gmail.com','$2y$12$Rz6oMqN2X/ETs4BC7.giUuh02.ES09zdwymBKC3I7s4/e4DwmxYw6'),(27,'nicktesting','nicktesting@gmail.com','$2y$12$VyqfjGDGhrQSXEEazUQn6ujRjjuK7jRlbL62X.nuj5KF6PgYHoosK'),(30,'nicktest','nicktest@gmail.com','$2y$12$qECAg0XEnk5QuXgyv159weKL7ULhHlE4kN4fztyRKdfCZL2g9SQHa'),(36,'Test2','test2@gmail.com','$2y$12$YYX7DxoDHaxuoni00a8V0OGuT0gX9B3N21tE9d2mGWn2MezBuP.2e'),(37,'james','jamestest@gmail.com','$2y$12$FCRCIx3LjNxxRy2c16dXQeXI7BOJQCuMtc4NYeBBQPN.1yQpl7e5S'),(39,'joe','email@email.com','$2y$12$rfS1..Uxc00YwgyT9XWq8OUj0dn8i41MHOJ9lGtNe1b.aPL9S/356'),(40,'nickistest','nickistest@gmail.com','$2y$12$3lCul8RNM/I0Nz7R.uyB1u9GLQHPgk1u3ipY1g0SniKICebWOQb7e'),(41,'nickistest1','nickistest1@gmail.com','$2y$12$PkPIuokuLdvGPqMxCxbUs.88lgKxNe0.zNVtl1MiGgJRnWV5Ub/VS'),(42,'alizeh','alizehh@gmail.com','$2y$12$1yXITb.rIsOLkV6whIO66.CT2OSxDM6NWaZBQJ2TEjLbPp555En9S'),(43,'peter','peter@gmail.com','$2y$12$2Kl6Xb59Wc/9M9TF11noJO5dZTgi4Iienl1XSrrfoa2.sPmOIcM2u'),(44,'jane','jane@gmail.com','$2y$12$OmAngmGtNUbGb6uzaj/qbOCeI0X.RfIwfsg/Dw5gQWjyNG1/fH40C'),(45,'joe2','jpc76@njit.edu','$2y$12$no945WAufle/Ea3Y3CPTnuVuBBE7yMedG2D9zApbGsRWcrZwUUHjW');
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping routines for database 'webdb'
--
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-11-11 13:11:22
