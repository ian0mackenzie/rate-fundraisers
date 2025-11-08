-- MySQL dump 10.13  Distrib 5.5.54, for debian-linux-gnu (x86_64)
--
-- Host: localhost    Database: rate_fundraisers
-- ------------------------------------------------------
-- Server version	5.5.54-0ubuntu0.14.04.1

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
-- Table structure for table `author`
--

DROP TABLE IF EXISTS `author`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `author` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(180) COLLATE utf8_unicode_ci NOT NULL,
  `username_canonical` varchar(180) COLLATE utf8_unicode_ci NOT NULL,
  `email` varchar(180) COLLATE utf8_unicode_ci NOT NULL,
  `email_canonical` varchar(180) COLLATE utf8_unicode_ci NOT NULL,
  `enabled` tinyint(1) NOT NULL,
  `salt` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `password` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `last_login` datetime DEFAULT NULL,
  `confirmation_token` varchar(180) COLLATE utf8_unicode_ci DEFAULT NULL,
  `password_requested_at` datetime DEFAULT NULL,
  `roles` json NOT NULL,
  `first_name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `last_name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `created_date` datetime NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `UNIQ_BDAFD8C892FC23A8` (`username_canonical`),
  UNIQUE KEY `UNIQ_BDAFD8C8A0D96FBF` (`email_canonical`),
  UNIQUE KEY `UNIQ_BDAFD8C8C05FB297` (`confirmation_token`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `author`
--

LOCK TABLES `author` WRITE;
/*!40000 ALTER TABLE `author` DISABLE KEYS */;
INSERT INTO `author` VALUES (1,'ian0mackenzie','ian0mackenzie','ian0mackenzie@gmail.com','ian0mackenzie@gmail.com',1,NULL,'$2y$13$7u.x5az6/F461JQ85/TnZu4/BMAazzwtfoPUFlN7m76PV4PoDBiqC','2017-02-27 08:11:21',NULL,NULL,'["ROLE_USER"]','Ian','Mackenzie','2017-02-23 11:10:44'),(2,'michelle0mackenzie','michelle0mackenzie','michelle0mackenzie@gmail.com','michelle0mackenzie@gmail.com',1,NULL,'$2y$13$saI2dZpUPZyfufwXACAfLuBaEKRhkJwfftur0bKPhDJUPTl6PEqkq','2017-02-26 20:48:17','rzWs2mRbQ2UK4aV9MPafk-wpt1JH9wNot23-eIj_nWw','2017-02-24 10:40:08','["ROLE_USER"]','Michelle','Mackenzie','2017-02-23 12:28:42'),(3,'colbydog','colbydog','colby.dog@gmail.com','colby.dog@gmail.com',1,NULL,'$2y$13$4aQL23GVMRNgM22gJhq6BO9330zNFLJAYJUfkXNSwADpvLjl0gwHS','2017-02-23 20:02:35',NULL,NULL,'["ROLE_USER"]','Colby','Dog','2017-02-23 20:02:35'),(4,'ian0mackenzie2','ian0mackenzie2','ian0mackenzie2@gmail.com','ian0mackenzie2@gmail.com',1,NULL,'$2y$13$1eeTEP2JY7YduFFxSiRhr.PmpzGikfbBUDEGC30RM4IZB.iAJYhg6','2017-02-27 08:15:07',NULL,NULL,'["ROLE_USER"]','Ian2','Mackenzie2','2017-02-24 16:30:26'),(5,'ian0mackenzie3','ian0mackenzie3','ian0mackenzie3@gmail.com','ian0mackenzie3@gmail.com',1,NULL,'$2y$13$IzoBHY5T4a4Yd/RpKNRmeeBaT2DsmuAF/8/gjkNTnNnCCcrzwKBLG','2017-02-27 08:15:17',NULL,NULL,'["ROLE_USER"]','Ian','Mackenzie','2017-02-24 16:56:02'),(6,'IaMackroy','iamackroy','123fakestreet@fakeemail.com','123fakestreet@fakeemail.com',1,NULL,'$2y$13$B.m8gdr3Oq2R/CTMeOpZ1.E2zlqX7n8OnRPe8RNuEtEBdgMdBJIne','2017-02-24 18:31:42',NULL,NULL,'["ROLE_USER"]','Ianonius','Mackroy','2017-02-24 18:31:41'),(7,'colmac','colmac','colmac@gmail.com','colmac@gmail.com',1,NULL,'$2y$13$/uOfa6VvdKG9XM9zLWxSGu7nX.sNsVA5dhAXXzORT3hMjHeS0v91S','2017-02-26 21:00:18',NULL,NULL,'["ROLE_USER"]','colin','mackenzie','2017-02-26 21:00:18'),(8,'ian0mackenzie4','ian0mackenzie4','ian0mackenzie4@gmail.com','ian0mackenzie4@gmail.com',1,NULL,'$2y$13$Y18pUUnRMU4TODNF6CJVrOasGWdH1tp.TSK7QsWsvp9Ogycm3057u','2017-02-27 08:15:57',NULL,NULL,'["ROLE_USER"]','Ian4','Mackenzie4','2017-02-27 08:15:57');
/*!40000 ALTER TABLE `author` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `fundraiser`
--

DROP TABLE IF EXISTS `fundraiser`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `fundraiser` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `author_id` int(11) DEFAULT NULL,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `description` longtext COLLATE utf8_unicode_ci,
  `thumbnail` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `created_date` datetime NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `UNIQ_6B7F53E15E237E06` (`name`),
  KEY `IDX_6B7F53E1F675F31B` (`author_id`),
  CONSTRAINT `FK_6B7F53E1F675F31B` FOREIGN KEY (`author_id`) REFERENCES `author` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `fundraiser`
--

LOCK TABLES `fundraiser` WRITE;
/*!40000 ALTER TABLE `fundraiser` DISABLE KEYS */;
INSERT INTO `fundraiser` VALUES (1,1,'Fundraiser One','Lorem ipsum dolor sit amet, consectetur adipiscing elit. Phasellus sit amet tellus et ipsum congue elementum in eu risus. Praesent ultrices arcu leo, eget dictum urna pretium non. Cras at arcu lacinia, rutrum orci quis, scelerisque augue. Vestibulum egestas nibh ornare, imperdiet elit ut, blandit nisi. Nunc mollis eleifend dictum. Nullam aliquam erat urna, facilisis auctor ante sollicitudin sed. In eu placerat nunc. Nullam varius auctor lectus vestibulum pulvinar.','https://s-media-cache-ak0.pinimg.com/736x/1d/48/3b/1d483b8bcc2fef65fd8a97596193b26c.jpg','2017-02-23 11:11:48'),(2,NULL,'Fundraiser Two','Proin laoreet odio et augue euismod convallis. Ut molestie elit tristique blandit convallis. Fusce vel est vitae quam commodo tempus. Nulla in neque nunc. Donec iaculis blandit ipsum, eu vehicula augue faucibus sed. Sed placerat dui mauris, sed cursus eros varius eu. Praesent sagittis congue elit, eget volutpat neque vehicula ut. Proin tempus purus risus, tempor feugiat lectus venenatis non. Fusce risus lorem, volutpat ut magna non, placerat interdum eros. Phasellus blandit nibh sapien, vitae bibendum est lacinia vitae.','https://s-media-cache-ak0.pinimg.com/736x/1d/48/3b/1d483b8bcc2fef65fd8a97596193b26c.jpg','2017-02-23 19:18:06'),(3,NULL,'Fundraiser Four','Pellentesque euismod ipsum in nibh tincidunt luctus. Nulla feugiat diam nibh, tincidunt hendrerit erat suscipit nec. Ut malesuada ex sed enim mattis, eget luctus est euismod. Nunc eu auctor lacus, eu eleifend dolor. Cras est enim, euismod sed ullamcorper eu, sagittis sed purus. Phasellus odio urna, placerat sit amet faucibus feugiat, tempor in magna. Maecenas mattis laoreet nisi eget gravida. Vivamus malesuada sit amet elit nec dapibus. Morbi mauris nisi, bibendum non velit et, condimentum vulputate enim. Aenean odio quam, placerat pellentesque metus non, vestibulum suscipit quam. Integer volutpat odio sed ipsum volutpat, a faucibus sem gravida.','https://s-media-cache-ak0.pinimg.com/736x/1d/48/3b/1d483b8bcc2fef65fd8a97596193b26c.jpg','2017-02-23 19:18:25'),(4,NULL,'Fundraiser Five','Lorem ipsum dolor sit amet, consectetur adipiscing elit. Morbi lacus mi, volutpat nec ipsum id, dignissim scelerisque mi. Etiam vehicula orci in erat mattis, in scelerisque ligula vestibulum. Proin maximus massa tellus, eget rutrum nunc lacinia vel. Nulla egestas convallis nisl, in sagittis velit semper ut. Aenean vulputate euismod ipsum, vitae bibendum sapien scelerisque non. Praesent blandit urna et ex porttitor, sit amet pretium massa luctus. In ut ex ut est rutrum blandit. Etiam at molestie justo.','https://s-media-cache-ak0.pinimg.com/736x/1d/48/3b/1d483b8bcc2fef65fd8a97596193b26c.jpg','2017-02-23 19:18:43'),(5,NULL,'Fundraiser Six','Maecenas interdum orci id rhoncus tincidunt. Donec eget mi tincidunt, iaculis odio quis, tempor nisl. Sed malesuada, libero id laoreet sagittis, erat metus ornare odio, in posuere eros magna non enim. Ut tempor leo mi, in dapibus tellus iaculis vel. Cras sit amet risus suscipit, hendrerit nisi eu, placerat mauris. Quisque blandit laoreet massa, sit amet euismod tortor tincidunt feugiat. Sed convallis metus facilisis pretium tempus. Donec rutrum hendrerit ipsum imperdiet hendrerit. Quisque pulvinar dolor non turpis bibendum placerat. Ut lacus dui, laoreet id elit dictum, viverra interdum neque. Vivamus quis commodo metus. Nulla venenatis condimentum scelerisque. Proin ut eros sit amet odio placerat congue vel sed turpis. Nunc scelerisque accumsan ligula, ut tempus augue ultricies at. Curabitur tempus justo eu urna ultrices sagittis. Quisque consequat vulputate ligula ac placerat.','https://s-media-cache-ak0.pinimg.com/736x/1d/48/3b/1d483b8bcc2fef65fd8a97596193b26c.jpg','2017-02-23 19:18:54'),(6,NULL,'Fundraiser Three','Lorem ipsum dolor sit amet, consectetur adipiscing elit. Morbi lacus mi, volutpat nec ipsum id, dignissim scelerisque mi. Etiam vehicula orci in erat mattis, in scelerisque ligula vestibulum. Proin maximus massa tellus, eget rutrum nunc lacinia vel. Nulla egestas convallis nisl, in sagittis velit semper ut. Aenean vulputate euismod ipsum, vitae bibendum sapien scelerisque non. Praesent blandit urna et ex porttitor, sit amet pretium massa luctus. In ut ex ut est rutrum blandit. Etiam at molestie justo.','https://s-media-cache-ak0.pinimg.com/736x/1d/48/3b/1d483b8bcc2fef65fd8a97596193b26c.jpg','2017-02-23 19:19:16'),(7,1,'Test Fundraiser A','fdgkdfj hfldkjvhfdv hdlfkjsv fldkvhlfd hvdlskhv dlkvjslfhvjdkslfvhfkldjvh flsdkv hfdlk jvlds','NA','2017-02-24 15:07:02'),(8,4,'Test Fundraiser B','gfdgf dsgds fvsgdgf ds gdgvdgdgvsdgsdv  sdv fds vv fd vfdvfdsv ddfs vsfdvsd','NA','2017-02-24 16:52:28'),(9,6,'I love fake email addresses','Make up a fake email','FAKE','2017-02-24 18:32:08'),(10,8,'Fundraiser Nine','vf bhg gnghnhg dbcxv cxb vc bcbcxb cvbvcbcvx bcvx bvcbcxbvcx','NA','2017-02-27 08:16:12');
/*!40000 ALTER TABLE `fundraiser` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `review`
--

DROP TABLE IF EXISTS `review`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `review` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `author_id` int(11) DEFAULT NULL,
  `fundraiser_id` int(11) DEFAULT NULL,
  `rating` smallint(6) NOT NULL,
  `title` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `review` longtext COLLATE utf8_unicode_ci,
  PRIMARY KEY (`id`),
  KEY `IDX_794381C6F675F31B` (`author_id`),
  KEY `IDX_794381C69F4D1DF6` (`fundraiser_id`),
  CONSTRAINT `FK_794381C69F4D1DF6` FOREIGN KEY (`fundraiser_id`) REFERENCES `fundraiser` (`id`),
  CONSTRAINT `FK_794381C6F675F31B` FOREIGN KEY (`author_id`) REFERENCES `author` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `review`
--

LOCK TABLES `review` WRITE;
/*!40000 ALTER TABLE `review` DISABLE KEYS */;
INSERT INTO `review` VALUES (1,2,1,4,'First Review','Maecenas eleifend nibh sed ipsum aliquam, quis tempor libero dapibus. Curabitur nec condimentum purus. Ut vestibulum elementum elit, quis ullamcorper ante posuere non. Suspendisse gravida enim sed sollicitudin ultrices. Duis tincidunt ligula enim, imperdiet scelerisque neque elementum nec. Cras id nisl a mi faucibus sollicitudin. Praesent tristique purus nec viverra laoreet.'),(2,1,1,2,'FANTASTIC!','SImply great'),(3,2,2,5,'Great!','This fundraiser was amazing. We all made so much money!'),(4,1,2,4,'My Review of Fundraier Two','It was a pretty good fundraiser'),(5,1,6,2,'Fundraiser 3 Review.','This was a terrible fundraiser'),(6,1,3,3,'abc','dsadsada'),(7,1,5,1,'fdsa','fdsfdsa fsad dsa'),(8,1,7,1,'abcd','dsc sa sdc sa dsa dscdsacdscdsac dasc dsac a'),(9,5,3,3,'gffgdgsfdgds','fvfgb vgfbd'),(10,6,9,1,'KX2','Not as good the second time'),(11,7,4,5,'New one','now');
/*!40000 ALTER TABLE `review` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2017-02-27  8:35:01
