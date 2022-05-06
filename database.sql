-- phpMyAdmin SQL Dump
-- version 4.0.5
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Sep 15, 2013 at 11:58 PM
-- Server version: 5.1.54
-- PHP Version: 5.3.17

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `arkonvio_enigmav`
--

-- --------------------------------------------------------

--
-- Table structure for table `enigmav_comment`
--

CREATE TABLE IF NOT EXISTS `enigmav_comment` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `parent_id` int(10) unsigned NOT NULL,
  `user_id` int(10) unsigned NOT NULL,
  `rating` int(10) unsigned NOT NULL,
  `type` varchar(3) NOT NULL,
  `data` longtext NOT NULL,
  `pattachment` varchar(64) NOT NULL,
  `permissions` varchar(3) NOT NULL,
  `created` datetime NOT NULL,
  `modified` datetime NOT NULL,
  `directory_id` int(10) unsigned NOT NULL,
  `total` int(10) unsigned NOT NULL,
  `sattachment` varchar(64) NOT NULL,
  `title` char(64) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=179 ;

--
-- Dumping data for table `enigmav_comment`
--

INSERT INTO `enigmav_comment` (`id`, `parent_id`, `user_id`, `rating`, `type`, `data`, `pattachment`, `permissions`, `created`, `modified`, `directory_id`, `total`, `sattachment`, `title`) VALUES
(178, 81, 1, 0, 'MSG', 'Comment Section Demonstration.', '', '', '2013-09-08 17:17:44', '2013-09-08 17:17:44', 1, 0, '', '');

-- --------------------------------------------------------

--
-- Table structure for table `enigmav_directory`
--

CREATE TABLE IF NOT EXISTS `enigmav_directory` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `parent_id` int(10) unsigned NOT NULL,
  `user_id` int(10) unsigned NOT NULL,
  `rating` int(10) unsigned NOT NULL,
  `type` varchar(3) NOT NULL,
  `data` mediumtext NOT NULL,
  `pattachment` varchar(64) NOT NULL,
  `permissions` varchar(3) NOT NULL,
  `created` datetime NOT NULL,
  `modified` datetime NOT NULL,
  `directory_id` int(10) unsigned NOT NULL,
  `total` int(10) unsigned NOT NULL,
  `sattachment` varchar(64) NOT NULL,
  `title` char(64) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=21 ;

--
-- Dumping data for table `enigmav_directory`
--

INSERT INTO `enigmav_directory` (`id`, `parent_id`, `user_id`, `rating`, `type`, `data`, `pattachment`, `permissions`, `created`, `modified`, `directory_id`, `total`, `sattachment`, `title`) VALUES
(1, 0, 1, 0, 'DIR', 'The premier Retrogaming and Ecco the Dolphin. Welcome to our interactive social + information network.', 'c4ca4238a0b923820dcc509a6f75849b.jpg', 'VU', '2013-09-05 19:27:06', '2013-09-15 20:31:25', 1, 7, '', 'The Arkonviox Network'),
(15, 1, 1, 0, 'DIR', 'Would you like to contribute an article to be featured on our front page? If it covers Retrogaming or Ecco the Dolphin we&#039;d love to feature it.', '9bf31c7ff062936a96d3c8bd1f8f2ff3.png', 'VU', '2013-09-14 16:20:33', '2013-09-14 17:54:14', 1, 1, '', '*Article Submissions'),
(16, 1, 1, 0, 'DIR', 'Artists who&#039;ve submitted their Retrogaming or Ecco the Dolphin artwork are featured in this section. If you are an artist that has something that belongs here, feel free to submit it.', 'c74d97b01eae257e44aa9d5bade97baf.png', 'VU', '2013-09-14 16:25:42', '2013-09-15 11:56:15', 1, 2, 'c74d97b01eae257e44aa9d5bade97baf.jpg', '*Art Gallery'),
(17, 1, 1, 0, 'DIR', 'A showcase of Retrogaming and Ecco the Dolphin music. All content featured in this section is submitted in the form of a YouTube video. Remixers welcome!', '70efdf2ec9b086079795c442636b55fb.png', 'VU', '2013-09-14 16:39:40', '2013-09-14 19:02:10', 1, 1, '', '*Video Game Music'),
(18, 1, 1, 0, 'DIR', 'Talk about anything that&#039;s on your mind. This is a free for all section for discussions that are on and off-topic. Use this section to introduce yourself or to show off pictures of Mittens, the list goes on.', '6f4922f45568161a8cdf4ad2299f6d23.png', 'VU', '2013-09-14 16:52:00', '2013-09-15 09:51:29', 1, 1, '', 'Community'),
(19, 1, 1, 0, 'DIR', 'Share or discuss the iconic Ecco the Dolphin series that made it&#039;s debut on the Sega Genesis/Mega Drive. Ask for help, or share your thoughts on what you thought about these games.', '1f0e3dad99908345f7439f8ffabdffc4.png', 'VU', '2013-09-14 16:53:19', '2013-09-14 17:36:18', 1, 0, '1f0e3dad99908345f7439f8ffabdffc4.jpg', 'Ecco the Dolphin'),
(20, 1, 1, 0, 'DIR', 'Retrogaming is the interest and discussion of obsolete consoles, and the games made for them. If the console is no longer in production, this is the place to talk about it.', '98f13708210194c475687be6106a3b84.png', 'VU', '2013-09-14 16:53:31', '2013-09-15 17:33:51', 1, 1, '98f13708210194c475687be6106a3b84.png', 'Retrogaming');

-- --------------------------------------------------------

--
-- Table structure for table `enigmav_message`
--

CREATE TABLE IF NOT EXISTS `enigmav_message` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `parent_id` int(10) unsigned NOT NULL,
  `user_id` int(10) unsigned NOT NULL,
  `directory_id` int(10) unsigned NOT NULL,
  `rating` int(10) unsigned NOT NULL,
  `total` int(10) unsigned NOT NULL,
  `type` varchar(3) CHARACTER SET utf8 NOT NULL,
  `data` mediumtext CHARACTER SET utf8 NOT NULL,
  `pattachment` varchar(64) CHARACTER SET utf8 NOT NULL,
  `permissions` varchar(3) CHARACTER SET utf8 NOT NULL,
  `created` datetime NOT NULL,
  `modified` datetime NOT NULL,
  `sattachment` varchar(64) CHARACTER SET utf8 NOT NULL,
  `title` char(64) CHARACTER SET utf8 NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=97 ;

--
-- Dumping data for table `enigmav_message`
--

INSERT INTO `enigmav_message` (`id`, `parent_id`, `user_id`, `directory_id`, `rating`, `total`, `type`, `data`, `pattachment`, `permissions`, `created`, `modified`, `sattachment`, `title`) VALUES
(82, 0, 1, 1, 0, 0, 'IMG', 'Atari Homebrew Sonic/SmashBros/MortalKombat\rHow To Make A Game\rDuck Tales Legacy\rSega CD Repair\rBattle Kid/I Want To Be The Guy\rPickford Brothers Overview\rNude Punchout\rEternal Champions Sega Saturn\rGTA 3', '9778d5d219c5080b9a6a17bef029331c.jpg', 'HU', '2013-09-08 21:52:41', '2013-09-14 19:32:10', '', 'Article Ideas'),
(81, 0, 1, 1, 1, 1, 'IMG', 'Contact Directory, Image Thumbs, Preview Message.', '43ec517d68b6edd3015b3edc9a11367b.jpg', 'HU', '2013-09-08 17:16:42', '2013-09-15 00:09:08', '', 'Todo List'),
(85, 0, 1, 15, 1, 0, 'IMG', 'Welcome to EnigmaV, a Social Information Network that provides the Interactive framework of the Arkonviox Network. What is EnigmaV? It is the combined aspects of a forum, a content manager, and a photo gallery all wrapped into one. It was designed to organize information for easy consumption, while at the same time encourage it&#039;s members to submit quality content. You won&#039;t find another system like it, it is one of a kind, custom made for the Arkonviox Network.\r\rMy name is Johnny de Alba, known in many circles as Arkonviox. I designed EnigmaV from scratch. Each section represents a category, containing content of different mediums. If you are an artist, you&#039;ll be able to show case your art in a corresponding section. There are sections for writers and musicians alike, however we also provide multiple outlets for discussion. The idea is community, something very few websites have been able to contain with the advent of social networks. If social networks centralize just people, then what of niche interests such as Retrogaming, or Ecco the Dolphin?\r\rEnigmaV is just a platform, a jumping off point to something greater, but it&#039;s useless without people. That is why we encourage you to register for an account and participate. If providing input is the only thing you can do at the moment, we provide comment sections just for that. So please join us, and help us build something great!\r\rSincerely,\r\r[i]Johnny L. de Alba[/i]\r(Arkonviox)', '3ef815416f775098fe977004015c6193.jpg', 'VU', '2013-09-14 17:54:14', '2013-09-14 18:21:10', '', 'About EnigmaV'),
(86, 0, 1, 16, 0, 0, 'IMG', '[keywords: ecco the dolphin, ecco defender of the future, dreamcast, playstation 2, fan artwork]\r[i]via [http://technofortomcats.deviantart.com/]technofortomcats.deviantart.com[/http][/i]\r\rKimberley Petrie is an exceptional artist, whose master with color, matches her skill in design. Her gallery is a fine example of a psychedelic trip for your soul, as well as a treat for your eyes. \r\rIn this sample, we have Blades In Motion, a scene from Ecco the Dolphin, Defender of the Future for the Dreamcast and PlayStation 2. Ecco has just earned the harness needed to operate the long dormant machines. This was a defining moment in that game, as Ecco signified a symbol of hope to all the others who lived in the dark would known as Man&#039;s Nightmare.', '93db85ed909c13838ff95ccfa94cebd9.jpg', 'VU', '2013-09-14 18:54:15', '2013-09-15 12:02:28', '', 'Blades in Motion by Kimberley Petrie'),
(87, 0, 1, 17, 0, 0, 'UTB', '[keywords: ninja gaiden, video game remix, nes, nintendo]\r[i]via [http://www.youtube.com/watch?v=i9r1zbMEO9U]youtube.com[/http] by eman3624[/i]', 'i9r1zbMEO9U', 'VU', '2013-09-14 19:02:10', '2013-09-14 19:04:04', '', 'Ninja Gaiden Act 4-2 Metal Version'),
(88, 0, 1, 1, 0, 0, 'MSG', '[i]##### Update - The system has since been repaired, all problems should be minimized but we would still like to have input.[/i]\r\rThe system is clear for use, feel free to register, but there still issues that need to be taken care of. So if you spot anything please report it here, and I&#039;ll do my best to address it. I thought the system was complete but now that it&#039;s up I find one issue after the other, and as of this writing, there are no other registered users. Again I apologize for the inconvenience, I was going to make a major announcement, until I realized how flawed things are, and this is the better version of Enigma!', '', 'VU', '2013-09-14 23:59:42', '2013-09-15 13:45:56', '', 'So Far Not A Good Start.'),
(89, 0, 1, 18, 0, 0, 'IMG', '[i]via [http://www.deviantart.com/morelikethis/117145629?view_mode=2]deviantart.com[/http] by The Real-NComics[/i]\r\rIt&#039;s strange how comic book like some of these serial killers were. The Zodiac Killer, Pogo the Evil Clown, they were all some of the most gruesome killers known to man, and yet they seem like something out of a Comic Book. The link above leads to a collection of artwork by &quot;The Real NComics&quot;, featuring different serial killers. It&#039;s a very interesting collection.', '7647966b7343c29048673252e490f736.jpg', 'VU', '2013-09-15 09:51:29', '2013-09-15 09:51:29', '', 'Amazing Artist on DeviantArt Specializes in Serial Killers'),
(90, 0, 1, 16, 0, 0, 'IMG', '[i]via [http://atolm.deviantart.com/]atolm.deviantart.com[/http] by Atolm[/i]\r\rA nice colored pencil piece by Atolm, drawn on black paper. It&#039;s a group of common dolphins, like those found in Ecco, Defender of the Future. May or may not be related to Ecco, but that won&#039;t stop us from featuring it, Dolphins are awesome.', '8613985ec49eb8f757ae6439e879bb2a.jpg', 'VU', '2013-09-15 11:56:15', '2013-09-15 12:36:04', '', 'Three Dolphins by Atolm'),
(91, 0, 1, 16, 0, 0, 'IMG', '[i]via [http://atolm.deviantart.com/]atolm.deviantart.com[/http] by Atolm[/i]\r\rMany years ago Arkonviox.com had an art contest. This piece titled Lunar Bay by Atolm was an entry from that year that would eventually win. It&#039;s a mixed medium piece consisting of colored pencils, and digital art. What makes this piece unique, and the reason it won by popular vote, is it depicts two worlds colliding. On one half you have a Vortex drone, adapted to live on Earth, moving throughout the shadows. On the other half is Ecco, with a ray of light guiding him through the darkness.', '54229abfcfa5649e7003b83dd4755294.jpg', 'VU', '2013-09-15 12:01:51', '2013-09-15 12:21:20', '', 'Lunar Bay by Atolm'),
(92, 0, 1, 16, 0, 0, 'IMG', '[i]via [http://nambroth.deviantart.com/]nambroth.deviantart.com[/http] by Nambroth[/i]\r\rThis stunning piece was an art contest entry for Arkonviox.com. Nambroth who is an incredible artist, submitted this intimidatingly beautiful piece, upping the game on the art contest that year. Surprisingly it didn&#039;t win. It was a difficult decision for voters, as there were a lot of great entries to choose from. Although it didn&#039;t win, we are honored to feature it, and hope it inspires more Ecco the Dolphin fan art.', '92cc227532d17e56e07902b254dfad10.jpg', 'VU', '2013-09-15 12:04:29', '2013-09-15 12:29:09', '', 'Aqua Tubeway by Nambroth'),
(93, 0, 1, 20, 0, 0, 'UTB', '[keywords: mortal kombat, mortal kombat 2, mortal kombat ii, play as smoke, video game gliches, sega genesis, super nintendo, snes, mega drive, sonic boom, teleporting uppercut]\rThis is a little trick I invented in my earlier years of gaming. It allows the player to take control of a glitched version of Smoke from Mortal Kombat 2. The only issue with him is he cannot do a Low Kick attack.\r\rWhat&#039;s different about Glitched SMoke is he doesn&#039;t have his regular arsenal of special attacks. Instead he&#039;s got these strange glitched moves, that are quite impressive. The attack featured in this video I&#039;ve named the Sonic Boom Teleporting Uppercut.', '4_oSIMbd7dc', 'VU', '2013-09-15 17:33:51', '2013-09-15 17:34:14', '', 'Mortal Kombat II - Smoke&#039;s Sonic Boom Teleporting Uppercut'),
(94, 0, 1, 1, 0, 0, 'IMG', 'I figured I&#039;d make an Image of Smoke&#039;s Sonic Boom Teleporting Uppercut.', 'f4b9ec30ad9f68f89b29639786cb62ef.png', 'VU', '2013-09-15 17:45:46', '2013-09-15 17:45:47', '', 'Smoke&#039;s Sonic Boom Teleporting Uppercut'),
(95, 0, 1, 1, 0, 0, 'UTB', '[keywords: mortal kombat 2, kintaro, smoke, play as smoke, mortal kombat secrets, mortal kombat glitches, retrogaming]\rUsing only glitched Smoke, while carefully trying to avoid the game from freezing, I managed to get up to Kintaro. Unfortunately the game froze mid fight.', 'CzYI4jhPsrY', 'VU', '2013-09-15 20:11:05', '2013-09-15 20:11:05', '', 'Glitched Smoke vs Kintaro - Mortal Kombat 2'),
(96, 0, 1, 1, 0, 0, 'UTB', 'Using the same trick to play as Smoke, I was able to play as Noob Saibot, who has the unusual ability to create useless dazed clones of himself. This is not the spear wielding Noob we all know and love, it is Glitched Noob Saibot.', 'TY7WGPKotlE', 'VU', '2013-09-15 20:31:25', '2013-09-15 20:32:05', '', 'Mortal Kombat 2 - Glitched Noob Saibot&#039;s Dazed Clone');

-- --------------------------------------------------------

--
-- Table structure for table `enigmav_message_rating`
--

CREATE TABLE IF NOT EXISTS `enigmav_message_rating` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `parent_id` int(10) unsigned NOT NULL,
  `uid` int(10) unsigned NOT NULL,
  `counter` int(10) unsigned NOT NULL,
  `ip` varchar(16) NOT NULL,
  `created` datetime NOT NULL,
  `modified` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=14 ;

--
-- Dumping data for table `enigmav_message_rating`
--

INSERT INTO `enigmav_message_rating` (`id`, `parent_id`, `uid`, `counter`, `ip`, `created`, `modified`) VALUES
(13, 85, 1, 1, '', '2013-09-14 18:21:10', '2013-09-14 18:21:10');

-- --------------------------------------------------------

--
-- Table structure for table `enigmav_session`
--

CREATE TABLE IF NOT EXISTS `enigmav_session` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `parent_id` int(10) unsigned NOT NULL,
  `name` varchar(64) NOT NULL,
  `data` varchar(64) NOT NULL,
  `ip` varchar(16) NOT NULL,
  `created` datetime NOT NULL,
  `modified` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=3209 ;

--
-- Dumping data for table `enigmav_session`
--

INSERT INTO `enigmav_session` (`id`, `parent_id`, `name`, `data`, `ip`, `created`, `modified`) VALUES
(3207, 1, 'FLOOD', '1379302285', '', '2013-09-15 17:33:51', '2013-09-15 20:31:25'),
(3206, 1, 'SEED', 'aca964b6d6d7a8aa3f4ee029022d1cc7', '', '2013-09-15 17:25:38', '2013-09-15 20:38:15'),
(3201, 0, 'SEED', '52e9e35456c3ab4d607fc9d85b31a211', '66.249.78.135', '2013-09-15 02:50:44', '2013-09-15 08:21:00'),
(3205, 0, 'SEED', '6d9bb1cb70d4235fd9380f1dec26ddd9', '98.234.135.97', '2013-09-15 17:25:28', '2013-09-15 17:25:37'),
(3202, 0, 'RANDOM', 'KF3IMSPq', '66.249.78.135', '2013-09-15 03:09:44', '2013-09-15 03:09:44'),
(3203, 0, 'RANDOM', '2c9CXdOk', '59.58.157.51', '2013-09-15 04:00:52', '2013-09-15 04:00:52'),
(3204, 0, 'SEED', '891ee5126e401b4d9be7f1cc8e970077', '59.58.157.51', '2013-09-15 04:00:52', '2013-09-15 04:00:54'),
(3208, 0, 'SEED', '45cf580905b66b0266f2be7832f67c01', '37.140.141.6', '2013-09-15 20:57:34', '2013-09-15 20:57:34');

-- --------------------------------------------------------

--
-- Table structure for table `enigmav_user`
--

CREATE TABLE IF NOT EXISTS `enigmav_user` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `email` varchar(64) NOT NULL,
  `password` varchar(64) NOT NULL,
  `fullname` varchar(64) NOT NULL,
  `sex` varchar(1) NOT NULL,
  `birthday` varchar(10) NOT NULL,
  `current_city` varchar(64) NOT NULL,
  `current_state` varchar(64) NOT NULL,
  `employer` varchar(64) NOT NULL,
  `position` varchar(64) NOT NULL,
  `highschool` varchar(64) NOT NULL,
  `college` varchar(64) NOT NULL,
  `major` varchar(64) NOT NULL,
  `degree` varchar(64) NOT NULL,
  `avatar` varchar(64) NOT NULL,
  `banner` varchar(64) NOT NULL,
  `type` varchar(3) NOT NULL,
  `permissions` varchar(3) NOT NULL,
  `ip` varchar(16) NOT NULL,
  `session_id` varchar(64) NOT NULL,
  `created` datetime NOT NULL,
  `modified` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=44 ;

--
-- Dumping data for table `enigmav_user`
--

INSERT INTO `enigmav_user` (`id`, `email`, `password`, `fullname`, `sex`, `birthday`, `current_city`, `current_state`, `employer`, `position`, `highschool`, `college`, `major`, `degree`, `avatar`, `banner`, `type`, `permissions`, `ip`, `session_id`, `created`, `modified`) VALUES
(1, 'email@email.com', '09bb63bf7635f9cfd50f022e7a3b0dba', 'Admin', '', '', '', '', '', '', '', '', '', '', '1.jpg', '', 'ADN', 'VU', '98.234.135.97', 'aabfb51b9f6229b8307e1d8a55c60bec', '2012-02-20 01:25:05', '2013-09-15 20:38:15');

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
