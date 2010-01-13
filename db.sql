SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";

--
-- Databáza: `mokujicms`
--

-- --------------------------------------------------------

--
-- Štruktúra tabuľky pre tabuľku `admin_modules`
--

CREATE TABLE IF NOT EXISTS `admin_modules` (
  `id` tinyint(4) NOT NULL AUTO_INCREMENT,
  `module_name` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `namespace` varchar(60) COLLATE utf8_unicode_ci NOT NULL,
  `status` varchar(10) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'enabled',
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`module_name`,`namespace`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=2 ;

-- --------------------------------------------------------

--
-- Štruktúra tabuľky pre tabuľku `admin_users`
--

CREATE TABLE IF NOT EXISTS `admin_users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `login` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `password` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `group` tinyint(4) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `login` (`login`),
  UNIQUE KEY `login_2` (`login`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Štruktúra tabuľky pre tabuľku `site_categories`
--

CREATE TABLE IF NOT EXISTS `site_categories` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `slug` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `parent` int(11) DEFAULT NULL,
  `added` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `position` int(11) DEFAULT NULL,
  `level` int(11) DEFAULT NULL,
  `description` text COLLATE utf8_unicode_ci,
  `template` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'default',
  PRIMARY KEY (`id`),
  UNIQUE KEY `title` (`title`),
  UNIQUE KEY `slug` (`slug`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=3 ;

-- --------------------------------------------------------

--
-- Štruktúra tabuľky pre tabuľku `site_menus`
--

CREATE TABLE IF NOT EXISTS `site_menus` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(20) COLLATE utf8_unicode_ci DEFAULT NULL,
  `added` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=3 ;

-- --------------------------------------------------------

--
-- Štruktúra tabuľky pre tabuľku `site_menu_items`
--

CREATE TABLE IF NOT EXISTS `site_menu_items` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `menu_id` int(11) NOT NULL,
  `title` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
  `url` text COLLATE utf8_unicode_ci,
  `parent` int(11) DEFAULT NULL,
  `added` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `position` int(11) DEFAULT NULL,
  `level` int(11) DEFAULT NULL,
  PRIMARY KEY (`menu_id`,`title`),
  UNIQUE KEY `id` (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=4 ;

-- --------------------------------------------------------

--
-- Štruktúra tabuľky pre tabuľku `site_pages`
--

CREATE TABLE IF NOT EXISTS `site_pages` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `slug` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `content` text COLLATE utf8_unicode_ci NOT NULL,
  `published` tinyint(1) DEFAULT '0',
  `added` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `publish_time` datetime DEFAULT NULL,
  `content_type` varchar(20) COLLATE utf8_unicode_ci DEFAULT NULL,
  `category` int(11) DEFAULT '0',
  `has_widgets` tinyint(1) DEFAULT '0',
  `template` varchar(30) COLLATE utf8_unicode_ci DEFAULT 'default',
  `author` int(11) DEFAULT NULL,
  `homepage` tinyint(1) NOT NULL DEFAULT '0',
  `keywords` text COLLATE utf8_unicode_ci NOT NULL,
  `description` text COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`),
  UNIQUE KEY `slug` (`slug`),
  FULLTEXT KEY `content_title` (`title`,`content`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=3 ;

-- --------------------------------------------------------

--
-- Štruktúra tabuľky pre tabuľku `site_tags`
--

CREATE TABLE IF NOT EXISTS `site_tags` (
  `tag_id` int(11) NOT NULL AUTO_INCREMENT,
  `tag_name` text COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`tag_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;
