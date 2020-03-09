-- Dumping database structure for board
DROP DATABASE IF EXISTS `board`;
CREATE DATABASE IF NOT EXISTS `board` /*!40100 DEFAULT CHARACTER SET latin1 */;
USE `board`;

-- Dumping structure for table board.posts
DROP TABLE IF EXISTS `posts`;
CREATE TABLE IF NOT EXISTS `posts` (
  `id` varchar(13) NOT NULL,
  `replyto` varchar(13) DEFAULT NULL,
  `postedby` varchar(10) DEFAULT NULL,
  `datetime` datetime DEFAULT NULL,
  `message` text,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- Dumping structure for table board.users
DROP TABLE IF EXISTS `users`;
CREATE TABLE IF NOT EXISTS `users` (
  `username` varchar(10) NOT NULL,
  `password` varchar(32) DEFAULT NULL,
  `fullname` varchar(45) DEFAULT NULL,
  `email` varchar(45) DEFAULT NULL,
  PRIMARY KEY (`username`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- Dumping data for table board.users: 1 rows

REPLACE INTO `users` (`username`, `password`, `fullname`, `email`) VALUES
	('smith', 'a029d0df84eb5549c641e04a9ef389e5', 'John Smith', 'smith@cse.uta.edu');

