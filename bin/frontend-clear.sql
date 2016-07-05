# ************************************************************
# Sequel Pro SQL dump
# Версия 4096
#
# http://www.sequelpro.com/
# http://code.google.com/p/sequel-pro/
#
# Адрес: 127.0.0.1 (MySQL 5.6.27)
# Схема: Shop
# Время создания: 2015-10-28 11:47:16 +0000
# ************************************************************


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;


# Дамп таблицы banners
# ------------------------------------------------------------

DROP TABLE IF EXISTS `banners`;

CREATE TABLE `banners` (
  `id` int(11) unsigned NOT NULL,
  `site_id` tinyint(2) unsigned DEFAULT NULL,
  `type` tinyint(1) DEFAULT NULL,
  `status` tinyint(4) DEFAULT NULL,
  `href` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `alt` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `image` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `last_update` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  UNIQUE KEY `uni_id_site_id` (`id`,`site_id`),
  KEY `idx_site_id` (`site_id`),
  CONSTRAINT `fk_site_banner_id` FOREIGN KEY (`site_id`) REFERENCES `shops` (`site_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



# Дамп таблицы brands
# ------------------------------------------------------------

DROP TABLE IF EXISTS `brands`;

CREATE TABLE `brands` (
  `id` smallint(5) unsigned NOT NULL COMMENT 'Brand ID',
  `name` text,
  `description` text NOT NULL COMMENT 'Description',
  `status` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `date_update` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  UNIQUE KEY `brand_uni` (`id`),
  KEY `brand_id_idx` (`id`),
  FULLTEXT KEY `full_name` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Shops brands';



# Дамп таблицы buy
# ------------------------------------------------------------

DROP TABLE IF EXISTS `buy`;

CREATE TABLE `buy` (
  `id` int(11) unsigned NOT NULL COMMENT 'Product Id',
  `top_ten` text NOT NULL,
  `last_update` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  CONSTRAINT `fk_id_product_id` FOREIGN KEY (`id`) REFERENCES `products` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



# Дамп таблицы categories
# ------------------------------------------------------------

DROP TABLE IF EXISTS `categories`;

CREATE TABLE `categories` (
  `id` smallint(5) unsigned NOT NULL DEFAULT '0' COMMENT 'Category ID',
  `name` varchar(45) NOT NULL DEFAULT '' COMMENT 'Category Name',
  `parent_id` smallint(5) unsigned NOT NULL DEFAULT '0' COMMENT 'Parent Category',
  `alias` varchar(64) NOT NULL DEFAULT '' COMMENT 'URL Alias',
  `sex` tinyint(1) unsigned DEFAULT '0' COMMENT 'Category sex',
  `status` tinyint(1) unsigned DEFAULT '1',
  `date_update` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `id_par_idx` (`id`,`parent_id`),
  KEY `idx_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Categories table';



# Дамп таблицы category_shop_relationship
# ------------------------------------------------------------

DROP TABLE IF EXISTS `category_shop_relationship`;

CREATE TABLE `category_shop_relationship` (
  `category_id` smallint(5) unsigned NOT NULL DEFAULT '0' COMMENT 'Categories rel',
  `category_parent_id` smallint(5) unsigned NOT NULL DEFAULT '0' COMMENT 'Parent category',
  `shop_id` tinyint(2) unsigned NOT NULL DEFAULT '0' COMMENT 'Shops rel',
  `sort` tinyint(1) unsigned NOT NULL DEFAULT '0',
  UNIQUE KEY `uni_category_chop` (`category_id`,`shop_id`),
  KEY `category_parent_idx` (`category_id`,`category_parent_id`),
  KEY `category_id_idx` (`category_id`),
  KEY `sort_cat_idx` (`sort`,`category_id`),
  KEY `sort_idx` (`sort`),
  KEY `fk_category_shop_idx` (`shop_id`),
  CONSTRAINT `dk_category_category` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_category_shop` FOREIGN KEY (`shop_id`) REFERENCES `shops` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Product Tags relationships';



# Дамп таблицы documents
# ------------------------------------------------------------

DROP TABLE IF EXISTS `documents`;

CREATE TABLE `documents` (
  `id` int(11) unsigned NOT NULL COMMENT 'Document Id',
  `href` varchar(255) DEFAULT '' COMMENT 'Document URI',
  `title` varchar(255) DEFAULT '' COMMENT 'Document title',
  `ru` text NOT NULL COMMENT 'Description text',
  `ua` text COMMENT 'Description text',
  `en` text COMMENT 'Description text',
  `site_id` tinyint(2) unsigned DEFAULT NULL COMMENT 'Site Id',
  `status` tinyint(1) unsigned DEFAULT NULL COMMENT 'Public status',
  `sort` tinyint(1) DEFAULT '0',
  `last_update` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT 'Record date update',
  PRIMARY KEY (`id`),
  UNIQUE KEY `uni_id_site_id` (`id`,`site_id`),
  KEY `idx_status` (`status`),
  KEY `idx_site` (`site_id`),
  KEY `idx_site_status` (`site_id`,`status`),
  KEY `idx_url` (`href`),
  CONSTRAINT `fk_site_doc` FOREIGN KEY (`site_id`) REFERENCES `shops` (`site_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Static pages';



# Дамп таблицы prices
# ------------------------------------------------------------

DROP TABLE IF EXISTS `prices`;

CREATE TABLE `prices` (
  `id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT 'Price ID',
  `product_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT 'product_id = products.id',
  `price` decimal(10,2) unsigned NOT NULL DEFAULT '0.00' COMMENT 'Full product price',
  `discount` decimal(10,2) unsigned DEFAULT '0.00' COMMENT 'Full discount price',
  `percent` tinyint(3) DEFAULT '0' COMMENT 'Discount %',
  `date_update` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`,`product_id`),
  UNIQUE KEY `price_prod_uni` (`id`,`product_id`),
  KEY `product_id_idx` (`product_id`),
  KEY `price_prod_idx` (`price`,`product_id`),
  KEY `discount_idx` (`discount`),
  KEY `id_disc_idx` (`id`,`discount`),
  CONSTRAINT `fk_prices_shop` FOREIGN KEY (`id`) REFERENCES `shops` (`price_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_product_price` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Shops prices';



# Дамп таблицы products
# ------------------------------------------------------------

DROP TABLE IF EXISTS `products`;

CREATE TABLE `products` (
  `id` int(11) unsigned NOT NULL COMMENT 'Product Id',
  `articul` varchar(16) NOT NULL DEFAULT '' COMMENT 'Articul',
  `name` text,
  `description` text NOT NULL COMMENT 'Description',
  `images` varchar(255) NOT NULL DEFAULT '' COMMENT 'Images (JSON)',
  `brand_id` smallint(5) unsigned DEFAULT NULL COMMENT 'Brand ID',
  `is_new` tinyint(1) DEFAULT '0',
  `sex` tinyint(1) unsigned DEFAULT '0' COMMENT 'Gender',
  `rating` int(11) DEFAULT '0' COMMENT 'Rating',
  `published` tinyint(1) unsigned DEFAULT '1' COMMENT 'Is published',
  `tags` text,
  `preview` varchar(255) DEFAULT NULL,
  `filter_size` varchar(512) DEFAULT NULL,
  `date_income` datetime DEFAULT NULL,
  `date_update` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `brands_fk_idx` (`brand_id`),
  KEY `prod_brand_idx` (`id`,`brand_id`),
  KEY `id_idx` (`id`),
  KEY `rating_product_idx` (`rating`,`id`),
  KEY `sex_product_idx` (`sex`,`id`),
  KEY `brand_prod_idx` (`brand_id`,`id`),
  KEY `art_rating_idx` (`articul`,`rating`),
  KEY `is_new_product` (`is_new`,`id`),
  KEY `prod_rating_idx` (`id`,`rating`),
  KEY `published_idx` (`published`),
  KEY `id_published_idx` (`id`,`published`),
  KEY `date_income_idx` (`date_income`),
  KEY `idx_date_update` (`date_update`),
  FULLTEXT KEY `full_name` (`name`),
  CONSTRAINT `product_brand_fk` FOREIGN KEY (`brand_id`) REFERENCES `brands` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Shops products';



# Дамп таблицы products_relationship
# ------------------------------------------------------------

DROP TABLE IF EXISTS `products_relationship`;

CREATE TABLE `products_relationship` (
  `product_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT 'Product ID',
  `category_id` smallint(5) unsigned DEFAULT NULL COMMENT 'Product category rel',
  `tag_id` int(11) unsigned DEFAULT NULL COMMENT 'Product tag rel',
  `tag_meta_id` tinyint(1) unsigned DEFAULT NULL COMMENT 'Tag meta id',
  UNIQUE KEY `uni_all` (`product_id`,`category_id`,`tag_id`,`tag_meta_id`),
  UNIQUE KEY `uni_product_tags` (`product_id`,`tag_id`,`tag_meta_id`),
  UNIQUE KEY `uni_product_category` (`product_id`,`category_id`),
  KEY `category_id_idx` (`category_id`),
  KEY `tag_id_idx` (`tag_id`),
  KEY `fk_tag_rel` (`tag_id`,`tag_meta_id`),
  CONSTRAINT `fk_category_id` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_product_id` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_tag_rel` FOREIGN KEY (`tag_id`, `tag_meta_id`) REFERENCES `tags` (`id`, `tag_meta_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Product Tags relationships';



# Дамп таблицы shops
# ------------------------------------------------------------

DROP TABLE IF EXISTS `shops`;

CREATE TABLE `shops` (
  `id` tinyint(2) unsigned NOT NULL COMMENT 'Shop ID',
  `site_id` tinyint(2) unsigned DEFAULT NULL COMMENT 'Site ID',
  `price_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT 'price_id = prices.id',
  `name` varchar(64) NOT NULL DEFAULT '' COMMENT 'Shop name',
  `title` varchar(128) NOT NULL DEFAULT '' COMMENT 'Shoip title',
  `code` varchar(5) DEFAULT '' COMMENT 'Code for Orders ident',
  `currency` varchar(3) NOT NULL DEFAULT '' COMMENT 'Currency',
  `currency_symbol` varchar(4) NOT NULL DEFAULT '' COMMENT '$ E etc..',
  `country_code` smallint(4) unsigned NOT NULL COMMENT 'Country phone code',
  `discounts` varchar(512) DEFAULT '' COMMENT 'Shop discounts config',
  `token_key` varchar(32) NOT NULL COMMENT 'Token Key',
  `country` char(2) NOT NULL DEFAULT 'ru' COMMENT 'Country',
  `delivery_ids` varchar(32) DEFAULT '' COMMENT 'Rel -> shops_deliveries',
  `payment_ids` varchar(32) DEFAULT NULL COMMENT 'Rel -> shops_payments',
  `date_update` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `shop_price_fk` (`price_id`),
  KEY `idx_site_id` (`site_id`),
  KEY `idx_price` (`price_id`),
  KEY `idx_country_code` (`country_code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Shops data  `site_id` tinyint(2) unsigned DEFAULT NULL,';



# Дамп таблицы shops_deliveries
# ------------------------------------------------------------

DROP TABLE IF EXISTS `shops_deliveries`;

CREATE TABLE `shops_deliveries` (
  `id` tinyint(3) unsigned NOT NULL,
  `title` varchar(255) NOT NULL,
  `text` text NOT NULL,
  `sort` tinyint(3) unsigned NOT NULL,
  `short_title` varchar(255) NOT NULL,
  `price` varchar(8) NOT NULL DEFAULT '0',
  `status` tinyint(1) NOT NULL DEFAULT '1',
  `last_update` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  UNIQUE KEY `uni_id` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



# Дамп таблицы shops_payments
# ------------------------------------------------------------

DROP TABLE IF EXISTS `shops_payments`;

CREATE TABLE `shops_payments` (
  `id` tinyint(3) unsigned NOT NULL,
  `title` varchar(255) NOT NULL,
  `text` text NOT NULL,
  `sort` tinyint(3) unsigned NOT NULL,
  `last_update` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  UNIQUE KEY `uni_id` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



# Дамп таблицы shops_regions
# ------------------------------------------------------------

DROP TABLE IF EXISTS `shops_regions`;

CREATE TABLE `shops_regions` (
  `id` smallint(4) unsigned NOT NULL COMMENT 'Key',
  `country_code` smallint(4) unsigned NOT NULL COMMENT 'Phone code',
  `region` varchar(50) DEFAULT NULL COMMENT 'Region name',
  `sort` tinyint(3) unsigned DEFAULT '0' COMMENT 'Sort index',
  `last_update` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT 'Update last',
  UNIQUE KEY `uni_id_code` (`id`,`country_code`),
  KEY `country_code` (`country_code`),
  KEY `sort` (`sort`),
  CONSTRAINT `fk_country_code` FOREIGN KEY (`country_code`) REFERENCES `shops` (`country_code`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



# Дамп таблицы tags
# ------------------------------------------------------------

DROP TABLE IF EXISTS `tags`;

CREATE TABLE `tags` (
  `id` int(11) unsigned DEFAULT NULL COMMENT 'Tag ID',
  `name` varchar(255) DEFAULT NULL COMMENT 'Tag name',
  `parent_id` smallint(5) unsigned DEFAULT NULL COMMENT 'Parent tag ID',
  `tag_meta_id` tinyint(1) unsigned DEFAULT NULL COMMENT 'Tag meta ID',
  `date_update` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT 'Date update',
  UNIQUE KEY `uni_id_meta_id` (`id`,`tag_meta_id`),
  KEY `id_parent_tag_idx` (`parent_id`,`id`),
  KEY `id_idx` (`id`),
  KEY `fk_tag_meta` (`tag_meta_id`),
  KEY `idx_tag_tag_meta` (`id`,`tag_meta_id`),
  CONSTRAINT `fk_tag_meta` FOREIGN KEY (`tag_meta_id`) REFERENCES `tags_meta` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Tags (Categories) table';



# Дамп таблицы tags_meta
# ------------------------------------------------------------

DROP TABLE IF EXISTS `tags_meta`;

CREATE TABLE `tags_meta` (
  `id` tinyint(1) unsigned NOT NULL COMMENT 'Tag meta id',
  `type` varchar(16) DEFAULT NULL COMMENT 'Tag type',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

LOCK TABLES `tags_meta` WRITE;
/*!40000 ALTER TABLE `tags_meta` DISABLE KEYS */;

INSERT INTO `tags_meta` (`id`, `type`)
VALUES
	(1,'tags'),
	(2,'sizes'),
	(3,'types'),
	(4,'category');

/*!40000 ALTER TABLE `tags_meta` ENABLE KEYS */;
UNLOCK TABLES;



/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
